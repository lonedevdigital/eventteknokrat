<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class AdminEventController extends Controller
{
    /**
     * Normalisasi role agar konsisten.
     */
    protected function normalizeRole(?string $role): string
    {
        $role = strtolower(trim((string) $role));

        // mapping aman jika ada variasi penamaan
        if ($role === 'admin') return 'superuser';
        if ($role === 'super_user') return 'superuser';
        if ($role === 'kemasis') return 'kemahasiswaan';

        return $role ?: 'superuser';
    }

    /**
     * Tampilkan list event + filter.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $this->normalizeRole($user->role ?? 'superuser');

        // dropdown kategori
        $categories = EventCategory::orderBy('nama_kategori')->get();

        // Semua filter tanggal pakai kolom tanggal_pelaksanaan
        $dateColumn = 'tanggal_pelaksanaan';

        $events = Event::with(['creator', 'category'])
            /**
             * ✅ FILTER VISIBILITY BERDASARKAN ROLE:
             * - superuser: lihat semua
             * - selain itu: hanya event owner_role = role user
             *
             * Bonus fallback: jika event lama owner_role masih NULL,
             * maka user masih bisa lihat event yang dia buat sendiri.
             */
            ->when($userRole !== 'superuser', function ($query) use ($userRole, $user) {
                $query->where(function ($q) use ($userRole, $user) {
                    $q->where('owner_role', $userRole)
                        ->orWhere(function ($qq) use ($user) {
                            $qq->whereNull('owner_role')
                                ->where('created_by_user_id', $user->id);
                        });
                });
            })

            // 1. Search nama event
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('nama_event', 'like', '%' . $request->q . '%');
            })

            // 2. Filter kategori
            ->when($request->filled('event_category_id'), function ($query) use ($request) {
                $query->where('event_category_id', $request->event_category_id);
            })

            // 3. Filter status pelaksanaan (berdasarkan tanggal_pelaksanaan)
            ->when($request->filled('status'), function ($query) use ($request, $dateColumn) {
                $today = now()->toDateString();

                if ($request->status === 'upcoming') {
                    $query->whereDate($dateColumn, '>', $today);
                } elseif ($request->status === 'ongoing') {
                    $query->whereDate($dateColumn, '=', $today);
                } elseif ($request->status === 'past') {
                    $query->whereDate($dateColumn, '<', $today);
                }
            })

            // 4. Filter tahun / bulan / tanggal
            ->when(
                $request->filled('year') || $request->filled('month') || $request->filled('day'),
                function ($query) use ($request, $dateColumn) {
                    if ($request->filled('year')) {
                        $query->whereYear($dateColumn, $request->year);
                    }
                    if ($request->filled('month')) {
                        $query->whereMonth($dateColumn, $request->month);
                    }
                    if ($request->filled('day')) {
                        $query->whereDay($dateColumn, $request->day);
                    }
                }
            )
            ->latest()
            ->get();

        return view('admin.events.index', compact('events', 'categories'));
    }

    /**
     * Form tambah event.
     */
    public function create()
    {
        $categories = EventCategory::orderBy('nama_kategori')->get();
        return view('admin.events.create', compact('categories'));
    }

    /**
     * Normalisasi input waktu_pelaksanaan supaya:
     * - "09.30.00" → "09:30"
     * - "09.30"    → "09:30"
     * - "09:30:00" → "09:30"
     */
    protected function normalizeTime(Request $request): void
    {
        if (! $request->filled('waktu_pelaksanaan')) return;

        $time = $request->input('waktu_pelaksanaan');
        $time = str_replace('.', ':', $time);

        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            $time = substr($time, 0, 5);
        }

        $request->merge(['waktu_pelaksanaan' => $time]);
    }

    /**
     * Simpan event baru.
     */
    public function store(Request $request)
    {
        $this->normalizeTime($request);

        $validated = $request->validate([
            'thumbnail_file'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail_url'        => 'nullable|url',
            'nama_event'           => 'required|string|max:255',
            'tempat_pelaksanaan'   => 'required|string|max:255',
            'waktu_pelaksanaan'    => 'nullable|date_format:H:i',
            'tanggal_pendaftaran'  => 'nullable|date',
            'tanggal_pelaksanaan'  => 'nullable|date',
            'deskripsi'            => 'nullable|string',
            'informasi_lainnya'    => 'nullable|string',
            'event_category_id'    => 'required|exists:event_categories,id',
        ]);

        $data = collect($validated)->only([
            'nama_event',
            'tempat_pelaksanaan',
            'waktu_pelaksanaan',
            'tanggal_pendaftaran',
            'tanggal_pelaksanaan',
            'deskripsi',
            'informasi_lainnya',
            'event_category_id',
        ])->toArray();

        $user = auth()->user();
        $data['created_by_user_id']  = $user->id;

        // ✅ kunci: set owner_role berdasarkan role pembuat
        $data['owner_role']  = $this->normalizeRole($user->role ?? 'superuser');

        // Thumbnail priority
        if ($request->hasFile('thumbnail_file')) {
            $path = $request->file('thumbnail_file')->store('event_covers', 'public');
            $data['thumbnail'] = 'storage/' . $path;
        } elseif (!empty($validated['thumbnail_url'] ?? null)) {
            $data['thumbnail'] = $validated['thumbnail_url'];
        }

        Event::create($data);

        return redirect()
            ->route('events.index')
            ->with('success', 'Event berhasil dibuat.');
    }

    /**
     * Form edit event.
     */
    public function edit(Event $event)
    {
        $this->authorizeEventByRole($event);

        $categories = EventCategory::orderBy('nama_kategori')->get();
        return view('admin.events.edit', compact('event', 'categories'));
    }

    /**
     * Update event.
     */
    public function update(Request $request, Event $event)
    {
        $this->authorizeEventByRole($event);

        $this->normalizeTime($request);

        $validated = $request->validate([
            'thumbnail_file'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail_url'        => 'nullable|url',
            'nama_event'           => 'required|string|max:255',
            'tempat_pelaksanaan'   => 'required|string|max:255',
            'waktu_pelaksanaan'    => 'nullable|date_format:H:i',
            'tanggal_pendaftaran'  => 'nullable|date',
            'tanggal_pelaksanaan'  => 'nullable|date',
            'deskripsi'            => 'nullable|string',
            'informasi_lainnya'    => 'nullable|string',
            'event_category_id'    => 'required|exists:event_categories,id',
        ]);

        $data = collect($validated)->only([
            'nama_event',
            'tempat_pelaksanaan',
            'waktu_pelaksanaan',
            'tanggal_pendaftaran',
            'tanggal_pelaksanaan',
            'deskripsi',
            'informasi_lainnya',
            'event_category_id',
        ])->toArray();

        if ($request->hasFile('thumbnail_file')) {
            $path = $request->file('thumbnail_file')->store('event_covers', 'public');
            $data['thumbnail'] = 'storage/' . $path;
        } elseif (!empty($validated['thumbnail_url'] ?? null)) {
            $data['thumbnail'] = $validated['thumbnail_url'];
        }

        // ✅ owner_role tidak boleh diganti user biasa.
        // (admin pun biasanya tidak perlu, jadi dibiarkan tetap)
        $event->update($data);

        return redirect()
            ->route('events.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    /**
     * Hapus event.
     */
    public function destroy(Event $event)
    {
        $this->authorizeEventByRole($event);

        $event->delete();

        return redirect()
            ->route('events.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    /**
     * ✅ Authorize berdasarkan role event
     * - superuser: allowed
     * - lainnya: hanya boleh kalau owner_role = role user
     */
    protected function authorizeEventByRole(Event $event): bool
    {
        $user = auth()->user();
        $userRole = $this->normalizeRole($user->role ?? 'superuser');

        if ($userRole === 'superuser') {
            return true;
        }

        $eventRole = $this->normalizeRole($event->owner_role ?? '');

        // fallback event lama (owner_role null) => izinkan kalau dia pembuat
        if (empty($eventRole)) {
            if ((int) $event->created_by_user_id !== (int) $user->id) {
                abort(403, 'Anda tidak memiliki izin untuk mengelola event ini.');
            }
            return true;
        }

        if ($eventRole !== $userRole) {
            abort(403, 'Anda tidak memiliki izin untuk mengelola event ini.');
        }

        return true;
    }
}

