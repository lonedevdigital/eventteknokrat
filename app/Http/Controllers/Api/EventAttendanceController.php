<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventQrToken;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class EventAttendanceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'qr_token' => 'required'
        ]);

        $user = $request->user();

        // 1. VALIDASI QR TOKEN
        $qr = EventQrToken::where('qr_token', $request->qr_token)->first();

        if (!$qr) {
            return response()->json([
                'status' => false,
                'message' => 'QR Code tidak valid'
            ], 400);
        }

        // 2. VALIDASI EXPIRED
        if (now()->greaterThan($qr->expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'QR Code sudah kadaluarsa'
            ], 410);
        }

        // 3. USER HARUS TERDAFTAR DI EVENT
        $registration = EventRegistration::where('event_id', $qr->event_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$registration) {
            return response()->json([
                'status' => false,
                'message' => 'Kamu belum terdaftar di event ini'
            ], 403);
        }

        // 4. CEK SUDAH PRESENSI?
        if ($registration->status === 'attended') {
            return response()->json([
                'status' => false,
                'message' => 'Kamu sudah presensi sebelumnya'
            ], 409);
        }

        // 5. UPDATE PRESENSI
        $registration->update([
            'attendance_at' => now(),
            'attendance_status' => 'present',
            'status' => 'attended'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Presensi berhasil dicatat',
            'data' => [
                'event' => $registration->event->nama_event,
                'scan_time' => now(),
                'status' => 'attended'
            ]
        ]);
    }
}
