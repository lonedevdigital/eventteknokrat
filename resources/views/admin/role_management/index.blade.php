@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Manajemen Role')

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
    .btn-grey-flat    { background-color: #95a5a6; cursor: default; }

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

    .badge-sistem {
        background: #6c757d;
        color: #fff;
        font-size: 10px;
        padding: 2px 8px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .badge-custom {
        background-color: #e8f5e9;
        color: var(--primary-green);
        border: 1px solid var(--primary-green);
        font-size: 10px;
        padding: 2px 8px;
        font-weight: 600;
    }
    .perm-chip {
        display: inline-block;
        background: #d4edda;
        color: #155724;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 3px;
        margin: 1px;
        font-weight: 500;
    }
    .perm-chip.empty {
        background: #f8f9fa;
        color: #adb5bd;
    }
</style>

<div class="container-fluid">

    @if(session('success'))
    <div class="alert fade show mb-3" style="background-color:#2ecc71;color:#fff;border:none;border-radius:0;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff;opacity:1"><span>&times;</span></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible mb-3" style="border-radius:0;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
    </div>
    @endif

    <div class="card border-0">

        <div class="card-header-flat d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold mb-0" style="font-size:1.1rem;">
                <i class="fas fa-shield-alt mr-2"></i> Daftar Role & Permissions
            </h3>
            <a href="{{ route('role-management.create') }}" class="btn btn-white-action btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Role Baru
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-flat mb-0">
                    <thead>
                        <tr>
                            <th class="text-center pl-3" style="width:46px;">No</th>
                            <th style="min-width:180px;">Role</th>
                            <th>Permissions</th>
                            <th class="text-center" style="min-width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td class="text-center pl-3 text-muted">{{ $loop->iteration }}</td>

                            <td>
                                <div class="font-weight-bold" style="font-size:.93rem;">{{ $role->label }}</div>
                                <code class="text-muted" style="font-size:11px;">{{ $role->name }}</code>
                                <span class="ml-1 {{ $role->is_system ? 'badge-sistem' : 'badge-custom' }}">
                                    {{ $role->is_system ? 'Sistem' : 'Custom' }}
                                </span>
                            </td>

                            <td>
                                @php $perms = $role->permissions ?? []; @endphp
                                @if(count($perms) === 0)
                                    <span class="perm-chip empty"><em>Tidak ada permission</em></span>
                                @else
                                    @foreach($perms as $perm)
                                        <span class="perm-chip">{{ $allPermissions[$perm] ?? $perm }}</span>
                                    @endforeach
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap:5px;">
                                    <a href="{{ route('role-management.edit', $role->id) }}"
                                       class="btn btn-sm btn-action-flat btn-warning-flat"
                                       title="Edit role ini">
                                        EDIT
                                    </a>
                                    @if(!$role->is_system)
                                        <form action="{{ route('role-management.destroy', $role->id) }}"
                                              method="POST" class="d-inline-block"
                                              onsubmit="return confirm('Hapus role &quot;{{ $role->label }}&quot;? Pastikan tidak ada user yang memakai role ini.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-action-flat btn-danger-flat" title="Hapus role">
                                                HAPUS
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-action-flat btn-grey-flat" disabled title="Role sistem tidak bisa dihapus">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-shield-alt fa-3x mb-3 d-block" style="opacity:.2;color:#27ae60;"></i>
                                Belum ada data role.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    $(function () { $('[data-toggle="tooltip"]').tooltip() });
</script>

@endsection
