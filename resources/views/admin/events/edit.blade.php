@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Edit Event')

@section('content')

    <style>
        /* --- THEME VARIABLES & RESET --- */
        :root {
            --primary-green: #27ae60;
            --primary-green-dark: #219150;
            --text-grey: #2c3e50;
            --border-color: #dee2e6;
        }

        /* Global Flat Reset */
        .card, .btn, .form-control, .input-group-text, .custom-file-label {
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        /* --- HEADER STYLE --- */
        .card-header-flat {
            background-color: var(--primary-green);
            color: #ffffff;
            padding: 15px 25px;
            border-bottom: none;
        }

        /* --- FORM STYLING --- */
        /* Ubah warna border saat input diklik (Focus) jadi Hijau */
        .form-control:focus, .custom-select:focus {
            border-color: var(--primary-green);
            box-shadow: none;
        }

        /* Label Form */
        label {
            font-weight: 500;
            color: var(--text-grey);
            font-size: 0.9rem;
        }

        /* Section Title (Garis Bawah Hijau) */
        .section-title {
            color: var(--primary-green);
            font-weight: 700;
            font-size: 1rem;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
            margin-bottom: 20px;
            margin-top: 10px;
            display: flex;
            align-items: center;
        }
        .section-title i {
            margin-right: 10px;
            background: #e8f5e9;
            padding: 8px;
            border-radius: 0;
            font-size: 0.9rem;
        }

        /* --- BUTTONS --- */
        .btn-green {
            background-color: var(--primary-green);
            color: #fff;
            border: none;
            padding: 10px 25px;
            font-weight: 600;
        }
        .btn-green:hover {
            background-color: var(--primary-green-dark);
            color: #fff;
        }

        .btn-cancel {
            background-color: #95a5a6;
            color: #fff;
            padding: 10px 25px;
        }
        .btn-cancel:hover {
            background-color: #7f8c8d;
            color: #fff;
        }

        /* Custom File Input Override */
        .custom-file-input:focus ~ .custom-file-label {
            border-color: var(--primary-green);
            box-shadow: none;
        }
    </style>

    <div class="container-fluid">
        {{--
           MODIFIKASI LEBAR:
           Menggunakan 'col-12' agar form mengambil lebar penuh (Full Width)
           sama persis seperti halaman index/tabel referensi Anda.
        --}}
        <div class="row">
            <div class="col-12">

                <div class="card border-0">

                    {{-- HEADER HIJAU FLAT --}}
                    <div class="card-header-flat">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-edit mr-2"></i> Edit Data Event
                        </h3>
                    </div>

                    {{-- FORM START --}}
                    <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-body p-4">

                            {{-- SECTION 1: INFORMASI UMUM --}}
                            <div class="section-title">
                                <i class="fas fa-info"></i> Informasi Dasar
                            </div>

                            <div class="row">
                                {{-- Nama Event --}}
                                <div class="col-12 mb-3">
                                    <label>Nama Event <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_event"
                                           class="form-control form-control-lg @error('nama_event') is-invalid @enderror"
                                           value="{{ old('nama_event', $event->nama_event) }}"
                                           placeholder="Contoh: Seminar Teknologi 2025"
                                           required>
                                    @error('nama_event')
                                    <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Kategori --}}
                                <div class="col-md-6 mb-3">
                                    <label>Kategori Event <span class="text-danger">*</span></label>
                                    <select name="event_category_id" class="form-control custom-select @error('event_category_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ old('event_category_id', $event->event_category_id) == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('event_category_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Slug --}}
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Slug (Otomatis)</label>
                                    <input type="text" class="form-control bg-light" value="{{ $event->slug }}" readonly>
                                </div>
                            </div>

                            {{-- SECTION 2: MEDIA (THUMBNAIL) --}}
                            <div class="bg-light p-3 border mb-4 mt-2">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        {{-- Preview Gambar --}}
                                        @if($event->thumbnail)
                                            <img src="{{ Str::startsWith($event->thumbnail, ['http']) ? $event->thumbnail : asset($event->thumbnail) }}"
                                                 alt="Current Thumbnail"
                                                 class="d-block border"
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                        @else
                                            <div style="width: 80px; height: 80px; background: #ddd; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <label class="font-weight-bold mb-1">Thumbnail Event</label>
                                        <div class="custom-file">
                                            <input type="file" name="thumbnail_file" class="custom-file-input @error('thumbnail_file') is-invalid @enderror" id="customFile">
                                            <label class="custom-file-label" for="customFile">Pilih gambar baru...</label>
                                        </div>
                                        <small class="text-muted">Format: JPG, PNG. Kosongkan jika tidak ingin mengubah.</small>
                                        @error('thumbnail_file')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 3: JADWAL & LOKASI --}}
                            <div class="section-title mt-4">
                                <i class="far fa-calendar-alt"></i> Waktu & Tempat
                            </div>

                            <div class="row">
                                {{-- Tempat --}}
                                <div class="col-md-12 mb-3">
                                    <label>Lokasi / Tempat <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                        </div>
                                        <input type="text" name="tempat_pelaksanaan" class="form-control border-left-0"
                                               value="{{ old('tempat_pelaksanaan', $event->tempat_pelaksanaan) }}"
                                               required>
                                    </div>
                                </div>

                                {{-- Tanggal Pelaksanaan --}}
                                <div class="col-md-4 mb-3">
                                    <label>Tanggal Event <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_pelaksanaan" class="form-control"
                                           value="{{ old('tanggal_pelaksanaan', $event->tanggal_pelaksanaan) }}">
                                </div>

                                {{-- Jam --}}
                                <div class="col-md-4 mb-3">
                                    <label>Jam Mulai</label>
                                    <input type="text" name="waktu_pelaksanaan" class="form-control"
                                           placeholder="HH:MM"
                                           value="{{ old('waktu_pelaksanaan', $event->waktu_pelaksanaan) }}">
                                </div>

                                {{-- Batas Daftar --}}
                                <div class="col-md-4 mb-3">
                                    <label>Batas Pendaftaran</label>
                                    <input type="date" name="tanggal_pendaftaran" class="form-control"
                                           value="{{ old('tanggal_pendaftaran', $event->tanggal_pendaftaran) }}">
                                    <small class="text-muted">Close Registration</small>
                                </div>
                            </div>

                            {{-- SECTION 4: DETAIL LENGKAP --}}
                            <div class="section-title mt-4">
                                <i class="fas fa-align-left"></i> Detail Lengkap
                            </div>

                            <div class="form-group mb-3">
                                <label>Deskripsi Event</label>
                                <textarea name="deskripsi" class="form-control" rows="5" placeholder="Jelaskan detail event di sini...">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label>Informasi Tambahan (Opsional)</label>
                                <textarea name="informasi_lainnya" class="form-control bg-light" rows="3" placeholder="Catatan tambahan untuk peserta...">{{ old('informasi_lainnya', $event->informasi_lainnya) }}</textarea>
                            </div>

                        </div>

                        {{-- FOOTER ACTION --}}
                        <div class="card-footer bg-white border-top text-right p-3">
                            <a href="{{ route('events.index') }}" class="btn btn-cancel mr-2">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-green px-4">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>
                    {{-- FORM END --}}

                </div>
            </div>
        </div>
    </div>

    {{-- Script Custom File Input --}}
    <script>
        document.querySelector('.custom-file-input').addEventListener('change', function(e){
            var fileName = document.getElementById("customFile").files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        })
    </script>

@endsection
