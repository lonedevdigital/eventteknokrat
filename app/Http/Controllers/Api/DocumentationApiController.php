<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Documentation;

class DocumentationApiController extends Controller
{
    public function index()
    {
        // Ambil semua event terbaru → lama
        $events = Event::orderBy('tanggal_pelaksanaan', 'desc')->get();

        // Mapping hasil
        $result = $events->map(function ($event) {

            // Ambil 1 dokumentasi pertama untuk thumbnail
            $thumbnail = Documentation::where('event_id', $event->id)
                ->orderBy('created_at', 'asc')
                ->first();

            return [
                'id'                  => $event->id,
                'nama_event'          => $event->nama_event,
                'tanggal_pelaksanaan' => $event->tanggal_pelaksanaan,
                'thumbnail'           => $thumbnail ? asset($thumbnail->file_path) : null,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Daftar event dan thumbnail dokumentasi',
            'data'    => $result
        ]);
    }
}
