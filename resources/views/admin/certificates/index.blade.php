@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Sertifikat')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
        --accent-red: #e74c3c;
        --bg-light: #ecf0f1;
        --text-grey: #2c3e50;
    }

    .card, .btn, .form-control, .input-group-text,
    .badge, .custom-select {
        border-radius: 0 !important;
        box-shadow: none !important;
        border-color: #dee2e6;
    }

    .card-header-flat {
        background-color: var(--primary-green);
        color: #fff;
        padding: 12px 20px;
        border-bottom: none;
    }

    .btn-green {
        background-color: var(--primary-green);
        color: #fff;
        border: none;
        font-weight: 600;
    }
    .btn-green:hover { background-color: var(--primary-green-dark); color: #fff; }

    .btn-action-flat {
        border: none;
        color: #fff;
        padding: 5px 14px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-action-flat:hover { color: #fff; opacity: 0.88; }
    .btn-green-flat { background-color: var(--primary-green); }
    .btn-grey-flat  { background-color: #95a5a6; }

    .table-flat thead th {
        background-color: var(--bg-light);
        color: #7f8c8d;
        border-bottom: 2px solid #bdc3c7;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        vertical-align: middle;
    }
    .table-flat tbody td {
        vertical-align: middle !important;
        color: var(--text-grey);
        padding: 10px 12px;
    }
    .table-hover tbody tr:hover { background-color: #f9fbfb; }

    .badge-kategori {
        background-color: #e8f5e9;
        color: var(--primary-green);
        border: 1px solid var(--primary-green);
        font-size: 10px;
        padding: 2px 7px;
        font-weight: 600;
    }
</style>

<div class="container-fluid">

    @if(session('success'))
    <div class="alert fade show mb-3" style="background-color:#2ecc71;color:#fff;border:none;border-radius:0;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff;opacity:1"><span>&times;</span></button>
    </div>
    @endif

    <div class="card border-0">

        <div class="card-header-flat d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold mb-0" style="font-size:1.1rem;">
                <i class="fas fa-certificate mr-2"></i> Manajemen Sertifikat Event
            </h3>
        </div>

        <div class="card-body p-0">

            {{-- Filter --}}
            <div class="p-3" style="background-color:#f8fafc; border-bottom:1px solid #e2e8f0;">
                <form action="{{ route('certificates.index') }}" method="GET" class="mb-0">
                    <div class="form-row align-items-center">
                        <div class="col-12 col-lg-3 mb-2 mb-lg-0">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white text-muted border-right-0">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" name="search"
                                       class="form-control border-left-0"
                                       placeholder="Cari nama event..."
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-6 col-lg-2 mb-2 mb-lg-0">
                            <input type="date" name="start_date" class="form-control form-control-sm"
                                   value="{{ request('start_date') }}">
                        </div>
                        <div class="col-6 col-lg-2 mb-2 mb-lg-0">
                            <input type="date" name="end_date" class="form-control form-control-sm"
                                   value="{{ request('end_date') }}">
                        </div>
                        <div class="col-6 col-lg-1 mb-2 mb-lg-0">
                            <select name="month" class="form-control form-control-sm custom-select">
                                <option value="">Bln</option>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-lg-1 mb-2 mb-lg-0">
                            <select name="year" class="form-control form-control-sm custom-select">
                                <option value="">Thn</option>
                                @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-12 col-lg-2 mb-2 mb-lg-0">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-green btn-sm" style="width:75%;">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                                <a href="{{ route('certificates.index') }}"
                                   class="btn btn-default btn-sm border bg-white text-center"
                                   style="width:25%;" title="Reset Filter">
                                    <i class="fas fa-sync-alt text-muted"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="table-responsive">
                <table class="table table-hover table-flat mb-0">
                    <thead>
                        <tr>
                            <th class="text-center pl-3" style="width:46px;">No</th>
                            <th style="min-width:220px;">Event</th>
                            <th class="text-center" style="width:120px;">Tanggal</th>
                            <th class="text-center" style="width:80px;">Hadir</th>
                            <th style="width:210px;">Kelengkapan</th>
                            <th class="text-center" style="width:110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($events as $ev)
                        @php
                            $hadir     = $ev->registrations()->where('status', 'attended')->count();
                            $hasBg     = !empty($ev->certificate_background);
                            $hasFormat = !empty($ev->certificate_number_format);
                            $isReady   = $hasBg && $hasFormat;
                        @endphp
                        <tr>
                            <td class="text-center pl-3 text-muted">{{ $loop->iteration }}</td>

                            <td>
                                <div class="font-weight-bold" style="font-size:.93rem;">{{ $ev->nama_event }}</div>
                                <span class="badge-kategori mt-1 d-inline-block">
                                    {{ $ev->category->nama_kategori ?? 'Umum' }}
                                </span>
                            </td>

                            <td class="text-center text-muted" style="font-size:.85rem;">
                                {{ $ev->tanggal_pelaksanaan
                                    ? \Carbon\Carbon::parse($ev->tanggal_pelaksanaan)->format('d M Y')
                                    : '-' }}
                            </td>

                            <td class="text-center">
                                <span class="font-weight-bold" style="font-size:1.1rem; color:var(--primary-green);">{{ $hadir }}</span>
                                <div class="text-muted" style="font-size:10px;">peserta</div>
                            </td>

                            <td>
                                <div class="d-flex flex-column" style="gap:4px;">
                                    <span class="d-inline-flex align-items-center" style="gap:5px; font-size:12px;">
                                        @if($hasBg)
                                            <i class="fas fa-check-circle text-success"></i>
                                            <span class="font-weight-bold text-success">Background tersedia</span>
                                        @else
                                            <i class="fas fa-times-circle text-danger"></i>
                                            <span class="text-danger">Background belum diupload</span>
                                        @endif
                                    </span>
                                    <span class="d-inline-flex align-items-center" style="gap:5px; font-size:12px;">
                                        @if($hasFormat)
                                            <i class="fas fa-check-circle text-success"></i>
                                            <span class="font-weight-bold text-success">Format nomor siap</span>
                                        @else
                                            <i class="fas fa-exclamation-circle text-warning"></i>
                                            <span class="text-warning">Format nomor belum diatur</span>
                                        @endif
                                    </span>
                                </div>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('certificates.event-detail', $ev->id) }}"
                                   class="btn btn-sm btn-action-flat {{ $isReady ? 'btn-green-flat' : 'btn-grey-flat' }}"
                                   data-toggle="tooltip"
                                   title="{{ $isReady ? 'Kelola Sertifikat' : 'Belum lengkap, klik untuk melengkapi' }}">
                                    <i class="fas fa-cog mr-1"></i> KELOLA
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-certificate fa-3x mb-3 d-block" style="opacity:.2; color:#27ae60;"></i>
                                <strong>Belum ada event selesai</strong>
                                <div class="small mt-1">Sertifikat hanya tersedia untuk event yang sudah terlaksana.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        @if(method_exists($events, 'links') && $events->hasPages())
        <div class="card-footer bg-white border-top p-3">
            <div class="d-flex justify-content-end">{{ $events->appends(request()->query())->links() }}</div>
        </div>
        @endif

    </div>
</div>

<script>
    $(function () { $('[data-toggle="tooltip"]').tooltip() });
</script>

@endsection
