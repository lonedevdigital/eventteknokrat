@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Kelola Sertifikat')

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
    .btn-entry {
        display:block; padding:22px 18px; text-align:center; border:2px solid #e2e8f0; color:var(--text-grey);
        font-weight:700; transition: all .15s ease;
    }
    .btn-entry:hover { border-color: var(--primary-green); color: var(--primary-green); text-decoration:none; }
    .btn-entry i { display:block; font-size: 1.7rem; margin-bottom:8px; color: var(--primary-green); }
    .table-flat thead th {
        background-color: var(--bg-light); color: #7f8c8d; border-bottom: 2px solid #bdc3c7;
        text-transform: uppercase; font-size: 0.62rem; letter-spacing: .5px; vertical-align: middle; padding: 9px 12px;
    }
    .table-flat tbody td { vertical-align: middle !important; color: var(--text-grey); padding: 9px 12px; font-size: 0.75rem; }
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
                <i class="fas fa-certificate mr-2"></i> Kelola Sertifikat — {{ $event->nama_event }}
            </h3>
            <div>
                <a href="{{ route('lomba.export.peserta.excel', $event->slug) }}" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);">
                    <i class="fas fa-file-excel mr-1"></i> Export Data Peserta
                </a>
                <a href="{{ route('lomba.manage', $event->slug) }}" class="btn btn-sm" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6 mb-2">
                    <a href="{{ route('lomba.certificates.designer', [$event->slug, 'panitia']) }}" class="btn-entry">
                        <i class="fas fa-user-tie"></i>
                        Sertifikat Panitia
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="{{ route('lomba.certificates.designer', [$event->slug, 'peserta']) }}" class="btn-entry">
                        <i class="fas fa-trophy"></i>
                        Sertifikat Peserta Lomba
                    </a>
                </div>
            </div>

            <form method="GET" class="form-row align-items-center mb-3">
                <div class="col-auto mb-2">
                    <select name="jenis" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="peserta" {{ $jenis === 'peserta' ? 'selected' : '' }}>Data Peserta</option>
                        <option value="panitia" {{ $jenis === 'panitia' ? 'selected' : '' }}>Data Panitia</option>
                    </select>
                </div>
                @if($jenis === 'peserta' && $event->branches->isNotEmpty())
                <div class="col-auto mb-2">
                    <select name="branch_id" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">— Semua Cabang —</option>
                        @foreach($event->branches as $b)
                            <option value="{{ $b->id }}" {{ (int) $branchId === $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </form>

            @if($jenis === 'peserta')
            <p class="text-muted small">
                <i class="fas fa-info-circle mr-1"></i>
                Hanya peserta yang seluruh berkas &amp; pembayarannya sudah di-Approve yang layak menerima sertifikat.
            </p>
            @endif

            <div class="table-responsive">
                <table class="table table-flat mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Nama</th>
                            <th style="width:110px;">NPM</th>
                            <th>{{ $jenis === 'panitia' ? 'Jabatan' : 'Cabang Lomba' }}</th>
                            <th style="width:130px;">Sertifikat</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($participants as $p)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $p['nama'] }}</td>
                            <td>{{ $p['npm'] ?: '—' }}</td>
                            <td>{{ $p['role'] }}</td>
                            <td>
                                @if($p['certificate_url'])
                                    <a href="{{ $p['certificate_url'] }}" target="_blank">Sudah Digenerate</a>
                                @else
                                    <span class="text-muted">Belum Digenerate</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                {{ $jenis === 'panitia' ? 'Belum ada panitia.' : 'Belum ada peserta yang memenuhi syarat kelayakan.' }}
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
