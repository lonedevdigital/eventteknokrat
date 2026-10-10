@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Kelola Lomba')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
        --accent-red: #e74c3c;
        --bg-light: #ecf0f1;
        --text-grey: #2c3e50;
        --border-color: #e2e8f0;
    }

    .card, .btn, .form-control, .custom-select, .badge { 
        border-radius: 0 !important; 
        box-shadow: none !important; 
    }

    /* ── HEADER FLAT ADMIN THEME ── */
    .card-header-flat {
        background-color: var(--primary-green);
        color: #fff; 
        padding: 12px 20px; 
        border-bottom: none;
    }
    .btn-action-flat {
        border: none; 
        color: #fff; 
        padding: 5px 12px;
        font-size: .75rem; 
        font-weight: 700; 
        letter-spacing: .5px;
    }
    .btn-action-flat:hover { color: #fff; opacity: .9; }

    .btn-green { 
        background-color: var(--primary-green); 
        color: #fff !important; 
        border: none; 
        font-weight: 600; 
        font-size: .82rem;
    }
    .btn-green:hover { 
        background-color: var(--primary-green-dark); 
        color: #fff; 
    }
    .btn-del { 
        background: var(--accent-red); 
        color: #fff; 
        border: none; 
        padding: 4px 9px; 
        font-size: .72rem; 
    }
    .btn-del:hover { color: #fff; opacity: .85; }

    .section-head {
        font-size: 11px; 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: .5px;
        color: #7f8c8d; 
        margin-bottom: 12px; 
        border-bottom: 1px solid #e2e8f0; 
        padding-bottom: 6px;
    }
    .info-pair { font-size: .85rem; margin-bottom: 6px; }
    .info-pair span.k { color: #7f8c8d; display: inline-block; width: 140px; font-weight: 600; }

    /* ── LAYOUT BAR TABS (MATCHING BASE THEME) ── */
    .layout-bar-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        margin-bottom: 20px;
        padding: 6px;
    }
    .nav-layout-bar {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .nav-layout-bar .nav-item {
        flex: 1;
        min-width: 170px;
    }
    .nav-layout-bar .nav-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        font-weight: 700;
        font-size: .85rem;
        color: var(--text-grey);
        background: #f8fafc;
        border: 1px solid var(--border-color);
        text-decoration: none;
        transition: all .15s ease;
        cursor: pointer;
    }
    .nav-layout-bar .nav-link:hover {
        background: var(--bg-light);
        color: var(--text-grey);
    }
    .nav-layout-bar .nav-link.active {
        background: var(--primary-green) !important;
        color: #ffffff !important;
        border-color: var(--primary-green) !important;
    }
    .nav-layout-bar .nav-link .badge {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 6px;
    }

    /* ── CONTENT BOXES ── */
    .card-clean {
        background: #ffffff;
        border: 1px solid var(--border-color);
        margin-bottom: 20px;
    }
    .card-header-clean {
        background: #ffffff;
        border-bottom: 2px solid var(--primary-green);
        padding: 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .card-clean-title {
        font-size: .95rem;
        font-weight: 700;
        color: var(--text-grey);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ── STAT CARDS FLAT ── */
    .stat-box-flat {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-top: 3px solid var(--primary-green);
        padding: 14px 18px;
        margin-bottom: 16px;
    }
    .stat-val-flat {
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--text-grey);
        line-height: 1.1;
        margin-bottom: 3px;
    }
    .stat-lbl-flat {
        font-size: .75rem;
        font-weight: 700;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin: 0;
    }
    .stat-sub-flat {
        font-size: .74rem;
        color: #95a5a6;
        margin-top: 2px;
    }

    /* ── TABLE FLAT ADMIN ── */
    .table-flat thead th {
        background-color: var(--bg-light); 
        color: #7f8c8d; 
        border-bottom: 2px solid #bdc3c7;
        text-transform: uppercase; 
        font-size: .72rem; 
        letter-spacing: .5px; 
        vertical-align: middle; 
        padding: 10px 12px;
        border-top: none;
    }
    .table-flat tbody td { 
        vertical-align: middle !important; 
        color: var(--text-grey); 
        padding: 10px 12px; 
        font-size: .85rem; 
        border-bottom: 1px solid #ecf0f1;
    }
    .table-flat tbody tr:hover { 
        background-color: #fbfcfc; 
    }

    /* ── PILLS STATUS ── */
    .pill { font-size: 10.5px; padding: 2px 8px; font-weight: 600; border: 1px solid; display: inline-block; }
    .pill-green { background: #f0fdf4; color: #27ae60; border-color: #c3e6cb; }
    .pill-amber { background: #fff8e1; color: #b8860b; border-color: #ffe08a; }
    .pill-red   { background: #fef2f2; color: #e74c3c; border-color: #fecaca; }
    .pill-grey  { background: #f8f9fa; color: #6c757d; border-color: #dee2e6; }
    .pill-blue  { background: #eef2ff; color: #3b5bdb; border-color: #c7d2fe; }

    /* ── AUTOCOMPLETE DROPDOWN ── */
    .autocomplete-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid var(--primary-green);
        max-height: 280px;
        overflow-y: auto;
        z-index: 1050;
        border-radius: 0;
        margin-top: 2px;
    }
    .autocomplete-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        transition: background .15s ease;
    }
    .autocomplete-item:hover, .autocomplete-item.active {
        background-color: #f0fdf4;
    }
    .autocomplete-item:last-child {
        border-bottom: none;
    }
</style>

<div class="container-fluid">

    {{-- ALERT PESAN --}}
    @if(session('success'))
    <div class="alert fade show mb-3" style="background-color:#2ecc71; color:#fff; border:none; border-radius:0;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff; opacity:1"><span>&times;</span></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert fade show mb-3" style="background-color:#e74c3c; color:#fff; border:none; border-radius:0;">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff; opacity:1"><span>&times;</span></button>
    </div>
    @endif

    {{-- ── HEADER LOMBA ── --}}
    <div class="card border-0 mb-3" style="border: 1px solid var(--border-color) !important;">
        <div class="card-header-flat d-flex align-items-center justify-content-between flex-wrap" style="gap:10px;">
            <h3 class="card-title font-weight-bold mb-0" style="font-size:1.05rem;">
                <i class="fas fa-trophy mr-2"></i> {{ $event->nama_event }}
            </h3>
            <div class="d-flex align-items-center" style="gap:6px;">
                <a href="{{ route('events.edit', $event->slug ?: $event->id) }}" class="btn btn-sm btn-action-flat"
                   style="background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.4);">
                    <i class="fas fa-edit mr-1"></i> Edit Data Perlombaan
                </a>
                <a href="{{ route('lomba.export.peserta.excel', $event->slug) }}" class="btn btn-sm btn-action-flat"
                   style="background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.4);">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
                <a href="{{ route('lomba.index') }}" class="btn btn-sm btn-action-flat"
                   style="background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.4);">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-pair"><span class="k">Skala Partisipasi</span>:
                        @if($event->partisipasi_skala === 'prodi') 
                            Prodi {{ $event->partisipasi_prodi }} 
                        @elseif($event->partisipasi_skala)
                            Tingkat {{ ucfirst($event->partisipasi_skala) }}
                        @else 
                            Seluruh Mahasiswa UTI 
                        @endif
                    </div>
                    <div class="info-pair"><span class="k">Tipe Lomba</span>:
                        <span class="pill {{ $event->tipe_bayar === 'berbayar' ? 'pill-amber' : 'pill-green' }}">
                            {{ ucfirst($event->tipe_bayar ?? 'Gratis') }}
                        </span>
                    </div>
                    <div class="info-pair"><span class="k">Tanggal</span>:
                        {{ $event->tanggal_pelaksanaan ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y') : '—' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-pair"><span class="k">Tempat</span>: {{ $event->tempat_pelaksanaan ?? '—' }}</div>
                    <div class="info-pair"><span class="k">Pembuat (PJ)</span>: {{ $event->creator->name ?? '—' }}</div>
                    <div class="info-pair"><span class="k">Cabang Lomba</span>: {{ $event->branches->count() }} Cabang</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- LAYOUT BAR (STATISTIK | KEPANITIAAN | CABANG | BERKAS)       --}}
    {{-- ============================================================ --}}
    <div class="layout-bar-card">
        <ul class="nav nav-layout-bar" id="manageLombaTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-statistik-btn" data-toggle="pill" href="#tab-statistik" role="tab" aria-controls="tab-statistik" aria-selected="true">
                    <i class="fas fa-chart-pie mr-1"></i> Statistik
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-kepanitiaan-btn" data-toggle="pill" href="#tab-kepanitiaan" role="tab" aria-controls="tab-kepanitiaan" aria-selected="false">
                    <i class="fas fa-users mr-1"></i> Kepanitiaan
                    <span class="badge {{ $event->committees->count() > 0 ? 'badge-secondary' : 'badge-light border' }} ml-1">
                        {{ $event->committees->count() + ($event->ketuaPelaksana ? 1 : 0) }}
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-cabang-btn" data-toggle="pill" href="#tab-cabang" role="tab" aria-controls="tab-cabang" aria-selected="false">
                    <i class="fas fa-code-branch mr-1"></i> Cabang Lomba
                    <span class="badge badge-secondary ml-1">{{ $event->branches->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-berkas-btn" data-toggle="pill" href="#tab-berkas" role="tab" aria-controls="tab-berkas" aria-selected="false">
                    <i class="fas fa-file-alt mr-1"></i> Kelengkapan Berkas
                    @if($teamsIncomplete->count() > 0)
                        <span class="badge badge-danger ml-1" title="{{ $teamsIncomplete->count() }} tim belum melengkapi berkas">
                            {{ $teamsIncomplete->count() }}
                        </span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    {{-- ============================================================ --}}
    {{-- TAB CONTENTS                                                 --}}
    {{-- ============================================================ --}}
    <div class="tab-content" id="manageLombaTabContent">

        {{-- ======================================================== --}}
        {{-- TAB 1: STATISTIK                                         --}}
        {{-- ======================================================== --}}
        <div class="tab-pane fade show active" id="tab-statistik" role="tabpanel" aria-labelledby="tab-statistik-btn">
            
            {{-- 1. KARTU METRIK STATISTIK --}}
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-box-flat" style="border-top-color: var(--primary-green);">
                        <div class="stat-val-flat">{{ $totalPendaftar }}</div>
                        <div class="stat-lbl-flat">Total Pendaftar</div>
                        <div class="stat-sub-flat">Seluruh registran terdata</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-box-flat" style="border-top-color: #2980b9;">
                        <div class="stat-val-flat">
                            {{ $totalTim }} <span style="font-size:.9rem; font-weight:600; color:#7f8c8d;">Tim</span>
                        </div>
                        <div class="stat-lbl-flat">Jumlah Tim</div>
                        <div class="stat-sub-flat">{{ $totalPesertaTim }} peserta dalam tim</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-box-flat" style="border-top-color: #16a085;">
                        <div class="stat-val-flat">
                            {{ $totalIndividu }} <span style="font-size:.9rem; font-weight:600; color:#7f8c8d;">Orang</span>
                        </div>
                        <div class="stat-lbl-flat">Jumlah Individu</div>
                        <div class="stat-sub-flat">Pendaftar perorangan/mandiri</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-box-flat" style="border-top-color: #f39c12;">
                        <div class="stat-val-flat">
                            {{ $teamsWithStatus->where('status_verifikasi', 'verified')->count() }} / {{ $totalTim }}
                        </div>
                        <div class="stat-lbl-flat">Tim Terverifikasi</div>
                        <div class="stat-sub-flat">Lolos seleksi berkas/bayar</div>
                    </div>
                </div>
            </div>

            {{-- 2. KAPASITAS PENDAFTAR TIAP CABANG LOMBA --}}
            <div class="card-clean">
                <div class="card-header-clean">
                    <h6 class="card-clean-title">
                        <i class="fas fa-chart-bar" style="color:var(--primary-green);"></i>
                        Kapasitas &amp; Kuota Pendaftar Tiap Cabang Lomba
                    </h6>
                    <span class="text-muted small">Tersedia {{ $event->branches->count() }} Cabang Perlombaan</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flat mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Nama Cabang Lomba</th>
                                    <th>Format Peserta</th>
                                    <th>Kapasitas (Kuota)</th>
                                    <th>Pendaftar Terisi</th>
                                    <th>Sisa Kuota</th>
                                    <th class="text-center" style="width:130px;">Status Kuota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($event->branches as $b)
                                    @php
                                        $terisiReg = $branchRegistrationsCount->get($b->id, 0);
                                        $terisiTim = $branchTeamsCount->get($b->id, 0);
                                        $isTeam = ($b->maks_anggota_tim > 1);
                                        $terisi = $isTeam ? $terisiTim : $terisiReg;
                                        $kuota = $b->kuota;
                                        $sisa = $kuota ? max(0, $kuota - $terisi) : null;
                                    @endphp
                                    <tr>
                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-weight-bold" style="font-size:.875rem;">{{ $b->nama_cabang }}</div>
                                            @if($b->tipe_bayar === 'berbayar')
                                                <small class="text-muted">Biaya: Rp {{ number_format($b->harga_tim ?: $b->harga_individual ?: $b->harga_pendaftaran, 0, ',', '.') }}</small>
                                            @else
                                                <span class="text-success font-weight-bold" style="font-size:11px;">Gratis</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isTeam)
                                                <span class="pill pill-blue">Tim (Maks. {{ $b->maks_anggota_tim }})</span>
                                            @else
                                                <span class="pill pill-grey">Individu</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold">
                                            {{ $kuota ? $kuota . ($isTeam ? ' Tim' : ' Peserta') : 'Tak Terbatas' }}
                                        </td>
                                        <td>
                                            <span class="font-weight-bold">{{ $terisi }} {{ $isTeam ? 'Tim' : 'Peserta' }}</span>
                                            @if($isTeam && $terisiReg > 0)
                                                <small class="text-muted">({{ $terisiReg }} orang)</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sisa !== null)
                                                <span class="font-weight-bold {{ $sisa == 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ $sisa }} {{ $isTeam ? 'Tim' : 'Peserta' }}
                                                </span>
                                            @else
                                                <span class="text-success font-weight-bold">Tersedia</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($kuota && $terisi >= $kuota)
                                                <span class="pill pill-red font-weight-bold">Penuh</span>
                                            @else
                                                <span class="pill pill-green font-weight-bold">Tersedia</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada cabang lomba yang didaftarkan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 3. LIST TIM YANG SUDAH DIVERIFIKASI PENDAFTARAN ATAU BELUM --}}
            <div class="card-clean">
                <div class="card-header-clean">
                    <div>
                        <h6 class="card-clean-title">
                            <i class="fas fa-check-double" style="color:var(--primary-green);"></i>
                            List Tim &amp; Status Verifikasi Pendaftaran
                        </h6>
                    </div>
                    <div class="d-flex align-items-center" style="gap:6px;">
                        <select id="filterStatusVerifikasi" class="form-control form-control-sm" style="width: 200px;" onchange="filterTimTable()">
                            <option value="all">Semua Status ({{ $teamsWithStatus->count() }})</option>
                            <option value="verified">Terverifikasi ({{ $teamsWithStatus->where('status_verifikasi', 'verified')->count() }})</option>
                            <option value="pending">Menunggu Verifikasi ({{ $teamsWithStatus->where('status_verifikasi', 'pending')->count() }})</option>
                            <option value="unsubmitted">Belum Upload Berkas ({{ $teamsWithStatus->where('status_verifikasi', 'unsubmitted')->count() }})</option>
                            <option value="rejected">Ditolak ({{ $teamsWithStatus->where('status_verifikasi', 'rejected')->count() }})</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flat mb-0" id="timVerifikasiTable">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Nama Tim</th>
                                    <th>Cabang Lomba</th>
                                    <th>Ketua Tim</th>
                                    <th>Anggota Tim</th>
                                    <th class="text-center" style="width:150px;">Status Verifikasi</th>
                                    <th class="text-center" style="width:110px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamsWithStatus as $t)
                                    <tr class="tim-row" data-status="{{ $t->status_verifikasi }}">
                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-weight-bold" style="font-size:.875rem;">{{ $t->team->nama_tim }}</div>
                                            <small class="text-muted">Dibuat: {{ $t->team->created_at ? $t->team->created_at->format('d M Y') : '—' }}</small>
                                        </td>
                                        <td>
                                            <span class="pill pill-grey font-weight-bold">{{ $t->team->branch->nama_cabang ?? '—' }}</span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ $t->team->ketua->name ?? '—' }}</div>
                                            <small class="text-muted">
                                                NPM: {{ $t->team->ketua->npm ?? ($t->team->ketua->mahasiswa->npm_mahasiswa ?? $t->team->ketua->username) }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold">{{ $t->team->members->count() }} Anggota</span>
                                            <small class="text-muted d-block" title="{{ $t->team->members->pluck('nama')->implode(', ') }}">
                                                {{ \Illuminate\Support\Str::limit($t->team->members->pluck('nama')->implode(', '), 30) }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            @if($t->status_verifikasi === 'verified')
                                                <span class="pill pill-green font-weight-bold"><i class="fas fa-check mr-1"></i> Terverifikasi</span>
                                            @elseif($t->status_verifikasi === 'pending')
                                                <span class="pill pill-amber font-weight-bold"><i class="fas fa-clock mr-1"></i> Menunggu Verifikasi</span>
                                            @elseif($t->status_verifikasi === 'rejected')
                                                <span class="pill pill-red font-weight-bold"><i class="fas fa-times mr-1"></i> Ditolak</span>
                                            @else
                                                <span class="pill pill-grey font-weight-bold"><i class="fas fa-info-circle mr-1"></i> Belum Upload Berkas</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('lomba.berkas', ['event' => $event->slug, 'branch_id' => $t->team->branch_id]) }}" 
                                               class="btn btn-sm btn-light border font-weight-bold" title="Review Berkas">
                                                <i class="fas fa-clipboard-check mr-1 text-primary"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada tim yang mendaftar pada perlombaan ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- ======================================================== --}}
        {{-- TAB 2: KEPANITIAAN                                       --}}
        {{-- ======================================================== --}}
        <div class="tab-pane fade" id="tab-kepanitiaan" role="tabpanel" aria-labelledby="tab-kepanitiaan-btn">
            
            <div class="row">
                {{-- 1. KETUA PELAKSANA --}}
                <div class="col-lg-5 mb-3">
                    <div class="card-clean h-100">
                        <div class="card-header-clean">
                            <h6 class="card-clean-title">
                                <i class="fas fa-user-tie" style="color:var(--primary-green);"></i>
                                Ketua Pelaksana
                            </h6>
                        </div>
                        <div class="card-body">
                            {{-- Info Ketua Pelaksana Saat Ini --}}
                            @if($event->ketuaPelaksana)
                                @php
                                    $ketuaNpm = $event->ketuaPelaksana->npm ?: ($event->ketuaPelaksana->mahasiswa->npm_mahasiswa ?? $event->ketuaPelaksana->username);
                                    $ketuaProdi = $event->ketuaPelaksana->mahasiswa->nama_program_studi ?? null;
                                @endphp
                                <div class="d-flex align-items-center justify-content-between p-3 mb-3 bg-light border">
                                    <div class="d-flex align-items-center" style="gap:12px;">
                                        <div class="d-flex align-items-center justify-content-center bg-white border text-success" style="width:40px; height:40px; font-size:1.1rem; flex-shrink:0;">
                                            <i class="fas fa-user-check"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-dark" style="font-size:0.95rem;">{{ $event->ketuaPelaksana->name }}</div>
                                            <div class="text-muted small">
                                                NPM: <strong class="text-dark">{{ $ketuaNpm }}</strong>
                                                @if($ketuaProdi) &bull; {{ $ketuaProdi }} @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($canAssignKetua)
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="kosongkanKetuaPelaksana()" title="Hapus Ketua Pelaksana">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="p-3 mb-3 text-center bg-light border text-muted small">
                                    <i class="fas fa-info-circle mr-1 text-secondary"></i> Belum ada Ketua Pelaksana yang ditetapkan.
                                </div>
                            @endif

                            @if($canAssignKetua)
                                <form action="{{ route('lomba.assign-ketua', $event->slug) }}" method="POST" id="formAssignKetua">
                                    @csrf
                                    <input type="hidden" name="ketua_pelaksana_user_id" id="hiddenKetuaId" value="">
                                    <input type="hidden" name="npm" id="hiddenKetuaNpm" value="">

                                    <div class="position-relative">
                                        <label class="small font-weight-bold text-muted mb-1">
                                            {{ $event->ketuaPelaksana ? 'Ganti Ketua Pelaksana:' : 'Tetapkan Ketua Pelaksana:' }}
                                        </label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   id="searchKetuaInput" 
                                                   class="form-control" 
                                                   placeholder="Ketik NPM atau Nama mahasiswa..." 
                                                   autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-green font-weight-bold px-3">
                                                    <i class="fas fa-save mr-1"></i> Simpan
                                                </button>
                                            </div>
                                        </div>

                                        {{-- FLOATING AUTOCOMPLETE DROPDOWN --}}
                                        <div id="ketuaSearchResults" class="autocomplete-dropdown shadow" style="display:none;"></div>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-light border small text-muted mb-0">
                                    <i class="fas fa-info-circle mr-1"></i> Hanya Penanggung Jawab / Admin yang berhak mengubah Ketua Pelaksana.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 2. DAFTAR PANITIA INTI --}}
                <div class="col-lg-7 mb-3">
                    <div class="card-clean h-100">
                        <div class="card-header-clean">
                            <h6 class="card-clean-title">
                                <i class="fas fa-users-cog" style="color:var(--primary-green);"></i>
                                Daftar Panitia Inti
                            </h6>
                            <span class="badge badge-secondary">{{ $panitiaInti->count() }}</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-flat mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:40px;">#</th>
                                            <th>Jabatan</th>
                                            <th>Nama</th>
                                            <th style="width:110px;">NPM</th>
                                            <th>Hak Akses</th>
                                            <th class="text-center" style="width:60px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($panitiaInti as $pi)
                                            <tr>
                                                <td class="text-muted">{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="pill pill-blue font-weight-bold">{{ $pi->jabatan }}</span>
                                                </td>
                                                <td class="font-weight-bold" style="font-size:.85rem;">{{ $pi->nama ?? '—' }}</td>
                                                <td class="text-muted" style="font-size:.82rem;">{{ $pi->npm ?? '—' }}</td>
                                                <td>
                                                    @if(is_array($pi->permissions) && count($pi->permissions) > 0)
                                                        @foreach($pi->permissions as $perm)
                                                            @php $permLabel = \App\Constants\CommitteePermissions::all()[$perm] ?? $perm; @endphp
                                                            <span class="badge badge-light border text-muted" style="font-size:9px;" title="{{ $permLabel }}">
                                                                <i class="fas fa-check text-success mr-1"></i>{{ explode(' ', $permLabel)[0] }}
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted" style="font-size:9px;"><i>Standar</i></span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('lomba.panitia.destroy', [$event->slug, $pi->id]) }}" method="POST" onsubmit="return confirm('Hapus panitia ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-del"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada Panitia Inti (Sekretaris, Bendahara, dll).</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. DAFTAR PANITIA ACARA & SEKSI/DIVISI LAIN --}}
            <div class="card-clean">
                <div class="card-header-clean">
                    <h6 class="card-clean-title">
                        <i class="fas fa-users" style="color:var(--primary-green);"></i>
                        Daftar Panitia Acara &amp; Divisi Teknis
                    </h6>
                    <span class="badge badge-secondary">{{ $panitiaAcara->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flat mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Jabatan Divisi</th>
                                    <th>Nama Panitia</th>
                                    <th style="width:120px;">NPM</th>
                                    <th>Hak Akses</th>
                                    <th class="text-center" style="width:60px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($panitiaAcara as $pa)
                                    <tr>
                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="pill pill-green font-weight-bold">{{ $pa->jabatan }}</span>
                                        </td>
                                        <td class="font-weight-bold" style="font-size:.85rem;">{{ $pa->nama ?? '—' }}</td>
                                        <td class="text-muted" style="font-size:.82rem;">{{ $pa->npm ?? '—' }}</td>
                                        <td>
                                            @if(is_array($pa->permissions) && count($pa->permissions) > 0)
                                                @foreach($pa->permissions as $perm)
                                                    @php $permLabel = \App\Constants\CommitteePermissions::all()[$perm] ?? $perm; @endphp
                                                    <span class="badge badge-light border text-muted" style="font-size:9px;" title="{{ $permLabel }}">
                                                                <i class="fas fa-check text-success mr-1"></i>{{ explode(' ', $permLabel)[0] }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted" style="font-size:9px;"><i>Standar</i></span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('lomba.panitia.destroy', [$event->slug, $pa->id]) }}" method="POST" onsubmit="return confirm('Hapus panitia ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-del"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada Panitia Acara atau seksi teknis lainnya.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 4. FORM TAMBAH PANITIA BARU --}}
            <div class="card-clean">
                <div class="card-header-clean">
                    <h6 class="card-clean-title">
                        <i class="fas fa-user-plus" style="color:var(--primary-green);"></i>
                        Tambah Anggota Panitia
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('lomba.panitia.store', $event->slug) }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-5 mb-2">
                                <label class="small mb-1 font-weight-bold">Jabatan</label>
                                <select name="jabatan" id="jabatanSelect" class="form-control form-control-sm" onchange="toggleCustomJabatan()">
                                    @foreach($defaultJabatan as $jb)
                                        <option value="{{ $jb }}">{{ $jb }}</option>
                                    @endforeach
                                    <option value="__custom__">+ Jabatan lain (custom)…</option>
                                </select>
                                <input type="text" name="jabatan_custom" id="jabatanCustom" class="form-control form-control-sm mt-1"
                                       placeholder="Tulis nama jabatan custom" style="display:none;">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="small mb-1 font-weight-bold">NPM Mahasiswa</label>
                                <input type="text" name="npm" class="form-control form-control-sm" placeholder="Wajib (Mahasiswa aktif)" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small mb-1 font-weight-bold">Nama (Opsional)</label>
                                <input type="text" name="nama" class="form-control form-control-sm" placeholder="Otomatis dari NPM jika kosong">
                            </div>
                        </div>

                        <hr class="mt-2 mb-2">
                        <div class="section-head mb-2 text-primary"><i class="fas fa-key mr-1"></i> Hak Akses (Permissions)</div>
                        <div class="row mb-3 px-2">
                            @foreach(\App\Constants\CommitteePermissions::all() as $key => $label)
                                <div class="col-md-4 col-sm-6 mb-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input perm-checkbox" 
                                               id="perm_{{ $key }}" name="permissions[]" value="{{ $key }}">
                                        <label class="custom-control-label small" style="cursor:pointer;" for="perm_{{ $key }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-green btn-sm">
                            <i class="fas fa-user-plus mr-1"></i> Tambahkan Panitia
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- ======================================================== --}}
        {{-- TAB 3: CABANG LOMBA                                      --}}
        {{-- ======================================================== --}}
        <div class="tab-pane fade" id="tab-cabang" role="tabpanel" aria-labelledby="tab-cabang-btn">
            
            <div class="card-clean">
                <div class="card-header-clean">
                    <h6 class="card-clean-title">
                        <i class="fas fa-code-branch" style="color:var(--primary-green);"></i>
                        Daftar Cabang Lomba
                    </h6>
                    <a href="{{ route('lomba.cabang.create', $event->slug) }}" class="btn btn-green btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Cabang Lomba
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flat mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Nama Cabang</th>
                                    <th>Format &amp; Peserta</th>
                                    <th>Biaya Pendaftaran</th>
                                    <th>Kapasitas Kuota</th>
                                    <th>Buku Panduan (Juknis)</th>
                                    <th>WhatsApp Group</th>
                                    <th>PJ Cabang</th>
                                    <th class="text-center" style="width:100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($event->branches as $b)
                                    @php
                                        $coverB = $b->cover_image ? (Str::startsWith($b->cover_image, ['http://', 'https://']) ? $b->cover_image : asset($b->cover_image)) : null;
                                        $juknisB = $b->juknis_file ? (Str::startsWith($b->juknis_file, ['http://', 'https://']) ? $b->juknis_file : asset($b->juknis_file)) : null;
                                        $isTeam = ($b->maks_anggota_tim > 1);
                                    @endphp
                                    <tr>
                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-weight-bold" style="font-size:.875rem;">{{ $b->nama_cabang }}</div>
                                            @if($b->deskripsi)
                                                <small class="text-muted">{{ Str::limit($b->deskripsi, 40) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isTeam)
                                                <span class="pill pill-blue">Tim (Maks. {{ $b->maks_anggota_tim }})</span>
                                            @else
                                                <span class="pill pill-grey">Individu</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($b->tipe_bayar === 'berbayar')
                                                <div class="font-weight-bold">
                                                    Rp {{ number_format($b->harga_tim ?: $b->harga_individual ?: $b->harga_pendaftaran, 0, ',', '.') }}
                                                </div>
                                                @if($isTeam && $b->harga_individual > 0 && $b->harga_individual != $b->harga_tim)
                                                    <small class="text-muted">Individu: Rp {{ number_format($b->harga_individual, 0, ',', '.') }}</small>
                                                @endif
                                            @else
                                                <span class="pill pill-green font-weight-bold">Gratis</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="font-weight-bold">
                                                {{ $b->kuota ? $b->kuota . ($isTeam ? ' Tim' : ' Peserta') : 'Tak Terbatas' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($juknisB)
                                                <a href="{{ $juknisB }}" target="_blank" class="badge badge-info" style="font-size:10px;">
                                                    <i class="fas fa-file-pdf mr-1"></i> Unduh Juknis
                                                </a>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($b->wa_group_link)
                                                <a href="{{ $b->wa_group_link }}" target="_blank" class="badge badge-success" style="font-size:10px;">
                                                    <i class="fab fa-whatsapp mr-1"></i> WA Group
                                                </a>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="font-weight-bold">{{ $b->penanggungJawab ? $b->penanggungJawab->name : '—' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('lomba.cabang.edit', [$event->slug, $b->id]) }}" class="btn btn-sm btn-light border" title="Edit Cabang">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('lomba.cabang.destroy', [$event->slug, $b->id]) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Hapus cabang lomba {{ $b->nama_cabang }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-del" title="Hapus Cabang">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada cabang lomba yang terdaftar.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- ======================================================== --}}
        {{-- TAB 4: KELENGKAPAN BERKAS                                --}}
        {{-- ======================================================== --}}
        <div class="tab-pane fade" id="tab-berkas" role="tabpanel" aria-labelledby="tab-berkas-btn">
            
            @php
                $globalDocs = $event->documentRequirements->whereNull('branch_id')->values();
                $branchGroupedDocs = $event->documentRequirements->whereNotNull('branch_id')->groupBy('branch_id');
            @endphp

            {{-- 1. KELOLA PERSYARATAN BERKAS & JUKNIS (DIKATEGORIKAN PER CABANG LOMBA) --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap mb-3" style="gap:10px;">
                <div>
                    <h6 class="font-weight-bold mb-1 text-dark" style="font-size:1.05rem;">
                        <i class="fas fa-folder-open mr-1" style="color:var(--primary-green);"></i>
                        Kelola Persyaratan Berkas &amp; Juknis per Cabang Lomba
                    </h6>
                    <small class="text-muted">Persyaratan dokumen dikategorikan khusus menurut cabang lomba dan persyaratan umum (global).</small>
                </div>
                <button class="btn btn-green btn-sm" onclick="openAddBerkasModal('', 'Semua Cabang Lomba')">
                    <i class="fas fa-plus mr-1"></i> Tambah Persyaratan Berkas
                </button>
            </div>

            {{-- FILTER PILLS CABANG --}}
            <div class="mb-3 d-flex flex-wrap align-items-center" style="gap:6px;">
                <span class="small font-weight-bold text-muted mr-1"><i class="fas fa-filter mr-1"></i> Filter Kategori:</span>
                <button type="button" class="btn btn-sm btn-light border filter-cat-btn active font-weight-bold" onclick="filterBerkasCategory('all', this)">
                    Semua Kategori ({{ $event->documentRequirements->count() }})
                </button>
                <button type="button" class="btn btn-sm btn-light border filter-cat-btn" onclick="filterBerkasCategory('global', this)">
                    <i class="fas fa-globe text-primary mr-1"></i> Umum ({{ $globalDocs->count() }})
                </button>
                @foreach($event->branches as $b)
                    @php $bDocsCount = isset($branchGroupedDocs[$b->id]) ? $branchGroupedDocs[$b->id]->count() : 0; @endphp
                    <button type="button" class="btn btn-sm btn-light border filter-cat-btn" onclick="filterBerkasCategory('branch-{{ $b->id }}', this)">
                        {{ $b->nama_cabang }} ({{ $bDocsCount }})
                    </button>
                @endforeach
            </div>

            {{-- KATEGORI 1: PERSYARATAN UMUM (SEMUA CABANG) --}}
            <div class="card-clean berkas-cat-card" id="cat-card-global">
                <div class="card-header-clean bg-light d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-clean-title">
                            <i class="fas fa-globe text-primary"></i>
                            Kategori: <strong>Persyaratan Umum (Berlaku untuk Semua Cabang Lomba)</strong>
                        </h6>
                        <small class="text-muted">Dokumen wajib/opsional yang harus diunggah oleh seluruh peserta di semua divisi lomba.</small>
                    </div>
                    <div class="d-flex align-items-center" style="gap:6px;">
                        <span class="badge badge-primary px-2 py-1">{{ $globalDocs->count() }} Dokumen</span>
                        <button type="button" class="btn btn-green btn-sm" onclick="openAddBerkasModal('', 'Semua Cabang Lomba')">
                            <i class="fas fa-plus mr-1"></i> Tambah Berkas Umum
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flat mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Nama Dokumen Syarat</th>
                                    <th>Format Diizinkan</th>
                                    <th>Kewajiban</th>
                                    <th class="text-center" style="width:70px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($globalDocs as $doc)
                                    <tr>
                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                        <td class="font-weight-bold" style="font-size:.875rem;">
                                            {{ $doc->nama_dokumen }}
                                        </td>
                                        <td>
                                            <span class="badge badge-light border text-muted" style="font-size:10px;">
                                                {{ strtoupper(implode(', ', $doc->format_diizinkan ?? ['PDF'])) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($doc->is_required)
                                                <span class="badge badge-danger" style="font-size:10px;">Wajib</span>
                                            @else
                                                <span class="badge badge-secondary" style="font-size:10px;">Opsional</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('lomba.dokumen.destroy', [$event->slug, $doc->id]) }}" method="POST"
                                                  onsubmit="return confirm('Hapus persyaratan berkas umum {{ $doc->nama_dokumen }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-del" title="Hapus Dokumen">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Belum ada persyaratan berkas umum.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- KATEGORI 2: PERSYARATAN KHUSUS PER CABANG LOMBA --}}
            @foreach($event->branches as $b)
                @php
                    $bDocs = $branchGroupedDocs->get($b->id, collect());
                    $juknisB = $b->juknis_file ? (Str::startsWith($b->juknis_file, ['http://', 'https://']) ? $b->juknis_file : asset($b->juknis_file)) : null;
                    $isTeam = ($b->maks_anggota_tim > 1);
                @endphp
                <div class="card-clean berkas-cat-card" id="cat-card-branch-{{ $b->id }}">
                    <div class="card-header-clean d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
                        <div>
                            <h6 class="card-clean-title">
                                <i class="fas fa-code-branch" style="color:var(--primary-green);"></i>
                                Cabang Lomba: <strong>{{ $b->nama_cabang }}</strong>
                                <span class="pill pill-blue ml-2" style="font-size:10.5px;">
                                    {{ $isTeam ? 'Tim (Maks. ' . $b->maks_anggota_tim . ')' : 'Individu' }}
                                </span>
                            </h6>
                            <div class="small mt-1 d-flex align-items-center" style="gap:12px;">
                                <span class="text-muted">
                                    <i class="fas fa-file-invoice mr-1"></i> {{ $bDocs->count() }} Berkas Khusus
                                </span>
                                @if($juknisB)
                                    <a href="{{ $juknisB }}" target="_blank" class="badge badge-info font-weight-bold" style="font-size:10px;">
                                        <i class="fas fa-file-pdf mr-1"></i> Juknis / Rulebook
                                    </a>
                                @else
                                    <span class="text-muted small"><i>Belum ada Juknis cabang</i></span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap:6px;">
                            <span class="badge badge-secondary px-2 py-1">{{ $bDocs->count() }} Berkas</span>
                            <button type="button" class="btn btn-green btn-sm" onclick="openAddBerkasModal('{{ $b->id }}', '{{ addslashes($b->nama_cabang) }}')">
                                <i class="fas fa-plus mr-1"></i> Tambah Berkas ({{ $b->nama_cabang }})
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($bDocs->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-flat mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:40px;">#</th>
                                            <th>Nama Dokumen Syarat</th>
                                            <th>Format Diizinkan</th>
                                            <th>Kewajiban</th>
                                            <th class="text-center" style="width:70px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bDocs as $doc)
                                            <tr>
                                                <td class="text-muted">{{ $loop->iteration }}</td>
                                                <td class="font-weight-bold" style="font-size:.875rem;">
                                                    {{ $doc->nama_dokumen }}
                                                </td>
                                                <td>
                                                    <span class="badge badge-light border text-muted" style="font-size:10px;">
                                                        {{ strtoupper(implode(', ', $doc->format_diizinkan ?? ['PDF'])) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($doc->is_required)
                                                        <span class="badge badge-danger" style="font-size:10px;">Wajib</span>
                                                    @else
                                                        <span class="badge badge-secondary" style="font-size:10px;">Opsional</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('lomba.dokumen.destroy', [$event->slug, $doc->id]) }}" method="POST"
                                                          onsubmit="return confirm('Hapus persyaratan berkas {{ $doc->nama_dokumen }} untuk cabang {{ $b->nama_cabang }}?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-del" title="Hapus Dokumen">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-3 text-muted small" style="background:#fcfdfe;">
                                <i class="fas fa-info-circle mr-1 text-primary"></i>
                                Belum ada berkas khusus untuk cabang <strong>{{ $b->nama_cabang }}</strong> (peserta hanya wajib mengunggah Persyaratan Umum).
                                <a href="javascript:void(0)" onclick="openAddBerkasModal('{{ $b->id }}', '{{ addslashes($b->nama_cabang) }}')" class="font-weight-bold text-success ml-2">
                                    <i class="fas fa-plus-circle mr-1"></i> Tambah Berkas Khusus
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- 2. KELOLA TIM YANG BELUM MELENGKAPI BERKASNYA --}}
            <div class="card-clean mt-4">
                <div class="card-header-clean d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
                    <div>
                        <h6 class="card-clean-title">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                            Daftar Tim yang Belum Melengkapi Berkasnya
                        </h6>
                        <small class="text-muted">Menampilkan tim yang belum mengunggah seluruh dokumen wajib yang disyaratkan atau masih pending/ditolak.</small>
                    </div>
                    <div class="d-flex align-items-center" style="gap:8px;">
                        <select id="filterBranchIncomplete" class="form-control form-control-sm" style="width: 220px;" onchange="filterIncompleteTable()">
                            <option value="all">Semua Cabang ({{ $teamsIncomplete->count() }} Tim)</option>
                            @foreach($event->branches as $b)
                                @php $cIncomplete = $teamsIncomplete->where('team.branch_id', $b->id)->count(); @endphp
                                <option value="{{ $b->id }}">{{ $b->nama_cabang }} ({{ $cIncomplete }} Tim)</option>
                            @endforeach
                        </select>
                        <span class="badge badge-danger">{{ $teamsIncomplete->count() }} Tim Belum Lengkap</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-flat mb-0" id="tableIncompleteTeams">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Nama Tim</th>
                                    <th>Cabang Lomba</th>
                                    <th>Ketua Tim &amp; Kontak</th>
                                    <th>Berkas yang Belum Lengkap</th>
                                    <th class="text-center">Status Berkas</th>
                                    <th class="text-center" style="width:110px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamsIncomplete as $ti)
                                    <tr class="incomplete-row" data-branch-id="{{ $ti->team->branch_id }}">
                                        <td class="text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-weight-bold" style="font-size:.875rem;">{{ $ti->team->nama_tim }}</div>
                                            <small class="text-muted">{{ $ti->team->members->count() }} Anggota</small>
                                        </td>
                                        <td>
                                            <span class="pill pill-grey font-weight-bold">{{ $ti->team->branch->nama_cabang ?? '—' }}</span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ $ti->team->ketua->name ?? '—' }}</div>
                                            <small class="text-muted d-block">
                                                NPM: {{ $ti->team->ketua->npm ?? ($ti->team->ketua->mahasiswa->npm_mahasiswa ?? $ti->team->ketua->username) }}
                                            </small>
                                            @if($ti->team->ketua->no_telepon || ($ti->team->ketua->mahasiswa && $ti->team->ketua->mahasiswa->no_hp))
                                                @php $waNo = $ti->team->ketua->no_telepon ?: $ti->team->ketua->mahasiswa->no_hp; @endphp
                                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $waNo)) }}" 
                                                   target="_blank" class="badge badge-success mt-1" style="font-size:10px;">
                                                    <i class="fab fa-whatsapp mr-1"></i> WA Ketua
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ti->missing_reqs->isNotEmpty())
                                                @foreach($ti->missing_reqs as $mr)
                                                    <span class="badge badge-danger mr-1 mb-1 font-weight-bold" style="font-size:9.5px;" title="Belum diupload">
                                                        <i class="fas fa-times mr-1"></i> {{ $mr->nama_dokumen }}
                                                    </span>
                                                @endforeach
                                            @elseif($ti->pending_docs->isNotEmpty())
                                                <span class="badge badge-warning" style="font-size:10px;">
                                                    <i class="fas fa-clock mr-1"></i> {{ $ti->pending_docs->count() }} Menunggu Review
                                                </span>
                                            @elseif($ti->rejected_docs->isNotEmpty())
                                                <span class="badge badge-danger" style="font-size:10px;">
                                                    <i class="fas fa-times mr-1"></i> {{ $ti->rejected_docs->count() }} Ditolak
                                                </span>
                                            @else
                                                <span class="text-muted small">Belum ada berkas</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="pill pill-amber font-weight-bold">Belum Lengkap</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('lomba.berkas', ['event' => $event->slug, 'branch_id' => $ti->team->branch_id]) }}" 
                                               class="btn btn-sm btn-light border font-weight-bold" title="Review Berkas">
                                                <i class="fas fa-clipboard-check mr-1 text-primary"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-success font-weight-bold py-4">
                                            <i class="fas fa-check-circle mr-1"></i> Seluruh tim telah melengkapi berkas persyaratan mereka.
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

</div>

{{-- MODAL TAMBAH PERSYARATAN BERKAS --}}
<div class="modal fade" id="addBerkasModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('lomba.dokumen.store', $event->slug) }}" method="POST" class="modal-content border-0 rounded-0">
            @csrf
            <div class="modal-header card-header-flat">
                <div>
                    <h5 class="modal-title font-weight-bold" style="font-size: 1rem;">
                        <i class="fas fa-file-alt mr-1"></i> Tambah Persyaratan Berkas
                    </h5>
                    <small id="modalBranchSubtitle" class="text-white opacity-80" style="opacity:.85;">Untuk: Semua Cabang</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label class="small font-weight-bold">Nama Dokumen Syarat</label>
                    <input type="text" name="nama_dokumen" class="form-control form-control-sm" placeholder="Contoh: KTM / Bukti Mahasiswa, Surat Keterangan Orang Tua" required>
                </div>
                <div class="form-group mb-3">
                    <label class="small font-weight-bold">Format Diizinkan</label>
                    <div>
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="cb_pdf" name="format_diizinkan[]" value="pdf" checked>
                            <label class="custom-control-label small" for="cb_pdf">PDF</label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="cb_jpg" name="format_diizinkan[]" value="jpg">
                            <label class="custom-control-label small" for="cb_jpg">JPG / PNG</label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="cb_url" name="format_diizinkan[]" value="url">
                            <label class="custom-control-label small" for="cb_url">URL Link (GDrive / YouTube)</label>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="sw_req" name="is_required" value="1" checked>
                        <label class="custom-control-label small font-weight-bold" for="sw_req">Wajib Dikumpulkan (Required)</label>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label class="small font-weight-bold">Berlaku untuk Cabang Lomba</label>
                    <select name="branch_id" id="modalBranchSelect" class="form-control form-control-sm">
                        <option value="">-- Berlaku untuk Semua Cabang Lomba (Global) --</option>
                        @foreach($event->branches as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light p-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm btn-green">Simpan Persyaratan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tab Switching & URL Hash Persistence
    document.addEventListener("DOMContentLoaded", function() {
        function activateTab(targetHash) {
            if (!targetHash) return;
            var tabBtn = document.querySelector('#manageLombaTabs a[href="' + targetHash + '"]');
            var targetPane = document.querySelector(targetHash);
            if (tabBtn && targetPane) {
                // Bersihkan kelas active dari semua tombol tab dan panel
                $('#manageLombaTabs .nav-link').removeClass('active').attr('aria-selected', 'false');
                $('#manageLombaTabContent .tab-pane').removeClass('show active');

                // Aktifkan tab dan panel yang dipilih
                $(tabBtn).addClass('active').attr('aria-selected', 'true');
                $(targetPane).addClass('show active');
            }
        }

        // Cek URL Hash saat halaman dimuat
        var hash = window.location.hash;
        if (hash && document.querySelector('#manageLombaTabs a[href="' + hash + '"]')) {
            activateTab(hash);
        }

        // Handler saat tab diklik
        $('#manageLombaTabs .nav-link').on('click', function (e) {
            e.preventDefault();
            var targetHash = this.getAttribute('href');
            activateTab(targetHash);
            if (history.replaceState) {
                history.replaceState(null, null, targetHash);
            } else {
                window.location.hash = targetHash;
            }
        });

        // ── LIVE AUTOCOMPLETE KETUA PELAKSANA (NPM / NAMA) ──
        var ketuaInput = document.getElementById('searchKetuaInput');
        var resultsBox = document.getElementById('ketuaSearchResults');
        var hiddenId = document.getElementById('hiddenKetuaId');
        var hiddenNpm = document.getElementById('hiddenKetuaNpm');
        var formAssign = document.getElementById('formAssignKetua');

        if (ketuaInput && resultsBox) {
            var debounceTimer = null;

            ketuaInput.addEventListener('input', function() {
                var val = this.value.trim();
                clearTimeout(debounceTimer);

                // Reset id jika user mengubah teks ketikan
                if (hiddenId) hiddenId.value = '';

                if (val.length < 2) {
                    resultsBox.style.display = 'none';
                    resultsBox.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(function() {
                    resultsBox.innerHTML = '<div class="p-2 text-center text-muted small"><i class="fas fa-spinner fa-spin mr-1"></i> Mencari mahasiswa...</div>';
                    resultsBox.style.display = 'block';

                    fetch('{{ route("lomba.search-mahasiswa") }}?q=' + encodeURIComponent(val))
                        .then(function(res) { return res.json(); })
                        .then(function(data) {
                            if (!data || data.length === 0) {
                                resultsBox.innerHTML = '<div class="p-2 text-center text-muted small"><i class="fas fa-info-circle mr-1"></i> Mahasiswa tidak ditemukan.</div>';
                                return;
                            }

                            var html = '';
                            data.forEach(function(item) {
                                var prodiText = item.prodi ? ('<small class="text-muted d-block" style="font-size:11px;"><i class="fas fa-graduation-cap mr-1 text-info"></i>' + item.prodi + '</small>') : '';
                                html += '<div class="autocomplete-item" ' +
                                        'data-id="' + item.id + '" ' +
                                        'data-npm="' + (item.npm || '') + '" ' +
                                        'data-name="' + item.name.replace(/"/g, '&quot;') + '">' +
                                            '<div class="d-flex justify-content-between align-items-center">' +
                                                '<div>' +
                                                    '<strong class="text-dark d-block" style="font-size:.875rem;">' + item.name + '</strong>' +
                                                    prodiText +
                                                '</div>' +
                                                '<span class="badge badge-primary px-2 py-1 font-weight-bold ml-2" style="font-size:11px; white-space:nowrap;">' + (item.npm || '-') + '</span>' +
                                            '</div>' +
                                        '</div>';
                            });
                            resultsBox.innerHTML = html;

                            resultsBox.querySelectorAll('.autocomplete-item').forEach(function(el) {
                                el.addEventListener('click', function() {
                                    var id = this.getAttribute('data-id');
                                    var npm = this.getAttribute('data-npm');
                                    var name = this.getAttribute('data-name');
                                    selectStudent(id, npm, name);
                                });
                            });
                        })
                        .catch(function(err) {
                            console.error('Search error:', err);
                            resultsBox.innerHTML = '<div class="p-2 text-center text-danger small"><i class="fas fa-exclamation-circle mr-1"></i> Gagal memuat data pencarian.</div>';
                        });
                }, 200);
            });

            // Sembunyikan hasil saat klik di luar area
            document.addEventListener('click', function(e) {
                if (!ketuaInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.style.display = 'none';
                }
            });

            // Tampilkan kembali hasil jika input masih ada teks dan difokuskan
            ketuaInput.addEventListener('focus', function() {
                if (this.value.trim().length >= 2 && resultsBox.innerHTML.trim() !== '') {
                    resultsBox.style.display = 'block';
                }
            });

            // Validasi submit jika user hanya ketik NPM tanpa klik dropdown
            if (formAssign) {
                formAssign.addEventListener('submit', function(e) {
                    var val = ketuaInput.value.trim();
                    if (!hiddenId.value && !val) {
                        e.preventDefault();
                        alert('Silakan ketik atau pilih mahasiswa terlebih dahulu.');
                        return;
                    }
                    if (!hiddenId.value && val) {
                        var matchedNpm = val.split('-')[0].trim();
                        if (hiddenNpm) hiddenNpm.value = matchedNpm;
                    }
                });
            }
        }

        function selectStudent(id, npm, name) {
            if (hiddenId) hiddenId.value = id;
            if (hiddenNpm) hiddenNpm.value = npm;
            if (ketuaInput) ketuaInput.value = (npm ? npm + ' - ' : '') + name;
            if (resultsBox) resultsBox.style.display = 'none';
        }

        window.kosongkanKetuaPelaksana = function() {
            if (confirm('Apakah Anda yakin ingin mengosongkan Ketua Pelaksana untuk perlombaan ini?')) {
                if (hiddenId) hiddenId.value = '';
                if (hiddenNpm) hiddenNpm.value = '';
                if (formAssign) formAssign.submit();
            }
        };
    });

    // Filter Kategori Berkas
    function filterBerkasCategory(catKey, btn) {
        document.querySelectorAll('.filter-cat-btn').forEach(b => b.classList.remove('active', 'btn-success'));
        if (btn) btn.classList.add('active');

        var cards = document.querySelectorAll('.berkas-cat-card');
        if (catKey === 'all') {
            cards.forEach(c => c.style.display = '');
        } else {
            cards.forEach(c => {
                if (c.id === 'cat-card-' + catKey) {
                    c.style.display = '';
                } else {
                    c.style.display = 'none';
                }
            });
        }
    }

    // Filter Tim Belum Lengkap Berdasarkan Cabang
    function filterIncompleteTable() {
        var val = document.getElementById('filterBranchIncomplete').value;
        var rows = document.querySelectorAll('#tableIncompleteTeams .incomplete-row');
        rows.forEach(r => {
            if (val === 'all' || r.getAttribute('data-branch-id') === val) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    // Modal Helper: Pre-select Cabang Lomba
    function openAddBerkasModal(branchId, branchName) {
        var sel = document.getElementById('modalBranchSelect');
        if (sel) {
            sel.value = branchId;
        }
        var sub = document.getElementById('modalBranchSubtitle');
        if (sub) {
            sub.textContent = branchName ? ('Untuk: ' + branchName) : 'Untuk: Semua Cabang Lomba';
        }
        $('#addBerkasModal').modal('show');
    }

    // Filter status verifikasi tabel tim (Tab Statistik)
    function filterTimTable() {
        var val = document.getElementById('filterStatusVerifikasi').value;
        var rows = document.querySelectorAll('#timVerifikasiTable .tim-row');
        rows.forEach(function(r) {
            if (val === 'all' || r.getAttribute('data-status') === val) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    // Toggle custom jabatan form
    function toggleCustomJabatan() {
        var sel = document.getElementById('jabatanSelect');
        var inp = document.getElementById('jabatanCustom');
        inp.style.display = (sel.value === '__custom__') ? 'block' : 'none';
        applyRolePresets(sel.value);
    }

    function applyRolePresets(role) {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);

        const presets = {
            'Bendahara Lomba': ['kelola_pembayaran'],
            'Sekretaris Lomba': ['lihat_peserta', 'kelola_berkas'],
            'Panitia Acara': ['lihat_peserta', 'input_nilai', 'tetapkan_juara'],
        };

        if (presets[role]) {
            presets[role].forEach(perm => {
                let cb = document.getElementById('perm_' + perm);
                if(cb) cb.checked = true;
            });
        }
    }
</script>

@endsection
