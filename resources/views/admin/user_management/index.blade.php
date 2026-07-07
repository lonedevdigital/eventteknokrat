@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Manajemen User')

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

    .card, .btn, .form-control, .input-group-text,
    .alert, .badge, .custom-select,
    .pagination .page-item .page-link {
        border-radius: 0 !important;
        box-shadow: none !important;
        border-color: #dee2e6;
    }

    .card-header-flat {
        background-color: var(--primary-green);
        color: #ffffff;
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

    .badge-role {
        background-color: #e8f5e9;
        color: var(--primary-green);
        border: 1px solid var(--primary-green);
        font-weight: 600;
        font-size: 0.75rem;
    }
</style>

<div class="container-fluid">

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert fade show mb-3" style="background-color:#2ecc71;color:#fff;border:none;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff;opacity:1"><span>&times;</span></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible mb-3">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('error') }}
    </div>
    @endif

    @if(session('generated_password'))
    <div class="alert alert-warning alert-dismissible mb-3">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h6 class="font-weight-bold mb-1"><i class="fas fa-key mr-1"></i> Password Otomatis</h6>
        <p class="mb-1">Password user diset sesuai Username/NPM:</p>
        <h4 class="text-center font-weight-bold">{{ session('generated_password') }}</h4>
        <p class="mb-0 small">Gunakan Username/NPM tersebut untuk login.</p>
    </div>
    @endif

    <div class="card border-0">

        {{-- Header Hijau --}}
        <div class="card-header-flat d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold mb-0" style="font-size:1.1rem;">
                <i class="fas fa-users mr-2"></i> Daftar User
            </h3>
            <a href="{{ route('user-management.create') }}" class="btn btn-white-action btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah User
            </a>
        </div>

        <div class="card-body p-0">

            {{-- Filter Row --}}
            <div class="p-3" style="background-color:#f8fafc; border-bottom:1px solid #e2e8f0;">
                <form method="GET" action="{{ route('user-management.index') }}">
                    <div class="form-row align-items-center">

                        {{-- Search --}}
                        <div class="col-12 col-lg-4 mb-2 mb-lg-0">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white text-muted border-right-0">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" name="search"
                                       class="form-control border-left-0"
                                       placeholder="Cari nama, username, atau email..."
                                       value="{{ $search ?? '' }}">
                            </div>
                        </div>

                        {{-- Role Filter --}}
                        @if(count($allowedRoles) > 1)
                        <div class="col-12 col-lg-3 mb-2 mb-lg-0">
                            <select name="role" class="form-control form-control-sm custom-select">
                                <option value="">— Semua Role —</option>
                                @foreach($allowedRoles as $roleKey => $roleLabel)
                                    <option value="{{ $roleKey }}" {{ ($filterRole ?? '') === $roleKey ? 'selected' : '' }}>
                                        {{ $roleLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        {{-- Tombol Filter & Reset --}}
                        <div class="col-12 col-lg-2 mb-2 mb-lg-0">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-green btn-sm" style="width:75%;">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                                <a href="{{ route('user-management.index') }}"
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
                            <th class="text-center pl-3" style="width:50px;">No</th>
                            <th style="min-width:220px;">Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-center" style="min-width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="text-center pl-3 text-muted">{{ $loop->iteration }}</td>

                            <td>
                                <span class="font-weight-bold d-block" style="font-size:.93rem;">{{ $user->name }}</span>
                                @if($user->no_telepon)
                                <small class="text-muted"><i class="fas fa-phone mr-1"></i>{{ $user->no_telepon }}</small>
                                @endif
                            </td>

                            <td>
                                <span class="text-monospace" style="font-size:.85rem;">{{ $user->username }}</span>
                            </td>

                            <td>
                                <span style="font-size:.85rem;">{{ $user->email }}</span>
                            </td>

                            <td>
                                <span class="badge badge-role px-2 py-1">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap:5px;">
                                    <a href="{{ route('user-management.edit', $user->id) }}"
                                       class="btn btn-sm btn-action-flat btn-warning-flat"
                                       title="Edit user">
                                        EDIT
                                    </a>
                                    <form action="{{ route('user-management.destroy', $user->id) }}"
                                          method="POST" class="d-inline-block"
                                          onsubmit="return confirm('Hapus user &quot;{{ $user->name }}&quot;?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-action-flat btn-danger-flat"
                                                title="Hapus user"
                                                {{ $user->id == auth()->id() ? 'disabled' : '' }}>
                                            HAPUS
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-user-slash fa-3x mb-3 opacity-25" style="color:#27ae60;"></i><br>
                                @if($search || $filterRole)
                                    Tidak ada user yang sesuai filter.
                                    <a href="{{ route('user-management.index') }}" class="d-block mt-2 small">Reset filter</a>
                                @else
                                    Belum ada data user.
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

<script>
    $(function () { $('[data-toggle="tooltip"]').tooltip() });
</script>

@endsection
