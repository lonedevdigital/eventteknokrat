@extends('templateAdminLTE.home')

@section('sub-judul', 'Manajemen User')
@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('generated_password'))
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-check"></i> User Berhasil Ditambahkan/Diperbarui</h5>
            <p>Password untuk pengguna ini telah diset sesuai dengan Username/NPM:</p>
            <h3 class="text-center">{{ session('generated_password') }}</h3>
            <p class="mb-0">Silakan gunakan Username/NPM tersebut untuk login.</p>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar User</h3>
                <div class="card-tools">
                    <a href="{{ route('user-management.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah User
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('user-management.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('user-management.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" {{ $user->id == auth()->id() ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data user found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
