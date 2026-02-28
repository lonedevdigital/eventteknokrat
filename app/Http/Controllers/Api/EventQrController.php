<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventQrToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventQrController extends Controller
{
    private const QR_LIFETIME_MINUTES = 5;

    public function generate(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        EventQrToken::where('event_id', $event->id)->delete();

        // generate token random 40 char
        $token = Str::random(40);

        $qr = EventQrToken::create([
            'event_id' => $event->id,
            'qr_token' => $token,
            'expires_at' => now()->addMinutes(self::QR_LIFETIME_MINUTES),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'QR berhasil dibuat',
            'qr_link' => url("/presensi/qr/" . $qr->qr_token),
            'expires_at' => $qr->expires_at,
            'lifetime_minutes' => self::QR_LIFETIME_MINUTES,
        ]);
    }
}
