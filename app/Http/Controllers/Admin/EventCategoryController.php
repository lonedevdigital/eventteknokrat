<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    // List semua kategori
    public function index()
    {
        $categories = EventCategory::latest()->get();

        return view('admin.event_categories.index', compact('categories'));
    }

    // Form tambah kategori
    public function create()
    {
        return view('admin.event_categories.create');
    }

    // Simpan kategori baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:event_categories,nama_kategori',
        ]);

        EventCategory::create($data);

        return redirect()
            ->route('event-categories.index')
            ->with('success', 'Kategori event berhasil ditambahkan.');
    }

    // Form edit kategori
    public function edit(EventCategory $eventCategory)
    {
        return view('admin.event_categories.edit', compact('eventCategory'));
    }

    // Update kategori
    public function update(Request $request, EventCategory $eventCategory)
    {
        $data = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:event_categories,nama_kategori,' . $eventCategory->id,
        ]);

        $eventCategory->update($data);

        return redirect()
            ->route('event-categories.index')
            ->with('success', 'Kategori event berhasil diperbarui.');
    }

    // Hapus kategori
    public function destroy(EventCategory $eventCategory)
    {
        $eventCategory->delete();

        return redirect()
            ->route('event-categories.index')
            ->with('success', 'Kategori event berhasil dihapus.');
    }
}

