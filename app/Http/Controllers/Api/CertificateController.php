<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function show(Request $request, $slug)
    {
        $user = $request->user(); // sekarang pakai user, bukan mahasiswa

        // Cari event berdasarkan slug
        $event = Event::where('slug', $slug)->first();

        if (!$event) {
            return response()->json([
                'status' => false,
                'message' => 'Event tidak ditemukan'
            ], 404);
        }

        // Cari registrasi berdasarkan event + user
        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)    // FIX UTAMA
            ->first();

        // Jika belum hadir → sertifikat tidak boleh keluar
        if (!$registration || $registration->status !== 'attended') {
            return response()->json([
                'status' => false,
                'message' => 'Sertifikat hanya tersedia setelah kamu melakukan presensi.'
            ], 403);
        }

        // Jika certificate_url belum ada, FE tetap dapat null
        $certificateUrl = $registration->certificate_url
            ? url($registration->certificate_url)
            : null;

        return response()->json([
            'status' => true,
            'message' => 'Sertifikat tersedia',
            'data' => [
                'event' => [
                    'id' => $event->id,
                    'nama_event' => $event->nama_event,
                    'tanggal_pelaksanaan' => $event->tanggal_pelaksanaan,
                    'waktu_pelaksanaan' => $event->waktu_pelaksanaan,
                ],
                'user' => [
                    'id' => $user->id,
                    'nama' => $user->name,
                ],
                'certificate_url' => $certificateUrl
            ]
        ]);
    }
}
