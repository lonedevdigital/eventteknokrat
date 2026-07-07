@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Kategori Event')

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
                <i class="fas fa-list-ul mr-2"></i> Daftar Kategori Event
            </h3>
            <a href="{{ route('event-categories.create') }}" class="btn btn-white-action btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Kategori
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-flat mb-0">
                    <thead>
                        <tr>
                            <th class="text-center pl-3" style="width:50px;">No</th>
                            <th>Nama Kategori</th>
                            <th class="text-center" style="width:150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td class="text-center pl-3 text-muted">{{ $loop->iteration }}</td>

                            <td class="font-weight-bold">{{ $cat->nama_kategori }}</td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap:5px;">
                                    <a href="{{ route('event-categories.edit', $cat->id) }}"
                                       class="btn btn-sm btn-action-flat btn-warning-flat"
                                       title="Edit kategori">
                                        EDIT
                                    </a>
                                    <form action="{{ route('event-categories.destroy', $cat->id) }}"
                                          method="POST" class="d-inline-block"
                                          onsubmit="return confirm('Hapus kategori &quot;{{ $cat->nama_kategori }}&quot;?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-action-flat btn-danger-flat"
                                                title="Hapus kategori">
                                            HAPUS
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="fas fa-list-ul fa-3x mb-3 d-block" style="opacity:.2; color:#27ae60;"></i>
                                Belum ada data kategori.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($categories, 'links') && $categories->hasPages())
        <div class="card-footer bg-white border-top p-3">
            <div class="d-flex justify-content-end">{{ $categories->links() }}</div>
        </div>
        @endif

    </div>
</div>

<script>
    $(function () { $('[data-toggle="tooltip"]').tooltip() });
</script>

@endsection
