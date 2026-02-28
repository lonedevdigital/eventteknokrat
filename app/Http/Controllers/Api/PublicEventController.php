<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    /**
     * GET /api/events
     * List event untuk publik (tanpa auth).
     */
    public function index(Request $request)
    {
        // kalau mau nanti bisa ditambah filter search / kategori
        $events = Event::with('category')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'List event',
            'data'    => $events,
        ]);
    }

    /**
     * GET /api/events/{slug}
     * Detail event berdasarkan slug.
     */
    public function show($slug)
    {
        $event = Event::with(['category', 'creator'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'status'  => true,
            'message' => 'Detail event',
            'data'    => $event,
        ]);
    }

    /**
     * GET /api/sliders
     * Data slider (misal ambil beberapa event terbaru yang ada thumbnail-nya).
     */
    public function sliders()
    {
        // contoh: ambil 5 event terbaru yang punya thumbnail
        $sliders = Event::whereNotNull('thumbnail')
            ->orderByDesc('created_at')
            ->take(5)
            ->get([
                'slug',
                'nama_event',
                'thumbnail',
                'tanggal_pelaksanaan',
                'waktu_pelaksanaan',
                'tempat_pelaksanaan',
            ]);

        return response()->json([
            'status'  => true,
            'message' => 'List slider event',
            'data'    => $sliders,
        ]);
    }
}
