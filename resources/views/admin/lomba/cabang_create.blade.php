@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Tambah Cabang Lomba')

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
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; color: #7f8c8d; margin-bottom: 12px;
        border-bottom: 1px solid #e2e8f0; padding-bottom: 6px;
    }
    .pay-toggle .btn { font-weight:600; }
    .custom-file-input:focus ~ .custom-file-label { border-color: var(--primary-green); }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="card border-0">
                <div class="card-header-flat d-flex align-items-center justify-content-between">
                    <h3 class="card-title font-weight-bold mb-0" style="font-size: 0.95rem;">
                        <i class="fas fa-sitemap mr-2"></i> Tambah Cabang Lomba — {{ $event->nama_event }}
                    </h3>
                    <a href="{{ route('lomba.manage', $event->slug) }}" class="btn btn-sm"
                       style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>

                <form action="{{ route('lomba.cabang.store', $event->slug) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">

                        @if($errors->any())
                        <div class="alert alert-danger" style="border-radius:0;">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Periksa kembali isian form:
                            <ul class="mb-0 mt-1 pl-3">
                                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="field-section">
                            <div class="field-section-title"><i class="fas fa-info-circle mr-1"></i> Informasi Cabang</div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Nama Cabang Lomba <span class="text-danger">*</span></label>
                                <input type="text" name="nama_cabang" class="form-control"
                                       value="{{ old('nama_cabang') }}" placeholder="Contoh: Kategori SMA" required>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Cover Cabang Lomba</label>
                                <div class="custom-file">
                                    <input type="file" name="cover_image" class="custom-file-input" id="coverFile" accept="image/*">
                                    <label class="custom-file-label" for="coverFile">Pilih gambar cover...</label>
                                </div>
                                <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 2MB.</small>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Deskripsi Cabang Lomba</label>
                                <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan ketentuan khusus cabang ini...">{{ old('deskripsi') }}</textarea>
                            </div>
                        </div>

                        <div class="field-section">
                            <div class="field-section-title"><i class="fas fa-money-bill-wave mr-1"></i> Tipe &amp; Harga Pendaftaran</div>
                            <div class="form-group mb-3">
                                <div class="btn-group btn-group-toggle pay-toggle" data-toggle="buttons">
                                    <label class="btn btn-outline-success {{ old('tipe_bayar','gratis')=='gratis'?'active':'' }}">
                                        <input type="radio" name="tipe_bayar" value="gratis" {{ old('tipe_bayar','gratis')=='gratis'?'checked':'' }} onchange="toggleBayar()"> Gratis
                                    </label>
                                    <label class="btn btn-outline-success {{ old('tipe_bayar')=='berbayar'?'active':'' }}">
                                        <input type="radio" name="tipe_bayar" value="berbayar" {{ old('tipe_bayar')=='berbayar'?'checked':'' }} onchange="toggleBayar()"> Berbayar
                                    </label>
                                </div>
                            </div>

                            <div id="harga_section" style="display:none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="small font-weight-bold">Harga Pendaftaran Individu (Rp)</label>
                                            <input type="number" name="harga_individual" class="form-control" min="0"
                                                   value="{{ old('harga_individual') }}" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="small font-weight-bold">Harga Pendaftaran Tim (Rp)</label>
                                            <input type="number" name="harga_tim" class="form-control" min="0"
                                                   value="{{ old('harga_tim') }}" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group mb-2">
                                <label class="font-weight-bold d-block">Jenis Peserta <span class="text-danger">*</span></label>
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-outline-success {{ old('jenis_peserta','individu')=='individu'?'active':'' }}">
                                        <input type="radio" name="jenis_peserta" value="individu" {{ old('jenis_peserta','individu')=='individu'?'checked':'' }} onchange="toggleJenisPeserta()"> Individu
                                    </label>
                                    <label class="btn btn-outline-success {{ old('jenis_peserta')=='tim'?'active':'' }}">
                                        <input type="radio" name="jenis_peserta" value="tim" {{ old('jenis_peserta')=='tim'?'checked':'' }} onchange="toggleJenisPeserta()"> Tim
                                    </label>
                                </div>
                            </div>

                            <div id="maks_anggota_wrap" class="form-group mb-0" style="display:none;">
                                <label class="small font-weight-bold">Maksimal Anggota per Tim</label>
                                <input type="number" name="maks_anggota_tim" class="form-control" min="1"
                                       value="{{ old('maks_anggota_tim') }}" placeholder="Contoh: 3">
                                <small class="text-muted">Peserta yang mendaftar cabang ini akan terkunci di cabang ini saja.</small>
                            </div>
                        </div>

                        <div class="field-section">
                            <div class="field-section-title"><i class="fas fa-file-pdf mr-1"></i> Juknis Cabang Lomba</div>
                            <div class="form-group mb-0">
                                <div class="custom-file">
                                    <input type="file" name="juknis_file" class="custom-file-input" id="juknisFile" accept="application/pdf">
                                    <label class="custom-file-label" for="juknisFile">Pilih file PDF juknis...</label>
                                </div>
                                <small class="text-muted">Format: PDF saja. Maks: 5MB.</small>
                            </div>
                        </div>

                        <div class="field-section mb-0">
                            <div class="field-section-title"><i class="fas fa-address-book mr-1"></i> Contact Person & Penanggung Jawab</div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold">Penanggung Jawab Cabang (NPM / Email)</label>
                                        <input type="text" name="pj_identifier" class="form-control"
                                               value="{{ old('pj_identifier') }}" placeholder="Contoh: 19312010 atau email@contoh.com (Opsional)">
                                        <small class="text-muted">Jika diisi, user ini akan diberikan akses khusus untuk mengelola nilai/cabang ini.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold">Nama</label>
                                        <input type="text" name="contact_person_nama" class="form-control"
                                               value="{{ old('contact_person_nama') }}" placeholder="Nama contact person">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold">No. HP / Kontak</label>
                                        <input type="text" name="contact_person_telepon" class="form-control"
                                               value="{{ old('contact_person_telepon') }}" placeholder="08xxxx">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold">Link Grup WhatsApp Cabang</label>
                                        <input type="url" name="wa_group_link" class="form-control"
                                               value="{{ old('wa_group_link') }}" placeholder="https://chat.whatsapp.com/...">
                                        <small class="text-muted">Grup khusus cabang ini (opsional). Akan dimunculkan setelah dokumen diapprove.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer bg-white text-right">
                        <a href="{{ route('lomba.manage', $event->slug) }}" class="btn btn-default mr-2">
                            <i class="fas fa-times mr-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-green px-4">
                            <i class="fas fa-save mr-1"></i> Simpan Cabang Lomba
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    function toggleBayar() {
        var berbayar = document.querySelector('input[name="tipe_bayar"]:checked');
        var show = berbayar && berbayar.value === 'berbayar';
        document.getElementById('harga_section').style.display = show ? 'block' : 'none';
    }

    function toggleJenisPeserta() {
        var tim = document.querySelector('input[name="jenis_peserta"]:checked');
        var show = tim && tim.value === 'tim';
        document.getElementById('maks_anggota_wrap').style.display = show ? 'block' : 'none';
    }

    document.getElementById('coverFile').addEventListener('change', function(e){
        e.target.nextElementSibling.innerText = e.target.files[0] ? e.target.files[0].name : 'Pilih gambar cover...';
    });
    document.getElementById('juknisFile').addEventListener('change', function(e){
        e.target.nextElementSibling.innerText = e.target.files[0] ? e.target.files[0].name : 'Pilih file PDF juknis...';
    });

    document.addEventListener('DOMContentLoaded', function () {
        toggleBayar();
        toggleJenisPeserta();
    });
</script>
@endsection
