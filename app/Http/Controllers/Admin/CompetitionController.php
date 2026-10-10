<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCommittee;
use App\Models\EventCompetitionBranch;
use App\Models\EventDocumentRequirement;
use App\Models\EventRegistration;
use App\Models\LombaJuara;
use App\Models\ParticipantDocument;
use App\Models\Mahasiswa;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    public function manage(Event $event, Request $request)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        // Lomba lama (dibuat sebelum fitur Berkas ada) mungkin belum punya baris default ini.
        $event->ensureDefaultDocumentRequirementsForAllBranches();

        $event->load(['category', 'ketuaPelaksana', 'committees' => function ($q) {
            $q->orderBy('id');
        }, 'branches' => function ($q) {
            $q->orderBy('id');
        }, 'documentRequirements', 'assessmentCriteria']);

        // Kandidat Ketua Pelaksana: Hanya siapkan current Ketua (jika ada), pencarian mahasiswa dilakukan via Ajax autocomplete
        $ketuaCandidates = $event->ketuaPelaksana ? collect([$event->ketuaPelaksana]) : collect();

        // Bendahara ditentukan dari Kepanitiaan (jabatan "Bendahara Lomba"), bukan field terpisah.
        $bendaharaCommittees = $event->committees->where('jabatan', 'Bendahara Lomba')->values();

        $defaultJabatan = EventCommittee::DEFAULT_JABATAN;
        $canAssignKetua = $this->canAssignKetua($event);
        $canManageData  = $event->canBeManagedBy(auth()->user());

        // Badge notifikasi di tombol "Review Berkas Peserta".
        $pendingBerkasCount = ParticipantDocument::whereHas('registration', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })->where('status', 'pending')->count();

        $registrants = EventRegistration::with(['user', 'mahasiswa', 'branch'])
            ->where('event_id', $event->id)
            ->when($request->filled('branch_id'), function ($q) use ($request) {
                $q->where('branch_id', $request->input('branch_id'));
            })
            ->when($request->filled('tipe_pendaftaran'), function ($q) use ($request) {
                $q->where('tipe_pendaftaran', $request->input('tipe_pendaftaran'));
            })
            ->orderByDesc('registered_at')
            ->paginate(15)
            ->withQueryString();

        // ── 1. DATA STATISTIK PENDAFTAR ──
        $totalPendaftar   = EventRegistration::where('event_id', $event->id)->count();
        $totalIndividu    = EventRegistration::where('event_id', $event->id)->where('tipe_pendaftaran', 'individu')->count();
        $totalTim         = Team::where('event_id', $event->id)->count();
        $totalPesertaTim  = EventRegistration::where('event_id', $event->id)->where('tipe_pendaftaran', 'tim')->count();

        // Kapasitas & Jumlah Terisi Tiap Cabang Lomba
        $branchRegistrationsCount = EventRegistration::where('event_id', $event->id)
            ->whereNotNull('branch_id')
            ->selectRaw('branch_id, count(*) as count')
            ->groupBy('branch_id')
            ->pluck('count', 'branch_id');

        $branchTeamsCount = Team::where('event_id', $event->id)
            ->whereNotNull('branch_id')
            ->selectRaw('branch_id, count(*) as count')
            ->groupBy('branch_id')
            ->pluck('count', 'branch_id');

        // Daftar Seluruh Tim & Status Verifikasi Berkas/Pendaftaran
        $teams = Team::with(['ketua.mahasiswa', 'branch', 'members'])
            ->where('event_id', $event->id)
            ->latest()
            ->get();

        $ketuaUserIds = $teams->pluck('ketua_user_id')->filter()->unique();
        $ketuaRegistrations = EventRegistration::with('documents')
            ->where('event_id', $event->id)
            ->whereIn('user_id', $ketuaUserIds)
            ->get()
            ->keyBy('user_id');

        $docReqsByBranch = $event->documentRequirements->groupBy('branch_id');
        $globalReqs      = $docReqsByBranch->get(null, collect());

        $teamsWithStatus = $teams->map(function ($team) use ($ketuaRegistrations, $docReqsByBranch, $globalReqs) {
            $reg          = $ketuaRegistrations->get($team->ketua_user_id);
            $branchReqs   = $docReqsByBranch->get($team->branch_id, collect());
            $allReqs      = $globalReqs->concat($branchReqs)->unique('id');
            $requiredReqs = $allReqs->where('is_required', true);

            $uploadedDocs = $reg ? $reg->documents : collect();
            $approvedDocs = $uploadedDocs->where('status', 'approved');
            $pendingDocs  = $uploadedDocs->where('status', 'pending');
            $rejectedDocs = $uploadedDocs->where('status', 'rejected');

            $uploadedReqIds = $uploadedDocs->pluck('event_document_requirement_id')->filter()->all();
            $missingReqs    = $requiredReqs->reject(fn ($r) => in_array($r->id, $uploadedReqIds))->values();

            $isComplete = $missingReqs->isEmpty() && $uploadedDocs->isNotEmpty() && $pendingDocs->isEmpty() && $rejectedDocs->isEmpty();
            $hasPending = $pendingDocs->isNotEmpty();
            $hasRejected = $rejectedDocs->isNotEmpty();
            $isVerified = ($reg && ($reg->status === 'attended' || $reg->status === 'confirmed')) || ($requiredReqs->isNotEmpty() && $approvedDocs->count() >= $requiredReqs->count());

            if ($isVerified) {
                $statusVerifikasi = 'verified';
                $statusLabel = 'Terverifikasi';
                $statusBadgeClass = 'badge-success';
            } elseif ($hasPending) {
                $statusVerifikasi = 'pending';
                $statusLabel = 'Menunggu Verifikasi';
                $statusBadgeClass = 'badge-warning';
            } elseif ($hasRejected) {
                $statusVerifikasi = 'rejected';
                $statusLabel = 'Ditolak';
                $statusBadgeClass = 'badge-danger';
            } elseif ($uploadedDocs->isEmpty()) {
                $statusVerifikasi = 'unsubmitted';
                $statusLabel = 'Belum Upload Berkas';
                $statusBadgeClass = 'badge-secondary';
            } else {
                $statusVerifikasi = 'unverified';
                $statusLabel = 'Terdaftar';
                $statusBadgeClass = 'badge-info';
            }

            return (object) [
                'team'               => $team,
                'registration'       => $reg,
                'status_verifikasi'  => $statusVerifikasi,
                'status_label'       => $statusLabel,
                'status_badge_class' => $statusBadgeClass,
                'is_complete'        => $isComplete,
                'missing_reqs'       => $missingReqs,
                'uploaded_docs'      => $uploadedDocs,
                'approved_docs'      => $approvedDocs,
                'pending_docs'       => $pendingDocs,
                'rejected_docs'      => $rejectedDocs,
            ];
        });

        $teamsIncomplete = $teamsWithStatus->filter(fn ($t) => ! $t->is_complete)->values();

        // ── 2. PENGELOMPOKAN KEPANITIAAN ──
        $panitiaInti = $event->committees->filter(function ($c) {
            $j = strtolower($c->jabatan);
            return str_contains($j, 'sekretaris') || str_contains($j, 'bendahara') || str_contains($j, 'inti') || str_contains($j, 'wakil');
        })->values();

        $panitiaAcara = $event->committees->reject(function ($c) use ($panitiaInti) {
            return $panitiaInti->contains('id', $c->id);
        })->values();

        return view('admin.lomba.manage', compact(
            'event', 'ketuaCandidates', 'bendaharaCommittees', 'defaultJabatan', 'canAssignKetua', 'canManageData',
            'registrants', 'pendingBerkasCount', 'totalPendaftar', 'totalIndividu', 'totalTim', 'totalPesertaTim',
            'branchRegistrationsCount', 'branchTeamsCount', 'teamsWithStatus', 'teamsIncomplete',
            'panitiaInti', 'panitiaAcara'
        ));
    }

    /**
     * Endpoint Ajax autocomplete pencarian mahasiswa/user berdasarkan NPM atau Nama.
     */
    public function searchMahasiswa(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $users = User::with('mahasiswa')
            ->where(function ($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                      ->orWhere('username', 'LIKE', "%{$q}%")
                      ->orWhere('npm', 'LIKE', "%{$q}%");
            })
            ->limit(20)
            ->get();

        $results = $users->map(function ($u) {
            $npm = $u->npm ?: ($u->mahasiswa->npm_mahasiswa ?? $u->username);
            $prodi = $u->mahasiswa->nama_program_studi ?? '';
            $prodiText = $prodi ? " ({$prodi})" : '';
            return [
                'id'       => $u->id,
                'name'     => $u->name,
                'npm'      => $npm,
                'prodi'    => $prodi,
                'text'     => "{$npm} - {$u->name}{$prodiText}",
            ];
        });

        return response()->json($results);
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
            'ketua_pelaksana_user_id' => 'nullable',
            'npm'                     => 'nullable|string|max:50',
        ]);

        $ketuaId = $validated['ketua_pelaksana_user_id'] ?? null;

        // Jika user memilih/menginputkan via NPM langsung
        if (! $ketuaId && ! empty($validated['npm'])) {
            $npm = trim($validated['npm']);
            $user = User::where('username', $npm)
                ->orWhere('npm', $npm)
                ->orWhereHas('mahasiswa', fn($m) => $m->where('npm_mahasiswa', $npm))
                ->first();
            if ($user) {
                $ketuaId = $user->id;
            } else {
                return back()->with('error', "Mahasiswa dengan NPM '{$npm}' tidak ditemukan.");
            }
        }

        // Pastikan user yang dipilih valid
        $candidate = null;
        if ($ketuaId) {
            $candidate = User::with('mahasiswa')->find($ketuaId);
            if (! $candidate) {
                return back()->with('error', 'User yang dipilih tidak ditemukan.');
            }
        }

        $event->update(['ketua_pelaksana_user_id' => $ketuaId]);

        $npmDisplay = $candidate ? ($candidate->npm ?: ($candidate->mahasiswa->npm_mahasiswa ?? $candidate->username)) : '';

        return back()->with('success', $ketuaId
            ? "Ketua Pelaksana berhasil ditetapkan: {$candidate->name} ({$npmDisplay})."
            : 'Ketua Pelaksana berhasil dikosongkan.');
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
            'npm'            => 'required|string|max:50',
            'permissions'    => 'nullable|array',
            'permissions.*'  => 'string',
        ], [
            'npm.required' => 'NPM wajib diisi — panitia lomba harus mahasiswa aktif yang terdaftar.',
        ]);

        // Tentukan jabatan final (dukung custom)
        $jabatan = $request->input('jabatan');
        if ($jabatan === '__custom__') {
            $jabatan = trim((string) $request->input('jabatan_custom'));
            if ($jabatan === '') {
                return back()->withInput()->with('error', 'Nama jabatan custom wajib diisi.');
            }
        }

        // Ketua Pelaksana cuma boleh ditetapkan lewat kartu "Tetapkan Ketua Pelaksana"
        // (Penanggung Jawab/Admin), bukan lewat Kepanitiaan — termasuk jabatan custom.
        if (strcasecmp($jabatan, 'Ketua Pelaksana') === 0) {
            return back()->withInput()->with('error', 'Ketua Pelaksana hanya dapat ditetapkan oleh Penanggung Jawab/Admin lewat kartu "Ketua Pelaksana".');
        }

        $isCustom = ! in_array($jabatan, EventCommittee::DEFAULT_JABATAN, true);

        // Bersihkan array permissions jika dikirim dari frontend
        $permissions = [];
        if ($request->has('permissions') && is_array($request->input('permissions'))) {
            $permissions = array_values(array_filter($request->input('permissions')));
        }

        $data = [
            'event_id'    => $event->id,
            'jabatan'     => $jabatan,
            'is_custom'   => $isCustom,
            'permissions' => $permissions,
        ];

        // Tautkan ke mahasiswa via NPM — panitia lomba wajib mahasiswa aktif, tidak ada input manual.
        $npm = trim((string) $request->input('npm'));
        $mahasiswa = Mahasiswa::where('npm_mahasiswa', $npm)->first();
        if (! $mahasiswa) {
            return back()->withInput()->with('error', "NPM {$npm} tidak ditemukan di data mahasiswa.");
        }
        $data['mahasiswa_id'] = $mahasiswa->id;
        $data['user_id']      = $mahasiswa->user_id;
        $data['npm']          = $mahasiswa->npm_mahasiswa;
        $data['nama']         = $mahasiswa->nama_mahasiswa;

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
     *  CABANG LOMBA (CRUD)
     * ------------------------------------------------------------------
     */

    public function createBranch(Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        return view('admin.lomba.cabang_create', compact('event'));
    }

    public function storeBranch(Request $request, Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $validated = $this->validateBranch($request);
        $data = $this->branchDataFromValidated($validated);
        $data['event_id'] = $event->id;

        if ($request->filled('pj_identifier')) {
            $userPj = \App\Models\User::where('npm', $request->pj_identifier)
                        ->orWhere('email', $request->pj_identifier)
                        ->first();
            if ($userPj) {
                $data['pj_user_id'] = $userPj->id;
            } else {
                return back()->withInput()->with('error', 'User PJ dengan NPM / Email tersebut tidak ditemukan.');
            }
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = 'storage/' . $request->file('cover_image')->store('lomba_cabang_covers', 'public');
        }
        if ($request->hasFile('juknis_file')) {
            $data['juknis_file'] = 'storage/' . $request->file('juknis_file')->store('lomba_cabang_juknis', 'public');
        }

        EventCompetitionBranch::create($data);

        return redirect()->route('lomba.manage', $event->slug)->with('success', 'Cabang lomba berhasil ditambahkan.');
    }

    public function editBranch(Event $event, EventCompetitionBranch $branch)
    {
        $this->ensureLomba($event);
        $this->ensureBranchBelongsToEvent($event, $branch);
        
        $isPj = $branch->pj_user_id === auth()->id();
        if (! $event->canBeManagedBy(auth()->user()) && !$isPj) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola cabang ini.');
        }

        return view('admin.lomba.cabang_edit', compact('event', 'branch'));
    }

    public function updateBranch(Request $request, Event $event, EventCompetitionBranch $branch)
    {
        $this->ensureLomba($event);
        $this->ensureBranchBelongsToEvent($event, $branch);

        $isPj = $branch->pj_user_id === auth()->id();
        if (! $event->canBeManagedBy(auth()->user()) && !$isPj) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola cabang ini.');
        }

        $validated = $this->validateBranch($request);
        $data = $this->branchDataFromValidated($validated);

        if ($request->filled('pj_identifier')) {
            $userPj = \App\Models\User::where('npm', $request->pj_identifier)
                        ->orWhere('email', $request->pj_identifier)
                        ->first();
            if ($userPj) {
                $data['pj_user_id'] = $userPj->id;
            } else {
                return back()->withInput()->with('error', 'User PJ dengan NPM / Email tersebut tidak ditemukan.');
            }
        } else {
            $data['pj_user_id'] = null; // Remove PJ if cleared
        }

        if ($request->hasFile('cover_image')) {
            $this->deleteBranchFile($branch->cover_image);
            $data['cover_image'] = 'storage/' . $request->file('cover_image')->store('lomba_cabang_covers', 'public');
        }
        if ($request->hasFile('juknis_file')) {
            $this->deleteBranchFile($branch->juknis_file);
            $data['juknis_file'] = 'storage/' . $request->file('juknis_file')->store('lomba_cabang_juknis', 'public');
        }

        $branch->update($data);

        // Sync Assessment Criteria
        $branch->assessmentCriteria()->delete();
        if ($request->filled('kriteria_nama')) {
            $namas = $request->input('kriteria_nama');
            $bobots = $request->input('kriteria_bobot');
            foreach ($namas as $index => $nama) {
                if (!empty($nama)) {
                    $branch->assessmentCriteria()->create([
                        'event_id'      => $event->id,
                        'nama_kriteria' => $nama,
                        'bobot'         => $bobots[$index] ?? 0,
                    ]);
                }
            }
        }

        $redirectUrl = $event->canBeManagedBy(auth()->user()) 
            ? route('lomba.manage', $event->slug)
            : route('lomba.penilaian', $event->slug);

        return redirect($redirectUrl)->with('success', 'Cabang lomba berhasil diperbarui.');
    }

    public function kriteria(Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $event->load(['assessmentCriteria' => function ($q) {
            $q->orderBy('id');
        }, 'branches']);

        return view('admin.lomba.kriteria', compact('event'));
    }

    public function storeAssessmentCriterion(Request $request, Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $validated = $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required|integer|min:1|max:100',
            'branch_id' => 'nullable|exists:event_competition_branches,id'
        ]);

        $event->assessmentCriteria()->create($validated);

        return back()->with('success', 'Kriteria penilaian berhasil ditambahkan.');
    }

    public function updateAssessmentCriterion(Request $request, Event $event, \App\Models\AssessmentCriterion $criterion)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        if ($criterion->event_id !== $event->id) abort(403);

        $validated = $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'bobot' => 'required|integer|min:1|max:100',
            'branch_id' => 'nullable|exists:event_competition_branches,id'
        ]);

        $criterion->update($validated);

        return back()->with('success', 'Kriteria penilaian berhasil diperbarui.');
    }

    public function destroyAssessmentCriterion(Event $event, \App\Models\AssessmentCriterion $criterion)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        if ($criterion->event_id !== $event->id) abort(403);

        $criterion->delete();

        return back()->with('success', 'Kriteria penilaian berhasil dihapus.');
    }

    public function destroyBranch(Event $event, EventCompetitionBranch $branch)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);
        $this->ensureBranchBelongsToEvent($event, $branch);

        if ($branch->teams()->exists() || $branch->registrations()->exists()) {
            return back()->with('error', 'Cabang lomba tidak dapat dihapus karena sudah memiliki peserta/tim terdaftar.');
        }

        $this->deleteBranchFile($branch->cover_image);
        $this->deleteBranchFile($branch->juknis_file);
        $branch->delete();

        return back()->with('success', 'Cabang lomba berhasil dihapus.');
    }

    protected function validateBranch(Request $request): array
    {
        return $request->validate([
            'nama_cabang'            => 'required|string|max:150',
            'deskripsi'              => 'nullable|string',
            'wa_group_link'          => 'nullable|url|max:255',
            'pj_identifier'          => 'nullable|string|max:255',
            'cover_image'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'juknis_file'            => 'nullable|file|mimes:pdf|max:5120',
            'tipe_bayar'             => 'required|in:gratis,berbayar',
            'harga_individual'       => 'required_if:tipe_bayar,berbayar|nullable|integer|min:0',
            'harga_tim'              => 'required_if:tipe_bayar,berbayar|nullable|integer|min:0',
            'jenis_peserta'          => 'required|in:individu,tim',
            'maks_anggota_tim'       => 'required_if:jenis_peserta,tim|nullable|integer|min:1',
            'contact_person_nama'    => 'nullable|string|max:150',
            'contact_person_telepon' => 'nullable|string|max:50',
        ], [
            'nama_cabang.required'          => 'Nama cabang lomba wajib diisi.',
            'harga_individual.required_if'  => 'Harga individu wajib diisi untuk cabang berbayar.',
            'harga_tim.required_if'         => 'Harga tim wajib diisi untuk cabang berbayar.',
            'maks_anggota_tim.required_if'  => 'Maksimal anggota tim wajib diisi.',
        ]);
    }

    protected function branchDataFromValidated(array $validated): array
    {
        $isTim = $validated['jenis_peserta'] === 'tim';

        return [
            'nama_cabang'            => $validated['nama_cabang'],
            'deskripsi'              => $validated['deskripsi'] ?? null,
            'wa_group_link'          => $validated['wa_group_link'] ?? null,
            'tipe_bayar'             => $validated['tipe_bayar'],
            'harga_individual'       => $validated['tipe_bayar'] === 'berbayar' ? ($validated['harga_individual'] ?? 0) : 0,
            'harga_tim'              => $validated['tipe_bayar'] === 'berbayar' ? ($validated['harga_tim'] ?? 0) : 0,
            'maks_anggota_tim'       => $isTim ? (int) $validated['maks_anggota_tim'] : 1,
            'contact_person_nama'    => $validated['contact_person_nama'] ?? null,
            'contact_person_telepon' => $validated['contact_person_telepon'] ?? null,
        ];
    }

    protected function ensureBranchBelongsToEvent(Event $event, EventCompetitionBranch $branch): void
    {
        if ((int) $branch->event_id !== (int) $event->id) {
            abort(404);
        }
    }

    protected function deleteBranchFile(?string $path): void
    {
        if (! $path || ! Str::startsWith($path, 'storage/')) {
            return;
        }
        $relative = Str::after($path, 'storage/');
        if ($relative !== '') {
            Storage::disk('public')->delete($relative);
        }
    }

    /**
     * ------------------------------------------------------------------
     *  BERKAS LOMBA (DOCUMENT REQUIREMENTS) CRUD
     * ------------------------------------------------------------------
     */

    public function storeDocumentRequirement(Request $request, Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $hasBranches = $event->branches()->exists();

        $validated = $request->validate([
            'nama_dokumen' => 'required|string|max:150',
            'format_diizinkan' => 'required|array',
            'format_diizinkan.*' => 'string',
            'is_required'  => 'nullable|boolean',
            'branch_id'    => [$hasBranches ? 'required' : 'nullable', 'exists:event_competition_branches,id'],
        ], [
            'nama_dokumen.required' => 'Nama berkas wajib diisi.',
            'format_diizinkan.required' => 'Pilih minimal satu format yang diizinkan.',
            'branch_id.required'    => 'Pilih cabang lomba untuk berkas ini.',
        ]);

        $branchId = $hasBranches ? (int) $validated['branch_id'] : null;
        if ($branchId && ! $event->branches()->whereKey($branchId)->exists()) {
            return back()->withInput()->with('error', 'Cabang lomba tidak valid.');
        }

        $maxUrutan = $event->documentRequirementsForBranch($branchId)->max('urutan');

        EventDocumentRequirement::create([
            'event_id'         => $event->id,
            'branch_id'        => $branchId,
            'nama_dokumen'     => $validated['nama_dokumen'],
            'jenis_file'       => in_array('pdf', $validated['format_diizinkan']) ? 'pdf' : 'gambar',
            'format_diizinkan' => $validated['format_diizinkan'],
            'is_required'      => $request->has('is_required') ? (bool) $validated['is_required'] : false,
            'is_default'       => false,
            'urutan'           => ($maxUrutan ?? -1) + 1,
        ]);

        return back()->with('success', 'Berkas berhasil ditambahkan.');
    }

    public function updateDocumentRequirement(Request $request, Event $event, EventDocumentRequirement $requirement)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);
        $this->ensureDocumentRequirementBelongsToEvent($event, $requirement);

        $validated = $request->validate([
            'nama_dokumen' => 'required|string|max:150',
            'format_diizinkan' => 'required|array',
            'format_diizinkan.*' => 'string',
            'is_required'  => 'nullable|boolean',
        ], [
            'nama_dokumen.required' => 'Nama berkas wajib diisi.',
            'format_diizinkan.required' => 'Pilih minimal satu format yang diizinkan.',
        ]);

        $requirement->update([
            'nama_dokumen'     => $validated['nama_dokumen'],
            'jenis_file'       => in_array('pdf', $validated['format_diizinkan']) ? 'pdf' : 'gambar',
            'format_diizinkan' => $validated['format_diizinkan'],
            'is_required'      => $request->has('is_required') ? (bool) $validated['is_required'] : false,
        ]);

        return back()->with('success', 'Berkas berhasil diperbarui.');
    }

    public function destroyDocumentRequirement(Event $event, EventDocumentRequirement $requirement)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);
        $this->ensureDocumentRequirementBelongsToEvent($event, $requirement);

        if ($requirement->is_default) {
            return back()->with('error', 'Berkas default (Bukti Pembayaran) tidak dapat dihapus.');
        }

        $requirement->delete();

        return back()->with('success', 'Berkas berhasil dihapus.');
    }

    /**
     * Geser urutan berkas naik/turun (tukar posisi dengan tetangga terdekat).
     */
    public function reorderDocumentRequirement(Request $request, Event $event, EventDocumentRequirement $requirement)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);
        $this->ensureDocumentRequirementBelongsToEvent($event, $requirement);

        $direction = $request->input('direction');

        $siblingQuery = EventDocumentRequirement::where('event_id', $event->id)
            ->where('id', '!=', $requirement->id);
        $requirement->branch_id
            ? $siblingQuery->where('branch_id', $requirement->branch_id)
            : $siblingQuery->whereNull('branch_id');

        if ($direction === 'up') {
            $sibling = (clone $siblingQuery)->where('urutan', '<', $requirement->urutan)->orderByDesc('urutan')->first();
        } else {
            $sibling = (clone $siblingQuery)->where('urutan', '>', $requirement->urutan)->orderBy('urutan')->first();
        }

        if ($sibling) {
            $tmp = $requirement->urutan;
            $requirement->update(['urutan' => $sibling->urutan]);
            $sibling->update(['urutan' => $tmp]);
        }

        return back();
    }

    protected function ensureDocumentRequirementBelongsToEvent(Event $event, EventDocumentRequirement $requirement): void
    {
        if ((int) $requirement->event_id !== (int) $event->id) {
            abort(404);
        }
    }

    /**
     * ------------------------------------------------------------------
     *  REVIEW BERKAS PESERTA
     * ------------------------------------------------------------------
     */

    public function documentReview(Event $event, Request $request)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $event->ensureDefaultDocumentRequirementsForAllBranches();

        $event->load(['branches']);

        $branchId = $request->filled('branch_id') ? (int) $request->query('branch_id') : null;
        if ($branchId && ! $event->branches->contains('id', $branchId)) {
            $branchId = null;
        }

        // Hanya "pembayar" (individu atau ketua tim) yang direview — anggota tim non-ketua tidak
        // pernah punya berkas sendiri (upload dibatasi ketua tim saja), jadi tidak perlu tampil.
        $registrantsQuery = EventRegistration::payers($event->id)
            ->with(['user', 'mahasiswa', 'branch', 'documents']);
        if ($branchId) {
            $registrantsQuery->where('branch_id', $branchId);
        }
        $registrants = $registrantsQuery->orderBy('registered_at')->get();

        if ($event->branches->isEmpty()) {
            $documentRequirements = $event->documentRequirementsForBranch(null)->get();
        } elseif ($branchId) {
            $documentRequirements = $event->documentRequirementsForBranch($branchId)->get();
        } else {
            // "Semua Cabang" — gabungan berkas dari seluruh cabang (kolom yang tidak berlaku
            // untuk peserta di cabang lain akan ditandai "—" di tabel, bukan disembunyikan).
            $documentRequirements = $event->documentRequirements()->get();
        }

        $pendingCount = ParticipantDocument::whereIn('event_registration_id', $registrants->pluck('id'))
            ->where('status', 'pending')
            ->count();

        return view('admin.lomba.berkas', compact('event', 'registrants', 'documentRequirements', 'branchId', 'pendingCount'));
    }

    public function reviewDocument(Request $request, Event $event, ParticipantDocument $document)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        if ((int) $document->registration->event_id !== (int) $event->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status'        => 'required|in:approved,rejected',
            'review_notes'  => 'nullable|string|max:500',
        ]);

        $document->update([
            'status'       => $validated['status'],
            'review_notes' => $validated['review_notes'] ?? null,
            'reviewed_by'  => auth()->id(),
            'reviewed_at'  => now(),
        ]);

        return back()->with('success', 'Status berkas berhasil diperbarui.');
    }

    /**
     * ------------------------------------------------------------------
     *  EXPORT DATA PESERTA
     * ------------------------------------------------------------------
     */

    public function exportPesertaExcel(Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        [$groups, $total] = $this->pesertaExportGroups($event);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LombaPesertaExport($event, $groups, $total),
            'Data_Peserta_' . $event->id . '.xlsx'
        );
    }

    public function exportPesertaPdf(Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        [$groups, $total] = $this->pesertaExportGroups($event);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.lomba.exports.peserta_pdf', [
            'event' => $event,
            'groups' => $groups,
            'totalPeserta' => $total,
        ]);

        return $pdf->download('Data_Peserta_' . $event->id . '.pdf');
    }

    /**
     * Susun data peserta terkelompok per Cabang Lomba (atau satu grup polos jika tidak ada cabang).
     * Tim direpresentasikan oleh satu baris (ketua tim) dengan daftar anggota bernomor.
     *
     * @return array{0: array<int, array{nama_cabang: ?string, rows: array}>, 1: int}
     */
    protected function pesertaExportGroups(Event $event): array
    {
        $branches = $event->branches()->orderBy('id')->get();

        $groups = [];
        $total = 0;

        if ($branches->isEmpty()) {
            $rows = $this->pesertaRowsForBranch($event, null);
            $groups[] = ['nama_cabang' => null, 'rows' => $rows];
            $total += count($rows);
        } else {
            foreach ($branches as $b) {
                $rows = $this->pesertaRowsForBranch($event, $b->id);
                $groups[] = ['nama_cabang' => $b->nama_cabang, 'rows' => $rows];
                $total += count($rows);
            }
        }

        return [$groups, $total];
    }

    protected function pesertaRowsForBranch(Event $event, ?int $branchId): array
    {
        $query = EventRegistration::payers($event->id)->with(['user', 'mahasiswa', 'branch']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $registrations = $query->orderBy('registered_at')->get();

        $rows = [];
        foreach ($registrations as $reg) {
            if ($reg->tipe_pendaftaran === 'tim') {
                $team = \App\Models\Team::with('members')
                    ->where('event_id', $event->id)
                    ->where('ketua_user_id', $reg->user_id)
                    ->first();

                // Hanya anggota yang sudah confirmed (bukan undangan pending) yang tampil di export.
                $anggota = $team
                    ? $team->members->where('status', \App\Models\TeamMember::STATUS_CONFIRMED)
                        ->values()
                        ->map(fn ($m, $i) => ($i + 1) . '. ' . $m->nama . ' (' . ($m->npm ?: '-') . ')')
                        ->implode("\n")
                    : '';

                $rows[] = [
                    'nama'    => $team->nama_tim ?? '-',
                    'npm'     => null,
                    'anggota' => $anggota,
                    'cabang'  => $reg->branch->nama_cabang ?? '-',
                ];
            } else {
                $rows[] = [
                    'nama'    => $reg->mahasiswa->nama_mahasiswa ?? $reg->user->name ?? '-',
                    'npm'     => $reg->mahasiswa->npm_mahasiswa ?? '-',
                    'anggota' => '',
                    'cabang'  => $reg->branch->nama_cabang ?? '-',
                ];
            }
        }

        return $rows;
    }

    /**
     * ------------------------------------------------------------------
     *  KELOLA JUARA
     * ------------------------------------------------------------------
     */

    public function juara(Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $branches = $event->branches()->orderBy('id')->get();

        $sections = $branches->isEmpty()
            ? [[
                'branch'      => null,
                'options'     => $this->juaraRegistrantOptions($event, null),
                'winners'     => $this->juaraWinnersForBranch($event, null),
            ]]
            : $branches->map(function ($branch) use ($event) {
                return [
                    'branch'  => $branch,
                    'options' => $this->juaraRegistrantOptions($event, $branch->id),
                    'winners' => $this->juaraWinnersForBranch($event, $branch->id),
                ];
            })->all();

        return view('admin.lomba.juara', compact('event', 'sections'));
    }

    public function storeJuara(Request $request, Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $validated = $request->validate([
            'branch_id'              => 'nullable|exists:event_competition_branches,id',
            'event_registration_id'  => 'required|exists:event_registrations,id',
            'juara_label'            => 'required|string|max:100',
        ], [
            'event_registration_id.required' => 'Pilih peserta/tim terlebih dahulu.',
            'juara_label.required'           => 'Nama juara wajib diisi.',
        ]);

        $branchId = $validated['branch_id'] ?? null;

        if ($branchId) {
            $this->ensureBranchBelongsToEvent($event, EventCompetitionBranch::findOrFail($branchId));
        }

        $registration = EventRegistration::payers($event->id)
            ->whereKey($validated['event_registration_id'])
            ->first();

        if (! $registration || (int) ($registration->branch_id ?? 0) !== (int) ($branchId ?? 0)) {
            return back()->with('error', 'Peserta/tim tidak valid untuk cabang ini.');
        }

        LombaJuara::create([
            'event_id'               => $event->id,
            'branch_id'              => $branchId,
            'event_registration_id'  => $registration->id,
            'juara_label'            => trim($validated['juara_label']),
        ]);

        return back()->with('success', 'Juara berhasil ditambahkan.');
    }

    public function destroyJuara(Event $event, LombaJuara $juara)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        if ((int) $juara->event_id !== (int) $event->id) {
            abort(404);
        }

        $juara->delete();

        return back()->with('success', 'Juara berhasil dihapus.');
    }

    public function exportJuaraPdf(Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $groups = $this->juaraExportGroups($event);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.lomba.exports.juara_pdf', [
            'event'  => $event,
            'groups' => $groups,
        ]);

        return $pdf->download('Data_Juara_' . $event->id . '.pdf');
    }

    /**
     * Opsi dropdown peserta/tim aktif (payer) untuk sebuah cabang (atau flat jika null).
     *
     * @return array<int, array{id:int, label:string}>
     */
    protected function juaraRegistrantOptions(Event $event, ?int $branchId): array
    {
        $query = EventRegistration::payers($event->id)->with(['user', 'mahasiswa']);
        $branchId ? $query->where('branch_id', $branchId) : $query->whereNull('branch_id');

        return $query->orderBy('registered_at')->get()->map(function ($reg) use ($event) {
            $label = $reg->tipe_pendaftaran === 'tim'
                ? '(Tim) ' . ($this->teamForRegistration($event, $reg)->nama_tim ?? $reg->user->name ?? '-')
                : ($reg->mahasiswa->nama_mahasiswa ?? $reg->user->name ?? '-');

            return ['id' => $reg->id, 'label' => $label];
        })->values()->all();
    }

    /**
     * Daftar juara yang sudah tercatat untuk sebuah cabang (atau flat jika null), siap tampil.
     *
     * @return array<int, array>
     */
    protected function juaraWinnersForBranch(Event $event, ?int $branchId): array
    {
        $query = LombaJuara::with(['registration.user', 'registration.mahasiswa'])
            ->where('event_id', $event->id);
        $branchId ? $query->where('branch_id', $branchId) : $query->whereNull('branch_id');

        return $query->orderBy('id')->get()
            ->map(fn ($juara) => $this->decorateJuara($event, $juara))
            ->values()->all();
    }

    /**
     * Susun data juara terkelompok per Cabang Lomba (atau satu grup polos jika tidak ada cabang),
     * dipakai untuk export PDF.
     *
     * @return array<int, array{nama_cabang: ?string, winners: array}>
     */
    protected function juaraExportGroups(Event $event): array
    {
        $branches = $event->branches()->orderBy('id')->get();

        if ($branches->isEmpty()) {
            return [['nama_cabang' => null, 'winners' => $this->juaraWinnersForBranch($event, null)]];
        }

        return $branches->map(function ($branch) use ($event) {
            return [
                'nama_cabang' => $branch->nama_cabang,
                'winners'     => $this->juaraWinnersForBranch($event, $branch->id),
            ];
        })->all();
    }

    /**
     * Bentuk satu baris juara siap-tampil (individu atau tim beserta anggotanya).
     */
    protected function decorateJuara(Event $event, LombaJuara $juara): array
    {
        $registration = $juara->registration;
        $isTeam = $registration && $registration->tipe_pendaftaran === 'tim';
        $team = $isTeam ? $this->teamForRegistration($event, $registration) : null;

        if ($isTeam) {
            return [
                'id'      => $juara->id,
                'label'   => $juara->juara_label,
                'is_team' => true,
                'nama'    => $team->nama_tim ?? '-',
                'npm'     => null,
                'asal'    => null,
                // Hanya anggota yang sudah confirmed (bukan undangan pending) yang tampil di daftar juara.
                'members' => $team
                    ? $team->members->where('status', \App\Models\TeamMember::STATUS_CONFIRMED)->map(fn ($m) => [
                        'nama' => $m->nama,
                        'npm'  => $m->npm ?: null,
                        'asal' => $this->resolveMemberAsal($m),
                    ])->values()->all()
                    : [],
            ];
        }

        return [
            'id'      => $juara->id,
            'label'   => $juara->juara_label,
            'is_team' => false,
            'nama'    => $registration->mahasiswa->nama_mahasiswa ?? $registration->user->name ?? '-',
            'npm'     => $registration->mahasiswa->npm_mahasiswa ?? null,
            'asal'    => $this->resolveIndividualAsal($registration),
            'members' => [],
        ];
    }

    /**
     * Asal sekolah/universitas untuk peserta individu. "users.npm" HANYA diisi untuk akun
     * mahasiswa diluar Teknokrat (lihat ExternalRegistrationController) — jadi TIDAK dipakai
     * sebagai penanda "mahasiswa Teknokrat". Satu-satunya penanda valid adalah relasi mahasiswa
     * (data sinkronisasi BAAK).
     */
    protected function resolveIndividualAsal(EventRegistration $registration): string
    {
        if ($registration->mahasiswa) {
            return 'Universitas Teknokrat Indonesia';
        }

        return $registration->user->institution->nama ?? '-';
    }

    /**
     * Asal sekolah/universitas untuk anggota tim. Sejak sistem undangan tim, setiap anggota
     * confirmed tertaut ke akun asli mereka (TeamController::snapshotMemberDataFromUser()) —
     * kalau "institution_id" terisi, anggota itu mahasiswa diluar Teknokrat (pakai data asli
     * dari akunnya); kalau kosong tapi sudah tertaut akun (user_id terisi), berarti mahasiswa
     * Teknokrat. Fallback "prodi" hanya untuk baris lama dari sebelum sistem undangan ada.
     */
    protected function resolveMemberAsal(\App\Models\TeamMember $member): string
    {
        if ($member->institution) {
            return $member->institution->nama;
        }

        if ($member->user_id) {
            return 'Universitas Teknokrat Indonesia';
        }

        return $member->prodi ?: '-';
    }

    /**
     * Cari tim (beserta anggota & institusi anggota) yang diwakili oleh registrasi ketua tim ini.
     */
    protected function teamForRegistration(Event $event, EventRegistration $registration)
    {
        return \App\Models\Team::with('members.institution')
            ->where('event_id', $event->id)
            ->where('ketua_user_id', $registration->user_id)
            ->first();
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
     * Boleh mengelola panitia/cabang/data lomba: admin core, PJ pembuat, atau Ketua Pelaksana yang ditugaskan.
     */
    protected function authorizeManage(Event $event): void
    {
        if (! $event->canBeManagedBy(auth()->user())) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola lomba ini.');
        }
    }

    /**
     * Boleh menetapkan Ketua Pelaksana: admin core atau PJ pembuat (bukan ketua).
     * Logic sebenarnya ada di EventPolicy::assignKetua().
     */
    protected function canAssignKetua(Event $event): bool
    {
        return app(\App\Policies\EventPolicy::class)->assignKetua(auth()->user(), $event);
    }

    protected function isAdminCore($user): bool
    {
        return app(\App\Policies\EventPolicy::class)->isAdminCore($user);
    }
}
