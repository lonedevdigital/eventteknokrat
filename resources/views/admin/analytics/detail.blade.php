@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Detail Event')

@section('content')

    <style>
        /* --- GLOBAL FLAT STYLE --- */
        :root {
            --primary-green: #27ae60;
            --primary-green-dark: #219150;
            --text-dark: #343a40;
        }

        /* Reset Radius & Shadow */
        .card, .btn, .small-box, .form-control, .table {
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        /* --- CARD STYLE --- */
        .card-sharp {
            border: 1px solid #dee2e6;
            background: #fff;
            margin-bottom: 20px;
        }

        /* Header Hijau Flat */
        .card-header-flat {
            background-color: var(--primary-green);
            color: #ffffff;
            padding: 15px 20px;
            border-bottom: none;
        }

        /* Sub-Header untuk Tabel (Solid Color) */
        .header-success { background-color: var(--primary-green); color: #fff; }
        .header-warning { background-color: #f39c12; color: #fff; }

        /* --- KPI BOXES --- */
        .small-box {
            transition: all 0.2s;
            border: none;
            margin-bottom: 20px;
        }
        .small-box:hover {
            transform: translateY(-3px);
            opacity: 0.95;
        }
        .small-box .inner { padding: 20px; }
        .small-box h3 { font-weight: 800; font-size: 2.2rem; margin: 0; }
        .small-box p { font-size: 1rem; font-weight: 500; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .small-box .icon { top: 10px; right: 10px; opacity: 0.2; font-size: 60px; }

        /* Warna KPI Flat */
        .bg-flat-blue   { background-color: #3498db !important; color: #fff; }
        .bg-flat-green  { background-color: #27ae60 !important; color: #fff; }
        .bg-flat-red    { background-color: #e74c3c !important; color: #fff; }

        /* --- TABLE STYLE --- */
        .table-flat thead th {
            background-color: #f4f6f9;
            color: #495057;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            border-bottom: 2px solid #dee2e6;
            border-top: none;
        }
        .table-flat td {
            vertical-align: middle;
            font-size: 0.9rem;
        }
    </style>

    <div class="container-fluid">

        {{-- HEADER UTAMA --}}
        <div class="card card-sharp border-0 mb-4">
            <div class="card-header-flat d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-chart-bar mr-2"></i> Analytics Event</h5>
                    <small style="opacity: 0.9;">{{ $event->nama_event }}</small>
                </div>
                <div class="text-right">
                    <span class="badge badge-light p-2 rounded-0">
                        <i class="far fa-calendar-alt mr-1"></i>
                        {{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- SECTION 1: STATISTICS (KPI) --}}
        <div class="row">
            {{-- Registrasi --}}
            <div class="col-md-4 col-12">
                <div class="small-box bg-flat-blue">
                    <div class="inner">
                        <h3>{{ $totalRegistered }}</h3>
                        <p>Total Pendaftar</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                </div>
            </div>

            {{-- Hadir --}}
            <div class="col-md-4 col-12">
                <div class="small-box bg-flat-green">
                    <div class="inner">
                        <h3>{{ $totalAttended }}</h3>
                        <p>Mahasiswa Hadir</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-double"></i></div>
                </div>
            </div>

            {{-- Tidak Hadir --}}
            <div class="col-md-4 col-12">
                <div class="small-box bg-flat-red">
                    <div class="inner">
                        <h3>{{ $totalRegistered - $totalAttended }}</h3>
                        <p>Tidak Hadir</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-times"></i></div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: CHART & EXPORT --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-sharp">
                    <div class="card-body">
                        <div class="row align-items-center">
                            {{-- Grafik --}}
                            <div class="col-md-8 border-right">
                                <h6 class="font-weight-bold text-muted text-uppercase mb-3 ml-2">Grafik Partisipasi</h6>
                                <div style="height: 300px; width: 100%;">
                                    <canvas id="eventChart"></canvas>
                                </div>
                            </div>

                            {{-- Tombol Export --}}
                            <div class="col-md-4 text-center py-4">
                                <h6 class="font-weight-bold text-muted text-uppercase mb-4">Unduh Laporan</h6>

                                <a href="{{ route('events.export.excel', $event->id) }}" class="btn btn-success btn-block mb-3 py-2 font-weight-bold" style="background-color: #217346; border: none;">
                                    <i class="fas fa-file-excel mr-2"></i> EXPORT EXCEL
                                </a>

                                <a href="{{ route('events.export.pdf', $event->id) }}" class="btn btn-danger btn-block py-2 font-weight-bold" style="background-color: #b30b00; border: none;">
                                    <i class="fas fa-file-pdf mr-2"></i> EXPORT PDF
                                </a>

                                <div class="mt-4 text-muted small">
                                    <i class="fas fa-info-circle mr-1"></i> Data diunduh sesuai kondisi real-time.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: TABEL DATA (Split View) --}}
        <div class="row">

            {{-- Tabel Hadir --}}
            <div class="col-lg-6 col-12">
                <div class="card card-sharp">
                    <div class="card-header header-success p-3">
                        <h6 class="mb-0 font-weight-bold"><i class="fas fa-check mr-2"></i> Mahasiswa Hadir</h6>
                    </div>
                    <div class="card-body p-0 table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-striped table-hover table-flat mb-0">
                            <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NPM</th>
                                <th>Prodi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($attended as $r)
                                <tr>
                                    <td class="font-weight-bold text-dark">{{ $r->mahasiswa->nama_mahasiswa }}</td>
                                    <td>{{ $r->mahasiswa->npm_mahasiswa }}</td>
                                    <td><span class="badge badge-light border">{{ $r->mahasiswa->nama_program_studi }}</span></td>
                                </tr>
                            @endforeach
                            @if($attended->count() === 0)
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data hadir.</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tabel Belum Hadir --}}
            <div class="col-lg-6 col-12">
                <div class="card card-sharp">
                    <div class="card-header header-warning p-3">
                        <h6 class="mb-0 font-weight-bold text-white"><i class="fas fa-clock mr-2"></i> Belum Hadir (Absen)</h6>
                    </div>
                    <div class="card-body p-0 table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-striped table-hover table-flat mb-0">
                            <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NPM</th>
                                <th>Prodi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($notAttended as $r)
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ $r->mahasiswa->nama_mahasiswa }}</td>
                                    <td>{{ $r->mahasiswa->npm_mahasiswa }}</td>
                                    <td><span class="badge badge-light border">{{ $r->mahasiswa->nama_program_studi }}</span></td>
                                </tr>
                            @endforeach
                            @if($notAttended->count() === 0)
                                <tr><td colspan="3" class="text-center text-muted py-4">Semua pendaftar telah hadir.</td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- CHART SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('eventChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar', // Bisa diganti 'doughnut' atau 'pie' jika ingin variasi
            data: {
                labels: ['Registrasi', 'Hadir', 'Tidak Hadir'],
                datasets: [{
                    label: 'Jumlah Mahasiswa',
                    data: [
                        {{ $totalRegistered }},
                        {{ $totalAttended }},
                        {{ $totalRegistered - $totalAttended }}
                    ],
                    backgroundColor: [
                        '#3498db', // Biru Flat (Registrasi)
                        '#27ae60', // Hijau Flat (Hadir)
                        '#e74c3c'  // Merah Flat (Tidak Hadir)
                    ],
                    borderWidth: 0, // Flat style tanpa border garis
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#343a40',
                        titleFont: { size: 13 },
                        bodyFont: { size: 14, weight: 'bold' },
                        padding: 10,
                        cornerRadius: 0, // Tooltip kotak
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f4f4f4' },
                        ticks: { stepSize: 1, font: { weight: 'bold' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold' } }
                    }
                }
            }
        });
    </script>

@endsection
