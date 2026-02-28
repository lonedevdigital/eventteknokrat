<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Info;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function index()
    {
        $infos = Info::orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.infos.index', compact('infos'));
    }

    public function create()
    {
        return view('admin.infos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'        => 'required|string|max:255',
            'isi'          => 'nullable|string',
            'is_published' => 'sometimes|boolean',
        ]);

        $data['created_by']   = auth()->id();
        $data['published_at'] = ($data['is_published'] ?? false) ? now() : null;

        Info::create($data);

        return redirect()
            ->route('infos.index')
            ->with('success', 'Info terkini berhasil dibuat.');
    }

    public function edit(Info $info)
    {
        return view('admin.infos.edit', compact('info'));
    }

    public function update(Request $request, Info $info)
    {
        $data = $request->validate([
            'judul'        => 'required|string|max:255',
            'isi'          => 'nullable|string',
            'is_published' => 'sometimes|boolean',
        ]);

        $data['published_at'] = ($data['is_published'] ?? false) ? now() : null;

        $info->update($data);

        return redirect()
            ->route('infos.index')
            ->with('success', 'Info terkini berhasil diperbarui.');
    }

    public function destroy(Info $info)
    {
        $info->delete();

        return redirect()
            ->route('infos.index')
            ->with('success', 'Info terkini berhasil dihapus.');
    }
}
