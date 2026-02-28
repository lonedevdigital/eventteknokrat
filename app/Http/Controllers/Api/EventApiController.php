<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;

class EventApiController extends Controller
{
    /**
     * 1. GET LIST EVENTS
     * GET /api/events
     */
    public function index()
    {
        // ambil semua event + kategori, urutkan berdasarkan tanggal_pelaksanaan terbaru dulu
        $events = Event::with('category')
            ->orderBy('tanggal_pelaksanaan', 'DESC')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'List Events',
            'data'    => $events,
        ]);
    }

    /**
     * 2. GET SLIDER EVENTS
     * Bisa pakai 5 event terdekat yang masih ada tanggal_pelaksanaan
     * GET /api/events/slider
     */
    public function slider()
    {
        $events = Event::with('category')
            ->whereNotNull('tanggal_pelaksanaan')
            ->orderBy('tanggal_pelaksanaan', 'ASC') // terdekat dulu
            ->limit(5)
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Slider Events',
            'data'    => $events,
        ]);
    }

    /**
     * 3. DETAIL EVENT VIA SLUG
     * GET /api/events/{slug}
     */
    public function detail($slug)
    {
        $event = Event::with(['category'])
            ->where('slug', $slug)
            ->first();

        if (! $event) {
            return response()->json([
                'status'  => false,
                'message' => 'Event tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail Event',
            'data'    => $event,
        ]);
    }

    /**
     * 4. DAFTAR PESERTA EVENT
     * GET /api/events/{event_id}/participants
     */
    public function participants($event_id)
    {
        // pastikan event ada
        $event = Event::find($event_id);

        if (! $event) {
            return response()->json([
                'status'  => false,
                'message' => 'Event tidak ditemukan',
            ], 404);
        }

        // ambil semua pendaftaran + user-nya
        $participants = EventRegistration::where('event_id', $event_id)
            ->with('user') // sesuaikan dengan relasi di EventRegistration
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Daftar Peserta Event',
            'event'   => [
                'id'            => $event->id,
                'nama_event'    => $event->nama_event,
                'slug'          => $event->slug,
                'tanggal'       => $event->tanggal_pelaksanaan,
                'jumlah_peserta'=> $event->jumlah_peserta,
            ],
            'data'    => $participants,
        ]);
    }
}
