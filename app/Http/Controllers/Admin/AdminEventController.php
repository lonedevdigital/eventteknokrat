<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventCompetitionBranch;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * Form tambah event / lomba.
     * - Tanpa ?type : tampilkan pemilih jenis (Event / Perlombaan)
     * - ?type=event : form event biasa
     * - ?type=lomba : form perlombaan
     */
    public function create(Request $request)
    {
        $type = $request->query('type');

        if (! in_array($type, [Event::TYPE_EVENT, Event::TYPE_LOMBA], true)) {
            return view('admin.events.create_choose');
        }

        $categories = EventCategory::orderBy('nama_kategori')->get();

        if ($type === Event::TYPE_LOMBA) {
            $prodiList = Mahasiswa::whereNotNull('nama_program_studi')
                ->where('nama_program_studi', '!=', '')
                ->distinct()
                ->orderBy('nama_program_studi')
                ->pluck('nama_program_studi');

            return view('admin.events.create_lomba', compact('categories', 'prodiList'));
        }

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
     * Simpan event/lomba baru (branch berdasarkan jenis).
     */
    public function store(Request $request)
    {
        if ($request->input('type') === Event::TYPE_LOMBA) {
            return $this->storeLomba($request);
        }

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
     * Simpan PERLOMBAAN baru.
     */
    protected function storeLomba(Request $request)
    {
        $this->normalizeTime($request);

        $validated = $request->validate([
            'nama_event'          => 'required|string|max:255',
            'partisipasi_skala'   => 'required|in:prodi,universitas',
            'partisipasi_prodi'   => 'required_if:partisipasi_skala,prodi|nullable|string|max:255',
            'flyer_file'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi'           => 'nullable|string',
            'tipe_bayar'          => 'required|in:gratis,berbayar',
            'tanggal_pelaksanaan' => 'required|date',
            'waktu_pelaksanaan'   => 'nullable|date_format:H:i',
            'tanggal_pendaftaran' => 'nullable|date',
            'tempat_pelaksanaan'  => 'required|string|max:255',

            // Detail tambahan lomba
            'syarat_ketentuan'    => 'nullable|string',
            'jadwal_timeline'     => 'nullable|string',
            'hadiah_penghargaan'  => 'nullable|string',
            'cara_pendaftaran'    => 'nullable|string',
            'juknis_file'         => 'nullable|file|mimes:pdf|max:5120',

            // Informasi kontak (array sejajar)
            'kontak_jabatan'      => 'nullable|array',
            'kontak_jabatan.*'    => 'nullable|string|max:100',
            'kontak_nama'         => 'nullable|array',
            'kontak_nama.*'       => 'nullable|string|max:150',
            'kontak_no'           => 'nullable|array',
            'kontak_no.*'         => 'nullable|string|max:100',

            // Cabang lomba (hanya jika berbayar)
            'cabang_nama'         => 'required_if:tipe_bayar,berbayar|nullable|array',
            'cabang_nama.*'       => 'nullable|string|max:150',
            'cabang_harga'        => 'nullable|array',
            'cabang_harga.*'      => 'nullable|integer|min:0',
        ], [
            'nama_event.required'        => 'Judul lomba wajib diisi.',
            'partisipasi_skala.required' => 'Skala partisipasi wajib dipilih.',
            'partisipasi_prodi.required_if' => 'Program studi wajib dipilih untuk skala per-prodi.',
            'tipe_bayar.required'        => 'Tipe lomba wajib dipilih.',
            'tanggal_pelaksanaan.required' => 'Tanggal pelaksanaan wajib diisi.',
            'tempat_pelaksanaan.required'  => 'Tempat pelaksanaan wajib diisi.',
            'cabang_nama.required_if'    => 'Lomba berbayar wajib memiliki minimal satu cabang lomba.',
        ]);

        // Rakit kontak menjadi array terstruktur
        $kontak = [];
        foreach (($request->input('kontak_jabatan', [])) as $i => $jabatan) {
            $nama = $request->input("kontak_nama.$i");
            $no   = $request->input("kontak_no.$i");
            if (blank($jabatan) && blank($nama) && blank($no)) {
                continue;
            }
            $kontak[] = [
                'jabatan' => $jabatan,
                'nama'    => $nama,
                'kontak'  => $no,
            ];
        }

        $user = auth()->user();

        // Kategori "Lomba" sebagai pusat perlombaan
        $lombaCategoryId = EventCategory::whereRaw('LOWER(nama_kategori) = ?', ['lomba'])->value('id');

        $data = [
            'type'                => Event::TYPE_LOMBA,
            'nama_event'          => $validated['nama_event'],
            'event_category_id'   => $lombaCategoryId,
            'partisipasi_skala'   => $validated['partisipasi_skala'],
            'partisipasi_prodi'   => $validated['partisipasi_skala'] === 'prodi'
                                        ? ($validated['partisipasi_prodi'] ?? null)
                                        : null,
            'tipe_bayar'          => $validated['tipe_bayar'],
            'deskripsi'           => $validated['deskripsi'] ?? null,
            'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'],
            'waktu_pelaksanaan'   => $validated['waktu_pelaksanaan'] ?? null,
            'tanggal_pendaftaran' => $validated['tanggal_pendaftaran'] ?? null,
            'tempat_pelaksanaan'  => $validated['tempat_pelaksanaan'],
            'kontak'              => $kontak,
            'syarat_ketentuan'    => $validated['syarat_ketentuan'] ?? null,
            'jadwal_timeline'     => $validated['jadwal_timeline'] ?? null,
            'hadiah_penghargaan'  => $validated['hadiah_penghargaan'] ?? null,
            'cara_pendaftaran'    => $validated['cara_pendaftaran'] ?? null,
            'created_by_user_id'  => $user->id,
            'owner_role'          => $this->normalizeRole($user->role ?? 'superuser'),
        ];

        // Flyer (dipakai juga sebagai thumbnail agar tampil di listing)
        if ($request->hasFile('flyer_file')) {
            $path = $request->file('flyer_file')->store('lomba_flyers', 'public');
            $data['flyer']     = 'storage/' . $path;
            $data['thumbnail'] = 'storage/' . $path;
        }

        // Juknis (file PDF)
        if ($request->hasFile('juknis_file')) {
            $path = $request->file('juknis_file')->store('lomba_juknis', 'public');
            $data['juknis_file'] = 'storage/' . $path;
        }

        $event = DB::transaction(function () use ($data, $validated, $request) {
            $event = Event::create($data);

            // Simpan cabang lomba bila berbayar
            if ($validated['tipe_bayar'] === 'berbayar') {
                foreach (($request->input('cabang_nama', [])) as $i => $namaCabang) {
                    if (blank($namaCabang)) {
                        continue;
                    }
                    EventCompetitionBranch::create([
                        'event_id'          => $event->id,
                        'nama_cabang'       => $namaCabang,
                        'harga_pendaftaran' => (int) $request->input("cabang_harga.$i", 0),
                    ]);
                }
            }

            return $event;
        });

        return redirect()
            ->route('events.index')
            ->with('success', 'Perlombaan "' . $event->nama_event . '" berhasil dibuat.');
    }

    /**
     * Form edit event / lomba (form berbeda sesuai jenis).
     */
    public function edit(Event $event)
    {
        $this->authorizeEventByRole($event);

        if ($event->isLomba()) {
            $event->load('branches');
            $prodiList = Mahasiswa::whereNotNull('nama_program_studi')
                ->where('nama_program_studi', '!=', '')
                ->distinct()
                ->orderBy('nama_program_studi')
                ->pluck('nama_program_studi');

            return view('admin.events.edit_lomba', compact('event', 'prodiList'));
        }

        $categories = EventCategory::orderBy('nama_kategori')->get();
        return view('admin.events.edit', compact('event', 'categories'));
    }

    /**
     * Update event / lomba.
     */
    public function update(Request $request, Event $event)
    {
        $this->authorizeEventByRole($event);

        if ($event->isLomba()) {
            return $this->updateLomba($request, $event);
        }

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
     * Update PERLOMBAAN.
     */
    protected function updateLomba(Request $request, Event $event)
    {
        $this->normalizeTime($request);

        $validated = $request->validate([
            'nama_event'          => 'required|string|max:255',
            'partisipasi_skala'   => 'required|in:prodi,universitas',
            'partisipasi_prodi'   => 'required_if:partisipasi_skala,prodi|nullable|string|max:255',
            'flyer_file'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi'           => 'nullable|string',
            'tipe_bayar'          => 'required|in:gratis,berbayar',
            'tanggal_pelaksanaan' => 'required|date',
            'waktu_pelaksanaan'   => 'nullable|date_format:H:i',
            'tanggal_pendaftaran' => 'nullable|date',
            'tempat_pelaksanaan'  => 'required|string|max:255',

            'syarat_ketentuan'    => 'nullable|string',
            'jadwal_timeline'     => 'nullable|string',
            'hadiah_penghargaan'  => 'nullable|string',
            'cara_pendaftaran'    => 'nullable|string',
            'juknis_file'         => 'nullable|file|mimes:pdf|max:5120',

            'kontak_jabatan'      => 'nullable|array',
            'kontak_jabatan.*'    => 'nullable|string|max:100',
            'kontak_nama'         => 'nullable|array',
            'kontak_nama.*'       => 'nullable|string|max:150',
            'kontak_no'           => 'nullable|array',
            'kontak_no.*'         => 'nullable|string|max:100',

            'cabang_nama'         => 'required_if:tipe_bayar,berbayar|nullable|array',
            'cabang_nama.*'       => 'nullable|string|max:150',
            'cabang_harga'        => 'nullable|array',
            'cabang_harga.*'      => 'nullable|integer|min:0',
        ], [
            'nama_event.required'           => 'Judul lomba wajib diisi.',
            'partisipasi_skala.required'    => 'Skala partisipasi wajib dipilih.',
            'partisipasi_prodi.required_if' => 'Program studi wajib dipilih untuk skala per-prodi.',
            'tipe_bayar.required'           => 'Tipe lomba wajib dipilih.',
            'tanggal_pelaksanaan.required'  => 'Tanggal pelaksanaan wajib diisi.',
            'tempat_pelaksanaan.required'   => 'Tempat pelaksanaan wajib diisi.',
            'cabang_nama.required_if'       => 'Lomba berbayar wajib memiliki minimal satu cabang lomba.',
        ]);

        // Rakit kontak
        $kontak = [];
        foreach (($request->input('kontak_jabatan', [])) as $i => $jabatan) {
            $nama = $request->input("kontak_nama.$i");
            $no   = $request->input("kontak_no.$i");
            if (blank($jabatan) && blank($nama) && blank($no)) {
                continue;
            }
            $kontak[] = ['jabatan' => $jabatan, 'nama' => $nama, 'kontak' => $no];
        }

        $data = [
            'nama_event'          => $validated['nama_event'],
            'partisipasi_skala'   => $validated['partisipasi_skala'],
            'partisipasi_prodi'   => $validated['partisipasi_skala'] === 'prodi'
                                        ? ($validated['partisipasi_prodi'] ?? null)
                                        : null,
            'tipe_bayar'          => $validated['tipe_bayar'],
            'deskripsi'           => $validated['deskripsi'] ?? null,
            'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'],
            'waktu_pelaksanaan'   => $validated['waktu_pelaksanaan'] ?? null,
            'tanggal_pendaftaran' => $validated['tanggal_pendaftaran'] ?? null,
            'tempat_pelaksanaan'  => $validated['tempat_pelaksanaan'],
            'kontak'              => $kontak,
            'syarat_ketentuan'    => $validated['syarat_ketentuan'] ?? null,
            'jadwal_timeline'     => $validated['jadwal_timeline'] ?? null,
            'hadiah_penghargaan'  => $validated['hadiah_penghargaan'] ?? null,
            'cara_pendaftaran'    => $validated['cara_pendaftaran'] ?? null,
        ];

        if ($request->hasFile('flyer_file')) {
            $path = $request->file('flyer_file')->store('lomba_flyers', 'public');
            $data['flyer']     = 'storage/' . $path;
            $data['thumbnail'] = 'storage/' . $path;
        }

        // Juknis (file PDF) — ganti hanya jika ada upload baru
        if ($request->hasFile('juknis_file')) {
            $path = $request->file('juknis_file')->store('lomba_juknis', 'public');
            $data['juknis_file'] = 'storage/' . $path;
        }

        DB::transaction(function () use ($event, $data, $validated, $request) {
            $event->update($data);

            // Sinkronkan cabang lomba: hapus lama, buat ulang dari input
            $event->branches()->delete();
            if ($validated['tipe_bayar'] === 'berbayar') {
                foreach (($request->input('cabang_nama', [])) as $i => $namaCabang) {
                    if (blank($namaCabang)) {
                        continue;
                    }
                    EventCompetitionBranch::create([
                        'event_id'          => $event->id,
                        'nama_cabang'       => $namaCabang,
                        'harga_pendaftaran' => (int) $request->input("cabang_harga.$i", 0),
                    ]);
                }
            }
        });

        return redirect()
            ->route('events.index')
            ->with('success', 'Perlombaan "' . $event->nama_event . '" berhasil diperbarui.');
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

