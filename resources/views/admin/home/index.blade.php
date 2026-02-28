@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Dashboard Analytics')

@section('content')

    {{-- SETUP DATA --}}
    @php
        $phpRole = strtoupper((string) (auth()->user()->role ?? '-'));

        // Variabel Filter
        $selectedFilter = request('filter', 'weekly');
        $selectedYear   = request('year');
        $selectedMonth  = request('month');
        $selectedDay    = request('day');
        $yearNow        = now()->year;

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    @endphp

    <style>
        /* --- GLOBAL SHARP STYLE (KOTAK) --- */
        * {
            border-radius: 0 !important;
        }

        /* --- PAGINATION FIX (PERBAIKAN ICON BESAR) --- */
        /* Memaksa icon SVG di dalam navigasi pagination menjadi kecil */
        .card-footer nav svg,
        .card-footer nav .w-5 {
            width: 20px !important;
            height: 20px !important;
        }
        /* Merapikan layout pagination agar ada di tengah */
        .card-footer nav {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .card-footer nav > div:first-child {
            display: none; /* Menyembunyikan text "Showing 1 to 10" jika mengganggu */
        }
        .card-footer nav > div:last-child {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        /* --- DASHBOARD HEAD --- */
        .dash-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .role-pill {
            padding: 0.4rem 1rem;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border: 1px solid #343a40;
        }

        /* --- KPI CARDS --- */
        .kpi .small-box {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            transition: transform 0.2s;
            min-height: 130px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border: none;
        }
        .kpi .small-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .kpi .small-box .inner {
            padding: 20px;
            z-index: 2;
        }
        .kpi .small-box h3 {
            font-weight: 800;
            font-size: 2.2rem;
            margin: 0;
            line-height: 1;
        }
        .kpi .small-box p {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .kpi-sub {
            font-size: 0.8rem;
            opacity: 0.8;
            font-style: italic;
        }
        .kpi .icon {
            top: 15px;
            right: 15px;
            opacity: 0.15;
            font-size: 60px;
        }

        /* --- CARD STYLE --- */
        .card-sharp {
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            background: #fff;
            height: 100%;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .card-sharp .card-header {
            padding: 12px 20px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        .card-header-success { background-color: #28a745; color: #fff; }
        .card-header-warning { background-color: #ffc107; color: #1f2d3d; }
        .card-header-dark    { background-color: #343a40; color: #fff; }

        .card-sharp .card-header h5 {
            font-weight: 700;
            margin: 0;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }

        /* --- TABLE STYLES --- */
        .table-fit {
            width: 100%;
            margin-bottom: 0;
        }

        .table-fit th {
            background-color: #e9ecef;
            border-top: none;
            color: #212529;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 10px;
            vertical-align: middle;
            text-transform: uppercase;
            border-bottom: 2px solid #dee2e6;
            white-space: nowrap;
        }

        .table-fit td {
            vertical-align: middle !important;
            padding: 10px;
            font-size: 0.9rem;
            border-bottom: 1px solid #f4f4f4;
        }

        .td-date { width: 1%; white-space: nowrap; color: #495057; font-weight: 500; }
        .td-stat { width: 1%; white-space: nowrap; text-align: center; }
        .td-action { width: 1%; white-space: nowrap; text-align: center; }
        .td-wrap { white-space: normal !important; word-break: break-word; }

        .progress { background-color: #e9ecef; box-shadow: none; }

        .table-rekap th {
            font-size: 0.85rem;
            padding: 12px;
            vertical-align: middle;
            text-transform: uppercase;
        }
    </style>

    {{-- HEADER DASHBOARD --}}
    <div class="dash-head">
        <div>
            <h3 class="mb-0" style="font-weight: 800; color: #343a40; letter-spacing: -0.5px;">Dashboard Analytics</h3>
            <small class="text-muted">Ringkasan aktivitas dan performa event.</small>
        </div>
        <div>
            <span id="dynamic-role-badge" class="badge badge-dark role-pill shadow-sm">
                <i class="fas fa-user-tag mr-1"></i> Role: Loading...
            </span>
        </div>
    </div>

    {{-- SECTION 1: KPI CARDS --}}
    <div class="row kpi">
        <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalEvents }}</h3>
                    <p>Total Event</p>
                    <div class="kpi-sub">{{ $totalCompleted }} event selesai</div>
                </div>
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalMahasiswa }}</h3>
                    <p>Mahasiswa</p>
                    <div class="kpi-sub">Data master sistem</div>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalRegistered }}</h3>
                    <p>Pendaftar</p>
                    <div class="kpi-sub">Total akumulasi</div>
                </div>
                <div class="icon"><i class="fas fa-user-plus"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-12 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalAttended }}</h3>
                    <p>Kehadiran</p>
                    <div class="kpi-sub">Peserta valid</div>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: LIST EVENT (SPLIT VIEW) --}}
    <div class="row">
        {{-- KIRI: Event Past --}}
        <div class="col-lg-6 col-12 mb-3">
            <div class="card card-sharp">
                <div class="card-header card-header-success">
                    <h5><i class="fas fa-check-double mr-2"></i> Event Selesai</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 table-fit">
                            <thead>
                            <tr>
                                <th class="td-event">Event</th>
                                <th class="td-date text-center">Tanggal</th>
                                <th class="td-stat text-center">Hadir</th>
                                <th class="td-action text-center">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($completedEvents as $ev)
                                <tr>
                                    <td class="td-wrap">
                                        <span class="d-block font-weight-bold text-dark">{{ $ev->nama_event }}</span>
                                        <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">
                                            <i class="fas fa-tag mr-1 text-xs"></i>{{ $ev->category->nama_kategori ?? '-' }}
                                        </small>
                                    </td>
                                    <td class="td-date text-center">
                                        {{ \Carbon\Carbon::parse($ev->tanggal_pelaksanaan)->format('d/m/y') }}
                                    </td>
                                    <td class="td-stat text-center">
                                        <span class="badge badge-success px-2 py-1">{{ $ev->attended_count ?? 0 }}</span>
                                    </td>
                                    <td class="td-action text-center">
                                        <a href="{{ url('/dashboard/events/'.$ev->id.'/detail') }}"
                                           class="btn btn-xs btn-info rounded-0" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox mb-2"></i><br>Belum ada event selesai.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- FOOTER PAGINATION --}}
                @if($completedEvents->hasPages())
                    <div class="card-footer bg-white p-2">
                        {{ $completedEvents->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- KANAN: Event Upcoming --}}
        <div class="col-lg-6 col-12 mb-3">
            <div class="card card-sharp">
                <div class="card-header card-header-warning">
                    <h5><i class="fas fa-clock mr-2"></i> Event Akan Datang</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 table-fit">
                            <thead>
                            <tr>
                                <th class="td-event">Event</th>
                                <th class="td-date text-center">Tanggal</th>
                                <th class="td-stat text-center">Daftar</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($notYetEvents as $ev)
                                <tr>
                                    <td class="td-wrap">
                                        <span class="d-block font-weight-bold text-dark">{{ $ev->nama_event }}</span>
                                        <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.75rem;">
                                            <i class="fas fa-tag mr-1 text-xs"></i>{{ $ev->category->nama_kategori ?? '-' }}
                                        </small>
                                    </td>
                                    <td class="td-date text-center">
                                        {{ \Carbon\Carbon::parse($ev->tanggal_pelaksanaan)->format('d/m/y') }}
                                    </td>
                                    <td class="td-stat text-center">
                                        <span class="badge badge-primary px-2 py-1">{{ $ev->registrations_count ?? 0 }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-calendar-times mb-2"></i><br>Tidak ada event mendatang.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- FOOTER PAGINATION --}}
                @if($notYetEvents->hasPages())
                    <div class="card-footer bg-white p-2">
                        {{ $notYetEvents->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SECTION 3: REKAP DATA --}}
    <div class="card card-sharp mb-5">
        <div class="card-header card-header-dark d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-chart-line mr-2"></i> Rekapitulasi Data
            </h5>
            <a href="{{ route('dashboard.export-pdf', request()->query()) }}" target="_blank" class="btn btn-light btn-sm rounded-0 font-weight-bold text-dark">
                <i class="fas fa-file-pdf mr-1 text-danger"></i> Export PDF
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('dashboard') }}" class="mb-4 pb-3 border-bottom bg-light p-3 border">
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <span class="text-dark font-weight-bold small text-uppercase">
                            <i class="fas fa-filter mr-1 text-primary"></i> Filter:
                        </span>
                    </div>

                    <div class="col-sm-2">
                        <select name="filter" class="form-control form-control-sm rounded-0 border-secondary" onchange="this.form.submit()">
                            <option value="weekly"  {{ $selectedFilter=='weekly'  ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="monthly" {{ $selectedFilter=='monthly' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="yearly"  {{ $selectedFilter=='yearly'  ? 'selected' : '' }}>Tahun Ini</option>
                        </select>
                    </div>

                    <div class="col-auto text-muted small px-2">|</div>

                    <div class="col-sm-2">
                        <select name="year" class="form-control form-control-sm rounded-0 border-secondary">
                            <option value="">- Tahun -</option>
                            @for($y = $yearNow; $y >= $yearNow - 5; $y--)
                                <option value="{{ $y }}" {{ (string)$selectedYear === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select name="month" class="form-control form-control-sm rounded-0 border-secondary">
                            <option value="">- Bulan -</option>
                            @foreach($months as $m => $label)
                                <option value="{{ $m }}" {{ (string)$selectedMonth === (string)$m ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select name="day" class="form-control form-control-sm rounded-0 border-secondary">
                            <option value="">- Tgl -</option>
                            @for($d = 1; $d <= 31; $d++)
                                <option value="{{ $d }}" {{ (string)$selectedDay === (string)$d ? 'selected' : '' }}>{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-auto ml-auto">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary btn-sm rounded-0 px-3" title="Terapkan Filter">
                                <i class="fas fa-search mr-1"></i> Cari
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm rounded-0 px-2" title="Reset Filter">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0 table-rekap">
                    <thead class="bg-dark text-white">
                    <tr>
                        <th class="align-middle border-bottom-0" style="min-width: 250px;">NAMA EVENT</th>
                        <th class="align-middle text-center text-nowrap border-bottom-0" style="width: 130px;">TANGGAL</th>
                        <th class="align-middle text-center border-bottom-0" style="width: 80px;">DAFTAR</th>
                        <th class="align-middle text-center border-bottom-0" style="width: 80px;">HADIR</th>
                        <th class="align-middle text-center border-bottom-0" style="width: 80px;">ABSEN</th>
                        <th class="align-middle text-center border-bottom-0" style="min-width: 140px;">PERFORMA</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rekapEvents as $ev)
                        @php
                            $rawPercent = $ev['registered'] > 0 ? ($ev['attended']/$ev['registered'])*100 : 0;
                            $percent = round($rawPercent);

                            $colorClass = 'bg-danger';
                            if($percent >= 50) $colorClass = 'bg-warning';
                            if($percent >= 80) $colorClass = 'bg-success';
                        @endphp
                        <tr>
                            <td class="align-middle">
                                <span class="d-block font-weight-bold text-dark" style="line-height: 1.2;">{{ $ev['nama_event'] }}</span>
                                <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                    {{ $ev['kategori_event'] }}
                                </small>
                            </td>
                            <td class="align-middle text-center text-nowrap text-secondary font-weight-bold" style="font-size: 0.9rem;">
                                {{ \Carbon\Carbon::parse($ev['tanggal'])->locale('id')->isoFormat('D MMM Y') }}
                            </td>
                            <td class="align-middle text-center font-weight-bold">{{ $ev['registered'] }}</td>
                            <td class="align-middle text-center font-weight-bold text-success">{{ $ev['attended'] }}</td>
                            <td class="align-middle text-center text-danger">{{ $ev['absent'] }}</td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 mr-2 rounded-0 border" style="height: 10px; background-color: #f1f1f1;">
                                        <div class="progress-bar {{ $colorClass }} rounded-0" role="progressbar"
                                             style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="font-weight-bold small text-dark" style="width: 35px; text-align: right;">
                                        {{ $percent }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <div class="py-4">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i>
                                    <h6 class="font-weight-bold text-gray-500">Data Tidak Ditemukan</h6>
                                    <small>Coba ubah filter tanggal atau kategori pencarian Anda.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const badge = document.getElementById('dynamic-role-badge');
            const storedRole = sessionStorage.getItem('role');
            const serverRole = "{{ $phpRole }}";

            if (storedRole) {
                badge.innerHTML = '<i class="fas fa-user-tag mr-1"></i> ROLE: ' + storedRole.toUpperCase();
                if(['superuser', 'admin'].includes(storedRole.toLowerCase())){
                    badge.classList.remove('badge-dark');
                    badge.classList.add('badge-danger');
                }
            } else {
                badge.innerHTML = '<i class="fas fa-user-tag mr-1"></i> ROLE: ' + serverRole;
            }
        });
    </script>

@endsection
