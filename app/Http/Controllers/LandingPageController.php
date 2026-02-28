<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventQrToken;
use App\Models\EventRegistration;
use App\Models\Info;
use App\Models\Sponsor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LandingPageController extends Controller
{
    public function index(Request $request): View
    {
        $today = now()->toDateString();
        $hotEvents = $this->hotEvents($today);
        $latestInfos = Info::query()
            ->where('is_published', true)
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get(['id', 'judul', 'isi', 'published_at', 'updated_at']);
        $sponsors = Sponsor::query()
            ->where('is_active', true)
            ->orderBy('urutan')
            ->orderByDesc('updated_at')
            ->get(['id', 'nama', 'link_url', 'logo_path']);
        $imageDocumentationFilter = function ($query) {
            $query->where(function ($imageQuery) {
                $imageQuery->whereIn('file_type', ['jpg', 'jpeg', 'png', 'gif', 'webp'])
                    ->orWhere(function ($nullTypeQuery) {
                        $nullTypeQuery->whereNull('file_type')
                            ->where(function ($pathQuery) {
                                $pathQuery->where('file_path', 'like', '%.jpg')
                                    ->orWhere('file_path', 'like', '%.jpeg')
                                    ->orWhere('file_path', 'like', '%.png')
                                    ->orWhere('file_path', 'like', '%.gif')
                                    ->orWhere('file_path', 'like', '%.webp');
                            });
                    });
            });
        };

        $galleryEvents = Event::query()
            ->select(['id', 'nama_event', 'tanggal_pelaksanaan'])
            ->whereHas('documentations', $imageDocumentationFilter)
            ->with(['documentations' => function ($query) use ($imageDocumentationFilter) {
                $imageDocumentationFilter($query);
                $query->orderBy('created_at', 'asc');
            }])
            ->orderByDesc('tanggal_pelaksanaan')
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        $events = $this->homeEventListQuery()
            ->paginate(8)
            ->withQueryString();

        return view('frontend.index', [
            'hotEvents' => $hotEvents,
            'latestInfos' => $latestInfos,
            'sponsors' => $sponsors,
            'events' => $events,
            'galleryEvents' => $galleryEvents,
        ]);
    }

    public function events(Request $request): View
    {
        $today = now()->toDateString();
        $categories = EventCategory::query()
            ->orderBy('nama_kategori')
            ->get(['id', 'nama_kategori']);

        $eventsQuery = $this->eventListQuery($today)
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('nama_event', 'like', '%' . trim((string) $request->query('q')) . '%');
            })
            ->when($request->filled('event_category_id'), function ($query) use ($request) {
                $query->where('event_category_id', (int) $request->query('event_category_id'));
            })
            ->when($request->filled('status'), function ($query) use ($request, $today) {
                $status = (string) $request->query('status');

                if ($status === 'upcoming') {
                    $query->whereDate('tanggal_pelaksanaan', '>', $today);
                } elseif ($status === 'ongoing') {
                    $query->whereDate('tanggal_pelaksanaan', '=', $today);
                } elseif ($status === 'past') {
                    $query->whereDate('tanggal_pelaksanaan', '<', $today);
                }
            })
            ->when($request->filled('month'), function ($query) use ($request) {
                $query->whereMonth('tanggal_pelaksanaan', (int) $request->query('month'));
            })
            ->when($request->filled('year'), function ($query) use ($request) {
                $query->whereYear('tanggal_pelaksanaan', (int) $request->query('year'));
            });

        $events = $eventsQuery
            ->paginate(8)
            ->withQueryString();

        return view('frontend.events', [
            'events' => $events,
            'categories' => $categories,
        ]);
    }

    public function eventDetail(Request $request, string $slug): View
    {
        $event = Event::query()
            ->with(['category', 'creator'])
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $hasRegistered = false;
        if ($request->user()) {
            $hasRegistered = EventRegistration::query()
                ->where('event_id', $event->id)
                ->where('user_id', $request->user()->id)
                ->exists();
        }

        return view('frontend.event-detail', [
            'event' => $event,
            'hasRegistered' => $hasRegistered,
            'isRegistrationClosed' => $event->is_registration_closed,
        ]);
    }

    public function registerEvent(Request $request, string $slug): RedirectResponse
    {
        $event = Event::query()
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $alreadyRegistered = EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($alreadyRegistered) {
            return redirect()
                ->route('frontend.events.show', $event->slug ?: $event->id)
                ->with('info', 'Anda sudah mendaftar event ini.');
        }

        if ($event->is_registration_closed) {
            return redirect()
                ->route('frontend.events.show', $event->slug ?: $event->id)
                ->with('info', 'Pendaftaran sudah ditutup.');
        }

        EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $request->user()->id,
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        return redirect()
            ->route('frontend.events.show', $event->slug ?: $event->id)
            ->with('success', 'Berhasil mendaftar event.');
    }

    public function myEvents(Request $request): View
    {
        $today = now()->toDateString();
        $categories = EventCategory::query()
            ->orderBy('nama_kategori')
            ->get(['id', 'nama_kategori']);

        $registrationsQuery = EventRegistration::query()
            ->with(['event.category'])
            ->where('user_id', $request->user()->id)
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = trim((string) $request->query('q'));

                $query->whereHas('event', function ($eventQuery) use ($keyword) {
                    $eventQuery->where('nama_event', 'like', '%' . $keyword . '%');
                });
            })
            ->when($request->filled('event_category_id'), function ($query) use ($request) {
                $categoryId = (int) $request->query('event_category_id');

                $query->whereHas('event', function ($eventQuery) use ($categoryId) {
                    $eventQuery->where('event_category_id', $categoryId);
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', (string) $request->query('status'));
            })
            ->when($request->filled('attendance_status'), function ($query) use ($request) {
                $query->where('attendance_status', (string) $request->query('attendance_status'));
            })
            ->when($request->filled('event_status'), function ($query) use ($request, $today) {
                $eventStatus = (string) $request->query('event_status');

                $query->whereHas('event', function ($eventQuery) use ($eventStatus, $today) {
                    if ($eventStatus === 'upcoming') {
                        $eventQuery->whereDate('tanggal_pelaksanaan', '>', $today);
                    } elseif ($eventStatus === 'ongoing') {
                        $eventQuery->whereDate('tanggal_pelaksanaan', '=', $today);
                    } elseif ($eventStatus === 'past') {
                        $eventQuery->whereDate('tanggal_pelaksanaan', '<', $today);
                    }
                });
            })
            ->orderByDesc('registered_at')
            ->orderByDesc('id');

        $registrations = $registrationsQuery
            ->paginate(8)
            ->withQueryString();

        return view('frontend.my-events', [
            'registrations' => $registrations,
            'categories' => $categories,
        ]);
    }

    public function attendanceScanner(Request $request): View
    {
        $recentAttendances = EventRegistration::query()
            ->with('event')
            ->where('user_id', $request->user()->id)
            ->where('status', 'attended')
            ->orderByDesc('attendance_at')
            ->limit(5)
            ->get();

        return view('frontend.attendance-scan', [
            'recentAttendances' => $recentAttendances,
        ]);
    }

    public function submitAttendance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qr_token' => ['required', 'string', 'max:255'],
        ]);

        $token = $this->extractAttendanceToken((string) $validated['qr_token']);
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Format QR tidak valid.',
            ], 422);
        }

        $qr = EventQrToken::query()
            ->with('event')
            ->where('qr_token', $token)
            ->first();

        if (!$qr) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid.',
            ], 404);
        }

        if (now()->greaterThan($qr->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code sudah kadaluarsa.',
            ], 410);
        }

        $registration = EventRegistration::query()
            ->where('event_id', $qr->event_id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum terdaftar di event ini.',
            ], 403);
        }

        if ($registration->status === 'attended') {
            return response()->json([
                'success' => true,
                'already' => true,
                'message' => 'Anda sudah absen sebelumnya.',
                'data' => [
                    'event' => $qr->event?->nama_event,
                    'attendance_at' => optional($registration->attendance_at)->toDateTimeString(),
                    'status' => 'Sudah Absen',
                ],
            ]);
        }

        $registration->update([
            'attendance_status' => 'present',
            'attendance_at' => now(),
            'status' => 'attended',
        ]);

        return response()->json([
            'success' => true,
            'already' => false,
            'message' => 'Absensi berhasil dicatat.',
            'data' => [
                'event' => $qr->event?->nama_event,
                'attendance_at' => optional($registration->attendance_at)->toDateTimeString(),
                'status' => 'Sudah Absen',
            ],
        ]);
    }

    public function certificates(Request $request): View
    {
        $certificates = EventRegistration::query()
            ->with(['event.category'])
            ->where('user_id', $request->user()->id)
            ->whereNotNull('certificate_url')
            ->orderByDesc('certificate_uploaded_at')
            ->paginate(8)
            ->withQueryString();

        return view('frontend.certificates', [
            'certificates' => $certificates,
        ]);
    }

    protected function hotEvents(string $today)
    {
        if (Schema::hasTable('event_recommendations')) {
            $hotEvents = Event::query()
                ->select('events.*')
                ->with('category')
                ->join('event_recommendations as recommendations', 'recommendations.event_id', '=', 'events.id')
                ->orderByRaw(
                    'CASE WHEN events.tanggal_pelaksanaan IS NULL THEN 2 WHEN events.tanggal_pelaksanaan >= ? THEN 0 ELSE 1 END',
                    [$today]
                )
                ->orderBy('events.tanggal_pelaksanaan', 'asc')
                ->orderByDesc('recommendations.created_at')
                ->limit(9)
                ->get();

            if ($hotEvents->isNotEmpty()) {
                return $hotEvents;
            }
        }

        $hotEvents = Event::query()
            ->with('category')
            ->whereNotNull('tanggal_pelaksanaan')
            ->whereDate('tanggal_pelaksanaan', '>=', $today)
            ->orderBy('tanggal_pelaksanaan', 'asc')
            ->limit(9)
            ->get();

        if ($hotEvents->count() < 9) {
            $extra = Event::query()
                ->with('category')
                ->whereNotIn('id', $hotEvents->pluck('id'))
                ->whereNotNull('tanggal_pelaksanaan')
                ->orderByDesc('tanggal_pelaksanaan')
                ->limit(9 - $hotEvents->count())
                ->get();

            $hotEvents = $hotEvents->concat($extra);
        }

        return $hotEvents;
    }

    protected function eventListQuery(string $today)
    {
        return Event::query()
            ->with('category')
            ->orderByRaw(
                'CASE WHEN tanggal_pelaksanaan IS NULL THEN 2 WHEN tanggal_pelaksanaan >= ? THEN 0 ELSE 1 END',
                [$today]
            )
            ->orderBy('tanggal_pelaksanaan', 'asc')
            ->orderByDesc('created_at');
    }

    protected function homeEventListQuery()
    {
        return Event::query()
            ->with('category')
            ->orderByRaw('CASE WHEN tanggal_pelaksanaan IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('tanggal_pelaksanaan')
            ->orderByDesc('created_at');
    }

    protected function extractAttendanceToken(string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        // Handle URL scan result: https://domain/presensi/qr/{token}?...
        if (preg_match('#/presensi/qr/([A-Za-z0-9\-_]{20,})#i', $raw, $matches)) {
            return $matches[1] ?? null;
        }

        // Handle plain token content.
        if (preg_match('/^[A-Za-z0-9\-_]{20,}$/', $raw)) {
            return $raw;
        }

        // Last fallback: pick likely token chunk from arbitrary payload text.
        if (preg_match('/([A-Za-z0-9\-_]{20,})/', $raw, $matches)) {
            return $matches[1] ?? null;
        }

        return null;
    }
}
