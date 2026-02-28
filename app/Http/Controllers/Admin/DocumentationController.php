<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Documentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->event_id;

        // List event terbaru -> lama
        $events = Event::orderBy('tanggal_pelaksanaan', 'desc')->get();

        // Filter dokumentasi jika event dipilih
        $docs = Documentation::when($filter, function ($query) use ($filter) {
            $query->where('event_id', $filter);
        })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.documentations.index', compact('events', 'docs', 'filter'));
    }

    public function create($event_id)
    {
        $event = Event::findOrFail($event_id);
        return view('admin.documentations.create', compact('event'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id'   => 'required|exists:events,id',
            'files'      => 'required',
            'files.*'    => 'file|mimes:jpg,jpeg,png,mp4,pdf|max:10240'
        ]);

        foreach ($request->file('files') as $file) {

            // Simpan ke storage
            $path = $file->store('documentation', 'public');

            Documentation::create([
                'event_id'  => $request->event_id,
                'file_path' => 'storage/' . $path,   // simpan path full
                'file_type' => $file->extension(),    // jpg/png/mp4/pdf
            ]);
        }

        return redirect()->route('events.index')
            ->with('success', 'Dokumentasi berhasil diupload.');
    }

    public function destroy($id)
    {
        $doc = Documentation::findOrFail($id);

        // Hapus file fisik
        if ($doc->file_path) {
            $realPath = public_path($doc->file_path); // karena disimpan "storage/..."
            if (file_exists($realPath)) {
                unlink($realPath);
            }
        }

        // Hapus row database
        $doc->delete();

        return back()->with('success', 'Dokumentasi berhasil dihapus.');
    }
}
