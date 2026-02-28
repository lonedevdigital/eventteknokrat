<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventRecommendation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventRecommendationController extends Controller
{
    private const MAX_TOTAL_RECOMMENDATIONS = 9;
    private const MAX_ROLE_RECOMMENDATIONS = 3;

    protected function normalizeRole(?string $role): string
    {
        $role = strtolower(trim((string) $role));

        if ($role === 'admin') return 'superuser';
        if ($role === 'super_user') return 'superuser';
        if ($role === 'kemasis') return 'kemahasiswaan';

        return $role ?: 'superuser';
    }

    protected function applyRoleVisibility($query, string $userRole, $user)
    {
        if ($userRole === 'superuser') {
            return $query;
        }

        return $query->where(function ($q) use ($userRole, $user) {
            $q->where('owner_role', $userRole)
                ->orWhere(function ($qq) use ($user) {
                    $qq->whereNull('owner_role')
                        ->where('created_by_user_id', $user->id);
                });
        });
    }

    protected function authorizeEventByRole(Event $event, string $userRole, $user): void
    {
        if ($userRole === 'superuser') {
            return;
        }

        $eventRole = $this->normalizeRole($event->owner_role ?? '');

        if (empty($eventRole)) {
            if ((int) $event->created_by_user_id !== (int) $user->id) {
                abort(403, 'Anda tidak memiliki izin untuk mengelola rekomendasi event ini.');
            }
            return;
        }

        if ($eventRole !== $userRole) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola rekomendasi event ini.');
        }
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $userRole = $this->normalizeRole($user->role ?? 'superuser');

        $categories = EventCategory::query()
            ->orderBy('nama_kategori')
            ->get(['id', 'nama_kategori']);

        $eventsQuery = Event::query()
            ->with(['category', 'creator', 'recommendation.selector'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('nama_event', 'like', '%' . trim((string) $request->query('q')) . '%');
            })
            ->when($request->filled('event_category_id'), function ($query) use ($request) {
                $query->where('event_category_id', (int) $request->query('event_category_id'));
            });

        $eventsQuery = $this->applyRoleVisibility($eventsQuery, $userRole, $user);

        $events = $eventsQuery
            ->orderByDesc(
                EventRecommendation::query()
                    ->select('created_at')
                    ->whereColumn('event_recommendations.event_id', 'events.id')
                    ->limit(1)
            )
            ->orderByDesc('tanggal_pelaksanaan')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $totalRecommended = EventRecommendation::query()->count();
        $roleLimited = in_array($userRole, ['baak', 'kemahasiswaan'], true);
        $roleRecommended = $roleLimited
            ? EventRecommendation::query()->where('selected_by_role', $userRole)->count()
            : null;

        $canAddMoreGlobal = $totalRecommended < self::MAX_TOTAL_RECOMMENDATIONS;
        $canAddMoreByRole = !$roleLimited || ((int) $roleRecommended < self::MAX_ROLE_RECOMMENDATIONS);
        $canRecommend = $canAddMoreGlobal && $canAddMoreByRole;

        return view('admin.events.recommendations', [
            'events' => $events,
            'categories' => $categories,
            'userRole' => $userRole,
            'totalRecommended' => $totalRecommended,
            'roleRecommended' => $roleRecommended,
            'maxTotal' => self::MAX_TOTAL_RECOMMENDATIONS,
            'maxRole' => self::MAX_ROLE_RECOMMENDATIONS,
            'canRecommend' => $canRecommend,
        ]);
    }

    public function toggle(Request $request, Event $event): RedirectResponse
    {
        $user = $request->user();
        $userRole = $this->normalizeRole($user->role ?? 'superuser');

        $this->authorizeEventByRole($event, $userRole, $user);

        $existingRecommendation = EventRecommendation::query()
            ->where('event_id', $event->id)
            ->first();

        if ($existingRecommendation) {
            $existingRecommendation->delete();

            return redirect()
                ->back()
                ->with('success', 'Event "' . $event->nama_event . '" dihapus dari rekomendasi.');
        }

        $totalRecommended = EventRecommendation::query()->count();
        if ($totalRecommended >= self::MAX_TOTAL_RECOMMENDATIONS) {
            return redirect()
                ->back()
                ->with('error', 'Maksimal rekomendasi adalah ' . self::MAX_TOTAL_RECOMMENDATIONS . ' event.');
        }

        if (in_array($userRole, ['baak', 'kemahasiswaan'], true)) {
            $roleRecommended = EventRecommendation::query()
                ->where('selected_by_role', $userRole)
                ->count();

            if ($roleRecommended >= self::MAX_ROLE_RECOMMENDATIONS) {
                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Role ' . strtoupper($userRole) . ' maksimal hanya bisa highlight ' . self::MAX_ROLE_RECOMMENDATIONS . ' event.'
                    );
            }
        }

        EventRecommendation::query()->create([
            'event_id' => $event->id,
            'selected_by_user_id' => $user->id,
            'selected_by_role' => $userRole,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Event "' . $event->nama_event . '" berhasil ditambahkan ke rekomendasi.');
    }
}
