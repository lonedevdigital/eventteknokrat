@extends('templateAdminLTE.home')

@section('sub-judul', 'Edit User')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Form Edit User</h3>
            </div>
            {{-- Note: we use $targetUser variable passed from controller --}}
            <form action="{{ route('user-management.update', $targetUser->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $targetUser->name) }}" required>
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required onchange="toggleFields()">
                            <option value="">-- Pilih Role --</option>
                            @foreach($allowedRoles as $key => $label)
                                <option value="{{ $key }}" {{ old('role', $targetUser->role) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Dynamic Fields for Penanggung Jawab --}}
                    <div id="pj_fields" style="display: none;">
                        <div class="form-group">
                            <label for="type">Tipe Penanggung Jawab</label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" onchange="toggleTypeFields()">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="dosen" {{ old('type', $targetUser->type) == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="mahasiswa" {{ old('type', $targetUser->type) == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            </select>
                            @error('type')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label id="username_label" for="username">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $targetUser->username) }}" required>
                        <small id="username_help" class="form-text text-muted">Username unik untuk login.</small>
                        @error('username')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" id="phone_field" style="display: none;">
                        <label for="no_telepon">Nomor HP / WhatsApp</label>
                        <input type="text" class="form-control @error('no_telepon') is-invalid @enderror" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $targetUser->no_telepon) }}">
                        @error('no_telepon')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $targetUser->email) }}" required>
                        @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('user-management.index') }}" class="btn btn-default">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleFields() {
        var role = document.getElementById('role').value;
        var pjFields = document.getElementById('pj_fields');
        
        if (role === 'penanggung_jawab') {
            pjFields.style.display = 'block';
        } else {
            pjFields.style.display = 'none';
        }
        toggleTypeFields();
    }

    function toggleTypeFields() {
        var role = document.getElementById('role').value;
        var type = document.getElementById('type').value;
        var usernameLabel = document.getElementById('username_label');
        var usernameHelp = document.getElementById('username_help');
        var phoneField = document.getElementById('phone_field');

        if (role === 'penanggung_jawab') {
            if (type === 'mahasiswa') {
                usernameLabel.innerText = 'NPM';
                usernameHelp.innerText = 'Masukkan NPM sebagai Username.';
                phoneField.style.display = 'block';
            } else if (type === 'dosen') {
                usernameLabel.innerText = 'NIDN / Username';
                usernameHelp.innerText = 'Masukkan NIDN atau Username yang diinginkan.';
                phoneField.style.display = 'none';
            } else {
                usernameLabel.innerText = 'Username';
                usernameHelp.innerText = 'Username unik untuk login.';
                phoneField.style.display = 'none';
            }
        } else {
             // For other roles, default behavior
             phoneField.style.display = 'none';
        }
    }

    // Run on load
    document.addEventListener('DOMContentLoaded', function() {
        toggleFields();
        toggleTypeFields();
    });
</script>
@endsection
