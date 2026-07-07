@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Tambah Event')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
    }
    .card, .btn, .form-control, .custom-select, .custom-file-label {
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
        color: #fff; border: none; font-weight: 600;
    }
    .btn-green:hover { background-color: var(--primary-green-dark); color: #fff; }
    .field-section {
        background: #f8fafc; border: 1px solid #e2e8f0;
        padding: 16px; margin-bottom: 16px;
    }
    .field-section-title {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #7f8c8d; margin-bottom: 12px;
        border-bottom: 1px solid #e2e8f0; padding-bottom: 6px;
    }
    .custom-file-input:focus ~ .custom-file-label { border-color: var(--primary-green); }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card border-0">
                <div class="card-header-flat d-flex align-items-center justify-content-between">
                    <h3 class="card-title font-weight-bold mb-0" style="font-size:1.05rem;">
                        <i class="fas fa-calendar-alt mr-2"></i> Form Tambah Event
                    </h3>
                    <a href="{{ route('events.create') }}" class="btn btn-sm"
                       style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);">
                        <i class="fas fa-arrow-left mr-1"></i> Ganti Jenis
                    </a>
                </div>

                <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="event">

                    <div class="card-body">

                        {{-- INFORMASI DASAR --}}
                        <div class="field-section">
                            <div class="field-section-title"><i class="fas fa-info-circle mr-1"></i> Informasi Dasar</div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Nama Event <span class="text-danger">*</span></label>
                                <input type="text" name="nama_event"
                                       class="form-control @error('nama_event') is-invalid @enderror"
                                       value="{{ old('nama_event') }}"
                                       placeholder="Contoh: Seminar Teknologi Masa Depan" required>
                                @error('nama_event')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">Kategori <span class="text-danger">*</span></label>
                                        <select name="event_category_id" class="form-control @error('event_category_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ old('event_category_id') == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->nama_kategori }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('event_category_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">Thumbnail (Gambar) <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file" name="thumbnail_file" class="custom-file-input @error('thumbnail_file') is-invalid @enderror" id="customFile">
                                            <label class="custom-file-label" for="customFile">Pilih file...</label>
                                        </div>
                                        <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 2MB.</small>
                                        @error('thumbnail_file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- WAKTU & TEMPAT --}}
                        <div class="field-section">
                            <div class="field-section-title"><i class="far fa-clock mr-1"></i> Waktu & Tempat</div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">Tempat Pelaksanaan <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            </div>
                                            <input type="text" name="tempat_pelaksanaan" class="form-control"
                                                   value="{{ old('tempat_pelaksanaan') }}" placeholder="Contoh: Aula Gedung A / Zoom Meeting" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">Jam Pelaksanaan</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                                            </div>
                                            <input type="text" name="waktu_pelaksanaan" class="form-control"
                                                   placeholder="Format: 08:00" value="{{ old('waktu_pelaksanaan') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Batas Pendaftaran</label>
                                        <input type="date" name="tanggal_pendaftaran" class="form-control" value="{{ old('tanggal_pendaftaran') }}">
                                        <small class="text-muted">Kosongkan jika tanpa batas pendaftaran.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Tanggal Event <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_pelaksanaan" class="form-control" value="{{ old('tanggal_pelaksanaan') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- DETAIL --}}
                        <div class="field-section">
                            <div class="field-section-title"><i class="fas fa-align-left mr-1"></i> Detail Lengkap</div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Deskripsi Event</label>
                                <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan detail event di sini...">{{ old('deskripsi') }}</textarea>
                            </div>
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Informasi Lainnya (Opsional)</label>
                                <textarea name="informasi_lainnya" class="form-control" rows="3" placeholder="Info tambahan seperti link grup WA, persyaratan khusus, dll...">{{ old('informasi_lainnya') }}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer bg-white text-right">
                        <a href="{{ route('events.index') }}" class="btn btn-default mr-2">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-green px-4">
                            <i class="fas fa-save mr-1"></i> Simpan Event
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    document.querySelector('.custom-file-input').addEventListener('change', function(e){
        var fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file...';
        e.target.nextElementSibling.innerText = fileName;
    });
</script>
@endsection
