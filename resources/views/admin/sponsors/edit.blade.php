@extends('templateAdminLTE.home')

@section('sub-breadcrumb', 'Edit Sponsor')
@section('page-title', 'Edit Sponsor')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Sponsor</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('sponsors.update', $sponsor->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Nama Sponsor</label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $sponsor->nama) }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Link Sponsor (Opsional)</label>
                            <input type="url" name="link_url" class="form-control @error('link_url') is-invalid @enderror" value="{{ old('link_url', $sponsor->link_url) }}" placeholder="https://example.com">
                            @error('link_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @php
                            $logo = $sponsor->logo_path;
                            if ($logo && !\Illuminate\Support\Str::startsWith($logo, ['http://', 'https://'])) {
                                $logo = asset($logo);
                            }
                        @endphp
                        @if($logo)
                            <div class="form-group">
                                <label>Logo Saat Ini</label>
                                <div>
                                    <img src="{{ $logo }}" alt="{{ $sponsor->nama }}" style="max-height:70px; width:auto; object-fit:contain;">
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label>Ganti Logo (Opsional)</label>
                            <div class="custom-file">
                                <input type="file" name="logo_file" class="custom-file-input @error('logo_file') is-invalid @enderror" id="logoFile" accept=".jpg,.jpeg,.png,.webp">
                                <label class="custom-file-label" for="logoFile">Pilih file logo...</label>
                            </div>
                            @error('logo_file')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti logo.</small>
                        </div>

                        <div class="form-group">
                            <label>Urutan Tampil</label>
                            <input type="number" name="urutan" class="form-control @error('urutan') is-invalid @enderror" value="{{ old('urutan', $sponsor->urutan) }}" min="0" max="9999">
                            @error('urutan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input type="hidden" name="is_active" value="0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="isActiveSwitch" name="is_active" value="1" {{ old('is_active', $sponsor->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="isActiveSwitch">Tampilkan di halaman frontend</label>
                            </div>
                        </div>

                        <button class="btn btn-primary">
                            <i class="fa fa-save"></i> Update
                        </button>
                        <a href="{{ route('sponsors.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('logoFile');
        if (!input) return;
        input.addEventListener('change', function () {
            const label = document.querySelector('label[for="logoFile"]');
            if (!label) return;
            const fileName = this.files && this.files[0] ? this.files[0].name : 'Pilih file logo...';
            label.textContent = fileName;
        });
    })();
</script>
@endpush

