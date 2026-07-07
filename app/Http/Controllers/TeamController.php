<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Mahasiswa;
use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    /**
     * Halaman My Teams — daftar tim yang dibuat mahasiswa (sebagai ketua tim).
     */
    public function index(Request $request)
    {
        $teams = Team::query()
            ->with(['event', 'branch', 'members'])
            ->where('ketua_user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('frontend.teams.index', compact('teams'));
    }

    /**
     * Form buat tim baru.
     * Hanya lomba yang SUDAH didaftari mahasiswa yang bisa dipilih.
     */
    public function create(Request $request)
    {
        $userId = $request->user()->id;

        $lombaList = Event::lomba()
            ->with('branches')
            ->whereHas('registrations', fn ($q) => $q->where('user_id', $userId))
            ->orderByDesc('tanggal_pelaksanaan')
            ->get();

        $myMahasiswa = Mahasiswa::where('user_id', $userId)->first();

        // Data siap-pakai untuk JS (hindari closure kompleks di dalam @json Blade)
        $lombaBranches = [];
        foreach ($lombaList as $l) {
            $lombaBranches[$l->id] = [
                'tipe_bayar' => $l->tipe_bayar,
                'branches'   => $l->branches->map(fn ($b) => [
                    'id'    => $b->id,
                    'nama'  => $b->nama_cabang,
                    'harga' => $b->harga_pendaftaran,
                ])->values()->all(),
            ];
        }

        $captain = [
            'nama'  => $myMahasiswa->nama_mahasiswa ?? '',
            'npm'   => $myMahasiswa->npm_mahasiswa ?? '',
            'prodi' => $myMahasiswa->nama_program_studi ?? '',
        ];

        return view('frontend.teams.create', compact('lombaList', 'lombaBranches', 'captain'));
    }

    /**
     * Simpan tim baru beserta anggotanya.
     */
    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'nama_tim'       => 'required|string|max:150',
            'event_id'       => 'required|exists:events,id',
            'branch_id'      => 'nullable|exists:event_competition_branches,id',
            'member_nama'    => 'required|array|min:1',
            'member_nama.*'  => 'nullable|string|max:150',
            'member_npm'     => 'nullable|array',
            'member_npm.*'   => 'nullable|string|max:50',
            'member_prodi'   => 'nullable|array',
            'member_prodi.*' => 'nullable|string|max:150',
        ], [
            'nama_tim.required' => 'Nama tim wajib diisi.',
            'event_id.required' => 'Pilih perlombaan terlebih dahulu.',
            'member_nama.required' => 'Minimal satu anggota tim wajib diisi.',
        ]);

        // Pastikan event adalah lomba & mahasiswa sudah terdaftar di lomba itu
        $event = Event::lomba()->find($validated['event_id']);
        if (! $event) {
            return back()->withInput()->with('error', 'Perlombaan tidak valid.');
        }

        $registered = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $userId)
            ->exists();
        if (! $registered) {
            return back()->withInput()->with('error', 'Anda harus mendaftar ke lomba ini terlebih dahulu sebelum membuat tim.');
        }

        // Satu mahasiswa = satu tim per lomba
        $alreadyHasTeam = Team::where('event_id', $event->id)
            ->where('ketua_user_id', $userId)
            ->exists();
        if ($alreadyHasTeam) {
            return back()->withInput()->with('error', 'Anda sudah membuat tim untuk lomba ini.');
        }

        // Validasi branch milik event (jika diisi)
        $branchId = $validated['branch_id'] ?? null;
        if ($branchId && ! $event->branches()->whereKey($branchId)->exists()) {
            return back()->withInput()->with('error', 'Cabang lomba tidak valid.');
        }

        // Rakit anggota (lewati baris kosong)
        $members = [];
        foreach (($validated['member_nama'] ?? []) as $i => $nama) {
            $nama  = trim((string) $nama);
            $npm   = trim((string) ($request->input("member_npm.$i") ?? ''));
            $prodi = trim((string) ($request->input("member_prodi.$i") ?? ''));
            if ($nama === '' && $npm === '') {
                continue;
            }
            $members[] = [
                'nama'  => $nama !== '' ? $nama : '(Tanpa Nama)',
                'npm'   => $npm,
                'prodi' => $prodi !== '' ? $prodi : null,
            ];
        }

        if (empty($members)) {
            return back()->withInput()->with('error', 'Minimal satu anggota tim wajib diisi.');
        }

        DB::transaction(function () use ($validated, $event, $branchId, $members, $userId) {
            $team = Team::create([
                'event_id'      => $event->id,
                'ketua_user_id' => $userId,
                'nama_tim'      => $validated['nama_tim'],
                'branch_id'     => $branchId,
            ]);

            foreach ($members as $m) {
                // Tautkan ke user jika NPM cocok dengan data mahasiswa
                $linkedUserId = null;
                if (! empty($m['npm'])) {
                    $mhs = Mahasiswa::where('npm_mahasiswa', $m['npm'])->first();
                    $linkedUserId = $mhs->user_id ?? null;
                }

                TeamMember::create([
                    'team_id' => $team->id,
                    'nama'    => $m['nama'],
                    'npm'     => $m['npm'],
                    'prodi'   => $m['prodi'],
                    'user_id' => $linkedUserId,
                ]);
            }
        });

        return redirect()->route('frontend.teams.index')
            ->with('success', 'Tim "' . $validated['nama_tim'] . '" berhasil dibuat.');
    }

    /**
     * Hapus tim milik sendiri.
     */
    public function destroy(Request $request, Team $team)
    {
        if ((int) $team->ketua_user_id !== (int) $request->user()->id) {
            abort(403, 'Anda hanya dapat menghapus tim yang Anda buat.');
        }

        $nama = $team->nama_tim;
        $team->delete();

        return redirect()->route('frontend.teams.index')
            ->with('success', 'Tim "' . $nama . '" berhasil dihapus.');
    }
}
