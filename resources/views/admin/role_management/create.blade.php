@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Tambah Role Baru')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
    }

    .card, .btn, .form-control {
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

    .perm-group-card {
        border: 1px solid #dee2e6;
        margin-bottom: 14px;
        overflow: hidden;
    }
    .perm-group-header {
        background: #f8f9fa;
        padding: 10px 14px;
        font-weight: 700;
        font-size: 13px;
        color: #343a40;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .perm-group-body {
        padding: 12px 14px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 10px;
    }
    .perm-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        border: 1px solid #dee2e6;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
        user-select: none;
    }
    .perm-item:hover { background: #f0fdf4; border-color: var(--primary-green); }
    .perm-item input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: var(--primary-green);
        flex-shrink: 0;
    }
    .perm-item.checked {
        background: #e8f5e9;
        border-color: var(--primary-green);
    }
    .perm-item label {
        margin: 0;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        color: #343a40;
    }
    .group-check-all {
        margin-left: auto;
        font-size: 11px;
        color: var(--primary-green);
        cursor: pointer;
        font-weight: 600;
    }
    .group-check-all:hover { text-decoration: underline; }
</style>

<div class="row">
    <div class="col-lg-9">
        <div class="card border-0">
            <div class="card-header-flat d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold mb-0" style="font-size:1.05rem;">
                    <i class="fas fa-plus-circle mr-2"></i>Form Tambah Role
                </h3>
                <a href="{{ route('role-management.index') }}" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.4);">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
            <form action="{{ route('role-management.store') }}" method="POST">
                @csrf
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">Slug / Kode Role <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name"
                                       value="{{ old('name') }}"
                                       placeholder="contoh: operator_lab"
                                       pattern="[a-z][a-z0-9_]*"
                                       required>
                                <small class="form-text text-muted">Huruf kecil, angka, dan underscore. Tidak bisa diubah setelah disimpan.</small>
                                @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <label for="label" class="font-weight-bold">Nama Tampil <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('label') is-invalid @enderror"
                                       id="label" name="label"
                                       value="{{ old('label') }}"
                                       placeholder="contoh: Operator Lab"
                                       required>
                                @error('label')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="font-weight-bold mb-3">
                        <i class="fas fa-check-square mr-2" style="color:var(--primary-green);"></i>Permissions / Hak Akses
                    </h6>

                    @foreach($permissionGroups as $groupName => $permissions)
                    <div class="perm-group-card">
                        <div class="perm-group-header">
                            <i class="fas fa-layer-group text-secondary"></i>
                            {{ $groupName }}
                            <span class="group-check-all" data-group="{{ $loop->index }}">Pilih Semua</span>
                        </div>
                        <div class="perm-group-body">
                            @foreach($permissions as $key => $permLabel)
                            <label class="perm-item {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}"
                                   data-group="{{ $loop->parent->index }}">
                                <input type="checkbox"
                                       name="permissions[]"
                                       value="{{ $key }}"
                                       {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
                                <span>{{ $permLabel }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @error('permissions')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror

                </div>
                <div class="card-footer bg-white">
                    <button type="submit" class="btn btn-green">
                        <i class="fas fa-save mr-1"></i> Simpan Role
                    </button>
                    <a href="{{ route('role-management.index') }}" class="btn btn-default ml-2">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card border-0" style="border:1px solid #e2e8f0 !important;">
            <div class="card-header bg-light" style="border-bottom:1px solid #e2e8f0;">
                <h6 class="card-title font-weight-bold mb-0"><i class="fas fa-info-circle mr-1 text-muted"></i> Petunjuk</h6>
            </div>
            <div class="card-body" style="font-size:13px;">
                <p><strong>Slug</strong> adalah kode unik role yang digunakan sistem, misalnya <code>operator_lab</code>.</p>
                <p><strong>Nama Tampil</strong> adalah nama yang terlihat di UI, misalnya <em>Operator Lab</em>.</p>
                <p><strong>Permissions</strong> menentukan modul apa yang bisa diakses oleh role ini.</p>
                <p class="mb-0 text-muted">Role custom dapat diedit dan dihapus kapan saja.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.perm-item input[type="checkbox"]').forEach(function (cb) {
        cb.addEventListener('change', function () {
            this.closest('.perm-item').classList.toggle('checked', this.checked);
        });
    });

    document.querySelectorAll('.group-check-all').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var groupIdx = this.dataset.group;
            var items = document.querySelectorAll('.perm-item[data-group="' + groupIdx + '"]');
            var allChecked = Array.from(items).every(function (item) {
                return item.querySelector('input').checked;
            });
            items.forEach(function (item) {
                var cb = item.querySelector('input');
                cb.checked = !allChecked;
                item.classList.toggle('checked', !allChecked);
            });
            btn.textContent = allChecked ? 'Pilih Semua' : 'Hapus Semua';
        });
    });

    var nameInput = document.getElementById('name');
    if (nameInput) {
        nameInput.addEventListener('input', function () {
            this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '_');
        });
    }
});
</script>

@endsection
