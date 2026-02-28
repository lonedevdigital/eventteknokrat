<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class EventRegistrationController extends Controller
{
    public function store(Request $request, $slug)
    {
        $user = $request->user(); // login = user

        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'status' => false,
                'message' => 'Event tidak ditemukan'
            ], 404);
        }

        // cek apakah user sudah daftar
        $already = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)    // FIX DI SINI
            ->first();

        if ($already) {
            return response()->json([
                'status' => false,
                'message' => 'Kamu sudah terdaftar di event ini'
            ], 409);
        }

        if ($event->is_registration_closed) {
            return response()->json([
                'status' => false,
                'message' => 'Pendaftaran event sudah ditutup'
            ], 422);
        }

        // DAFTARKAN USER
        $reg = EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $user->id,         // FIX DI SINI
            'status' => 'registered'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mendaftar event',
            'data' => $reg
        ]);
    }

    public function myEvents(Request $request)
    {
        $user = $request->user();

        $registrations = EventRegistration::with('event')
            ->where('user_id', $user->id)    // SUDAH BENAR
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'List event yang telah diregistrasi',
            'data' => $registrations->map(function ($item) {
                return [
                    'id' => $item->event->id,
                    'nama_event' => $item->event->nama_event,
                    'thumbnail' => $item->event->thumbnail,
                    'tempat_pelaksanaan' => $item->event->tempat_pelaksanaan,
                    'tanggal_pelaksanaan' => $item->event->tanggal_pelaksanaan,
                    'waktu_pelaksanaan' => $item->event->waktu_pelaksanaan,
                    'status_pendaftaran' => $item->status, // lebih akurat
                ];
            })
        ]);
    }
}
