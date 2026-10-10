@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Kelola Pembayaran')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
        --accent-red: #e74c3c;
        --bg-light: #ecf0f1;
        --text-grey: #2c3e50;
    }
    .card, .btn, .form-control, .custom-select, .badge { border-radius: 0 !important; box-shadow: none !important; }
    .card-header-flat { background-color: var(--primary-green); color: #fff; padding: 12px 20px; border-bottom: none; }
    .btn-green { background-color: var(--primary-green); color:#fff; border:none; font-weight:600; }
    .btn-green:hover { background-color: var(--primary-green-dark); color:#fff; }
    .table-flat thead th {
        background-color: var(--bg-light); color: #7f8c8d; border-bottom: 2px solid #bdc3c7;
        text-transform: uppercase; font-size: 0.62rem; letter-spacing: .5px; vertical-align: middle; padding: 9px 12px;
    }
    .table-flat tbody td { vertical-align: middle !important; color: var(--text-grey); padding: 9px 12px; font-size: 0.75rem; }
    .status-pill { font-size: 10px; padding: 3px 9px; font-weight: 700; display: inline-block; }
    .status-approved { background:#f0fdf4; color:#27ae60; border:1px solid #c3e6cb; }
    .status-rejected { background:#fef2f2; color:#e74c3c; border:1px solid #fecaca; }
    .status-pending { background:#fff8e1; color:#b8860b; border:1px solid #ffe08a; }
    .status-none { background:#f8f9fa; color:#6c757d; border:1px solid #dee2e6; }
    .btn-verify { background: var(--primary-green); color:#fff; border:none; padding:4px 10px; font-size: 0.62rem; }
</style>

<div class="container-fluid">

    @if(session('success'))
    <div class="alert fade show mb-3" style="background-color:#2ecc71;color:#fff;border:none;border-radius:0;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff;opacity:1"><span>&times;</span></button>
    </div>
    @endif

    <div class="card border-0 mb-3">
        <div class="card-header-flat d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold mb-0" style="font-size: 0.95rem;">
                <i class="fas fa-money-bill-wave mr-2"></i> Pembayaran — {{ $event->nama_event }}
            </h3>
            <a href="{{ route('payment.index') }}" class="btn btn-sm"
               style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        <div class="card-body">
            <form method="GET" class="form-row align-items-center mb-3">
                <div class="col-auto">
                    <select name="tipe_pendaftaran" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">— Semua Tipe —</option>
                        <option value="individu" {{ request('tipe_pendaftaran') === 'individu' ? 'selected' : '' }}>Individual</option>
                        <option value="tim" {{ request('tipe_pendaftaran') === 'tim' ? 'selected' : '' }}>Tim</option>
                    </select>
                </div>
                <div class="col-auto">
                    <a href="{{ route('payment.show', $event->slug) }}" class="btn btn-sm btn-default border">Reset</a>
                </div>
                <div class="col-auto ml-auto">
                    <a href="{{ route('payment.export.excel', array_merge(['event' => $event->id], request()->query())) }}" class="btn btn-sm btn-green">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                    <a href="{{ route('payment.export.pdf', array_merge(['event' => $event->id], request()->query())) }}" class="btn btn-sm" style="background:var(--accent-red);color:#fff;">
                        <i class="fas fa-file-pdf mr-1"></i> Export PDF
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-flat mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Nama Mahasiswa / Tim</th>
                            <th style="width:100px;">NPM</th>
                            <th>Cabang Lomba</th>
                            <th style="width:110px;">Tipe</th>
                            <th style="width:130px;">Bukti Bayar</th>
                            <th style="width:110px;">Status</th>
                            <th class="text-center" style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($registrations as $reg)
                        @php
                            $doc = $reg->documents->first();
                            $isTim = $reg->tipe_pendaftaran === 'tim';
                            $namaTampil = $isTim ? ($reg->nama_tim ?? '—') : ($reg->mahasiswa->nama_mahasiswa ?? $reg->user->name ?? '—');
                            $fileUrl = $doc ? (\Illuminate\Support\Str::startsWith($doc->file_path, ['http://', 'https://']) ? $doc->file_path : asset($doc->file_path)) : null;
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $namaTampil }}</td>
                            <td>{{ $isTim ? '—' : ($reg->mahasiswa->npm_mahasiswa ?? '—') }}</td>
                            <td>{{ $reg->branch->nama_cabang ?? '—' }}</td>
                            <td>{{ $isTim ? 'Tim' : 'Individual' }}</td>
                            <td>
                                @if($fileUrl)
                                    <a href="{{ $fileUrl }}" target="_blank">Lihat Bukti</a>
                                @else
                                    <span class="text-muted">Belum Upload</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-pill status-{{ $doc->status ?? 'none' }}">
                                    {{ $doc ? ($doc->status === 'approved' ? 'Terverifikasi' : ($doc->status === 'rejected' ? 'Ditolak' : 'Menunggu')) : 'Belum Upload' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($doc && $doc->status !== 'approved')
                                <form action="{{ route('payment.verify', [$event->slug, $doc->id]) }}" method="POST" onsubmit="return confirm('Verifikasi pembayaran ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-verify"><i class="fas fa-check mr-1"></i> Verifikasi</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada peserta.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
