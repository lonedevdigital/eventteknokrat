<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardAnalyticsController extends Controller
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
     * Filter query event berdasarkan Role User yang login.
     * Ini menjamin user tidak melihat data milik divisi lain.
     */
    protected function applyEventRoleFilter($query, string $userRole, $user)
    {
        // 1. Superuser: Lihat Semua
        if ($userRole === 'superuser') {
            return $query;
        }

        // 2. User Lain: Lihat berdasarkan owner_role
        return $query->where(function ($q) use ($userRole, $user) {
            // A. Event milik divisi ini
            $q->where('owner_role', $userRole)

                // B. Fallback untuk event lama (owner_role null) tapi buatan user ini
                ->orWhere(function ($qq) use ($user) {
                    $qq->whereNull('owner_role')
                        ->where('created_by_user_id', $user->id); // Pastikan kolom DB 'created_by_user_id'
                });
        });
    }

    /**
     * Main Function untuk menampilkan Dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $this->normalizeRole($user->role);
        $now = Carbon::now();

        // ==========================================
        // 1. BASE QUERY (Pusat Data)
        // ==========================================
        // Kita siapkan query dasar yang sudah terfilter role-nya.
        // Semua data di bawah akan mengambil dari base ini.
        $baseQuery = Event::query();
        $this->applyEventRoleFilter($baseQuery, $userRole, $user);

        // ==========================================
        // 2. DATA KPI (Global Summary by Role)
        // ==========================================

        // A. Total Event Dibuat
        $totalEvents = (clone $baseQuery)->count();

        // B. Total Event Terlaksana (Yang tanggalnya sudah lewat)
        $totalCompleted = (clone $baseQuery)
            ->where('tanggal_pelaksanaan', '<', $now)
            ->count();

        // C. Total Mahasiswa (Global Data Master)
        $totalMahasiswa = Mahasiswa::count();

        // D. Statistik Kehadiran (Registration vs Attended)
        // Kita hitung dari registrations yang event-nya milik role ini
        $regQuery = EventRegistration::whereHas('event', function($q) use ($userRole, $user) {
            $this->applyEventRoleFilter($q, $userRole, $user);
        });

        $totalRegistered = (clone $regQuery)->count();
        $totalAttended   = (clone $regQuery)->where('status', 'attended')->count();


        // ==========================================
        // 3. DATA LIST EVENT (Tables)
        // ==========================================

        // List A: Event Sudah Terlaksana (Past) - Pagination
        $completedEvents = (clone $baseQuery)
            ->with(['category'])
            ->withCount('registrations')
            ->withCount(['registrations as attended_count' => fn($q) => $q->where('status', 'attended')])
            ->where('tanggal_pelaksanaan', '<', $now)
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->paginate(5, ['*'], 'completed_page')
            ->withQueryString();

        // List B: Event Belum Terlaksana (Upcoming) - Pagination
        $notYetEvents = (clone $baseQuery)
            ->with(['category'])
            ->withCount('registrations')
            ->withCount(['registrations as attended_count' => fn($q) => $q->where('status', 'attended')])
            ->where('tanggal_pelaksanaan', '>=', $now)
            ->orderBy('tanggal_pelaksanaan', 'asc')
            ->paginate(5, ['*'], 'notyet_page')
            ->withQueryString();


        // ==========================================
        // 4. FILTERING & REKAP DATA
        // ==========================================
        // Fitur Filter: Mingguan, Bulanan, Tahunan, ATAU Custom Tanggal

        $filterType = $request->get('filter', 'weekly'); // default weekly
        $rekapQuery = (clone $baseQuery); // start from base role filtered

        // Cek apakah user input filter Custom (Tahun/Bulan/Tanggal)
        $hasCustomFilter = $request->filled('year') || $request->filled('month') || $request->filled('day');

        if ($hasCustomFilter) {
            // FILTER CUSTOM
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
            // FILTER RANGE (Weekly/Monthly/Yearly) relative to NOW
            if ($filterType === 'monthly') {
                $rekapQuery->whereBetween('tanggal_pelaksanaan', [
                    $now->copy()->startOfMonth(),
                    $now->copy()->endOfMonth()
                ]);
            } elseif ($filterType === 'yearly') {
                $rekapQuery->whereBetween('tanggal_pelaksanaan', [
                    $now->copy()->startOfYear(),
                    $now->copy()->endOfYear()
                ]);
            } else {
                // Default Weekly
                $rekapQuery->whereBetween('tanggal_pelaksanaan', [
                    $now->copy()->startOfWeek(),
                    $now->copy()->endOfWeek()
                ]);
            }
        }

        // Ambil data rekap
        $rawRekap = $rekapQuery
            ->with(['category'])
            ->withCount('registrations')
            ->withCount(['registrations as attended_count' => fn($q) => $q->where('status', 'attended')])
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->get();

        // Format data rekap jadi array sederhana untuk View
        $rekapEvents = $rawRekap->map(function ($ev) {
            $reg = (int) $ev->registrations_count;
            $att = (int) $ev->attended_count;
            return [
                'kategori_event' => $ev->category->nama_kategori ?? '-',
                'nama_event'     => $ev->nama_event,
                'tanggal'        => $ev->tanggal_pelaksanaan,
                'registered'     => $reg,
                'attended'       => $att,
                'absent'         => max(0, $reg - $att),
            ];
        })->toArray();


        // ==========================================
        // 5. RETURN VIEW
        // ==========================================
        // Mengirim semua variable ke home/index.blade.php

        return view('admin.home.index', compact(
        // KPI
            'totalEvents',
            'totalCompleted',
            'totalMahasiswa',
            'totalRegistered',
            'totalAttended',

            // List Tables
            'completedEvents',
            'notYetEvents',

            // Rekap & Filter Data
            'rekapEvents',
            'filterType'
        ));
    }
}


