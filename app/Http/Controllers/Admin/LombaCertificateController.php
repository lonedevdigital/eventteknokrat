<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCommittee;
use App\Models\EventRegistration;
use App\Models\LombaCertificate;
use App\Models\LombaCertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LombaCertificateController extends Controller
{
    /**
     * Halaman "Kelola Sertifikat" — pilih jenis (Panitia/Peserta), filter cabang, lihat kelayakan.
     */
    public function manage(Event $event, Request $request)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $event->load('branches');

        $jenis = in_array($request->input('jenis'), ['panitia', 'peserta'], true) ? $request->input('jenis') : 'peserta';
        $branchId = $request->filled('branch_id') ? (int) $request->input('branch_id') : null;

        $participants = $jenis === 'panitia'
            ? $this->panitiaList($event)
            : $this->pesertaList($event, $branchId);

        return view('admin.lomba.certificates.manage', compact('event', 'jenis', 'branchId', 'participants'));
    }

    /**
     * Designer sertifikat (duplikat dari admin.certificates.detail, endpoint di-repoint ke skema lomba).
     */
    public function designer(Event $event, string $jenis)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        if (! in_array($jenis, ['panitia', 'peserta'], true)) {
            abort(404);
        }

        $participants = $jenis === 'panitia' ? $this->panitiaList($event) : $this->pesertaList($event, null);

        return view('admin.lomba.certificates.detail', compact('event', 'jenis', 'participants'));
    }

    /**
     * JSON list peserta/panitia untuk proses generate di designer.
     */
    public function apiParticipants(Event $event, string $jenis)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        if (! in_array($jenis, ['panitia', 'peserta'], true)) {
            abort(404);
        }

        $participants = $jenis === 'panitia' ? $this->panitiaList($event) : $this->pesertaList($event, null);

        return response()->json(['participants' => array_values($participants)]);
    }

    /**
     * Terima file sertifikat hasil generate dari canvas designer, simpan ke lomba_certificates.
     */
    public function uploadGenerated(Request $request)
    {
        $validated = $request->validate([
            'event_id'  => 'required|integer|exists:events,id',
            'jenis'     => 'required|in:panitia,peserta',
            'ref'       => 'required|string|max:100',
            'user_id'   => 'nullable|integer',
            'nama'      => 'required|string|max:150',
            'npm'       => 'nullable|string|max:50',
            'branch_id' => 'nullable|integer',
            'file'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $event = Event::findOrFail($validated['event_id']);
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $dir = "lomba_certificates/{$event->id}";
        Storage::disk('public')->makeDirectory($dir);

        $ext = $request->file('file')->getClientOriginalExtension() ?: $request->file('file')->extension();
        $filename = 'cert-' . $validated['jenis'] . '-' . Str::slug($validated['ref']) . '.' . $ext;
        $path = $request->file('file')->storeAs($dir, $filename, 'public');

        $template = LombaCertificateTemplate::where('event_id', $event->id)
            ->where('jenis', $validated['jenis'])
            ->first();

        // Cari record lama (by user_id jika ada, atau nama+npm jika tidak) supaya re-generate menimpa, bukan duplikat.
        $existingQuery = LombaCertificate::where('event_id', $event->id)->where('jenis', $validated['jenis']);
        if (! empty($validated['user_id'])) {
            $existingQuery->where('user_id', $validated['user_id']);
        } else {
            $existingQuery->whereNull('user_id')
                ->where('nama', $validated['nama'])
                ->where('npm', $validated['npm'] ?? null);
        }
        $existing = $existingQuery->first();
        if ($existing) {
            $this->deleteFile($existing->file_path);
            $existing->delete();
        }

        LombaCertificate::create([
            'event_id'                      => $event->id,
            'lomba_certificate_template_id' => $template?->id,
            'jenis'                         => $validated['jenis'],
            'user_id'                       => $validated['user_id'] ?? null,
            'nama'                          => $validated['nama'],
            'npm'                           => $validated['npm'] ?? null,
            'branch_id'                     => $validated['branch_id'] ?? null,
            'file_path'                     => 'storage/' . $path,
            'issued_at'                     => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function templateShow(Request $request, Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $jenis = in_array($request->query('jenis'), ['panitia', 'peserta'], true) ? $request->query('jenis') : 'peserta';

        $template = LombaCertificateTemplate::firstOrCreate(
            ['event_id' => $event->id, 'jenis' => $jenis],
            ['name' => 'Default', 'canvas_width' => 2000, 'canvas_height' => 1414]
        );

        return response()->json([
            'success'  => true,
            'template' => [
                'id'            => $template->id,
                'jenis'         => $template->jenis,
                'name'          => $template->name,
                'canvas_width'  => $template->canvas_width,
                'canvas_height' => $template->canvas_height,
                'template_json' => $template->template_json ? json_decode($template->template_json, true) : null,
            ],
        ]);
    }

    public function templateStore(Request $request, Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizeManage($event);

        $validated = $request->validate([
            'jenis'         => 'required|in:panitia,peserta',
            'name'          => 'nullable|string|max:150',
            'canvas_width'  => 'required|integer',
            'canvas_height' => 'required|integer',
            'template_json' => 'required|array',
        ]);

        $template = LombaCertificateTemplate::updateOrCreate(
            ['event_id' => $event->id, 'jenis' => $validated['jenis']],
            [
                'name'          => $validated['name'] ?? 'Custom Layout',
                'canvas_width'  => $validated['canvas_width'],
                'canvas_height' => $validated['canvas_height'],
                'template_json' => json_encode($validated['template_json']),
            ]
        );

        return response()->json(['success' => true, 'id' => $template->id, 'jenis' => $template->jenis]);
    }

    /**
     * Peserta yang LAYAK menerima sertifikat: seluruh berkas (termasuk Bukti Pembayaran) sudah Approve.
     * Tim di-split per anggota (bukan per ketua) karena sertifikat berbasis Nama & NPM individu.
     */
    protected function pesertaList(Event $event, ?int $branchId): array
    {
        $registrations = EventRegistration::with(['user', 'mahasiswa', 'branch'])
            ->where('event_id', $event->id)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->get()
            ->filter(fn ($reg) => $reg->allDocumentsApproved());

        $certs = LombaCertificate::where('event_id', $event->id)->where('jenis', 'peserta')->get();

        return $registrations->map(function ($reg) use ($certs) {
            $cert = $reg->user_id
                ? $certs->first(fn ($c) => (int) $c->user_id === (int) $reg->user_id)
                : null;

            return [
                'ref'              => 'reg_' . $reg->id,
                'nama'             => $reg->mahasiswa->nama_mahasiswa ?? $reg->user->name ?? '-',
                'npm'              => $reg->mahasiswa->npm_mahasiswa ?? null,
                'role'             => $reg->branch->nama_cabang ?? 'Peserta',
                'user_id'          => $reg->user_id,
                'branch_id'        => $reg->branch_id,
                'certificate_url'  => $cert ? $this->publicUrl($cert->file_path) : null,
            ];
        })->values()->all();
    }

    /**
     * Panitia lomba (dari EventCommittee) — tidak ada gate kelayakan (panitia tidak upload berkas).
     */
    protected function panitiaList(Event $event): array
    {
        $committees = EventCommittee::where('event_id', $event->id)->get();
        $certs = LombaCertificate::where('event_id', $event->id)->where('jenis', 'panitia')->get();

        return $committees->map(function ($c) use ($certs) {
            $cert = $certs->first(function ($cert) use ($c) {
                if ($c->user_id) {
                    return (int) $cert->user_id === (int) $c->user_id;
                }

                return $cert->user_id === null && $cert->nama === $c->nama && $cert->npm === $c->npm;
            });

            return [
                'ref'             => 'committee_' . $c->id,
                'nama'            => $c->nama ?? '-',
                'npm'             => $c->npm,
                'role'            => $c->jabatan,
                'user_id'         => $c->user_id,
                'branch_id'       => null,
                'certificate_url' => $cert ? $this->publicUrl($cert->file_path) : null,
            ];
        })->values()->all();
    }

    protected function publicUrl(string $path): string
    {
        return Str::startsWith($path, ['http://', 'https://']) ? $path : asset($path);
    }

    protected function deleteFile(?string $path): void
    {
        if (! $path || ! Str::startsWith($path, 'storage/')) {
            return;
        }

        $relative = Str::after($path, 'storage/');
        if ($relative !== '') {
            Storage::disk('public')->delete($relative);
        }
    }

    protected function ensureLomba(Event $event): void
    {
        if (! $event->isLomba()) {
            abort(404, 'Data perlombaan tidak ditemukan.');
        }
    }

    protected function authorizeManage(Event $event): void
    {
        if (! $event->canBeManagedBy(auth()->user())) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola sertifikat lomba ini.');
        }
    }
}
