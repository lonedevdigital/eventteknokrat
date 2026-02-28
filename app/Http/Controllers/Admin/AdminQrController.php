<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventQrToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AdminQrController extends Controller
{
    private const QR_LIFETIME_MINUTES = 5;

    protected function resolveEventByReference(string $reference): Event
    {
        $reference = trim($reference);

        return Event::query()
            ->where('slug', $reference)
            ->orWhere('id', $reference)
            ->firstOrFail();
    }

    protected function buildQrPath(string $token): string
    {
        return route('public.qr.show', ['qr_token' => $token], false);
    }

    protected function normalizeRole(?string $role): string
    {
        $role = strtolower(trim((string) $role));

        if ($role === 'admin') return 'superuser';
        if ($role === 'super_user') return 'superuser';
        if ($role === 'kemasis') return 'kemahasiswaan';

        return $role ?: 'superuser';
    }

    protected function authorizeEventByRole(Event $event): void
    {
        $user = auth()->user();
        $userRole = $this->normalizeRole($user->role ?? 'superuser');

        if ($userRole === 'superuser') {
            return;
        }

        $eventRole = $this->normalizeRole($event->owner_role ?? '');

        if (empty($eventRole)) {
            if ((int) $event->created_by_user_id !== (int) $user->id) {
                abort(403, 'Anda tidak memiliki izin untuk mengelola QR event ini.');
            }
            return;
        }

        if ($eventRole !== $userRole) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola QR event ini.');
        }
    }

    protected function createFreshToken(Event $event): EventQrToken
    {
        // Hapus token lama agar hanya ada satu token aktif setiap event.
        EventQrToken::where('event_id', $event->id)->delete();

        return EventQrToken::create([
            'event_id'    => $event->id,
            'qr_token'    => Str::random(40),
            'expires_at'  => now()->addMinutes(self::QR_LIFETIME_MINUTES),
        ]);
    }

    /**
     * Generate QR token untuk event
     */
    public function generate(string $eventRef)
    {
        $event = $this->resolveEventByReference($eventRef);
        $this->authorizeEventByRole($event);

        $qr = $this->createFreshToken($event);

        // Link untuk presensi
        $qrLink = $this->buildQrPath($qr->qr_token);

        return view('admin.qr.show', [
            'event'   => $event,
            'qrLink'  => $qrLink,
            'expires' => $qr->expires_at,
            'refreshEndpoint' => route('admin.events.qr.refresh', $event->id),
            'lifetimeMinutes' => self::QR_LIFETIME_MINUTES,
        ]);
    }

    public function refresh(Event $event): JsonResponse
    {
        $this->authorizeEventByRole($event);

        $qr = $this->createFreshToken($event);
        $qrLink = $this->buildQrPath($qr->qr_token);

        return response()->json([
            'success' => true,
            'qr_link' => $qrLink,
            'qr_url' => url($qrLink),
            'token' => $qr->qr_token,
            'expires_at_iso' => $qr->expires_at?->toIso8601String(),
            'expires_at_human' => $qr->expires_at?->format('d M Y H:i:s'),
            'lifetime_minutes' => self::QR_LIFETIME_MINUTES,
        ]);
    }
}
