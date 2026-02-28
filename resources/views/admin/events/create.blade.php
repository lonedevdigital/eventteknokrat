@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Tambah Event')

@section('content')
    <div class="container-fluid">

        <div class="row justify-content-center">
            <div class="col-md-10">

                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-plus-circle mr-2"></i>Form Tambah Event Baru
                        </h3>
                    </div>

                    {{-- Form Start --}}
                    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="card-body">

                            {{-- SECTION 1: INFORMASI DASAR --}}
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle mr-1"></i> Informasi Dasar
                            </h6>

                            <div class="form-group mb-3">
                                <label class="font-weight-normal">Nama Event <span class="text-danger">*</span></label>
                                <input type="text" name="nama_event"
                                       class="form-control @error('nama_event') is-invalid @enderror"
                                       value="{{ old('nama_event') }}"
                                       placeholder="Contoh: Seminar Teknologi Masa Depan" required>
                                @error('nama_event')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                {{-- Kategori --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-normal">Kategori <span class="text-danger">*</span></label>
                                        <select name="event_category_id" class="form-control @error('event_category_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ old('event_category_id') == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->nama_kategori }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('event_category_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Thumbnail Upload --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-normal">Thumbnail (Gambar) <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file" name="thumbnail_file" class="custom-file-input @error('thumbnail_file') is-invalid @enderror" id="customFile">
                                            <label class="custom-file-label" for="customFile">Pilih file...</label>
                                        </div>
                                        <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 2MB.</small>
                                        @error('thumbnail_file')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 2: WAKTU & TEMPAT --}}
                            <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">
                                <i class="far fa-clock mr-1"></i> Waktu & Tempat
                            </h6>

                            <div class="row">
                                {{-- Tempat --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-normal">Tempat Pelaksanaan <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            </div>
                                            <input type="text" name="tempat_pelaksanaan" class="form-control"
                                                   value="{{ old('tempat_pelaksanaan') }}" placeholder="Contoh: Aula Gedung A / Zoom Meeting" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Waktu (Jam) --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-normal">Jam Pelaksanaan</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-clock"></i></span>
                                            </div>
                                            <input type="text" name="waktu_pelaksanaan" class="form-control"
                                                   placeholder="Format: 08:00 - 12:00" value="{{ old('waktu_pelaksanaan') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Tanggal Pendaftaran --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-normal">Batas Pendaftaran</label>
                                        <input type="date" name="tanggal_pendaftaran" class="form-control" value="{{ old('tanggal_pendaftaran') }}">
                                        <small class="text-muted">Biarkan kosong jika tidak ada batas pendaftaran.</small>
                                    </div>
                                </div>

                                {{-- Tanggal Pelaksanaan --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-normal">Tanggal Event <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_pelaksanaan" class="form-control" value="{{ old('tanggal_pelaksanaan') }}" required>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 3: DETAIL --}}
                            <h6 class="text-primary border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-align-left mr-1"></i> Detail Lengkap
                            </h6>

                            <div class="form-group mb-3">
                                <label class="font-weight-normal">Deskripsi Event</label>
                                <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan detail event di sini...">{{ old('deskripsi') }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-normal">Informasi Lainnya (Opsional)</label>
                                <textarea name="informasi_lainnya" class="form-control" rows="3" placeholder="Info tambahan seperti link grup WA, persyaratan khusus, dll...">{{ old('informasi_lainnya') }}</textarea>
                            </div>

                        </div>

                        <div class="card-footer bg-light text-right">
                            <a href="{{ route('events.index') }}" class="btn btn-secondary mr-2">
                                <i class="fas fa-arrow-left mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save mr-1"></i> Simpan Event
                            </button>
                        </div>

                    </form>
                    {{-- Form End --}}

                </div>
            </div>
        </div>
    </div>

    {{-- Script Kecil agar Nama File muncul saat upload --}}
    <script>
        document.querySelector('.custom-file-input').addEventListener('change', function(e){
            var fileName = document.getElementById("customFile").files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        })
    </script>
@endsection
