<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class EventAnalyticsController extends Controller
{
    /**
     * Normalisasi nama role agar seragam.
     */
    protected function normalizeRole(?string $role): string
    {
        $role = strtolower(trim((string) $role));

        if ($role === 'admin') return 'superuser';
        if ($role === 'super_user') return 'superuser';
        if ($role === 'kemasis') return 'kemahasiswaan';

        return $role ?: '';
    }

    /**
     * Helper untuk menerapkan filter role pada Query Builder.
     * Ini menjamin konsistensi logic antara Dashboard, List, dan Analytics.
     */
    protected function applyEventRoleFilter($query, string $userRole, $user)
    {
        // 1. Superuser lihat semua
        if ($userRole === 'superuser') {
            return $query;
        }

        // 2. User biasa difilter
        return $query->where(function ($q) use ($userRole, $user) {
            // A. Event milik role user
            $q->where('owner_role', $userRole)

                // B. Fallback: Event lama (owner_role NULL) yang dibuat user ini
                ->orWhere(function ($qq) use ($user) {
                    $qq->whereNull('owner_role')
                        ->where('created_by_user_id', $user->id);
                });
        });
    }

    /**
     * Cek izin akses untuk SATU event spesifik (Detail/Export).
     * Jika gagal, langsung abort 403.
     */
    protected function authorizeEventByRole(Event $event): void
    {
        $user = auth()->user();
        $userRole = $this->normalizeRole($user->role);

        // Superuser bebas akses
        if ($userRole === 'superuser') return;

        $eventRole = $this->normalizeRole($event->owner_role);

        // Logic Fallback: Jika event tidak punya owner_role (data lama)
        if (empty($eventRole)) {
            // Cek apakah user ini pembuatnya
            if ((int) $event->created_by_user_id !== (int) $user->id) {
                abort(403, 'Anda tidak memiliki izin untuk melihat analytics event ini.');
            }
            return;
        }

        // Logic Utama: Role event harus sama dengan role user
        if ($eventRole !== $userRole) {
            abort(403, 'Anda tidak memiliki izin untuk melihat analytics event ini.');
        }
    }

    // ==========================================
    // 1. LIST EVENT TERLAKSANA (HALAMAN UTAMA)
    // ==========================================
    public function completed()
    {
        $user = auth()->user();
        $userRole = $this->normalizeRole($user->role);

        // Query Dasar
        $query = Event::with(['category', 'registrations']);

        // Terapkan Filter Role
        $this->applyEventRoleFilter($query, $userRole, $user);

        // Filter Event yang SUDAH LEWAT tanggalnya
        $events = $query->where('tanggal_pelaksanaan', '<', now())
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->get();

        return view('admin.analytics.completed', compact('events'));
    }

    // ==========================================
    // 2. DETAIL EVENT ANALYTICS
    // ==========================================
    public function detail($id)
    {
        $event = Event::findOrFail($id);

        // Security Check
        $this->authorizeEventByRole($event);

        // Ambil data registrasi
        $registrations = EventRegistration::where('event_id', $id)
            ->with('mahasiswa') // Eager load mahasiswa biar ringan
            ->get();

        $totalRegistered = $registrations->count();

        // Filter collection (tidak perlu query ulang ke DB)
        $attended = $registrations->where('status', 'attended');
        $totalAttended = $attended->count();

        // Sisanya dianggap tidak hadir / baru terdaftar
        $notAttended = $registrations->where('status', '!=', 'attended');

        return view('admin.analytics.detail', compact(
            'event',
            'registrations',
            'totalRegistered',
            'totalAttended',
            'attended',
            'notAttended'
        ));
    }

    // ==========================================
    // 3. EXPORT EXCEL
    // ==========================================
    public function exportExcel($id)
    {
        $event = Event::findOrFail($id);

        // Security Check (PENTING: Mencegah user ganti ID di URL untuk download punya orang lain)
        $this->authorizeEventByRole($event);

        // Pastikan class Export kamu juga menerima filter atau hanya data raw event ID
        return Excel::download(new \App\Exports\EventExport($id), "Rekap_Event_{$id}.xlsx");
    }

    // ==========================================
    // 4. EXPORT PDF
    // ==========================================
    public function exportPDF($id)
    {
        $event = Event::findOrFail($id);

        // Security Check
        $this->authorizeEventByRole($event);

        $registrations = EventRegistration::where('event_id', $id)
            ->with('mahasiswa')
            ->orderBy('created_at', 'asc') // Urutkan biar rapi di PDF
            ->get();

        $pdf = Pdf::loadView('admin.analytics.pdf', compact('event', 'registrations'));

        // Opsi: set paper landscape jika kolom banyak
        // $pdf->setPaper('a4', 'landscape');

        return $pdf->download("Rekap_Event_{$id}.pdf");
    }
}

