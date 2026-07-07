@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Dashboard Analytics')

@section('content')

@php
    $phpRole        = strtoupper((string) (auth()->user()->role ?? '-'));
    $selectedFilter = request('filter', 'weekly');
    $selectedYear   = request('year');
    $selectedMonth  = request('month');
    $selectedDay    = request('day');
    $yearNow        = now()->year;

    $months = [
        1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
        5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
        9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
    ];
@endphp

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
        --accent-yellow: #f39c12;
        --bg-light: #ecf0f1;
        --text-grey: #2c3e50;
    }

    .card, .btn, .form-control, .input-group-text,
    .badge, .custom-select, .progress {
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    /* ---- Header flat ---- */
    .card-header-flat {
        background-color: var(--primary-green);
        color: #fff;
        padding: 11px 18px;
        border-bottom: none;
    }
    .card-header-flat h5,
    .card-header-flat .card-title {
        font-weight: 700;
        font-size: 1rem;
        margin: 0;
        color: #fff;
    }

    /* ---- KPI small-box overrides ---- */
    .kpi-row .small-box {
        min-height: 120px;
        border: none;
        margin-bottom: 0;
    }
    .kpi-row .small-box .inner h3 {
        font-size: 2rem;
        font-weight: 800;
    }
    .kpi-row .small-box .inner p {
        font-size: .95rem;
        font-weight: 600;
        margin-bottom: 2px;
    }
    .kpi-sub {
        font-size: .78rem;
        opacity: .8;
        font-style: italic;
    }
    .kpi-row .icon { opacity: .15; font-size: 55px; top: 12px; right: 12px; }

    /* ---- Flat table ---- */
    .table-flat thead th {
        background-color: var(--bg-light);
        color: #7f8c8d;
        border-bottom: 2px solid #bdc3c7;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        vertical-align: middle;
        padding: 9px 12px;
        white-space: nowrap;
    }
    .table-flat tbody td {
        vertical-align: middle !important;
        color: var(--text-grey);
        padding: 9px 12px;
        font-size: .875rem;
        border-bottom: 1px solid #f0f0f0;
    }
    .table-hover tbody tr:hover { background-color: #f9fbfb; }

    /* ---- Buttons ---- */
    .btn-green {
        background-color: var(--primary-green);
        color: #fff;
        border: none;
        font-weight: 600;
    }
    .btn-green:hover { background-color: var(--primary-green-dark); color: #fff; }

    /* ---- Filter bar ---- */
    .filter-bar {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 18px;
    }

    /* ---- Progress bar ---- */
    .progress { height: 8px; background-color: #e9ecef; border: 1px solid #dee2e6; }

    /* ---- Role badge ---- */
    .role-pill {
        background: rgba(255,255,255,0.18);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.4);
        padding: 4px 12px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    /* ---- Section dividers ---- */
    .section-label {
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: #95a5a6;
        margin-bottom: 8px;
    }

    /* ---- Status badges ---- */
    .badge-selesai {
        background-color: #d4edda;
        color: #155724;
        font-size: 10px;
        padding: 2px 7px;
        font-weight: 700;
    }
    .badge-upcoming {
        background-color: #fff3cd;
        color: #856404;
        font-size: 10px;
        padding: 2px 7px;
        font-weight: 700;
    }
</style>

<div class="container-fluid">

    {{-- ═══════════════════════════════ HEADER DASHBOARD ═══════════════════════════════ --}}
    <div class="card border-0 mb-3">
        <div class="card-header-flat d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
            <div>
                <h4 class="font-weight-bold mb-0" style="font-size:1.15rem; color:#fff;">
                    <i class="fas fa-chart-line mr-2"></i> Dashboard Analytics
                </h4>
                <small style="color:rgba(255,255,255,.7);">Ringkasan aktivitas dan performa event</small>
            </div>
            <div class="d-flex align-items-center" style="gap:8px;">
                <span class="role-pill">
                    <i class="fas fa-user-tag mr-1"></i> {{ $phpRole }}
                </span>
                <a href="{{ route('dashboard.export-pdf', request()->query()) }}"
                   target="_blank"
                   class="btn btn-sm"
                   style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.4);">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════ KPI CARDS ═══════════════════════════════ --}}
    <div class="row kpi-row mb-3">
        <div class="col-xl-3 col-md-6 col-12 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalEvents }}</h3>
                    <p>Total Event</p>
                    <div class="kpi-sub">{{ $totalCompleted }} event selesai</div>
                </div>
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12 mb-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalMahasiswa }}</h3>
                    <p>Mahasiswa</p>
                    <div class="kpi-sub">Data master sistem</div>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalRegistered }}</h3>
                    <p>Pendaftar</p>
                    <div class="kpi-sub">Total akumulasi</div>
                </div>
                <div class="icon"><i class="fas fa-user-plus"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalAttended }}</h3>
                    <p>Kehadiran</p>
                    <div class="kpi-sub">Peserta valid hadir</div>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════ EVENT LISTS ═══════════════════════════════ --}}
    <div class="row mb-3">

        {{-- Event Selesai --}}
        <div class="col-lg-6 col-12 mb-3">
            <div class="card border-0" style="border:1px solid #dee2e6!important;">
                <div class="card-header-flat d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="fas fa-check-double mr-2"></i> Event Selesai
                    </h5>
                    <span class="badge-selesai">{{ $completedEvents->total() }} event</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-flat mb-0">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th class="text-center" style="width:90px;">Tanggal</th>
                                    <th class="text-center" style="width:65px;">Hadir</th>
                                    <th class="text-center" style="width:55px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($completedEvents as $ev)
                                <tr>
                                    <td>
                                        <span class="font-weight-bold d-block" style="font-size:.88rem; line-height:1.3;">
                                            {{ $ev->nama_event }}
                                        </span>
                                        <small class="text-muted" style="font-size:.75rem;">
                                            {{ $ev->category->nama_kategori ?? '-' }}
                                        </small>
                                    </td>
                                    <td class="text-center text-muted" style="font-size:.82rem;">
                                        {{ \Carbon\Carbon::parse($ev->tanggal_pelaksanaan)->format('d/m/y') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="font-weight-bold" style="color:var(--primary-green);">
                                            {{ $ev->attended_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ url('/dashboard/events/'.$ev->id.'/detail') }}"
                                           class="btn btn-sm btn-green" style="padding:3px 8px; font-size:.75rem;"
                                           title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-check fa-2x mb-2 d-block" style="opacity:.2;"></i>
                                        Belum ada event selesai.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($completedEvents->hasPages())
                <div class="card-footer bg-white p-2 d-flex justify-content-center">
                    {{ $completedEvents->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Event Akan Datang --}}
        <div class="col-lg-6 col-12 mb-3">
            <div class="card border-0" style="border:1px solid #dee2e6!important;">
                <div class="card-header d-flex align-items-center justify-content-between"
                     style="background:#f39c12;color:#fff;padding:11px 18px;border-bottom:none;">
                    <h5 class="mb-0 font-weight-bold" style="font-size:1rem;">
                        <i class="fas fa-clock mr-2"></i> Event Akan Datang
                    </h5>
                    <span class="badge-upcoming" style="background:rgba(255,255,255,0.25);color:#fff;border-color:rgba(255,255,255,0.4);">
                        {{ $notYetEvents->total() }} event
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-flat mb-0">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th class="text-center" style="width:90px;">Tanggal</th>
                                    <th class="text-center" style="width:70px;">Pendaftar</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($notYetEvents as $ev)
                                <tr>
                                    <td>
                                        <span class="font-weight-bold d-block" style="font-size:.88rem; line-height:1.3;">
                                            {{ $ev->nama_event }}
                                        </span>
                                        <small class="text-muted" style="font-size:.75rem;">
                                            {{ $ev->category->nama_kategori ?? '-' }}
                                        </small>
                                    </td>
                                    <td class="text-center text-muted" style="font-size:.82rem;">
                                        {{ \Carbon\Carbon::parse($ev->tanggal_pelaksanaan)->format('d/m/y') }}
                                    </td>
                                    <td class="text-center font-weight-bold" style="color:var(--accent-yellow);">
                                        {{ $ev->registrations_count ?? 0 }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-times fa-2x mb-2 d-block" style="opacity:.2;"></i>
                                        Tidak ada event mendatang.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($notYetEvents->hasPages())
                <div class="card-footer bg-white p-2 d-flex justify-content-center">
                    {{ $notYetEvents->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════ REKAPITULASI ═══════════════════════════════ --}}
    <div class="card border-0 mb-4">
        <div class="card-header-flat d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="fas fa-table mr-2"></i> Rekapitulasi Data
            </h5>
        </div>

        {{-- Filter --}}
        <div class="filter-bar">
            <form method="GET" action="{{ route('dashboard') }}">
                <div class="form-row align-items-center">

                    <div class="col-auto mb-1">
                        <select name="filter" class="form-control form-control-sm custom-select" onchange="this.form.submit()">
                            <option value="weekly"  {{ $selectedFilter=='weekly'  ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="monthly" {{ $selectedFilter=='monthly' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="yearly"  {{ $selectedFilter=='yearly'  ? 'selected' : '' }}>Tahun Ini</option>
                        </select>
                    </div>

                    <div class="col-auto mb-1 text-muted small">|</div>

                    <div class="col-auto mb-1">
                        <select name="year" class="form-control form-control-sm custom-select">
                            <option value="">— Tahun —</option>
                            @for($y = $yearNow; $y >= $yearNow - 5; $y--)
                                <option value="{{ $y }}" {{ (string)$selectedYear === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-auto mb-1">
                        <select name="month" class="form-control form-control-sm custom-select">
                            <option value="">— Bulan —</option>
                            @foreach($months as $m => $label)
                                <option value="{{ $m }}" {{ (string)$selectedMonth === (string)$m ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto mb-1">
                        <select name="day" class="form-control form-control-sm custom-select">
                            <option value="">— Tgl —</option>
                            @for($d = 1; $d <= 31; $d++)
                                <option value="{{ $d }}" {{ (string)$selectedDay === (string)$d ? 'selected' : '' }}>
                                    {{ str_pad($d, 2, '0', STR_PAD_LEFT) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-auto mb-1">
                        <div class="btn-group">
                            <button type="submit" class="btn btn-green btn-sm px-3">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            <a href="{{ route('dashboard') }}"
                               class="btn btn-default btn-sm border bg-white px-2" title="Reset Filter">
                                <i class="fas fa-sync-alt text-muted"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        {{-- Tabel Rekap --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-flat mb-0">
                    <thead>
                        <tr>
                            <th style="min-width:220px;">Nama Event</th>
                            <th class="text-center" style="width:120px;">Tanggal</th>
                            <th class="text-center" style="width:75px;">Daftar</th>
                            <th class="text-center" style="width:75px;">Hadir</th>
                            <th class="text-center" style="width:75px;">Absen</th>
                            <th style="min-width:140px;">Performa</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($rekapEvents as $ev)
                        @php
                            $rawPercent = $ev['registered'] > 0 ? ($ev['attended'] / $ev['registered']) * 100 : 0;
                            $percent    = round($rawPercent);
                            $barColor   = $percent >= 80 ? 'bg-success' : ($percent >= 50 ? 'bg-warning' : 'bg-danger');
                        @endphp
                        <tr>
                            <td>
                                <span class="font-weight-bold d-block" style="font-size:.9rem; line-height:1.3;">
                                    {{ $ev['nama_event'] }}
                                </span>
                                <small class="text-muted" style="font-size:.75rem;">{{ $ev['kategori_event'] }}</small>
                            </td>
                            <td class="text-center text-muted" style="font-size:.85rem;">
                                {{ \Carbon\Carbon::parse($ev['tanggal'])->locale('id')->isoFormat('D MMM Y') }}
                            </td>
                            <td class="text-center font-weight-bold">{{ $ev['registered'] }}</td>
                            <td class="text-center font-weight-bold" style="color:var(--primary-green);">{{ $ev['attended'] }}</td>
                            <td class="text-center text-danger">{{ $ev['absent'] }}</td>
                            <td>
                                <div class="d-flex align-items-center" style="gap:8px;">
                                    <div class="progress flex-grow-1" style="height:8px;">
                                        <div class="progress-bar {{ $barColor }}"
                                             role="progressbar"
                                             style="width:{{ $percent }}%;"
                                             aria-valuenow="{{ $percent }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="font-weight-bold text-dark" style="font-size:.8rem; min-width:32px; text-align:right;">
                                        {{ $percent }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 d-block" style="opacity:.2; color:#27ae60;"></i>
                                <strong>Data tidak ditemukan</strong>
                                <div class="small mt-1">Coba ubah filter tanggal atau rentang waktu.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

@endsection
