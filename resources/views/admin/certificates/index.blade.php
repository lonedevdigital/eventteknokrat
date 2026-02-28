@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Sertifikat')

@section('content')

    <style>
        /* --- THEME VARIABLES & RESET --- */
        :root {
            --primary-green: #27ae60;
            --primary-green-dark: #219150;
            --text-grey: #2c3e50;
        }

        /* Global Flat Reset */
        .card, .btn, .badge, .alert, .form-control, .pagination .page-item .page-link {
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

        /* --- TABLE STYLE --- */
        .table-flat thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            border-bottom: 2px solid #e2e8f0;
            border-top: none;
            padding: 12px 15px;
            vertical-align: middle;
            letter-spacing: 0.5px;
        }
        .table-flat td {
            vertical-align: middle !important;
            padding: 15px;
            font-size: 0.9rem;
            border-bottom: 1px solid #f1f5f9;
            color: var(--text-grey);
        }

        /* --- CHECKLIST STYLE --- */
        .checklist-item {
            display: flex;
            align-items: flex-start; /* Align top agar teks panjang aman */
            font-size: 0.85rem;
            margin-bottom: 6px;
        }
        .checklist-icon {
            min-width: 24px;
            text-align: center;
            margin-right: 10px;
            padding-top: 2px; /* Sedikit turun agar sejajar teks */
            font-size: 1rem;
        }
        .text-green-theme { color: var(--primary-green); }

        /* --- BUTTONS --- */
        .btn-green {
            background-color: var(--primary-green);
            color: #fff;
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-green:hover {
            background-color: var(--primary-green-dark);
            color: #fff;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                {{-- FILTER SECTION --}}
                <div class="card card-sharp border-0 mb-3">
                    <div class="card-header-flat py-2" data-toggle="collapse" data-target="#filterCollapse" style="cursor: pointer;">
                        <h4 class="card-title font-weight-bold mb-0" style="font-size: 1rem;">
                            <i class="fas fa-filter mr-2"></i> Filter & Pencarian
                        </h4>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-white">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="collapse show" id="filterCollapse">
                        <div class="card-body bg-light p-3">
                            <form action="{{ route('certificates.index') }}" method="GET">
                                <div class="row">
                                    {{-- Search --}}
                                    <div class="col-md-3 mb-2">
                                        <label class="small font-weight-bold">Cari Event</label>
                                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama Event..." value="{{ request('search') }}">
                                    </div>

                                    {{-- Date Range --}}
                                    <div class="col-md-2 mb-2">
                                        <label class="small font-weight-bold">Dari Tanggal</label>
                                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="small font-weight-bold">Sampai Tanggal</label>
                                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                                    </div>

                                    {{-- Month --}}
                                    <div class="col-md-2 mb-2">
                                        <label class="small font-weight-bold">Bulan</label>
                                        <select name="month" class="form-control form-control-sm">
                                            <option value="">-- Semua --</option>
                                            @foreach(range(1, 12) as $m)
                                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Year --}}
                                    <div class="col-md-1 mb-2">
                                        <label class="small font-weight-bold">Tahun</label>
                                        <select name="year" class="form-control form-control-sm">
                                            <option value="">--</option>
                                            @for($y = date('Y'); $y >= 2020; $y--)
                                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    {{-- Limit --}}
                                    <div class="col-md-2 mb-2">
                                        <label class="small font-weight-bold">Baris</label>
                                        <select name="limit" class="form-control form-control-sm">
                                            @foreach([20, 30, 40, 50, 100] as $lim)
                                                <option value="{{ $lim }}" {{ request('limit') == $lim ? 'selected' : '' }}>{{ $lim }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="col-md-12 text-right mt-2">
                                        <a href="{{ route('certificates.index') }}" class="btn btn-secondary btn-sm mr-1">
                                            <i class="fas fa-undo"></i> Reset
                                        </a>
                                        <button type="submit" class="btn btn-green btn-sm">
                                            <i class="fas fa-search"></i> Terapkan Filter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card card-sharp border-0">

                    {{-- HEADER HIJAU FLAT --}}
                    <div class="card-header-flat d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold mb-0" style="font-size: 1.1rem;">
                            <i class="fas fa-certificate mr-2"></i> Manajemen Sertifikat Event
                        </h3>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-flat mb-0">
                                <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th style="min-width: 250px;">Informasi Event</th>
                                    <th style="min-width: 140px;" class="text-center">Tanggal</th>
                                    <th class="text-center" style="width: 100px;">Peserta</th>
                                    <th style="min-width: 250px;">Status Kelengkapan</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($events as $index => $ev)
                                    @php
                                        // Hitung data
                                        $hadir = $ev->registrations()->where('status', 'attended')->count();

                                        // Cek kelengkapan
                                        $hasBg = !empty($ev->certificate_background);
                                        $hasFormat = !empty($ev->certificate_number_format);
                                    @endphp

                                    <tr>
                                        {{-- NO --}}
                                        <td class="text-center text-muted">{{ $loop->iteration }}</td>

                                        {{-- EVENT --}}
                                        <td>
                                            <span class="d-block font-weight-bold text-dark mb-1" style="font-size: 1rem;">
                                                {{ $ev->nama_event }}
                                            </span>
                                            <span class="badge badge-light border text-uppercase" style="color: #64748b;">
                                                <i class="fas fa-tag mr-1 text-xs"></i> {{ $ev->category->nama_kategori ?? 'UMUM' }}
                                            </span>
                                        </td>

                                        {{-- TANGGAL --}}
                                        <td class="text-center">
                                            @if($ev->tanggal_pelaksanaan)
                                                <span class="text-secondary font-weight-bold" style="font-size: 0.85rem;">
                                                    {{ \Carbon\Carbon::parse($ev->tanggal_pelaksanaan)->format('d M Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        {{-- JUMLAH HADIR --}}
                                        <td class="text-center">
                                            <h5 class="mb-0 font-weight-bold text-green-theme">{{ $hadir }}</h5>
                                            <small class="text-muted" style="font-size: 0.75rem;">Hadir</small>
                                        </td>

                                        {{-- STATUS KELENGKAPAN --}}
                                        <td>
                                            <div class="d-flex flex-column">

                                                {{-- 1. Background Check --}}
                                                <div class="checklist-item">
                                                    <div class="checklist-icon">
                                                        @if($hasBg)
                                                            <i class="fas fa-check-circle text-green-theme"></i>
                                                        @else
                                                            <i class="fas fa-times-circle text-danger"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="d-block font-weight-bold {{ $hasBg ? 'text-green-theme' : 'text-danger' }}" style="line-height: 1.2;">
                                                            Template Background
                                                        </span>
                                                        <small class="text-muted">
                                                            {{ $hasBg ? 'Tersedia' : 'Belum diupload' }}
                                                        </small>
                                                    </div>
                                                </div>

                                                {{-- 2. Format Check --}}
                                                <div class="checklist-item">
                                                    <div class="checklist-icon">
                                                        @if($hasFormat)
                                                            <i class="fas fa-check-circle text-green-theme"></i>
                                                        @else
                                                            <i class="fas fa-exclamation-circle text-warning"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="d-block font-weight-bold {{ $hasFormat ? 'text-green-theme' : 'text-warning' }}" style="line-height: 1.2;">
                                                            Format Nomor
                                                        </span>
                                                        <small class="text-muted">
                                                            {{ $hasFormat ? 'Terkonfigurasi' : 'Perlu setting' }}
                                                        </small>
                                                    </div>
                                                </div>

                                            </div>
                                        </td>

                                        {{-- AKSI --}}
                                        <td class="text-center">
                                            <a href="{{ route('certificates.event-detail', $ev->id) }}"
                                               class="btn btn-green btn-sm px-3 shadow-sm"
                                               data-toggle="tooltip"
                                               title="Kelola Sertifikat">
                                                <i class="fas fa-cog mr-1"></i> KELOLA
                                            </a>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="py-3">
                                                <i class="fas fa-certificate fa-3x mb-3" style="color: #cbd5e1;"></i>
                                                <h6 class="font-weight-bold text-secondary">Belum Ada Event Selesai</h6>
                                                <small class="text-muted">Sertifikat hanya tersedia untuk event yang sudah terlaksana.</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Footer/Pagination --}}
                    @if(method_exists($events, 'links'))
                        <div class="card-footer bg-white border-top p-3">
                            <div class="float-right">
                                {{ $events->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>

@endsection
