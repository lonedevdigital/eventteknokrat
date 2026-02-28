<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MyEventController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $filter = $request->query('filter'); // ongoing, upcoming, finished

        $query = EventRegistration::with('event')
            ->where('user_id', $user->id);

        $today = Carbon::today();

        // FILTERING EVENTS
        if ($filter === 'ongoing') {
            $query->whereHas('event', fn($q) =>
            $q->whereDate('tanggal_pelaksanaan', $today)
            );
        }

        if ($filter === 'upcoming') {
            $query->whereHas('event', fn($q) =>
            $q->whereDate('tanggal_pelaksanaan', '>', $today)
            );
        }

        if ($filter === 'finished') {
            $query->whereHas('event', fn($q) =>
            $q->whereDate('tanggal_pelaksanaan', '<', $today)
            );
        }

        $events = $query->get()->map(fn($item) => $this->formatEvent($item));

        return response()->json([
            'status' => true,
            'message' => 'List event yang Anda ikuti',
            'data' => $events
        ]);
    }

    private function formatEvent($item)
    {
        return [
            'registration_id' => $item->id,
            'event_id'        => $item->event->id,
            'slug'            => $item->event->slug,
            'nama_event'      => $item->event->nama_event,
            'thumbnail'       => $item->event->thumbnail,
            'tempat'          => $item->event->tempat_pelaksanaan,
            'tanggal'         => $item->event->tanggal_pelaksanaan,
            'waktu'           => $item->event->waktu_pelaksanaan,
            'status_daftar'   => $item->status,          // registered / attended
            'attendance_at'   => $item->attendance_at,

            // FIX UTAMA
            'certificate_url' => $item->certificate_url
                ? url($item->certificate_url)
                : null,
        ];
    }

}
