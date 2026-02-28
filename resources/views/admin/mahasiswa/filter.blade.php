<div class="p-3">
    <div class="row">

        {{-- KOLOM KIRI: FORM FILTER (Grid 9) --}}
        <div class="col-lg-9 col-12">
            <form method="get">
                {{-- Gunakan align-items-end agar tombol sejajar bawah --}}
                <div class="row align-items-end">

                    {{-- 1. FILTER ANGKATAN --}}
                    <div class="col-md-4 col-12 mb-2">
                        <label class="small mb-1 font-weight-bold text-muted text-uppercase">Angkatan</label>
                        <select name="angkatan" class="form-control form-control-sm select2" required>
                            <option value="">-- Pilih Angkatan --</option>
                            @php $now = date('Y'); @endphp
                            @for ($i = $now; $i >= 2015; $i--)
                                <option value="{{ $i }}" {{ (string)$angkatan === (string)$i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- 2. FILTER PRODI --}}
                    <div class="col-md-5 col-12 mb-2">
                        <label class="small mb-1 font-weight-bold text-muted text-uppercase">Program Studi</label>
                        <select name="prodi" class="form-control form-control-sm select2" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach ($dataProdi as $prodi)
                                <option value="{{ $prodi->id_prodi }}"
                                    {{ (string)$prodiId === (string)$prodi->id_prodi ? 'selected' : '' }}>
                                    {{ $prodi->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. TOMBOL TERAPKAN (Hijau Flat) --}}
                    <div class="col-md-3 col-12 mb-2">
                        {{-- KITA UBAH KE btn-success DAN PAKSA WARNA HIJAU #27ae60 --}}
                        <button type="submit" class="btn btn-success btn-sm btn-block font-weight-bold"
                                style="background-color: #27ae60; border-color: #27ae60; letter-spacing: 0.5px;">
                            <i class="fa fa-filter mr-1"></i> FILTER
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- KOLOM KANAN: TOMBOL SINKRONISASI (Grid 3) --}}
        <div class="col-lg-3 col-12 mb-2 d-flex align-items-end">

            @if (!empty($angkatan))
                <form id="sync-form" method="post" action="{{ route('data-mahasiswa.store') }}" class="w-100">
                    @csrf
                    <input type="hidden" name="angkatan" value="{{ $angkatan }}">
                    <input type="hidden" name="prodi" value="{{ $prodiId }}">
                    {{-- Tombol Sync warna Dark/Abu Gelap --}}
                    <button type="button" id="sync-button" class="btn btn-dark btn-sm btn-block font-weight-bold"
                            title="Sinkronisasi Data ke Server"
                            style="background-color: #34495e; border-color: #34495e;"
                            data-angkatan="{{ $angkatan }}"
                            data-prodi="{{ $prodiId }}">
                        <i class="fa fa-sync-alt mr-1"></i> SINKRONISASI
                    </button>
                </form>
            @else
                {{-- Tombol Disabled --}}
                <button class="btn btn-secondary btn-sm btn-block disabled font-weight-bold" disabled
                        style="opacity: 0.6; cursor: not-allowed; border-radius: 0;">
                    <i class="fa fa-sync-alt mr-1"></i> SINKRONISASI
                </button>
            @endif

        </div>

    </div>
</div>
