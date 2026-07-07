<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCommittee;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    /**
     * Daftar lomba sesuai role:
     * - admin core: semua lomba
     * - penanggung_jawab: lomba yang dia buat
     * - ketua_pelaksana: lomba yang di-assign ke dia
     */
    public function index()
    {
        $user = auth()->user();

        $query = Event::lomba()->with(['category', 'ketuaPelaksana', 'creator'])->withCount('committees');

        if ($user->isKetuaPelaksana()) {
            $query->where('ketua_pelaksana_user_id', $user->id);
        } elseif ($user->isPenanggungJawab()) {
            $query->where('created_by_user_id', $user->id);
        }
        // admin core: tanpa filter

        $lombaList = $query->latest()->get();

        return view('admin.lomba.index', compact('lombaList'));
    }

    /**
     * Halaman kelola lomba: assign Ketua Pelaksana + manajemen Panitia.
     */
    public function manage(Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $event->load(['category', 'ketuaPelaksana', 'committees' => function ($q) {
            $q->orderBy('id');
        }]);

        // Kandidat Ketua Pelaksana (user dengan role ketua_pelaksana)
        $ketuaCandidates = User::where('role', User::LEVEL_KETUA_PELAKSANA)
            ->orderBy('name')
            ->get(['id', 'name', 'username']);

        $defaultJabatan = EventCommittee::DEFAULT_JABATAN;
        $canAssignKetua = $this->canAssignKetua($event);

        return view('admin.lomba.manage', compact(
            'event', 'ketuaCandidates', 'defaultJabatan', 'canAssignKetua'
        ));
    }

    /**
     * Penanggung Jawab / admin menetapkan Ketua Pelaksana untuk lomba.
     */
    public function assignKetua(Request $request, Event $event)
    {
        $this->ensureLomba($event);

        if (! $this->canAssignKetua($event)) {
            abort(403, 'Hanya Penanggung Jawab / Admin yang dapat menetapkan Ketua Pelaksana.');
        }

        $validated = $request->validate([
            'ketua_pelaksana_user_id' => 'nullable|exists:users,id',
        ]);

        $ketuaId = $validated['ketua_pelaksana_user_id'] ?? null;

        // Pastikan user yang dipilih benar-benar berperan ketua_pelaksana
        if ($ketuaId) {
            $candidate = User::find($ketuaId);
            if (! $candidate || $candidate->role !== User::LEVEL_KETUA_PELAKSANA) {
                return back()->with('error', 'User yang dipilih bukan Ketua Pelaksana.');
            }
        }

        $event->update(['ketua_pelaksana_user_id' => $ketuaId]);

        return back()->with('success', $ketuaId
            ? 'Ketua Pelaksana berhasil ditetapkan.'
            : 'Ketua Pelaksana dikosongkan.');
    }

    /**
     * Tambah anggota panitia (oleh Ketua Pelaksana / PJ / admin).
     */
    public function storePanitia(Request $request, Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $request->validate([
            'jabatan'        => 'required|string|max:100',
            'jabatan_custom' => 'nullable|string|max:100',
            'npm'            => 'nullable|string|max:50',
            'nama'           => 'nullable|string|max:150',
        ]);

        // Tentukan jabatan final (dukung custom)
        $jabatan = $request->input('jabatan');
        if ($jabatan === '__custom__') {
            $jabatan = trim((string) $request->input('jabatan_custom'));
            if ($jabatan === '') {
                return back()->withInput()->with('error', 'Nama jabatan custom wajib diisi.');
            }
        }
        $isCustom = ! in_array($jabatan, EventCommittee::DEFAULT_JABATAN, true);

        $data = [
            'event_id'  => $event->id,
            'jabatan'   => $jabatan,
            'is_custom' => $isCustom,
        ];

        // Coba tautkan ke mahasiswa via NPM
        $npm = trim((string) $request->input('npm'));
        if ($npm !== '') {
            $mahasiswa = Mahasiswa::where('npm_mahasiswa', $npm)->first();
            if (! $mahasiswa) {
                return back()->withInput()->with('error', "NPM {$npm} tidak ditemukan di data mahasiswa.");
            }
            $data['mahasiswa_id'] = $mahasiswa->id;
            $data['user_id']      = $mahasiswa->user_id;
            $data['npm']          = $mahasiswa->npm_mahasiswa;
            $data['nama']         = $mahasiswa->nama_mahasiswa;
        } else {
            // Input manual
            $nama = trim((string) $request->input('nama'));
            if ($nama === '') {
                return back()->withInput()->with('error', 'Isi NPM mahasiswa atau nama panitia secara manual.');
            }
            $data['nama'] = $nama;
        }

        EventCommittee::create($data);

        return back()->with('success', 'Panitia berhasil ditambahkan.');
    }

    /**
     * Hapus anggota panitia.
     */
    public function destroyPanitia(Event $event, EventCommittee $committee)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        if ((int) $committee->event_id !== (int) $event->id) {
            abort(404);
        }

        $committee->delete();

        return back()->with('success', 'Panitia berhasil dihapus.');
    }

    /**
     * ------------------------------------------------------------------
     *  HELPER OTORISASI
     * ------------------------------------------------------------------
     */

    protected function ensureLomba(Event $event): void
    {
        if (! $event->isLomba()) {
            abort(404, 'Data perlombaan tidak ditemukan.');
        }
    }

    /**
     * Boleh mengelola panitia: admin core, PJ pembuat, atau Ketua Pelaksana yang ditugaskan.
     */
    protected function authorizeManage(Event $event): void
    {
        $user = auth()->user();

        if ($this->isAdminCore($user)) {
            return;
        }
        if ($user->isPenanggungJawab() && (int) $event->created_by_user_id === (int) $user->id) {
            return;
        }
        if ($user->isKetuaPelaksana() && (int) $event->ketua_pelaksana_user_id === (int) $user->id) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses untuk mengelola lomba ini.');
    }

    /**
     * Boleh menetapkan Ketua Pelaksana: admin core atau PJ pembuat (bukan ketua).
     */
    protected function canAssignKetua(Event $event): bool
    {
        $user = auth()->user();

        if ($this->isAdminCore($user)) {
            return true;
        }

        return $user->isPenanggungJawab() && (int) $event->created_by_user_id === (int) $user->id;
    }

    protected function isAdminCore($user): bool
    {
        return $user->isSuperUser() || $user->isBaak() || $user->isKemahasiswaan();
    }
}
