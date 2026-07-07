@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Lomba')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
        --bg-light: #ecf0f1;
        --text-grey: #2c3e50;
    }
    .card, .btn, .badge { border-radius: 0 !important; box-shadow: none !important; }
    .card-header-flat {
        background-color: var(--primary-green);
        color: #fff; padding: 12px 20px; border-bottom: none;
    }
    .btn-action-flat {
        border: none; color: #fff; padding: 5px 12px;
        font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    }
    .btn-action-flat:hover { color: #fff; opacity: .9; }
    .btn-green-flat { background-color: var(--primary-green); }
    .table-flat thead th {
        background-color: var(--bg-light); color: #7f8c8d;
        border-bottom: 2px solid #bdc3c7; text-transform: uppercase;
        font-size: .72rem; letter-spacing: .5px; vertical-align: middle; padding: 9px 12px;
    }
    .table-flat tbody td { vertical-align: middle !important; color: var(--text-grey); padding: 10px 12px; font-size: .875rem; }
    .table-hover tbody tr:hover { background-color: #f9fbfb; }
    .pill { font-size: 10px; padding: 2px 8px; font-weight: 600; border: 1px solid; }
    .pill-green  { background:#f0fdf4; color:#27ae60; border-color:#c3e6cb; }
    .pill-grey   { background:#f8f9fa; color:#6c757d; border-color:#dee2e6; }
    .pill-amber  { background:#fff8e1; color:#b8860b; border-color:#ffe08a; }
</style>

<div class="container-fluid">

    @if(session('success'))
    <div class="alert fade show mb-3" style="background-color:#2ecc71;color:#fff;border:none;border-radius:0;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff;opacity:1"><span>&times;</span></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert fade show mb-3" style="background-color:#e74c3c;color:#fff;border:none;border-radius:0;">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff;opacity:1"><span>&times;</span></button>
    </div>
    @endif

    <div class="card border-0">
        <div class="card-header-flat d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold mb-0" style="font-size:1.1rem;">
                <i class="fas fa-trophy mr-2"></i>
                {{ auth()->user()->isKetuaPelaksana() ? 'Lomba Saya' : 'Kelola Perlombaan' }}
            </h3>
            @unless(auth()->user()->isKetuaPelaksana())
            <a href="{{ route('events.create', ['type' => 'lomba']) }}" class="btn btn-sm"
               style="background:#fff;color:var(--primary-green);font-weight:600;">
                <i class="fas fa-plus mr-1"></i> Buat Perlombaan
            </a>
            @endunless
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-flat mb-0">
                    <thead>
                        <tr>
                            <th class="text-center pl-3" style="width:50px;">No</th>
                            <th>Nama Lomba</th>
                            <th style="width:140px;">Skala</th>
                            <th class="text-center" style="width:90px;">Tipe</th>
                            <th style="width:170px;">Ketua Pelaksana</th>
                            <th class="text-center" style="width:80px;">Panitia</th>
                            <th class="text-center" style="width:120px;">Tanggal</th>
                            <th class="text-center" style="width:110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($lombaList as $lomba)
                        <tr>
                            <td class="text-center pl-3 text-muted">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold" style="font-size:.93rem;">{{ $lomba->nama_event }}</td>
                            <td>
                                @if($lomba->partisipasi_skala === 'prodi')
                                    <span class="pill pill-grey">Prodi: {{ $lomba->partisipasi_prodi }}</span>
                                @else
                                    <span class="pill pill-green">Seluruh UTI</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($lomba->tipe_bayar === 'berbayar')
                                    <span class="pill pill-amber">Berbayar</span>
                                @else
                                    <span class="pill pill-green">Gratis</span>
                                @endif
                            </td>
                            <td>
                                @if($lomba->ketuaPelaksana)
                                    <span class="font-weight-bold" style="font-size:.85rem;">{{ $lomba->ketuaPelaksana->name }}</span>
                                @else
                                    <span class="text-muted font-italic" style="font-size:.8rem;">Belum ditetapkan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="font-weight-bold">{{ $lomba->committees_count }}</span>
                            </td>
                            <td class="text-center text-muted" style="font-size:.8rem;">
                                {{ $lomba->tanggal_pelaksanaan ? \Carbon\Carbon::parse($lomba->tanggal_pelaksanaan)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('lomba.manage', $lomba->id) }}" class="btn btn-sm btn-action-flat btn-green-flat">
                                    <i class="fas fa-cog mr-1"></i> Kelola
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-trophy fa-3x mb-3 d-block" style="opacity:.2; color:#27ae60;"></i>
                                @if(auth()->user()->isKetuaPelaksana())
                                    Belum ada lomba yang ditugaskan kepada Anda.
                                @else
                                    Belum ada perlombaan. Klik "Buat Perlombaan" untuk memulai.
                                @endif
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
