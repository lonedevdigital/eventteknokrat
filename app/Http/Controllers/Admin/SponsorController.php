<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::query()
            ->orderBy('urutan')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.sponsors.index', compact('sponsors'));
    }

    public function create()
    {
        return view('admin.sponsors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'link_url' => 'nullable|url|max:255',
            'logo_file' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'urutan' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        $path = $request->file('logo_file')->store('sponsors', 'public');

        Sponsor::create([
            'nama' => $validated['nama'],
            'link_url' => $validated['link_url'] ?? null,
            'logo_path' => 'storage/' . $path,
            'urutan' => (int) ($validated['urutan'] ?? 0),
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('sponsors.index')
            ->with('success', 'Sponsor berhasil ditambahkan.');
    }

    public function edit(Sponsor $sponsor)
    {
        return view('admin.sponsors.edit', compact('sponsor'));
    }

    public function update(Request $request, Sponsor $sponsor)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'link_url' => 'nullable|url|max:255',
            'logo_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'urutan' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'nama' => $validated['nama'],
            'link_url' => $validated['link_url'] ?? null,
            'urutan' => (int) ($validated['urutan'] ?? 0),
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ];

        if ($request->hasFile('logo_file')) {
            $this->deleteLocalLogo($sponsor->logo_path);
            $path = $request->file('logo_file')->store('sponsors', 'public');
            $data['logo_path'] = 'storage/' . $path;
        }

        $sponsor->update($data);

        return redirect()
            ->route('sponsors.index')
            ->with('success', 'Sponsor berhasil diperbarui.');
    }

    public function destroy(Sponsor $sponsor)
    {
        $this->deleteLocalLogo($sponsor->logo_path);
        $sponsor->delete();

        return redirect()
            ->route('sponsors.index')
            ->with('success', 'Sponsor berhasil dihapus.');
    }

    private function deleteLocalLogo(?string $logoPath): void
    {
        if (!$logoPath) {
            return;
        }

        if (!Str::startsWith($logoPath, 'storage/')) {
            return;
        }

        $relative = Str::after($logoPath, 'storage/');
        if ($relative !== '') {
            Storage::disk('public')->delete($relative);
        }
    }
}

