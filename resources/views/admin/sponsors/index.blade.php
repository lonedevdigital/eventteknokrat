@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Sponsor')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
        --accent-yellow: #f1c40f;
        --accent-red: #e74c3c;
        --bg-light: #ecf0f1;
        --text-grey: #2c3e50;
    }

    .card, .btn, .form-control, .badge {
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

    .btn-white-action {
        background-color: #fff;
        color: var(--primary-green);
        font-weight: 600;
        border: 1px solid #fff;
    }
    .btn-white-action:hover { background-color: #f0fdf4; color: var(--primary-green-dark); }

    .btn-action-flat {
        border: none;
        color: #fff;
        padding: 5px 12px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-action-flat:hover { color: #fff; opacity: 0.88; }
    .btn-warning-flat { background-color: var(--accent-yellow); }
    .btn-danger-flat  { background-color: var(--accent-red); }

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

    .badge-aktif {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        font-size: 11px;
        padding: 3px 8px;
        font-weight: 600;
    }
    .badge-nonaktif {
        background-color: #f8f9fa;
        color: #6c757d;
        border: 1px solid #dee2e6;
        font-size: 11px;
        padding: 3px 8px;
    }

    .logo-box {
        height: 44px;
        display: flex;
        align-items: center;
    }
    .logo-box img {
        height: 44px;
        width: auto;
        max-width: 130px;
        object-fit: contain;
        border: 1px solid #dee2e6;
        padding: 3px;
        background: #fff;
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
                <i class="fas fa-handshake mr-2"></i> Daftar Sponsor & Partner
            </h3>
            <a href="{{ route('sponsors.create') }}" class="btn btn-white-action btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Sponsor
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-flat mb-0">
                    <thead>
                        <tr>
                            <th class="text-center pl-3" style="width:50px;">No</th>
                            <th style="width:150px;">Logo</th>
                            <th>Nama</th>
                            <th>Link</th>
                            <th class="text-center" style="width:80px;">Urutan</th>
                            <th class="text-center" style="width:100px;">Status</th>
                            <th class="text-center" style="width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($sponsors as $sponsor)
                        @php
                            $logo = $sponsor->logo_path;
                            if ($logo && !\Illuminate\Support\Str::startsWith($logo, ['http://', 'https://'])) {
                                $logo = asset($logo);
                            }
                        @endphp
                        <tr>
                            <td class="text-center pl-3 text-muted">{{ $sponsors->firstItem() + $loop->index }}</td>

                            <td>
                                <div class="logo-box">
                                    @if($logo)
                                        <img src="{{ $logo }}" alt="{{ $sponsor->nama }}">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center text-muted bg-light"
                                             style="width:80px; height:44px; border:1px solid #dee2e6; font-size:11px;">
                                            <i class="fas fa-image mr-1"></i> No Logo
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="font-weight-bold" style="font-size:.93rem;">{{ $sponsor->nama }}</td>

                            <td style="font-size:.83rem;">
                                @if($sponsor->link_url)
                                    <a href="{{ $sponsor->link_url }}" target="_blank" rel="noopener noreferrer"
                                       class="text-truncate d-block" style="max-width:200px; color:var(--primary-green);">
                                        <i class="fas fa-external-link-alt mr-1" style="font-size:10px;"></i>
                                        {{ \Illuminate\Support\Str::limit($sponsor->link_url, 30) }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="font-weight-bold" style="font-size:1rem;">{{ $sponsor->urutan }}</span>
                            </td>

                            <td class="text-center">
                                @if($sponsor->is_active)
                                    <span class="badge-aktif"><i class="fas fa-circle mr-1" style="font-size:8px;"></i>Aktif</span>
                                @else
                                    <span class="badge-nonaktif">Nonaktif</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap:5px;">
                                    <a href="{{ route('sponsors.edit', $sponsor->id) }}"
                                       class="btn btn-sm btn-action-flat btn-warning-flat"
                                       title="Edit sponsor">
                                        EDIT
                                    </a>
                                    <form action="{{ route('sponsors.destroy', $sponsor->id) }}"
                                          method="POST" class="d-inline-block"
                                          onsubmit="return confirm('Yakin ingin menghapus sponsor ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-action-flat btn-danger-flat"
                                                title="Hapus sponsor">
                                            HAPUS
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-handshake fa-3x mb-3 d-block" style="opacity:.2; color:#27ae60;"></i>
                                Belum ada data sponsor.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($sponsors->hasPages())
        <div class="card-footer bg-white border-top p-3">
            <div class="d-flex justify-content-end">{{ $sponsors->links() }}</div>
        </div>
        @endif

    </div>
</div>

@endsection
