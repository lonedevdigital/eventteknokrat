<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Mahasiswa;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Statistik
        $totalEvents = Event::count();
        $totalMahasiswa = Mahasiswa::count();
        $totalRegistered = EventRegistration::count();
        $totalAttended = EventRegistration::where('status', 'attended')->count();

        // EVENT SUDAH TERLAKSANA (pagination 5)
        $completedEvents = Event::where('tanggal_pelaksanaan', '<', now())
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->paginate(5, ['*'], 'completed_page');

        // EVENT BELUM TERLAKSANA (pagination 5)
        $notYetEvents = Event::where('tanggal_pelaksanaan', '>=', now())
            ->orderBy('tanggal_pelaksanaan', 'asc')
            ->paginate(5, ['*'], 'upcoming_page');

        // FILTER REKAP
        $filter = $request->filter ?? 'monthly';

        if ($filter === 'weekly') {
            $start = Carbon::now()->startOfWeek();
            $end   = Carbon::now()->endOfWeek();
        } elseif ($filter === 'yearly') {
            $start = Carbon::now()->startOfYear();
            $end   = Carbon::now()->endOfYear();
        } else {
            $start = Carbon::now()->startOfMonth();
            $end   = Carbon::now()->endOfMonth();
        }

        // REKAP EVENT
        $rekapEvents = Event::whereBetween('tanggal_pelaksanaan', [$start, $end])
            ->orderBy('tanggal_pelaksanaan', 'asc')
            ->get()
            ->map(function ($ev) {
                $registered = $ev->registrations()->count();
                $attended = $ev->registrations()->where('status', 'attended')->count();

                return [
                    'kategori_event' => $ev->category->nama_kategori ?? '-',
                    'nama_event' => $ev->nama_event,
                    'tanggal' => $ev->tanggal_pelaksanaan,
                    'registered' => $registered,
                    'attended' => $attended,
                    'absent' => $registered - $attended,
                ];
            });

        return view("admin.home.index", compact(
            'totalEvents',
            'totalMahasiswa',
            'totalRegistered',
            'totalAttended',
            'completedEvents',
            'notYetEvents',
            'rekapEvents',
            'filter',
            'start',
            'end'
        ));
    }

    public function exportRekapPdf(Request $request)
    {
        $filter = $request->filter ?? 'monthly';

        if ($filter === 'weekly') {
            $start = Carbon::now()->startOfWeek();
            $end   = Carbon::now()->endOfWeek();
        } elseif ($filter === 'yearly') {
            $start = Carbon::now()->startOfYear();
            $end   = Carbon::now()->endOfYear();
        } else {
            $start = Carbon::now()->startOfMonth();
            $end   = Carbon::now()->endOfMonth();
        }

        $events = Event::whereBetween('tanggal_pelaksanaan', [$start, $end])->get();

        $pdf = Pdf::loadView('admin.rekap.pdf', compact('events', 'start', 'end', 'filter'));

        return $pdf->download("Rekap_Event_{$filter}.pdf");
    }
}
