@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Detail Analytics Event')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
        --accent-amber: #f39c12;
        --accent-red: #e74c3c;
        --accent-blue: #3498db;
        --bg-light: #ecf0f1;
        --text-grey: #2c3e50;
    }

    .card, .btn, .form-control, .badge, .progress {
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    /* ── Card header ── */
    .card-header-flat {
        background-color: var(--primary-green);
        color: #fff;
        padding: 11px 18px;
        border-bottom: none;
    }
    .card-header-amber {
        background-color: var(--accent-amber);
        color: #fff;
        padding: 11px 18px;
        border-bottom: none;
    }
    .card-header-flat h5,
    .card-header-amber h5,
    .card-header-flat h6,
    .card-header-amber h6 {
        font-weight: 700;
        margin: 0;
        font-size: .97rem;
        color: #fff;
    }

    /* ── KPI boxes ── */
    .kpi-box {
        padding: 20px 22px;
        color: #fff;
        position: relative;
        overflow: hidden;
        min-height: 110px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .kpi-box .kpi-num {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 4px;
    }
    .kpi-box .kpi-label {
        font-size: .88rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
        opacity: .9;
    }
    .kpi-box .kpi-icon {
        position: absolute;
        right: 14px;
        bottom: 8px;
        font-size: 3.5rem;
        opacity: .12;
    }
    .kpi-blue  { background: var(--accent-blue); }
    .kpi-green { background: var(--primary-green); }
    .kpi-red   { background: var(--accent-red); }

    /* ── Flat table ── */
    .table-flat thead th {
        background-color: var(--bg-light);
        color: #7f8c8d;
        border-bottom: 2px solid #bdc3c7;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: .5px;
        vertical-align: middle;
        padding: 9px 12px;
    }
    .table-flat tbody td {
        vertical-align: middle !important;
        color: var(--text-grey);
        padding: 9px 12px;
        font-size: .875rem;
        border-bottom: 1px solid #f0f0f0;
    }
    .table-hover tbody tr:hover { background-color: #f9fbfb; }

    /* ── Export buttons ── */
    .btn-excel {
        background-color: #217346;
        color: #fff;
        border: none;
        font-weight: 700;
        letter-spacing: .3px;
    }
    .btn-excel:hover { background-color: #1a5c38; color: #fff; }

    .btn-pdf {
        background-color: #c0392b;
        color: #fff;
        border: none;
        font-weight: 700;
        letter-spacing: .3px;
    }
    .btn-pdf:hover { background-color: #a93226; color: #fff; }

    /* ── Progress ── */
    .progress { height: 10px; background: #ecf0f1; border: 1px solid #dee2e6; }

    /* ── Rate circle ── */
    .rate-display {
        text-align: center;
        padding: 16px 0 8px;
    }
    .rate-display .rate-num {
        font-size: 2.8rem;
        font-weight: 800;
        color: var(--primary-green);
        line-height: 1;
    }
    .rate-display .rate-label {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .6px;
        color: #95a5a6;
        font-weight: 600;
    }

    /* ── Prodi badge ── */
    .prodi-tag {
        background: #f0fdf4;
        color: var(--primary-green);
        border: 1px solid #c3e6cb;
        font-size: 10px;
        padding: 2px 7px;
        font-weight: 600;
        white-space: nowrap;
    }
</style>

<div class="container-fluid">

    {{-- ═══ HEADER ═══ --}}
    <div class="card border-0 mb-3">
        <div class="card-header-flat d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
            <div>
                <h5 class="mb-0 font-weight-bold" style="font-size:1.1rem;">
                    <i class="fas fa-chart-bar mr-2"></i> Analytics Event
                </h5>
                <small style="color:rgba(255,255,255,.8); font-size:.85rem;">
                    {{ $event->nama_event }}
                </small>
            </div>
            <div class="d-flex align-items-center" style="gap:8px;">
                <span style="background:rgba(255,255,255,.18); color:#fff; border:1px solid rgba(255,255,255,.35); padding:4px 12px; font-size:.8rem;">
                    <i class="far fa-calendar-alt mr-1"></i>
                    {{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y') }}
                </span>
                <a href="{{ route('dashboard') }}"
                   class="btn btn-sm"
                   style="background:rgba(255,255,255,.15); color:#fff; border:1px solid rgba(255,255,255,.4);">
                    <i class="fas fa-arrow-left mr-1"></i> Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- ═══ KPI ROW ═══ --}}
    <div class="row mb-3">
        <div class="col-md-4 col-12 mb-3">
            <div class="card border-0 kpi-box kpi-blue">
                <div class="kpi-num">{{ $totalRegistered }}</div>
                <div class="kpi-label">Total Pendaftar</div>
                <i class="fas fa-users kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-3">
            <div class="card border-0 kpi-box kpi-green">
                <div class="kpi-num">{{ $totalAttended }}</div>
                <div class="kpi-label">Mahasiswa Hadir</div>
                <i class="fas fa-check-double kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4 col-12 mb-3">
            <div class="card border-0 kpi-box kpi-red">
                <div class="kpi-num">{{ $totalRegistered - $totalAttended }}</div>
                <div class="kpi-label">Tidak Hadir</div>
                <i class="fas fa-user-times kpi-icon"></i>
            </div>
        </div>
    </div>

    {{-- ═══ CHART + EXPORT ═══ --}}
    <div class="row mb-3">

        {{-- Grafik Partisipasi --}}
        <div class="col-lg-8 col-12 mb-3">
            <div class="card border-0" style="border:1px solid #dee2e6!important;">
                <div class="card-header-flat d-flex align-items-center justify-content-between">
                    <h6><i class="fas fa-chart-bar mr-2"></i> Grafik Partisipasi</h6>
                    @php
                        $rate = $totalRegistered > 0 ? round(($totalAttended / $totalRegistered) * 100) : 0;
                        $rateColor = $rate >= 80 ? '#27ae60' : ($rate >= 50 ? '#f39c12' : '#e74c3c');
                    @endphp
                    <span style="color:rgba(255,255,255,.8); font-size:.8rem;">
                        Tingkat kehadiran:
                        <strong style="font-size:1rem;">{{ $rate }}%</strong>
                    </span>
                </div>

                <div class="card-body">
                    {{-- Progress bar kehadiran --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Kehadiran ({{ $totalAttended }}/{{ $totalRegistered }})</span>
                            <span style="color:{{ $rateColor }}; font-weight:700;">{{ $rate }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar"
                                 role="progressbar"
                                 style="width:{{ $rate }}%; background-color:{{ $rateColor }};"
                                 aria-valuenow="{{ $rate }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    {{-- Chart --}}
                    <div style="position:relative; height:240px;">
                        <canvas id="eventChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Unduh Laporan --}}
        <div class="col-lg-4 col-12 mb-3">
            <div class="card border-0" style="border:1px solid #dee2e6!important; height:100%;">
                <div class="card-header-flat">
                    <h6><i class="fas fa-download mr-2"></i> Unduh Laporan</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center" style="gap:12px;">

                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase mb-2" style="letter-spacing:.4px;">
                            Format File
                        </div>
                        <a href="{{ route('events.export.excel', $event->id) }}"
                           class="btn btn-excel btn-block py-2 mb-2">
                            <i class="fas fa-file-excel mr-2"></i> Export Excel (.xlsx)
                        </a>
                        <a href="{{ route('events.export.pdf', $event->id) }}"
                           class="btn btn-pdf btn-block py-2">
                            <i class="fas fa-file-pdf mr-2"></i> Export PDF
                        </a>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between text-muted" style="font-size:.82rem;">
                        <span><i class="fas fa-users mr-1"></i> Total Pendaftar</span>
                        <strong class="text-dark">{{ $totalRegistered }}</strong>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size:.82rem;">
                        <span><i class="fas fa-check-circle mr-1 text-success"></i> Hadir</span>
                        <strong style="color:var(--primary-green);">{{ $totalAttended }}</strong>
                    </div>
                    <div class="d-flex justify-content-between text-muted" style="font-size:.82rem;">
                        <span><i class="fas fa-times-circle mr-1 text-danger"></i> Tidak Hadir</span>
                        <strong class="text-danger">{{ $totalRegistered - $totalAttended }}</strong>
                    </div>

                    <div class="text-center mt-1" style="background:#f8fafc; border:1px solid #e2e8f0; padding:10px 0;">
                        <div style="font-size:1.8rem; font-weight:800; color:{{ $rateColor }}; line-height:1;">
                            {{ $rate }}%
                        </div>
                        <div style="font-size:.7rem; text-transform:uppercase; letter-spacing:.5px; color:#95a5a6; font-weight:600;">
                            Tingkat Kehadiran
                        </div>
                    </div>

                    <p class="text-muted mb-0" style="font-size:.78rem;">
                        <i class="fas fa-info-circle mr-1"></i> Data real-time saat diunduh.
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══ TABEL HADIR & BELUM HADIR ═══ --}}
    <div class="row">

        {{-- Tabel Hadir --}}
        <div class="col-lg-6 col-12 mb-3">
            <div class="card border-0" style="border:1px solid #dee2e6!important;">
                <div class="card-header-flat d-flex align-items-center justify-content-between">
                    <h6><i class="fas fa-check-circle mr-2"></i> Mahasiswa Hadir</h6>
                    <span style="background:rgba(255,255,255,.18); color:#fff; border:1px solid rgba(255,255,255,.3); padding:2px 10px; font-size:.78rem; font-weight:700;">
                        {{ $attended->count() }} orang
                    </span>
                </div>
                <div class="card-body p-0" style="max-height:420px; overflow-y:auto;">
                    <table class="table table-hover table-flat mb-0">
                        <thead>
                            <tr>
                                <th class="pl-3" style="width:36px;">#</th>
                                <th>Nama</th>
                                <th style="width:110px;">NPM</th>
                                <th>Prodi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($attended as $r)
                            <tr>
                                <td class="pl-3 text-muted">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold" style="font-size:.88rem;">
                                    {{ $r->mahasiswa->nama_mahasiswa }}
                                </td>
                                <td class="text-monospace" style="font-size:.82rem;">
                                    {{ $r->mahasiswa->npm_mahasiswa }}
                                </td>
                                <td>
                                    <span class="prodi-tag">
                                        {{ $r->mahasiswa->nama_program_studi }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-check fa-2x mb-2 d-block" style="opacity:.2;"></i>
                                    Belum ada data hadir.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tabel Belum Hadir --}}
        <div class="col-lg-6 col-12 mb-3">
            <div class="card border-0" style="border:1px solid #dee2e6!important;">
                <div class="card-header-amber d-flex align-items-center justify-content-between">
                    <h6><i class="fas fa-clock mr-2"></i> Belum Hadir (Absen)</h6>
                    <span style="background:rgba(255,255,255,.18); color:#fff; border:1px solid rgba(255,255,255,.3); padding:2px 10px; font-size:.78rem; font-weight:700;">
                        {{ $notAttended->count() }} orang
                    </span>
                </div>
                <div class="card-body p-0" style="max-height:420px; overflow-y:auto;">
                    <table class="table table-hover table-flat mb-0">
                        <thead>
                            <tr>
                                <th class="pl-3" style="width:36px;">#</th>
                                <th>Nama</th>
                                <th style="width:110px;">NPM</th>
                                <th>Prodi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($notAttended as $r)
                            <tr>
                                <td class="pl-3 text-muted">{{ $loop->iteration }}</td>
                                <td class="text-muted" style="font-size:.88rem;">
                                    {{ $r->mahasiswa->nama_mahasiswa }}
                                </td>
                                <td class="text-monospace" style="font-size:.82rem; color:#adb5bd;">
                                    {{ $r->mahasiswa->npm_mahasiswa }}
                                </td>
                                <td>
                                    <span class="prodi-tag" style="background:#fff3cd; color:#856404; border-color:#ffc107;">
                                        {{ $r->mahasiswa->nama_program_studi }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-trophy fa-2x mb-2 d-block" style="opacity:.2; color:#27ae60;"></i>
                                    Semua pendaftar telah hadir!
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- ═══ CHART SCRIPT ═══ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
    var ctx = document.getElementById('eventChart').getContext('2d');

    var totalReg  = {{ $totalRegistered }};
    var totalHadir = {{ $totalAttended }};
    var totalAbsen = totalReg - totalHadir;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Total Pendaftar', 'Hadir', 'Tidak Hadir'],
            datasets: [{
                data: [totalReg, totalHadir, totalAbsen],
                backgroundColor: ['#3498db', '#27ae60', '#e74c3c'],
                borderWidth: 0,
                barPercentage: 0.55,
                categoryPercentage: 0.7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#2c3e50',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 14, weight: 'bold' },
                    padding: 10,
                    cornerRadius: 0,
                    displayColors: false,
                    callbacks: {
                        label: function(ctx) {
                            return ctx.parsed.y + ' orang';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f0f0f0', drawBorder: false },
                    ticks: {
                        stepSize: 1,
                        font: { size: 11 },
                        color: '#95a5a6'
                    },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 12, weight: '600' },
                        color: '#555'
                    },
                    border: { display: false }
                }
            }
        }
    });
})();
</script>

@endsection
