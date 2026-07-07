@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Tambah Perlombaan')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
        --accent-red: #e74c3c;
    }
    .card, .btn, .form-control, .custom-select, .custom-file-label {
        border-radius: 0 !important;
        box-shadow: none !important;
    }
    .card-header-flat {
        background-color: var(--primary-green);
        color: #fff; padding: 12px 20px; border-bottom: none;
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
    .repeat-row { background:#fff; border:1px solid #e2e8f0; padding:10px; margin-bottom:8px; }
    .btn-remove-row {
        background: var(--accent-red); color:#fff; border:none;
        padding: 6px 10px; font-size:.75rem;
    }
    .btn-add-row {
        background: #fff; color: var(--primary-green);
        border: 1px dashed var(--primary-green); font-weight:600; font-size:.8rem;
    }
    .btn-add-row:hover { background:#f0fdf4; color: var(--primary-green-dark); }
    .pay-toggle .btn { font-weight:600; }
    .custom-file-input:focus ~ .custom-file-label { border-color: var(--primary-green); }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card border-0">
                <div class="card-header-flat d-flex align-items-center justify-content-between">
                    <h3 class="card-title font-weight-bold mb-0" style="font-size:1.05rem;">
                        <i class="fas fa-trophy mr-2"></i> Form Tambah Perlombaan
                    </h3>
                    <a href="{{ route('events.create') }}" class="btn btn-sm"
                       style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);">
                        <i class="fas fa-arrow-left mr-1"></i> Ganti Jenis
                    </a>
                </div>

                <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="lomba">

                    <div class="card-body">

                        @if($errors->any())
                        <div class="alert alert-danger" style="border-radius:0;">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Periksa kembali isian form:
                            <ul class="mb-0 mt-1 pl-3">
                                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- INFORMASI LOMBA --}}
                        <div class="field-section">
                            <div class="field-section-title"><i class="fas fa-info-circle mr-1"></i> Informasi Lomba</div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Judul Lomba <span class="text-danger">*</span></label>
                                <input type="text" name="nama_event" class="form-control"
                                       value="{{ old('nama_event') }}" placeholder="Contoh: Lomba UI/UX Design Nasional 2026" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">Skala Partisipasi <span class="text-danger">*</span></label>
                                        <select name="partisipasi_skala" id="partisipasi_skala" class="form-control" required onchange="toggleProdi()">
                                            <option value="">-- Pilih Skala --</option>
                                            <option value="universitas" {{ old('partisipasi_skala')=='universitas'?'selected':'' }}>Seluruh Mahasiswa Universitas Teknokrat Indonesia</option>
                                            <option value="prodi" {{ old('partisipasi_skala')=='prodi'?'selected':'' }}>Berdasarkan Program Studi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3" id="prodi_wrap" style="display:none;">
                                        <label class="font-weight-bold">Program Studi <span class="text-danger">*</span></label>
                                        <select name="partisipasi_prodi" class="form-control">
                                            <option value="">-- Pilih Program Studi --</option>
                                            @foreach($prodiList as $prodi)
                                                <option value="{{ $prodi }}" {{ old('partisipasi_prodi')==$prodi?'selected':'' }}>{{ $prodi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Flyer Lomba</label>
                                <div class="custom-file">
                                    <input type="file" name="flyer_file" class="custom-file-input" id="flyerFile" accept="image/*">
                                    <label class="custom-file-label" for="flyerFile">Pilih gambar flyer...</label>
                                </div>
                                <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 2MB.</small>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Deskripsi Lomba</label>
                                <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan ketentuan, tema, hadiah, dan detail lomba...">{{ old('deskripsi') }}</textarea>
                            </div>
                        </div>

                        {{-- WAKTU & TEMPAT --}}
                        <div class="field-section">
                            <div class="field-section-title"><i class="far fa-clock mr-1"></i> Waktu & Tempat Pelaksanaan</div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_pelaksanaan" class="form-control" value="{{ old('tanggal_pelaksanaan') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">Jam Pelaksanaan</label>
                                        <input type="text" name="waktu_pelaksanaan" class="form-control" placeholder="Format: 08:00" value="{{ old('waktu_pelaksanaan') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">Batas Pendaftaran</label>
                                        <input type="date" name="tanggal_pendaftaran" class="form-control" value="{{ old('tanggal_pendaftaran') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Tempat Pelaksanaan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <input type="text" name="tempat_pelaksanaan" class="form-control" value="{{ old('tempat_pelaksanaan') }}" placeholder="Contoh: Auditorium UTI / Online" required>
                                </div>
                            </div>
                        </div>

                        {{-- DETAIL & KETENTUAN --}}
                        <div class="field-section">
                            <div class="field-section-title"><i class="fas fa-clipboard-list mr-1"></i> Detail & Ketentuan Lomba</div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold"><i class="fas fa-check-circle text-success mr-1"></i> Syarat &amp; Ketentuan</label>
                                <textarea name="syarat_ketentuan" class="form-control" rows="4"
                                          placeholder="Contoh:&#10;1. Peserta merupakan mahasiswa aktif&#10;2. Setiap tim maksimal 3 orang&#10;3. ...">{{ old('syarat_ketentuan') }}</textarea>
                                <small class="text-muted">Tuliskan syarat &amp; ketentuan peserta. Bisa per baris/poin.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold"><i class="far fa-calendar-check text-primary mr-1"></i> Jadwal &amp; Timeline</label>
                                <textarea name="jadwal_timeline" class="form-control" rows="4"
                                          placeholder="Contoh:&#10;Pendaftaran: 1 - 30 Juni 2026&#10;Babak Penyisihan: 5 Juli 2026&#10;Final: 20 Juli 2026">{{ old('jadwal_timeline') }}</textarea>
                                <small class="text-muted">Rincian tahapan & tanggal penting perlombaan.</small>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold"><i class="fas fa-medal text-warning mr-1"></i> Hadiah &amp; Penghargaan</label>
                                <textarea name="hadiah_penghargaan" class="form-control" rows="4"
                                          placeholder="Contoh:&#10;Juara 1: Rp 5.000.000 + Sertifikat&#10;Juara 2: Rp 3.000.000 + Sertifikat&#10;Juara 3: Rp 1.000.000 + Sertifikat">{{ old('hadiah_penghargaan') }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold"><i class="fas fa-user-plus text-info mr-1"></i> Cara Pendaftaran</label>
                                <textarea name="cara_pendaftaran" class="form-control" rows="4"
                                          placeholder="Contoh:&#10;1. Isi formulir pendaftaran&#10;2. Lakukan pembayaran (jika berbayar)&#10;3. Konfirmasi ke panitia">{{ old('cara_pendaftaran') }}</textarea>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold"><i class="fas fa-file-pdf text-danger mr-1"></i> Juknis (Petunjuk Teknis)</label>
                                <div class="custom-file">
                                    <input type="file" name="juknis_file" class="custom-file-input" id="juknisFile" accept="application/pdf">
                                    <label class="custom-file-label" for="juknisFile">Pilih file PDF juknis...</label>
                                </div>
                                <small class="text-muted">Format: PDF saja. Maks: 5MB.</small>
                            </div>
                        </div>

                        {{-- TIPE LOMBA --}}
                        <div class="field-section">
                            <div class="field-section-title"><i class="fas fa-money-bill-wave mr-1"></i> Tipe Lomba</div>
                            <div class="form-group mb-2">
                                <div class="btn-group btn-group-toggle pay-toggle" data-toggle="buttons">
                                    <label class="btn btn-outline-success {{ old('tipe_bayar','gratis')=='gratis'?'active':'' }}">
                                        <input type="radio" name="tipe_bayar" value="gratis" {{ old('tipe_bayar','gratis')=='gratis'?'checked':'' }} onchange="toggleBayar()"> Gratis
                                    </label>
                                    <label class="btn btn-outline-success {{ old('tipe_bayar')=='berbayar'?'active':'' }}">
                                        <input type="radio" name="tipe_bayar" value="berbayar" {{ old('tipe_bayar')=='berbayar'?'checked':'' }} onchange="toggleBayar()"> Berbayar
                                    </label>
                                </div>
                            </div>

                            {{-- CABANG LOMBA (hanya berbayar) --}}
                            <div id="cabang_section" style="display:none;">
                                <hr>
                                <label class="font-weight-bold mb-2">
                                    <i class="fas fa-sitemap mr-1"></i> Informasi Cabang Lomba
                                    <small class="text-muted font-weight-normal">(nama cabang &amp; harga pendaftaran)</small>
                                </label>
                                <div id="cabang_rows"></div>
                                <button type="button" class="btn btn-add-row btn-sm" onclick="addCabang()">
                                    <i class="fas fa-plus mr-1"></i> Tambah Cabang Lomba
                                </button>
                            </div>
                        </div>

                        {{-- INFORMASI KONTAK --}}
                        <div class="field-section mb-0">
                            <div class="field-section-title"><i class="fas fa-address-book mr-1"></i> Informasi Kontak</div>
                            <p class="text-muted small mb-2">Kontak panitia yang dapat dihubungi (mis. Ketua Pelaksana, Bendahara).</p>
                            <div id="kontak_rows"></div>
                            <button type="button" class="btn btn-add-row btn-sm" onclick="addKontak()">
                                <i class="fas fa-plus mr-1"></i> Tambah Kontak
                            </button>
                        </div>

                    </div>

                    <div class="card-footer bg-white text-right">
                        <a href="{{ route('events.index') }}" class="btn btn-default mr-2">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-green px-4">
                            <i class="fas fa-save mr-1"></i> Simpan Perlombaan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    // Toggle prodi dropdown
    function toggleProdi() {
        var skala = document.getElementById('partisipasi_skala').value;
        document.getElementById('prodi_wrap').style.display = (skala === 'prodi') ? 'block' : 'none';
    }

    // Toggle cabang lomba section
    function toggleBayar() {
        var berbayar = document.querySelector('input[name="tipe_bayar"]:checked');
        var show = berbayar && berbayar.value === 'berbayar';
        document.getElementById('cabang_section').style.display = show ? 'block' : 'none';
        // Pastikan minimal 1 baris cabang saat berbayar
        if (show && document.querySelectorAll('#cabang_rows .repeat-row').length === 0) {
            addCabang();
        }
    }

    // Cabang lomba rows
    function addCabang(nama, harga) {
        var wrap = document.getElementById('cabang_rows');
        var div = document.createElement('div');
        div.className = 'repeat-row';
        div.innerHTML = `
            <div class="form-row align-items-end">
                <div class="col-md-6 mb-2">
                    <label class="small mb-1">Nama Cabang</label>
                    <input type="text" name="cabang_nama[]" class="form-control form-control-sm" placeholder="Contoh: Kategori SMA" value="${nama||''}">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="small mb-1">Harga Pendaftaran (Rp)</label>
                    <input type="number" name="cabang_harga[]" class="form-control form-control-sm" min="0" placeholder="0" value="${harga||''}">
                </div>
                <div class="col-md-2 mb-2 text-right">
                    <button type="button" class="btn btn-remove-row btn-sm btn-block" onclick="this.closest('.repeat-row').remove()">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>`;
        wrap.appendChild(div);
    }

    // Kontak rows
    function addKontak(jabatan, nama, no) {
        var wrap = document.getElementById('kontak_rows');
        var div = document.createElement('div');
        div.className = 'repeat-row';
        div.innerHTML = `
            <div class="form-row align-items-end">
                <div class="col-md-4 mb-2">
                    <label class="small mb-1">Jabatan</label>
                    <input type="text" name="kontak_jabatan[]" class="form-control form-control-sm" placeholder="Contoh: Ketua Pelaksana" value="${jabatan||''}">
                </div>
                <div class="col-md-4 mb-2">
                    <label class="small mb-1">Nama</label>
                    <input type="text" name="kontak_nama[]" class="form-control form-control-sm" placeholder="Nama lengkap" value="${nama||''}">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small mb-1">No. HP / Kontak</label>
                    <input type="text" name="kontak_no[]" class="form-control form-control-sm" placeholder="08xxxx" value="${no||''}">
                </div>
                <div class="col-md-1 mb-2 text-right">
                    <button type="button" class="btn btn-remove-row btn-sm btn-block" onclick="this.closest('.repeat-row').remove()">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>`;
        wrap.appendChild(div);
    }

    // Flyer filename
    document.getElementById('flyerFile').addEventListener('change', function(e){
        e.target.nextElementSibling.innerText = e.target.files[0] ? e.target.files[0].name : 'Pilih gambar flyer...';
    });

    // Juknis filename
    document.getElementById('juknisFile').addEventListener('change', function(e){
        e.target.nextElementSibling.innerText = e.target.files[0] ? e.target.files[0].name : 'Pilih file PDF juknis...';
    });

    // Init
    document.addEventListener('DOMContentLoaded', function () {
        toggleProdi();
        toggleBayar();
        // Default 2 baris kontak
        addKontak('Ketua Pelaksana', '', '');
        addKontak('Bendahara', '', '');
    });
</script>
@endsection
