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
    }
    .card, .btn, .form-control, .custom-select, .badge { border-radius: 0 !important; box-shadow: none !important; }
    .card-header-flat {
        background-color: var(--primary-green);
        color: #fff; padding: 12px 20px; border-bottom: none;
    }
    .btn-green { background-color: var(--primary-green); color:#fff; border:none; font-weight:600; }
    .btn-green:hover { background-color: var(--primary-green-dark); color:#fff; }
    .section-head {
        font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
        color: #7f8c8d; margin-bottom: 12px; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px;
    }
    .info-pair { font-size: .85rem; margin-bottom: 6px; }
    .info-pair span.k { color:#7f8c8d; display:inline-block; width:130px; }
    .table-flat thead th {
        background-color: var(--bg-light); color: #7f8c8d; border-bottom: 2px solid #bdc3c7;
        text-transform: uppercase; font-size: .72rem; letter-spacing: .5px; vertical-align: middle; padding: 9px 12px;
    }
    .table-flat tbody td { vertical-align: middle !important; color: var(--text-grey); padding: 9px 12px; font-size: .875rem; }
    .jabatan-badge { font-size: 10px; padding: 2px 8px; font-weight: 600; background:#f0fdf4; color:#27ae60; border:1px solid #c3e6cb; }
    .jabatan-badge.custom { background:#eef2ff; color:#3b5bdb; border-color:#c7d2fe; }
    .btn-del { background: var(--accent-red); color:#fff; border:none; padding:4px 9px; font-size:.72rem; }
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

    {{-- HEADER --}}
    <div class="card border-0 mb-3">
        <div class="card-header-flat d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold mb-0" style="font-size:1.05rem;">
                <i class="fas fa-trophy mr-2"></i> {{ $event->nama_event }}
            </h3>
            <a href="{{ route('lomba.index') }}" class="btn btn-sm"
               style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-pair"><span class="k">Skala Partisipasi</span>:
                        @if($event->partisipasi_skala === 'prodi') {{ $event->partisipasi_prodi }} @else Seluruh Mahasiswa UTI @endif
                    </div>
                    <div class="info-pair"><span class="k">Tipe Lomba</span>:
                        {{ $event->tipe_bayar === 'berbayar' ? 'Berbayar' : 'Gratis' }}
                    </div>
                    <div class="info-pair"><span class="k">Tanggal</span>:
                        {{ $event->tanggal_pelaksanaan ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y') : '—' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-pair"><span class="k">Tempat</span>: {{ $event->tempat_pelaksanaan }}</div>
                    <div class="info-pair"><span class="k">Pembuat (PJ)</span>: {{ $event->creator->name ?? '—' }}</div>
                    @if($event->tipe_bayar === 'berbayar')
                    <div class="info-pair"><span class="k">Cabang Lomba</span>: {{ $event->branches->count() }} cabang</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- KETUA PELAKSANA --}}
        <div class="col-lg-5 mb-3">
            <div class="card border-0 h-100" style="border:1px solid #e2e8f0 !important;">
                <div class="card-header bg-white" style="border-bottom:2px solid var(--primary-green);">
                    <h6 class="card-title font-weight-bold mb-0" style="font-size:.92rem;">
                        <i class="fas fa-user-tie mr-1" style="color:var(--primary-green);"></i> Ketua Pelaksana
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Ketua Pelaksana diberi akses untuk mengelola lomba ini &amp; kepanitiaannya.
                    </p>

                    <div class="mb-3 p-2" style="background:#f8fafc; border:1px solid #e2e8f0;">
                        <small class="text-muted d-block">Saat ini:</small>
                        <span class="font-weight-bold">
                            {{ $event->ketuaPelaksana->name ?? 'Belum ditetapkan' }}
                        </span>
                        @if($event->ketuaPelaksana)
                            <small class="text-muted d-block">{{ $event->ketuaPelaksana->username }}</small>
                        @endif
                    </div>

                    @if($canAssignKetua)
                    <form action="{{ route('lomba.assign-ketua', $event->id) }}" method="POST">
                        @csrf
                        <div class="form-group mb-2">
                            <label class="small font-weight-bold">Tetapkan / Ubah Ketua Pelaksana</label>
                            <select name="ketua_pelaksana_user_id" class="form-control form-control-sm">
                                <option value="">— Kosongkan —</option>
                                @foreach($ketuaCandidates as $cand)
                                    <option value="{{ $cand->id }}" {{ $event->ketua_pelaksana_user_id == $cand->id ? 'selected' : '' }}>
                                        {{ $cand->name }} ({{ $cand->username }})
                                    </option>
                                @endforeach
                            </select>
                            @if($ketuaCandidates->isEmpty())
                                <small class="text-danger d-block mt-1">
                                    Belum ada user ber-role "Ketua Pelaksana". Buat dulu di Manajemen User.
                                </small>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-green btn-sm btn-block">
                            <i class="fas fa-save mr-1"></i> Simpan Ketua Pelaksana
                        </button>
                    </form>
                    @else
                    <div class="alert alert-light border mb-0" style="font-size:.82rem;">
                        <i class="fas fa-info-circle mr-1"></i>
                        Hanya Penanggung Jawab / Admin yang dapat mengubah Ketua Pelaksana.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- PANITIA --}}
        <div class="col-lg-7 mb-3">
            <div class="card border-0 h-100" style="border:1px solid #e2e8f0 !important;">
                <div class="card-header bg-white" style="border-bottom:2px solid var(--primary-green);">
                    <h6 class="card-title font-weight-bold mb-0" style="font-size:.92rem;">
                        <i class="fas fa-users mr-1" style="color:var(--primary-green);"></i> Kepanitiaan
                        <span class="badge badge-secondary ml-1">{{ $event->committees->count() }}</span>
                    </h6>
                </div>
                <div class="card-body">

                    {{-- Form tambah panitia --}}
                    <form action="{{ route('lomba.panitia.store', $event->id) }}" method="POST" class="mb-3 p-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                        @csrf
                        <div class="section-head">Tambah Panitia</div>
                        <div class="form-row">
                            <div class="col-md-5 mb-2">
                                <label class="small mb-1">Jabatan</label>
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
                                <label class="small mb-1">NPM Mahasiswa</label>
                                <input type="text" name="npm" class="form-control form-control-sm" placeholder="Opsional">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small mb-1">Nama (manual)</label>
                                <input type="text" name="nama" class="form-control form-control-sm" placeholder="Jika tanpa NPM">
                            </div>
                        </div>
                        <small class="text-muted d-block mb-2">
                            <i class="fas fa-info-circle mr-1"></i> Isi <b>NPM</b> untuk menautkan mahasiswa terdaftar, atau isi <b>Nama</b> manual.
                        </small>
                        <button type="submit" class="btn btn-green btn-sm">
                            <i class="fas fa-user-plus mr-1"></i> Tambah Panitia
                        </button>
                    </form>

                    {{-- Daftar panitia --}}
                    <div class="table-responsive">
                        <table class="table table-flat mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Jabatan</th>
                                    <th>Nama</th>
                                    <th style="width:120px;">NPM</th>
                                    <th class="text-center" style="width:70px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($event->committees as $c)
                                <tr>
                                    <td class="text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="jabatan-badge {{ $c->is_custom ? 'custom' : '' }}">{{ $c->jabatan }}</span>
                                    </td>
                                    <td class="font-weight-bold" style="font-size:.85rem;">{{ $c->nama ?? '—' }}</td>
                                    <td class="text-muted" style="font-size:.82rem;">{{ $c->npm ?? '—' }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('lomba.panitia.destroy', [$event->id, $c->id]) }}" method="POST"
                                              onsubmit="return confirm('Hapus panitia ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-del"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada panitia.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleCustomJabatan() {
        var sel = document.getElementById('jabatanSelect');
        var inp = document.getElementById('jabatanCustom');
        inp.style.display = (sel.value === '__custom__') ? 'block' : 'none';
    }
</script>
@endsection
