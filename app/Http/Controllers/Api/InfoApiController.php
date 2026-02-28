<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Info;
use Illuminate\Http\Request;

class InfoApiController extends Controller
{
    public function index()
    {
        // Hanya ambil yang dipublish
        $infos = Info::where('is_published', 1)
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'judul', 'isi', 'updated_at']);

        return response()->json([
            'status' => true,
            'message' => 'Daftar Info Terkini',
            'data' => $infos
        ]);
    }

    public function show($id)
    {
        $info = Info::where('id', $id)
            ->where('is_published', 1)
            ->first();

        if (!$info) {
            return response()->json([
                'status' => false,
                'message' => 'Info tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail Info',
            'data' => $info
        ]);
    }
}
