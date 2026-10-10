<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventDocumentRequirement;
use App\Models\ParticipantDocument;
use App\Models\Team;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    /**
     * Daftar lomba di mana user ini tercatat sebagai "Bendahara Lomba" di Kepanitiaan
     * (admin core melihat semua).
     */
    public function index()
    {
        $user = auth()->user();

        $query = Event::lomba()->with('category')->withCount('registrations');

        if (! ($user->isSuperUser() || $user->isBaak() || $user->isKemahasiswaan())) {
            $query->whereHas('committees', function ($q) use ($user) {
                $q->where('jabatan', 'Bendahara Lomba')->where('user_id', $user->id);
            });
        }

        $lombaList = $query->latest()->get();

        return view('admin.payment.index', compact('lombaList'));
    }

    /**
     * Daftar Bukti Pembayaran seluruh peserta lomba ini.
     * Untuk tipe tim, hanya ketua tim yang direpresentasikan (satu pembayaran per tim).
     */
    public function show(Request $request, Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizePayment($event);

        $event->ensureDefaultDocumentRequirementsForAllBranches();

        $defaultRequirementIds = EventDocumentRequirement::where('event_id', $event->id)
            ->where('is_default', true)
            ->pluck('id')
            ->all();

        $registrations = $this->payerRegistrationsQuery($event, $request)
            ->with(['user', 'mahasiswa', 'branch', 'documents' => function ($q) use ($defaultRequirementIds) {
                $q->whereIn('event_document_requirement_id', $defaultRequirementIds);
            }])
            ->orderBy('registered_at')
            ->get();

        $teamNames = Team::where('event_id', $event->id)
            ->whereIn('ketua_user_id', $registrations->pluck('user_id'))
            ->pluck('nama_tim', 'ketua_user_id');

        $registrations->each(function ($reg) use ($teamNames) {
            $reg->nama_tim = $teamNames->get($reg->user_id);
        });

        return view('admin.payment.show', compact('event', 'registrations'));
    }

    /**
     * Verifikasi bukti pembayaran (menandakan pembayaran diterima).
     */
    public function verify(Event $event, ParticipantDocument $document)
    {
        $this->ensureLomba($event);
        $this->authorizePayment($event);

        if ((int) $document->registration->event_id !== (int) $event->id) {
            abort(404);
        }

        $document->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function exportExcel(Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizePayment($event);

        $rows = $this->exportRows($event);

        return Excel::download(new \App\Exports\LombaPembayaranExport($event, $rows), 'Rekap_Pembayaran_' . $event->id . '.xlsx');
    }

    public function exportPdf(Event $event)
    {
        $this->ensureLomba($event);
        $this->authorizePayment($event);

        $rows = $this->exportRows($event);

        $pdf = Pdf::loadView('admin.payment.pdf', ['event' => $event, 'rows' => $rows]);

        return $pdf->download('Rekap_Pembayaran_' . $event->id . '.pdf');
    }

    /**
     * Baris data untuk export (dikelompokkan Jenis Pendaftaran > Cabang Lomba > Nama).
     */
    protected function exportRows(Event $event): \Illuminate\Support\Collection
    {
        $event->ensureDefaultDocumentRequirementsForAllBranches();

        $defaultRequirementIds = EventDocumentRequirement::where('event_id', $event->id)
            ->where('is_default', true)
            ->pluck('id')
            ->all();

        $registrations = $this->payerRegistrationsQuery($event, request())
            ->with(['user', 'mahasiswa', 'branch', 'documents' => function ($q) use ($defaultRequirementIds) {
                $q->whereIn('event_document_requirement_id', $defaultRequirementIds);
            }])
            ->get();

        $teamNames = Team::where('event_id', $event->id)
            ->whereIn('ketua_user_id', $registrations->pluck('user_id'))
            ->pluck('nama_tim', 'ketua_user_id');

        return $registrations->map(function ($reg) use ($teamNames) {
            $doc = $reg->documents->first();
            $namaTim = $teamNames->get($reg->user_id);
            return [
                'jenis_pendaftaran' => $reg->tipe_pendaftaran === 'tim' ? 'Tim' : 'Individual',
                'cabang_lomba'      => $reg->branch->nama_cabang ?? '-',
                'nama'              => $namaTim ?: ($reg->mahasiswa->nama_mahasiswa ?? $reg->user->name ?? '-'),
                'npm'               => $reg->tipe_pendaftaran === 'tim' ? '' : ($reg->mahasiswa->npm_mahasiswa ?? '-'),
                'status_pembayaran' => $doc ? ($doc->status === 'approved' ? 'Terverifikasi' : ($doc->status === 'rejected' ? 'Ditolak' : 'Menunggu')) : 'Belum Upload',
            ];
        })->sortBy(['jenis_pendaftaran', 'cabang_lomba', 'nama'])->values();
    }

    protected function payerRegistrationsQuery(Event $event, Request $request)
    {
        return \App\Models\EventRegistration::payers($event->id)
            ->when($request->filled('tipe_pendaftaran'), function ($q) use ($request) {
                $q->where('tipe_pendaftaran', $request->input('tipe_pendaftaran'));
            });
    }

    protected function ensureLomba(Event $event): void
    {
        if (! $event->isLomba()) {
            abort(404, 'Data perlombaan tidak ditemukan.');
        }
    }

    protected function authorizePayment(Event $event): void
    {
        if (! $event->canBeManagedByBendahara(auth()->user())) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola pembayaran lomba ini.');
        }
    }
}
