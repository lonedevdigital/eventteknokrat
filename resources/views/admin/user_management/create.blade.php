@extends('templateAdminLTE.home')

@section('sub-judul', 'Tambah User')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Tambah User</h3>
            </div>
            <form action="{{ route('user-management.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required onchange="toggleFields()">
                            <option value="">-- Pilih Role --</option>
                            @foreach($allowedRoles as $key => $label)
                                <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
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
                                <option value="dosen" {{ old('type') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                                <option value="mahasiswa" {{ old('type') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            </select>
                            @error('type')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label id="username_label" for="username">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                        <small id="username_help" class="form-text text-muted">Username unik untuk login.</small>
                        @error('username')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group" id="phone_field" style="display: none;">
                        <label for="no_telepon">Nomor HP / WhatsApp</label>
                        <input type="text" class="form-control @error('no_telepon') is-invalid @enderror" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}">
                        @error('no_telepon')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Password akan di-generate otomatis oleh sistem dan ditampilkan setelah disimpan.
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
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
        
        // Check if role is Penanggung Jawab
        // Assuming the value for Penanggung Jawab is 'penanggung_jawab' based on previous context
        if (role === 'penanggung_jawab') {
            pjFields.style.display = 'block';
        } else {
            pjFields.style.display = 'none';
            // Reset fields
            document.getElementById('type').value = "";
            document.getElementById('username_label').innerText = 'Username';
            document.getElementById('username_help').innerText = 'Username unik untuk login.';
            document.getElementById('phone_field').style.display = 'none';
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
                phoneField.style.display = 'none'; // Phone hidden for Dosen as per request
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

    // Run on load in case of validation errors returning old input
    document.addEventListener('DOMContentLoaded', function() {
        toggleFields();
        toggleTypeFields();
    });
</script>
@endsection
