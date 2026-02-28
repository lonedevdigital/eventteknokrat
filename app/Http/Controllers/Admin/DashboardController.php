<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    /**
     * Normalisasi nama role agar sesuai dengan database.
     */
    protected function normalizeRole(?string $role): string
    {
        $role = strtolower(trim((string) $role));
        if ($role === 'admin') return 'superuser';
        if ($role === 'super_user') return 'superuser';
        if ($role === 'kemasis') return 'kemahasiswaan';

        // Default kosong jika tidak ada, nanti di-handle di logic filter
        return $role ?: '';
    }

    /**
     * Menerapkan filter role ke query Event.
     * Ini adalah jantung dari request-mu.
     */
    protected function applyEventRoleFilter($query, string $userRole, $user)
    {
        // 1. Jika Superuser, jangan filter apa-apa (lihat semua)
        if ($userRole === 'superuser') {
            return $query;
        }

        // 2. Jika user biasa (BEM, Kemahasiswaan, dll)
        return $query->where(function ($q) use ($userRole, $user) {
            // A. Lihat event yang owner_role-nya sama dengan role user
            $q->where('owner_role', $userRole)

                // B. (Fallback) Lihat event lama (owner_role NULL) TAPI yang dibuat oleh user ini
                ->orWhere(function ($qq) use ($user) {
                    $qq->whereNull('owner_role')
                        ->where('created_by_user_id', $user->id);
                    // Pastikan kolom di DB 'created_by_user_id'
                    // Sesuaikan dengan yang ada di AdminEventController kamu.
                });
        });
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $this->normalizeRole($user->role);

        // Jika role tidak terdeteksi, safety check (opsional)
        if (!$userRole) {
            abort(403, 'Role akun Anda tidak valid.');
        }

        // ==========================================================
        // 1. BASE QUERY (Semua data berpusat dari sini)
        // ==========================================================
        // Kita siapkan satu query dasar yang sudah difilter role-nya.
        // Semua query di bawah akan meng-clone query ini.
        $baseQuery = Event::query();
        $this->applyEventRoleFilter($baseQuery, $userRole, $user);

        // ==========================================================
        // 2. KPI CARDS (Total Event, Registrasi, Kehadiran)
        // ==========================================================

        // A. Total Event (Sesuai Role)
        $totalEvents = (clone $baseQuery)->count();

        // B. Total Mahasiswa (Global - karena ini data master)
        $totalMahasiswa = Mahasiswa::count();

        // C. Total Registrasi & Hadir (Harus difilter by Event Role juga)
        // Kita cari EventRegistration dimana event-nya termasuk dalam akses user
        $baseRegistrations = EventRegistration::whereHas('event', function ($q) use ($userRole, $user) {
            $this->applyEventRoleFilter($q, $userRole, $user);
        });

        $totalRegistered = (clone $baseRegistrations)->count();
        $totalAttended   = (clone $baseRegistrations)->where('status', 'attended')->count();

        // Persentase
        $attendanceRate = $totalRegistered > 0
            ? round(($totalAttended / $totalRegistered) * 100, 2)
            : 0;

        // ==========================================================
        // 3. LIST EVENT (Completed & Upcoming)
        // ==========================================================
        $now = Carbon::now();

        // Event Sudah Terlaksana (Past)
        $completedEvents = (clone $baseQuery)
            ->with(['category'])
            ->withCount('registrations')
            ->withCount(['registrations as attended_count' => function ($q) {
                $q->where('status', 'attended');
            }])
            ->where('tanggal_pelaksanaan', '<', $now)
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->paginate(5, ['*'], 'completed_page')
            ->withQueryString();

        // Event Belum Terlaksana (Upcoming)
        $notYetEvents = (clone $baseQuery)
            ->with(['category'])
            ->withCount('registrations')
            ->withCount(['registrations as attended_count' => function ($q) {
                $q->where('status', 'attended');
            }])
            ->where('tanggal_pelaksanaan', '>=', $now)
            ->orderBy('tanggal_pelaksanaan', 'asc')
            ->paginate(5, ['*'], 'notyet_page')
            ->withQueryString();

        // ==========================================================
        // 4. REKAP EVENT (Filter Mingguan/Bulanan/Custom)
        // ==========================================================
        $filter = $request->get('filter', 'weekly');
        $rekapQuery = (clone $baseQuery); // Tetap gunakan base query yang sudah difilter role

        // Logika Filter Tanggal
        if ($request->filled('year') || $request->filled('month') || $request->filled('day')) {
            // CUSTOM FILTER
            if ($request->filled('year')) {
                $rekapQuery->whereYear('tanggal_pelaksanaan', $request->year);
            }
            if ($request->filled('month')) {
                $rekapQuery->whereMonth('tanggal_pelaksanaan', $request->month);
            }
            if ($request->filled('day')) {
                $rekapQuery->whereDay('tanggal_pelaksanaan', $request->day);
            }
        } else {
            // RANGE FILTER (Weekly/Monthly/Yearly relative to NOW)
            if ($filter === 'monthly') {
                $rekapQuery->whereBetween('tanggal_pelaksanaan', [
                    $now->copy()->startOfMonth(),
                    $now->copy()->endOfMonth()
                ]);
            } elseif ($filter === 'yearly') {
                $rekapQuery->whereBetween('tanggal_pelaksanaan', [
                    $now->copy()->startOfYear(),
                    $now->copy()->endOfYear()
                ]);
            } else { // Default Weekly
                $rekapQuery->whereBetween('tanggal_pelaksanaan', [
                    $now->copy()->startOfWeek(),
                    $now->copy()->endOfWeek()
                ]);
            }
        }

        $rawRekap = $rekapQuery
            ->with(['category'])
            ->withCount('registrations')
            ->withCount(['registrations as attended_count' => fn($q) => $q->where('status', 'attended')])
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->get();

        // Format data untuk View
        $rekapEvents = $rawRekap->map(function ($ev) {
            $reg = (int) $ev->registrations_count;
            $att = (int) $ev->attended_count;
            return [
                'kategori_event' => $ev->category->nama_kategori ?? '-',
                'nama_event'     => $ev->nama_event,
                'tanggal'        => $ev->tanggal_pelaksanaan, // Biarkan raw, view yang format
                'registered'     => $reg,
                'attended'       => $att,
                'absent'         => max(0, $reg - $att),
            ];
        })->toArray(); // Ubah ke array agar mudah di loop di blade

        return view('admin.dashboard.index', compact(
            'totalEvents',
            'totalMahasiswa',
            'totalRegistered',
            'totalAttended',
            'attendanceRate',
            'completedEvents',
            'notYetEvents',
            'filter',
            'rekapEvents'
        ));
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $userRole = $this->normalizeRole($user->role);

        // 1. BASE QUERY (Logic sama persis dengan Index)
        $baseQuery = Event::query();
        $this->applyEventRoleFilter($baseQuery, $userRole, $user);

        // 2. LOGIC FILTER REKAP (Copy dari Index)
        $filter = $request->get('filter', 'weekly');
        $rekapQuery = (clone $baseQuery);
        $now = Carbon::now();

        if ($request->filled('year') || $request->filled('month') || $request->filled('day')) {
            if ($request->filled('year')) $rekapQuery->whereYear('tanggal_pelaksanaan', $request->year);
            if ($request->filled('month')) $rekapQuery->whereMonth('tanggal_pelaksanaan', $request->month);
            if ($request->filled('day')) $rekapQuery->whereDay('tanggal_pelaksanaan', $request->day);
        } else {
            if ($filter === 'monthly') {
                $rekapQuery->whereBetween('tanggal_pelaksanaan', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
            } elseif ($filter === 'yearly') {
                $rekapQuery->whereBetween('tanggal_pelaksanaan', [$now->copy()->startOfYear(), $now->copy()->endOfYear()]);
            } else {
                $rekapQuery->whereBetween('tanggal_pelaksanaan', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
            }
        }

        // 3. AMBIL DATA
        $rawRekap = $rekapQuery
            ->with(['category'])
            ->withCount('registrations')
            ->withCount(['registrations as attended_count' => fn($q) => $q->where('status', 'attended')])
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->get();

        // 4. GENERATE PDF
        $pdf = Pdf::loadView('admin.dashboard.pdf_rekap', [
            'rekapEvents' => $rawRekap, // Kirim raw object agar lebih fleksibel di view PDF
            'filter' => $filter,
            'user' => $user
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Rekap-Event-'.now()->format('YmdHis').'.pdf');
    }
}


