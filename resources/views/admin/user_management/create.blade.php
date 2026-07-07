@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Tambah User')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
    }

    .card, .btn, .form-control, .custom-select {
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .card-header-flat {
        background-color: var(--primary-green);
        color: #fff;
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

    .info-box-custom {
        background: #f0fdf4;
        border: 1px solid #c3e6cb;
        border-left: 4px solid var(--primary-green);
        padding: 12px 14px;
        font-size: 13px;
        color: #155724;
    }

    .field-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 16px;
        margin-bottom: 16px;
    }
    .field-section-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #7f8c8d;
        margin-bottom: 12px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 6px;
    }
</style>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0">
            <div class="card-header-flat d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold mb-0" style="font-size:1.05rem;">
                    <i class="fas fa-user-plus mr-2"></i> Form Tambah User
                </h3>
                <a href="{{ route('user-management.index') }}"
                   class="btn btn-sm"
                   style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.4);">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('user-management.store') }}" method="POST">
                @csrf
                <div class="card-body">

                    {{-- Identitas --}}
                    <div class="field-section">
                        <div class="field-section-title"><i class="fas fa-id-card mr-1"></i> Identitas User</div>

                        <div class="form-group mb-3">
                            <label for="name" class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Masukkan nama lengkap"
                                   required>
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="font-weight-bold">Email <span class="text-danger">*</span></label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email"
                                   value="{{ old('email') }}"
                                   placeholder="contoh@email.com"
                                   required>
                            @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Role & Tipe --}}
                    <div class="field-section">
                        <div class="field-section-title"><i class="fas fa-shield-alt mr-1"></i> Role & Tipe</div>

                        <div class="form-group mb-3">
                            <label for="role" class="font-weight-bold">Role <span class="text-danger">*</span></label>
                            <select class="form-control @error('role') is-invalid @enderror"
                                    id="role" name="role"
                                    required onchange="toggleFields()">
                                <option value="">— Pilih Role —</option>
                                @foreach($allowedRoles as $key => $label)
                                    <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Tipe PJ (muncul saat role = penanggung_jawab) --}}
                        <div id="pj_fields" style="display:none;">
                            <div class="form-group mb-3">
                                <label for="type" class="font-weight-bold">Tipe Penanggung Jawab <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror"
                                        id="type" name="type"
                                        onchange="toggleTypeFields()">
                                    <option value="">— Pilih Tipe —</option>
                                    <option value="dosen"     {{ old('type') == 'dosen'     ? 'selected' : '' }}>Dosen</option>
                                    <option value="mahasiswa" {{ old('type') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                </select>
                                @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Akun --}}
                    <div class="field-section">
                        <div class="field-section-title"><i class="fas fa-key mr-1"></i> Data Akun</div>

                        <div class="form-group mb-3">
                            <label id="username_label" for="username" class="font-weight-bold">
                                Username <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('username') is-invalid @enderror"
                                   id="username" name="username"
                                   value="{{ old('username') }}"
                                   required>
                            <small id="username_help" class="form-text text-muted">
                                Username unik untuk login.
                            </small>
                            @error('username')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-0" id="phone_field" style="display:none;">
                            <label for="no_telepon" class="font-weight-bold">Nomor HP / WhatsApp</label>
                            <input type="text"
                                   class="form-control @error('no_telepon') is-invalid @enderror"
                                   id="no_telepon" name="no_telepon"
                                   value="{{ old('no_telepon') }}"
                                   placeholder="08xxxxxxxxxx">
                            @error('no_telepon')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="info-box-custom">
                        <i class="fas fa-info-circle mr-2"></i>
                        Password akan di-generate otomatis — sama dengan <strong>Username / NPM</strong> yang diisi.
                        Password ditampilkan setelah user berhasil disimpan.
                    </div>

                </div>
                <div class="card-footer bg-white">
                    <button type="submit" class="btn btn-green">
                        <i class="fas fa-save mr-1"></i> Simpan User
                    </button>
                    <a href="{{ route('user-management.index') }}" class="btn btn-default ml-2">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0" style="border:1px solid #e2e8f0 !important;">
            <div class="card-header bg-light" style="border-bottom:1px solid #e2e8f0;">
                <h6 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-info-circle mr-1 text-muted"></i> Petunjuk Pengisian
                </h6>
            </div>
            <div class="card-body" style="font-size:13px;">
                <p><strong>Nama Lengkap</strong> — nama yang akan ditampilkan di sistem.</p>
                <p><strong>Email</strong> — email unik, digunakan untuk identifikasi akun.</p>
                <p><strong>Role</strong> — menentukan hak akses user di panel admin.</p>
                <hr class="my-2">
                <p class="mb-1"><strong>Penanggung Jawab — Dosen:</strong><br>
                    Username bebas (misal: NIDN atau nama singkat).</p>
                <p class="mb-1"><strong>Penanggung Jawab — Mahasiswa:</strong><br>
                    Username harus berupa <strong>NPM</strong> yang terdaftar di data mahasiswa.</p>
                <hr class="my-2">
                <p class="mb-0 text-muted">
                    <i class="fas fa-lock mr-1"></i>
                    Password otomatis = Username yang diisi. User bisa mengganti password setelah login.
                </p>
            </div>
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
        document.getElementById('type').value = '';
        document.getElementById('username_label').childNodes[0].textContent = 'Username ';
        document.getElementById('username_help').textContent = 'Username unik untuk login.';
        document.getElementById('phone_field').style.display = 'none';
    }
    toggleTypeFields();
}

function toggleTypeFields() {
    var role = document.getElementById('role').value;
    var type = document.getElementById('type').value;
    var usernameLabel = document.getElementById('username_label');
    var usernameHelp  = document.getElementById('username_help');
    var phoneField    = document.getElementById('phone_field');

    if (role === 'penanggung_jawab') {
        if (type === 'mahasiswa') {
            usernameLabel.childNodes[0].textContent = 'NPM ';
            usernameHelp.textContent = 'Masukkan NPM sebagai Username. Harus terdaftar di data mahasiswa.';
            phoneField.style.display = 'block';
        } else if (type === 'dosen') {
            usernameLabel.childNodes[0].textContent = 'NIDN / Username ';
            usernameHelp.textContent = 'Masukkan NIDN atau Username yang diinginkan.';
            phoneField.style.display = 'none';
        } else {
            usernameLabel.childNodes[0].textContent = 'Username ';
            usernameHelp.textContent = 'Username unik untuk login.';
            phoneField.style.display = 'none';
        }
    } else {
        phoneField.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    toggleFields();
    toggleTypeFields();
});
</script>

@endsection
