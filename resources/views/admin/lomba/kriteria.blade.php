@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Kelola Kriteria Penilaian')

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
    .table-flat thead th {
        background-color: var(--bg-light); color: #7f8c8d; border-bottom: 2px solid #bdc3c7;
        text-transform: uppercase; font-size: 0.62rem; letter-spacing: .5px; vertical-align: middle; padding: 9px 12px;
    }
    .table-flat tbody td { vertical-align: middle !important; color: var(--text-grey); padding: 9px 12px; font-size: 0.775rem; }
    .btn-del { background: var(--accent-red); color:#fff; border:none; padding:4px 9px; font-size: 0.62rem; }
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

    <div class="card border-0 mb-3">
        <div class="card-header-flat d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold mb-0" style="font-size: 0.95rem;">
                <i class="fas fa-star mr-2"></i> Kriteria Penilaian - {{ $event->nama_event }}
            </h3>
            <a href="{{ route('lomba.manage', $event->slug) }}" class="btn btn-sm"
               style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Kelola Lomba
            </a>
        </div>
        <div class="card-body">
            <button class="btn btn-green mb-3" data-toggle="modal" data-target="#addKriteriaModal">
                <i class="fas fa-plus mr-1"></i> Tambah Kriteria
            </button>
            <div class="table-responsive">
                <table class="table table-flat mb-0">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            <th>Bobot (%)</th>
                            <th>Cabang Spesifik</th>
                            <th class="text-center" style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($event->assessmentCriteria as $crit)
                        <tr>
                            <td class="font-weight-bold">{{ $crit->nama_kriteria }}</td>
                            <td>{{ $crit->bobot }}%</td>
                            <td>{{ $crit->branch ? $crit->branch->nama_cabang : 'Semua Cabang' }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light border" data-toggle="modal" data-target="#editKriteriaModal{{ $crit->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('lomba.kriteria.destroy', [$event->slug, $crit->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kriteria?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-del"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editKriteriaModal{{ $crit->id }}">
                            <div class="modal-dialog">
                                <form action="{{ route('lomba.kriteria.update', [$event->slug, $crit->id]) }}" method="POST" class="modal-content border-0 rounded-0">
                                    @csrf @method('PUT')
                                    <div class="modal-header card-header-flat">
                                        <h5 class="modal-title">Edit Kriteria Penilaian</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body text-left">
                                        <div class="form-group">
                                            <label>Nama Kriteria</label>
                                            <input type="text" name="nama_kriteria" class="form-control" value="{{ $crit->nama_kriteria }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Bobot Nilai (%)</label>
                                            <input type="number" name="bobot" class="form-control" min="1" max="100" value="{{ $crit->bobot }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Spesifik untuk Cabang (Opsional)</label>
                                            <select name="branch_id" class="form-control">
                                                <option value="">-- Berlaku untuk Semua Cabang --</option>
                                                @foreach($event->branches as $b)
                                                    <option value="{{ $b->id }}" {{ $crit->branch_id == $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-green">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kriteria yang ditambahkan.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kriteria -->
<div class="modal fade" id="addKriteriaModal">
    <div class="modal-dialog">
        <form action="{{ route('lomba.kriteria.store', $event->slug) }}" method="POST" class="modal-content border-0 rounded-0">
            @csrf
            <div class="modal-header card-header-flat">
                <h5 class="modal-title">Tambah Kriteria Penilaian</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Bobot Nilai (%)</label>
                    <input type="number" name="bobot" class="form-control" min="1" max="100" required>
                </div>
                <div class="form-group">
                    <label>Spesifik untuk Cabang (Opsional)</label>
                    <select name="branch_id" class="form-control">
                        <option value="">-- Berlaku untuk Semua Cabang --</option>
                        @foreach($event->branches as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-green">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection
