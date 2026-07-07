@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Designer Sertifikat')

@section('content')

    {{-- --- STYLES --- --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Bree+Serif&family=Dancing+Script:wght@400;700&family=Libre+Baskerville:wght@400;700&family=Open+Sans:wght@400;600;700&display=swap');
        @import url('https://fonts.cdnfonts.com/css/jimmy-script');

        /* Container Editor */
        .editor-container {
            background-color: #f8f9fa;
            border: 2px dashed #adb5bd;
            border-radius: 8px;
            overflow: hidden; /* Mencegah scrollbar ganda */
            position: relative;
            min-height: 500px;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Start dari atas agar tidak terpotong */
            padding: 40px;
        }

        .editor-container.assignment-mode {
            background-color: #ffffff;
            min-height: 0;
            padding: 20px;
        }

        /* Canvas Wrapper (Area Gambar) */
        .canvas-wrapper {
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            line-height: 0;
            user-select: none;
            transition: transform 0.2s;
        }

        .canvas-wrapper img {
            max-width: 100%; /* Responsif di container */
            height: auto;
            pointer-events: none; /* Gambar tidak bisa di-drag browser default */
        }

        #assignmentPreviewCanvas {
            display: block;
            max-width: 100%;
            height: auto;
            background: #ffffff;
        }

        /* Elemen Teks yang Bisa Digeser */
        .draggable-item {
            position: absolute;
            cursor: move;
            border: 1px dashed rgba(0, 123, 255, 0.4);
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 8px;
            white-space: nowrap;
            transform-origin: center;
            transition: border 0.1s, background 0.1s;
            z-index: 10;
        }

        .draggable-item:hover {
            border: 1px solid #007bff;
            background: rgba(255, 255, 255, 0.5);
        }

        .draggable-item.active {
            border: 2px solid #007bff;
            background: rgba(255, 255, 255, 0.85);
            z-index: 20;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .form-color-input {
            height: 38px;
            width: 100%;
            padding: 2px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        /* Animasi Collapse */
        .collapse-anim {
            overflow: hidden;
            height: 0;
            opacity: 0;
            transition: height 0.35s ease, opacity 0.25s ease;
        }
        .collapse-anim.show {
            opacity: 1;
            height: auto;
        }

        .assignment-col-width {
            max-width: 90px;
        }

        .assignment-table-overlay {
            position: absolute;
            border: 2px dashed #17a2b8;
            background: rgba(23, 162, 184, 0.08);
            box-sizing: border-box;
            z-index: 40;
            cursor: move;
            pointer-events: auto;
        }

        .assignment-overlay-label {
            position: absolute;
            top: -24px;
            left: 0;
            padding: 2px 8px;
            border-radius: 4px;
            background: #17a2b8;
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .assignment-overlay-handle {
            position: absolute;
            background: #17a2b8;
            border: 1px solid #ffffff;
            box-sizing: border-box;
            z-index: 5;
        }

        .assignment-overlay-resize {
            width: 14px;
            height: 14px;
            right: -7px;
            bottom: -7px;
            cursor: nwse-resize;
        }

        .assignment-overlay-row-height {
            width: 16px;
            height: 16px;
            left: 50%;
            transform: translateX(-50%);
            bottom: -8px;
            border-radius: 50%;
            cursor: ns-resize;
        }

        .assignment-column-handle {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 12px;
            transform: translateX(-50%);
            cursor: col-resize;
            z-index: 4;
        }

        .assignment-column-handle::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            border-left: 2px dashed rgba(23, 162, 184, 0.9);
            transform: translateX(-50%);
        }

        .tool-section-toggle {
            border: 1px solid #d6dbe1;
            background: #f8f9fa;
        }

        .tool-section-toggle:hover {
            background: #eef2f5;
        }

        .tool-section-toggle:focus {
            box-shadow: none;
        }

        .tool-section-body {
            display: none;
        }

        .tool-section-body.show {
            display: block;
        }

        .section-chevron {
            transition: transform 0.2s ease;
        }

        .section-chevron.collapsed {
            transform: rotate(180deg);
        }
    </style>

    {{-- Green flat theme overrides --}}
    <style>
        :root { --primary-green: #27ae60; --primary-green-dark: #219150; }

        .card-header-flat {
            background-color: var(--primary-green);
            color: #fff;
            padding: 11px 18px;
            border-bottom: none;
        }
        .card-header-flat h5, .card-header-flat h6,
        .card-header-flat .card-title { color:#fff; font-weight:700; margin:0; }

        /* Override btn-primary → green (for mode toggle & generate btn) */
        .btn-primary {
            background-color: var(--primary-green) !important;
            border-color: var(--primary-green) !important;
            color: #fff !important;
        }
        .btn-primary:hover {
            background-color: var(--primary-green-dark) !important;
            border-color: var(--primary-green-dark) !important;
        }
        .btn-outline-primary {
            color: var(--primary-green) !important;
            border-color: var(--primary-green) !important;
            background: transparent !important;
        }
        .btn-outline-primary:hover,
        .btn-outline-primary.active {
            background-color: var(--primary-green) !important;
            color: #fff !important;
        }
        .btn-success {
            background-color: var(--primary-green) !important;
            border-color: var(--primary-green) !important;
        }
        .btn-success:hover { background-color: var(--primary-green-dark) !important; }
        .btn-outline-success {
            color: var(--primary-green) !important;
            border-color: var(--primary-green) !important;
        }
        .btn-outline-success:hover {
            background-color: var(--primary-green) !important;
            color: #fff !important;
        }

        /* Flat table */
        .table-flat thead th {
            background-color: #ecf0f1;
            color: #7f8c8d;
            border-bottom: 2px solid #bdc3c7;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: .5px;
            vertical-align: middle;
            padding: 9px 12px;
        }
        .table-flat tbody td {
            vertical-align: middle !important;
            padding: 9px 12px;
            font-size: .875rem;
            border-bottom: 1px solid #f0f0f0;
        }
        .table-hover tbody tr:hover { background-color: #f9fbfb; }

        /* Log area */
        #logArea { border-radius: 0 !important; }

        /* Progress bar in generate */
        #progressBar { border-radius: 0 !important; }

        /* Tool section toggle border */
        .tool-section-toggle { border-radius: 0 !important; }

        /* Custom-file border */
        .custom-file-label { border-radius: 0 !important; }
        .custom-file-input:focus ~ .custom-file-label { border-color: var(--primary-green); box-shadow: none; }

        /* ---- MODE TAB BUTTONS inside card-header-flat ---- */
        /* Active tab → kotak putih, teks hijau */
        .card-header-flat .btn-group .btn-primary {
            background: rgba(255,255,255,0.95) !important;
            color: var(--primary-green) !important;
            border-color: #fff !important;
            font-weight: 700;
        }
        .card-header-flat .btn-group .btn-primary:hover {
            background: #fff !important;
        }
        /* Inactive tab → teks putih redup, transparan */
        .card-header-flat .btn-group .btn-outline-primary {
            background: rgba(255,255,255,0.08) !important;
            color: rgba(255,255,255,0.55) !important;
            border-color: rgba(255,255,255,0.3) !important;
            font-weight: 600;
        }
        .card-header-flat .btn-group .btn-outline-primary:hover:not(:disabled) {
            background: rgba(255,255,255,0.2) !important;
            color: rgba(255,255,255,0.9) !important;
            border-color: rgba(255,255,255,0.55) !important;
        }
        /* Disabled (belum diaktifkan) */
        .card-header-flat .btn-group .btn-outline-primary:disabled,
        .card-header-flat .btn-group .btn-outline-primary[disabled] {
            background: rgba(255,255,255,0.04) !important;
            color: rgba(255,255,255,0.22) !important;
            border-color: rgba(255,255,255,0.12) !important;
            cursor: not-allowed;
        }

        /* Context upload section animation */
        #uploadSectionMain, #uploadSectionAssignment {
            transition: opacity .2s ease;
        }

        /* ---- DRAGGABLE OVERLAYS untuk JP/Assignment (Nama & Event) ---- */
        .at-draggable {
            position: absolute;
            cursor: move;
            border: 1px dashed rgba(39, 174, 96, 0.7);
            background: rgba(255, 255, 255, 0.82);
            color: #155724;
            padding: 3px 9px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            user-select: none;
            z-index: 30;
            transition: border 0.1s, background 0.1s;
            pointer-events: auto;
        }
        .at-draggable:hover {
            border: 1px solid var(--primary-green);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 8px rgba(39,174,96,.25);
        }
        .at-draggable.at-active {
            border: 2px solid var(--primary-green);
            background: #fff;
            box-shadow: 0 4px 12px rgba(39,174,96,.35);
            z-index: 50;
        }
        /* Label kecil di atas overlay */
        .at-draggable::before {
            content: attr(data-at-key);
            position: absolute;
            top: -17px;
            left: 0;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            background: var(--primary-green);
            color: #fff;
            padding: 1px 5px;
            white-space: nowrap;
            letter-spacing: .4px;
        }
        #at_event::before { content: "Event & Tanggal"; }
        #at_nama::before  { content: "Nama"; }
        #at_npm::before   { content: "NPM"; }
    </style>

    <div class="container-fluid pb-5">

        <div class="row">
            {{-- KOLOM KIRI: TOOLS & PROPERTIES --}}
            <div class="col-lg-3 col-md-12 mb-4">

                <div class="card border-0 h-100" style="border:1px solid #dee2e6!important;">
                    <div class="card-header-flat d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0" style="font-size:.97rem;">
                            <i class="fas fa-sliders-h mr-2"></i> Editor Tools
                        </h5>
                        <a href="{{ route('certificates.index') }}"
                           class="btn btn-sm"
                           style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);font-size:.78rem;">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>

                    <div class="card-body p-3">

                        {{-- 1. UPLOAD BACKGROUND (context-sensitive per mode) --}}
                        <div class="mb-4 pb-3 border-bottom">

                            {{-- MAIN CERTIFICATE upload --}}
                            <div id="uploadSectionMain">
                                <label class="font-weight-bold small text-muted text-uppercase mb-2">
                                    <i class="fas fa-image mr-1"></i> 1. Background Sertifikat Utama
                                </label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="bgFile" accept="image/jpeg,image/png">
                                    <label class="custom-file-label" for="bgFile">Pilih Gambar...</label>
                                </div>
                                <small class="text-muted d-block mt-1">Gunakan file JPG/PNG (Landscape A4).</small>
                            </div>

                            {{-- JP/ASSIGNMENT upload (tampil saat JP mode aktif) --}}
                            <div id="uploadSectionAssignment" style="display:none;">
                                <label class="font-weight-bold small text-muted text-uppercase mb-2">
                                    <i class="fas fa-image mr-1"></i> 1. Background JP / Assignment
                                </label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="assignmentBgFileDirect" accept="image/jpeg,image/png">
                                    <label class="custom-file-label" for="assignmentBgFileDirect">Pilih Gambar...</label>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    JPG/PNG Landscape A4. Biarkan kosong untuk pakai background sertifikat utama.
                                </small>
                            </div>

                        </div>

                        {{-- 2. INPUT KONTEN --}}
                        <div class="mb-4 pb-3 border-bottom">
                            <label class="font-weight-bold small text-muted text-uppercase mb-2">2. Isi Konten Variabel</label>

                            {{-- Nomor Sertifikat --}}
                            <div class="form-group mb-2">
                                <label class="small mb-1">Format Nomor <i class="fas fa-info-circle text-info" title="Gunakan $001 untuk auto increment dengan padding 3 digit, atau $1 untuk tanpa padding."></i></label>
                                <input type="text" id="input_nomor" class="form-control form-control-sm" value="NO: SRT/{{ date('Y') }}/$001">
                                <small class="text-success d-block mt-1" id="nomorPreview" style="font-size: 0.75rem;">
                                    <i class="fas fa-eye mr-1"></i> Preview urutan ke-1: <b>-</b>
                                </small>
                            </div>

                            {{-- Deskripsi Event --}}
                            <div class="form-group mb-2">
                                <label class="small mb-1">Deskripsi Event</label>
                                <textarea id="input_deskripsi" class="form-control form-control-sm" rows="3">Telah berpartisipasi aktif dalam kegiatan {{ $event->nama_event }} yang diselenggarakan pada tanggal {{ $event->tanggal_pelaksanaan }}</textarea>
                            </div>
                        </div>

                        <div class="mb-4 pb-3 border-bottom">
                            <button type="button" id="btnToggleAssignmentSection" class="btn btn-sm btn-block tool-section-toggle text-left d-flex justify-content-between align-items-center" aria-expanded="true">
                                <span class="font-weight-bold small text-muted text-uppercase">3. JP / Assignment Page</span>
                                <i id="iconAssignmentSection" class="fas fa-chevron-up section-chevron"></i>
                            </button>

                            <div id="sectionAssignmentBody" class="tool-section-body show mt-2">
                                <div class="custom-control custom-switch mb-2">
                                    <input type="checkbox" class="custom-control-input" id="toggleAssignmentPage">
                                    <label class="custom-control-label small" for="toggleAssignmentPage">Aktifkan halaman tambahan JP / Assignment</label>
                                </div>

                                <div id="assignmentSettingsPanel" style="display:none;">
                                    {{-- Background source (disembunyikan saat JP tab aktif, digantikan Section 1) --}}
                                    <div class="form-group mb-2" id="assignmentBgSourceGroup">
                                        <label class="small d-block mb-1">Background JP / Assignment</label>

                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="assignmentBgSourceMain" name="assignment_bg_source" class="custom-control-input" checked>
                                            <label class="custom-control-label small" for="assignmentBgSourceMain">Gunakan background sertifikat utama</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-2">
                                            <input type="radio" id="assignmentBgSourceCustom" name="assignment_bg_source" class="custom-control-input">
                                            <label class="custom-control-label small" for="assignmentBgSourceCustom">Upload background khusus JP / Assignment</label>
                                        </div>

                                        <div id="assignmentBgUploadWrap" style="display:none;">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="assignmentBgFile" accept="image/jpeg,image/png">
                                                <label class="custom-file-label" for="assignmentBgFile">Pilih Gambar...</label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Judul/Subjudul dihapus; Event & Tanggal jadi header default otomatis --}}
                                    <input type="hidden" id="assignmentTitle" value="">
                                    <input type="hidden" id="assignmentSubtitle" value="">

                                    {{-- Variabel Tambahan (Nama & NPM) --}}
                                    <div class="mb-3 p-2" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                        <label class="small font-weight-bold d-block mb-2"
                                               style="color:#7f8c8d; text-transform:uppercase; letter-spacing:.4px; font-size:10px;">
                                            <i class="fas fa-plus-circle mr-1" style="color:var(--primary-green);"></i> Variabel Tambahan
                                        </label>
                                        <div class="custom-control custom-switch mb-1">
                                            <input type="checkbox" class="custom-control-input" id="toggleVarNama">
                                            <label class="custom-control-label small" for="toggleVarNama">Tampilkan Nama</label>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="toggleVarNpm">
                                            <label class="custom-control-label small" for="toggleVarNpm">Tampilkan NPM</label>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="small mb-0">Kolom Tabel</label>
                                        <button type="button" class="btn btn-xs btn-outline-primary" id="btnAddAssignmentColumn">
                                            <i class="fas fa-plus mr-1"></i> Tambah Kolom
                                        </button>
                                    </div>
                                    <div id="assignmentColumnsEditor" class="mb-2"></div>

                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="small mb-0">Isi Tabel</label>
                                        <button type="button" class="btn btn-xs btn-outline-primary" id="btnAddAssignmentRow">
                                            <i class="fas fa-plus mr-1"></i> Tambah Baris
                                        </button>
                                    </div>

                                    <div class="table-responsive border rounded mb-2" style="max-height: 240px;">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="thead-light" id="assignmentRowsHead"></thead>
                                            <tbody id="assignmentRowsBody"></tbody>
                                        </table>
                                    </div>

                                    <div class="border rounded p-2 mb-2">
                                        <label class="small font-weight-bold d-block mb-2">Layout Tabel</label>
                                        <div class="form-group mb-2">
                                            <label class="small mb-1">Font Family</label>
                                            <select id="assignmentTableFontFamily" class="form-control form-control-sm">
                                                <option value="Jimmy Script">Jimmy Script</option>
                                                <option value="Libre Baskerville">Libre Braskeville</option>
                                                <option value="Open Sans">Open Sans</option>
                                                <option value="Bree Serif">Bree Serif</option>
                                            </select>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-6 col-md-3 mb-2">
                                                <label class="small mb-1">Font (px)</label>
                                                <input type="number" id="assignmentTableFontSize" class="form-control form-control-sm" min="8" max="64">
                                            </div>
                                            <div class="col-6 col-md-3 mb-2">
                                                <label class="small mb-1">Header BG</label>
                                                <input type="color" id="assignmentTableHeaderBg" class="form-control form-control-sm form-color-input">
                                            </div>
                                            <div class="col-6 col-md-3 mb-2">
                                                <label class="small mb-1">Border</label>
                                                <input type="color" id="assignmentTableBorderColor" class="form-control form-control-sm form-color-input">
                                            </div>
                                            <div class="col-6 col-md-3 mb-2">
                                                <label class="small mb-1">Text</label>
                                                <input type="color" id="assignmentTableTextColor" class="form-control form-control-sm form-color-input">
                                            </div>
                                        </div>

                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="assignmentTableShowTotal">
                                            <label class="custom-control-label small" for="assignmentTableShowTotal">Tampilkan baris TOTAL</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 pb-3 border-bottom">
                            <button type="button" id="btnTogglePropertiesSection" class="btn btn-sm btn-block tool-section-toggle text-left d-flex justify-content-between align-items-center" aria-expanded="true">
                                <span class="font-weight-bold small text-muted text-uppercase">4. Properti</span>
                                <i id="iconPropertiesSection" class="fas fa-chevron-up section-chevron"></i>
                            </button>

                            <div id="sectionPropertiesBody" class="tool-section-body show mt-2">
                                <div id="propertiesPanel" style="display:none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="font-weight-bold small text-success text-uppercase mb-0">Properti Aktif</label>
                                        <span class="badge badge-light border" id="activeElName">-</span>
                                    </div>

                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="small">Ukuran (px)</label>
                                            <input type="number" id="prop_size" class="form-control form-control-sm" min="10" max="300">
                                        </div>
                                        <div class="col-6">
                                            <label class="small">Warna</label>
                                            <input type="color" id="prop_color" class="form-control form-control-sm form-color-input">
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-2">
                                        <div class="col-12">
                                            <label class="small">Alignment</label>
                                            <select id="prop_align" class="form-control form-control-sm">
                                                <option value="left">Kiri</option>
                                                <option value="center">Tengah</option>
                                                <option value="right">Kanan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-2">
                                        <div class="col-12">
                                            <label class="small">Font Family</label>
                                            <select id="prop_font" class="form-control form-control-sm">
                                                <option value="Jimmy Script">Jimmy Script</option>
                                                <option value="Libre Baskerville">Libre Braskeville</option>
                                                <option value="Open Sans">Open Sans</option>
                                                <option value="Bree Serif">Bree Serif</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-2">
                                        <div class="col-12 d-flex justify-content-between">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="prop_bold">
                                                <label class="custom-control-label small" for="prop_bold">Bold</label>
                                            </div>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="prop_italic">
                                                <label class="custom-control-label small" for="prop_italic">Italic</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="propertiesPlaceholder" class="text-center text-muted py-4">
                                    <i class="fas fa-mouse-pointer fa-2x mb-2 opacity-50"></i>
                                    <p class="small mb-0">Klik teks di gambar untuk edit style.</p>
                                </div>
                            </div>
                        </div>

                        <div style="border-top:1px solid #e2e8f0; padding-top:12px; margin-top:4px;">
                            <button id="btnSaveTemplate" class="btn btn-outline-success btn-block btn-sm mb-2" style="font-weight:700;">
                                <i class="fas fa-save mr-1"></i> Simpan Template
                            </button>
                            <button id="btnGenerate" class="btn btn-primary btn-block" style="font-weight:700; font-size:.95rem; padding:9px;">
                                <i class="fas fa-magic mr-1"></i> Generate Sertifikat
                            </button>
                        </div>

                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: WORKSPACE --}}
            <div class="col-lg-9 col-md-12 mb-4">

                <div class="card border-0 h-100" style="border:1px solid #dee2e6!important;">
                    <div class="card-header-flat d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" style="font-size:.97rem;">
                            <i class="fas fa-paint-brush mr-2"></i> Visual Designer
                        </h5>
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <div class="btn-group btn-group-sm" id="designerModeGroup">
                                <button type="button" id="modeMainDesigner" class="btn btn-primary">Sertifikat Utama</button>
                                <button type="button" id="modeAssignmentDesigner" class="btn btn-outline-primary" disabled>JP / Assignment</button>
                            </div>
                            <span class="badge badge-warning" style="border-radius:0!important;">
                                <i class="fas fa-arrows-alt mr-1"></i> Drag & Drop
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-0 bg-white">
                        <div class="editor-container" id="editorContainer">

                            {{-- CANVAS WRAPPER --}}
                            <div class="canvas-wrapper" id="canvasWrapper" style="display:none;">
                                {{-- Background Image --}}
                                <img id="bgPreview" src="#" alt="Background">

                                {{-- Draggable Elements --}}
                                <div class="draggable-item" id="el_nama" data-key="nama">[Nama Peserta]</div>
                                <div class="draggable-item" id="el_role" data-key="role">[Peran Peserta]</div>
                                <div class="draggable-item" id="el_nomor" data-key="nomor">[Nomor Sertifikat]</div>
                                <div class="draggable-item" id="el_deskripsi" data-key="deskripsi" style="max-width: 600px; white-space: normal; text-align: center;">[Deskripsi Event]</div>
                            </div>

                            <div class="canvas-wrapper" id="assignmentCanvasWrapper" style="display:none; line-height:1; background:#fff;">
                                <canvas id="assignmentPreviewCanvas"></canvas>

                                {{-- Overlay draggable JP/Assignment --}}
                                {{-- Event & Tanggal: selalu tampil di JP mode, posisi default = atas tengah --}}
                                <div class="at-draggable" id="at_event" data-at-key="event"
                                     style="display:none; left:35%; top:9%;">
                                    <i class="fas fa-grip-lines" style="font-size:9px; margin-right:4px; opacity:.7;"></i>
                                    <span>Event &amp; Tanggal</span>
                                </div>
                                {{-- Nama: opsional, hanya tampil jika diaktifkan --}}
                                <div class="at-draggable" id="at_nama" data-at-key="nama"
                                     style="display:none; left:8%; top:18%;">
                                    <i class="fas fa-grip-lines" style="font-size:9px; margin-right:4px; opacity:.7;"></i>
                                    <span>Nama</span>
                                </div>
                                {{-- NPM: opsional, hanya tampil jika diaktifkan --}}
                                <div class="at-draggable" id="at_npm" data-at-key="npm"
                                     style="display:none; left:8%; top:22%;">
                                    <i class="fas fa-grip-lines" style="font-size:9px; margin-right:4px; opacity:.7;"></i>
                                    <span>NPM</span>
                                </div>

                                <div id="assignmentTableOverlay" class="assignment-table-overlay" style="display:none;">
                                    <span class="assignment-overlay-label">Edit Tabel</span>
                                    <div id="assignmentColumnHandles"></div>
                                    <div class="assignment-overlay-handle assignment-overlay-row-height" data-overlay-action="row-height"></div>
                                    <div class="assignment-overlay-handle assignment-overlay-resize" data-overlay-action="resize"></div>
                                </div>
                            </div>

                            {{-- Placeholder --}}
                            <div id="editorPlaceholder" class="text-center text-muted m-auto">
                                <i class="fas fa-image fa-4x mb-3 text-secondary"></i>
                                <h5 class="text-dark">Belum ada background</h5>
                                <p class="text-muted">Silakan upload gambar background di panel sebelah kiri.</p>
                            </div>

                        </div>
                    </div>

                    {{-- Console Log / Progress --}}
                    <div class="card-footer bg-white border-top" id="consoleFooter" style="display:none;">
                        <p class="font-weight-bold mb-1 small text-uppercase">Proses Generate:</p>
                        <div class="progress mb-2" style="height: 20px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 0%">0%</div>
                        </div>
                        <div id="logArea" class="border rounded p-2 bg-light text-monospace small text-dark" style="height: 100px; overflow-y: auto; border: 1px solid #ddd;"></div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ROW BAWAH: DATA PESERTA & FORM TAMBAH MANUAL --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0" style="border:1px solid #dee2e6!important;">
                    <div class="card-header-flat d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0" style="font-size:.97rem;">
                            <i class="fas fa-users mr-2"></i> Data Peserta & Kehadiran
                        </h5>
                        <button type="button" class="btn btn-sm btn-success" id="btnToggleAddParticipant"
                                style="font-size:.78rem; font-weight:700; border:1px solid rgba(255,255,255,.4); background:rgba(255,255,255,.15); color:#fff;">
                            <i class="fas fa-user-plus mr-1"></i> Tambah Manual
                        </button>
                    </div>

                    {{-- FORM TAMBAH PESERTA MANUAL (COLLAPSIBLE) --}}
                    <div class="collapse-anim bg-light border-bottom" id="addParticipantPanel">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-3" style="color:var(--primary-green);">
                                <i class="fas fa-user-plus mr-1"></i> Form Tambah Peserta Manual
                            </h6>
                            <div class="row align-items-end">
                                <div class="col-md-4 col-sm-12 mb-2">
                                    <label class="small text-muted mb-1">NPM Mahasiswa</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="add_npm" placeholder="Contoh: 202131xxx">
                                    </div>
                                </div>
                                <div class="col-md-5 col-sm-12 mb-2">
                                    <label class="small text-muted mb-1">Prodi (Kode / Nama)</label>
                                    <input type="text" class="form-control" id="add_prodi" placeholder="Contoh: Informatika">
                                </div>
                                <div class="col-md-3 col-sm-12 mb-2">
                                    <button class="btn btn-success btn-block" type="button" id="btnAddParticipant">
                                        <i class="fas fa-save mr-1"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TABEL PESERTA --}}
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-flat text-nowrap mb-0">
                                <thead>
                                <tr>
                                    <th>Nama Mahasiswa</th>
                                    <th>NPM</th>
                                    <th>Role</th>
                                    <th>Sertifikat</th>
                                    <th class="text-center" style="width: 100px;">Absensi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse ($registrations as $reg)
                                    @php
                                        // Cek attendance_status atau status utama
                                        $isPresent = ($reg->attendance_status === 'present') || ($reg->status === 'attended');
                                    @endphp
                                    <tr id="row-reg-{{ $reg->id }}">
                                        <td>
                                            <span class="font-weight-bold">{{ $reg->mahasiswa->nama_mahasiswa ?? '-' }}</span>
                                        </td>
                                        <td>{{ $reg->mahasiswa->npm_mahasiswa ?? '-' }}</td>
                                        <td>
                                            <span style="background:#f0fdf4;color:#27ae60;border:1px solid #c3e6cb;font-size:10px;padding:2px 7px;font-weight:600;border-radius:0;">
                                                {{ $reg->role ?? 'Peserta' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($reg->certificate_url)
                                                <a href="{{ asset($reg->certificate_url) }}" target="_blank"
                                                   class="btn btn-xs btn-outline-primary"
                                                   style="font-size:.75rem; font-weight:700; padding:3px 8px;">
                                                    <i class="fas fa-file-download mr-1"></i> Download
                                                </a>
                                            @else
                                                <span class="text-muted" style="font-size:.82rem; font-style:italic;">Belum digenerate</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input attendance-checkbox"
                                                       id="switch-{{ $reg->id }}"
                                                       data-registration-id="{{ $reg->id }}"
                                                    {{ $isPresent ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="switch-{{ $reg->id }}">
                                                    <span class="badge {{ $isPresent ? 'badge-success' : 'badge-danger' }}" id="badge-att-{{ $reg->id }}">
                                                        {{ $isPresent ? 'Hadir' : 'Absen' }}
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Belum ada peserta terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>

    {{-- --- LOGIC EDITOR & GENERATOR --- --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // --- CONSTANTS & CONFIG ---
            let SCALE = 1;
            let ORIGINAL_WIDTH = 0;
            let ORIGINAL_HEIGHT = 0;
            const eventName = @json($event->nama_event ?? '-');
            const eventDate = @json($event->tanggal_pelaksanaan ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y') : '-');

            const defaultConfig = {
                'nama':      { x: 50, y: 40, size: 80, color: '#000000', align: 'center', bold: true,  italic: false, font: 'Open Sans', text: '[Nama Peserta]' },
                'role':      { x: 50, y: 55, size: 50, color: '#0062ff', align: 'center', bold: true,  italic: false, font: 'Open Sans', text: '[Peran Peserta]' },
                'nomor':     { x: 50, y: 25, size: 30, color: '#000000', align: 'center', bold: false, italic: false, font: 'Open Sans', text: '[Nomor Sertifikat]' },
                'deskripsi': { x: 50, y: 65, size: 30, color: '#333333', align: 'center', bold: false, italic: false, font: 'Open Sans', text: '[Deskripsi Event]', width: 60 }
            };
            let config = JSON.parse(JSON.stringify(defaultConfig));

            const FONT_OPTIONS = ['Jimmy Script', 'Libre Baskerville', 'Open Sans', 'Bree Serif'];
            const FONT_STACKS = {
                'Jimmy Script': '"Jimmy Script", "Dancing Script", cursive',
                'Libre Baskerville': '"Libre Baskerville", serif',
                'Open Sans': '"Open Sans", sans-serif',
                'Bree Serif': '"Bree Serif", serif',
            };

            function normalizeFontName(value, fallback = 'Open Sans') {
                if (value === 'Libre Braskeville') return 'Libre Baskerville';
                return FONT_OPTIONS.includes(value) ? value : fallback;
            }

            function getFontStack(value) {
                const normalized = normalizeFontName(value, 'Open Sans');
                return FONT_STACKS[normalized] || FONT_STACKS['Open Sans'];
            }

            function getPrimaryFontFamily(value) {
                const normalized = normalizeFontName(value, 'Open Sans');
                if (normalized === 'Jimmy Script') return '"Jimmy Script"';
                if (normalized === 'Libre Baskerville') return '"Libre Baskerville"';
                if (normalized === 'Bree Serif') return '"Bree Serif"';
                return '"Open Sans"';
            }

            function getDefaultAssignmentPage() {
                return {
                    enabled: false,
                    background_source: 'main',
                    title: 'Rekap JP / Point Assignment',
                    subtitle: 'Ringkasan capaian pembelajaran peserta',
                    columns: ['Materi / Aktivitas', 'JP', 'Point Assignment'],
                    column_widths: [45, 27.5, 27.5],
                    table_settings: {
                        x: 8,
                        y: 36,
                        width: 84,
                        row_height: 5.5,
                        font_size: 14,
                        font_family: 'Open Sans',
                        header_bg: '#e9ecef',
                        border_color: '#222222',
                        text_color: '#111111',
                        show_total: true,
                    },
                    rows: [
                        ['Sesi Materi 1', '2', '20'],
                        ['Sesi Materi 2', '2', '20'],
                        ['Mini Project', '4', '60'],
                    ],
                };
            }
            let assignmentPage = getDefaultAssignmentPage();

            // Global state posisi & visibilitas teks di halaman JP (diakses oleh script terpisah)
            if (!window._assignTextPos) {
                window._assignTextPos = {
                    event_x: null, event_y: null,
                    nama_enabled: false, nama_x: null, nama_y: null,
                    npm_enabled:  false, npm_x:  null, npm_y:  null,
                };
            }

            let activeElementKey = null;

            // --- DOM ELEMENTS ---
            const bgFile = document.getElementById('bgFile');
            const bgPreview = document.getElementById('bgPreview');
            const canvasWrapper = document.getElementById('canvasWrapper');
            const assignmentCanvasWrapper = document.getElementById('assignmentCanvasWrapper');
            const assignmentPreviewCanvas = document.getElementById('assignmentPreviewCanvas');
            const assignmentTableOverlay = document.getElementById('assignmentTableOverlay');
            const assignmentColumnHandles = document.getElementById('assignmentColumnHandles');
            const editorPlaceholder = document.getElementById('editorPlaceholder');
            const editorContainer = document.getElementById('editorContainer');
            const modeMainDesigner = document.getElementById('modeMainDesigner');
            const modeAssignmentDesigner = document.getElementById('modeAssignmentDesigner');
            const bgFileLabel = document.querySelector('label[for="bgFile"]');
            let activeDesignerMode = 'main';
            let mainBgObjectUrl = null;
            let assignmentBgObjectUrl = null;
            let assignmentCustomBackgroundImage = null;
            let assignmentOverlayState = null;

            // Inputs Content
            const inputNomor = document.getElementById('input_nomor');
            const nomorPreview = document.getElementById('nomorPreview');
            const inputDeskripsi = document.getElementById('input_deskripsi');

            // Panels & Tools
            const propPanel = document.getElementById('propertiesPanel');
            const propPlaceholder = document.getElementById('propertiesPlaceholder');
            const elNameDisplay = document.getElementById('activeElName');
            const btnToggleAssignmentSection = document.getElementById('btnToggleAssignmentSection');
            const btnTogglePropertiesSection = document.getElementById('btnTogglePropertiesSection');
            const sectionAssignmentBody = document.getElementById('sectionAssignmentBody');
            const sectionPropertiesBody = document.getElementById('sectionPropertiesBody');
            const iconAssignmentSection = document.getElementById('iconAssignmentSection');
            const iconPropertiesSection = document.getElementById('iconPropertiesSection');

            // Inputs Properties
            const propSize = document.getElementById('prop_size');
            const propColor = document.getElementById('prop_color');
            const propAlign = document.getElementById('prop_align');
            const propFont = document.getElementById('prop_font');
            const propBold = document.getElementById('prop_bold');
            const propItalic = document.getElementById('prop_italic');

            // Assignment controls
            const toggleAssignmentPage = document.getElementById('toggleAssignmentPage');
            const assignmentSettingsPanel = document.getElementById('assignmentSettingsPanel');
            const assignmentTitle = document.getElementById('assignmentTitle');
            const assignmentSubtitle = document.getElementById('assignmentSubtitle');
            const assignmentColumnsEditor = document.getElementById('assignmentColumnsEditor');
            const assignmentRowsHead = document.getElementById('assignmentRowsHead');
            const assignmentRowsBody = document.getElementById('assignmentRowsBody');
            const btnAddAssignmentColumn = document.getElementById('btnAddAssignmentColumn');
            const btnAddAssignmentRow = document.getElementById('btnAddAssignmentRow');
            const assignmentBgSourceMain = document.getElementById('assignmentBgSourceMain');
            const assignmentBgSourceCustom = document.getElementById('assignmentBgSourceCustom');
            const assignmentBgUploadWrap = document.getElementById('assignmentBgUploadWrap');
            const assignmentBgFile = document.getElementById('assignmentBgFile');
            const assignmentBgFileLabel = document.querySelector('label[for="assignmentBgFile"]');
            const assignmentTableFontSize = document.getElementById('assignmentTableFontSize');
            const assignmentTableFontFamily = document.getElementById('assignmentTableFontFamily');
            const assignmentTableHeaderBg = document.getElementById('assignmentTableHeaderBg');
            const assignmentTableBorderColor = document.getElementById('assignmentTableBorderColor');
            const assignmentTableTextColor = document.getElementById('assignmentTableTextColor');
            const assignmentTableShowTotal = document.getElementById('assignmentTableShowTotal');

            // Buttons
            const btnGenerate = document.getElementById('btnGenerate');

            // --- INITIALIZATION ---
            initAssignmentVisualOverlay();
            initToolSectionToggles();
            initAssignmentSettings();
            ensureFontFamiliesLoaded(FONT_OPTIONS);
            loadTemplateFromServer();
            updateNomorPreview(); // Init preview
            refreshDesignerModeButtons();
            refreshDesignerWorkspace();
            // Expose render function untuk diakses script terpisah (draggable overlays)
            window._reRenderAssignment = renderAssignmentDesignerPreview;

            function setToolSectionState(sectionName, isOpen) {
                const sectionMap = {
                    assignment: {
                        button: btnToggleAssignmentSection,
                        body: sectionAssignmentBody,
                        icon: iconAssignmentSection,
                    },
                    properties: {
                        button: btnTogglePropertiesSection,
                        body: sectionPropertiesBody,
                        icon: iconPropertiesSection,
                    },
                };

                const section = sectionMap[sectionName];
                if (!section || !section.button || !section.body || !section.icon) return;

                section.body.classList.toggle('show', Boolean(isOpen));
                section.icon.classList.toggle('collapsed', !isOpen);
                section.button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }

            function initToolSectionToggles() {
                if (btnToggleAssignmentSection && sectionAssignmentBody) {
                    btnToggleAssignmentSection.addEventListener('click', () => {
                        const nextOpenState = !sectionAssignmentBody.classList.contains('show');
                        setToolSectionState('assignment', nextOpenState);
                    });
                    setToolSectionState('assignment', sectionAssignmentBody.classList.contains('show'));
                }

                if (btnTogglePropertiesSection && sectionPropertiesBody) {
                    btnTogglePropertiesSection.addEventListener('click', () => {
                        const nextOpenState = !sectionPropertiesBody.classList.contains('show');
                        setToolSectionState('properties', nextOpenState);
                    });
                    setToolSectionState('properties', sectionPropertiesBody.classList.contains('show'));
                }
            }

            // ============================================
            // 1. HELPER: PARSE NOMOR ($001 -> 001)
            // ============================================
            function parseCertificateNumber(templateStr, loopIndex) {
                // Regex: cari tanda $ diikuti angka (0-9)
                return templateStr.replace(/\$(\d+)/g, (match, digits) => {
                    const startNumber = parseInt(digits, 10);
                    const currentNumber = startNumber + loopIndex;

                    // Jika user nulis $001 (ada leading zero), pakai padding
                    if (digits.startsWith('0')) {
                        return currentNumber.toString().padStart(digits.length, '0');
                    }
                    return currentNumber.toString();
                });
            }

            function updateNomorPreview() {
                const raw = inputNomor.value;
                const result = parseCertificateNumber(raw, 0); // Preview index 0
                nomorPreview.innerHTML = `<i class="fas fa-eye mr-1"></i> Preview urutan ke-1: <b>${result}</b>`;
            }

            // Event listener live typing
            inputNomor.addEventListener('input', updateNomorPreview);

            let jsPdfLoadPromise = null;

            function hasJsPdfLibrary() {
                return Boolean(window.jspdf && window.jspdf.jsPDF);
            }

            function loadScriptWithTimeout(src, timeoutMs = 12000) {
                return new Promise((resolve, reject) => {
                    const script = document.createElement('script');
                    let finished = false;
                    const timer = setTimeout(() => {
                        if (finished) return;
                        finished = true;
                        script.remove();
                        reject(new Error(`Timeout memuat script: ${src}`));
                    }, timeoutMs);

                    script.src = src;
                    script.async = true;
                    script.onload = () => {
                        if (finished) return;
                        finished = true;
                        clearTimeout(timer);
                        resolve();
                    };
                    script.onerror = () => {
                        if (finished) return;
                        finished = true;
                        clearTimeout(timer);
                        script.remove();
                        reject(new Error(`Gagal memuat script: ${src}`));
                    };

                    document.head.appendChild(script);
                });
            }

            async function ensureJsPdfLibrary() {
                if (hasJsPdfLibrary()) return true;
                if (jsPdfLoadPromise) return jsPdfLoadPromise;

                const sources = [
                    'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js',
                    'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js',
                    'https://unpkg.com/jspdf@2.5.1/dist/jspdf.umd.min.js',
                ];

                jsPdfLoadPromise = (async () => {
                    for (const src of sources) {
                        try {
                            await loadScriptWithTimeout(src, 10000);
                        } catch (_) {
                            // Lanjut ke source berikutnya.
                        }
                        if (hasJsPdfLibrary()) {
                            return true;
                        }
                    }
                    return hasJsPdfLibrary();
                })();

                const ready = await jsPdfLoadPromise;
                if (!ready) {
                    jsPdfLoadPromise = null;
                }
                return ready;
            }

            async function ensureFontFamiliesLoaded(fontNames) {
                if (!document.fonts || typeof document.fonts.load !== 'function') return;
                const uniqueFonts = Array.from(new Set((fontNames || []).map((name) => normalizeFontName(name)).filter(Boolean)));
                await Promise.all(uniqueFonts.map(async (fontName) => {
                    const primaryFamily = getPrimaryFontFamily(fontName);
                    try {
                        await Promise.race([
                            document.fonts.load(`400 32px ${primaryFamily}`),
                            new Promise((resolve) => setTimeout(resolve, 800)),
                        ]);
                    } catch (_) {
                        // Abaikan jika browser gagal load, fallback font tetap berjalan.
                    }
                }));
            }

            function getUsedFontNamesFromState(assignmentSnapshot = null) {
                const mainFonts = Object.values(config).map((item) => normalizeFontName(item.font || 'Open Sans'));
                const assignmentFont = normalizeFontName((assignmentSnapshot?.table_settings?.font_family) || assignmentPage?.table_settings?.font_family || 'Open Sans');
                return [...mainFonts, assignmentFont];
            }

            async function loadImageFromFile(file) {
                return await new Promise((resolve, reject) => {
                    const objectUrl = URL.createObjectURL(file);
                    const image = new Image();
                    image.onload = () => resolve({ image, objectUrl });
                    image.onerror = () => {
                        URL.revokeObjectURL(objectUrl);
                        reject(new Error('Gagal memuat gambar.'));
                    };
                    image.src = objectUrl;
                });
            }

            function getAssignmentBackgroundImage() {
                if (assignmentPage.background_source === 'custom' && assignmentCustomBackgroundImage) {
                    return assignmentCustomBackgroundImage;
                }
                if (bgPreview.naturalWidth > 0) {
                    return bgPreview;
                }
                return null;
            }

            function normalizeAssignmentPage(rawPage) {
                const fallback = getDefaultAssignmentPage();
                if (!rawPage || typeof rawPage !== 'object') {
                    return fallback;
                }

                const normalizedColumns = Array.isArray(rawPage.columns) && rawPage.columns.length
                    ? rawPage.columns.map((col, idx) => {
                        if (typeof col === 'string' && col.trim()) return col.trim();
                        if (col && typeof col === 'object' && typeof col.label === 'string' && col.label.trim()) return col.label.trim();
                        return `Kolom ${idx + 1}`;
                    })
                    : fallback.columns.slice();

                const columns = normalizedColumns.length ? normalizedColumns : ['Kolom 1'];
                const sourceRows = Array.isArray(rawPage.rows) ? rawPage.rows : fallback.rows;
                const rows = sourceRows.map((row) => {
                    if (Array.isArray(row)) {
                        const cells = row.slice(0, columns.length).map((cell) => String(cell ?? ''));
                        while (cells.length < columns.length) cells.push('');
                        return cells;
                    }

                    if (row && typeof row === 'object') {
                        const cells = columns.map((_, idx) => {
                            const keyed = row[`col${idx + 1}`];
                            if (typeof keyed !== 'undefined') return String(keyed ?? '');
                            const indexed = row[idx];
                            if (typeof indexed !== 'undefined') return String(indexed ?? '');
                            return '';
                        });
                        return cells;
                    }

                    return columns.map(() => '');
                });

                const defaultWidths = columns.map((_, idx) => {
                    if (columns.length === 1) return 100;
                    if (idx === 0) return 45;
                    return 55 / (columns.length - 1);
                });
                const sourceColumnWidths = Array.isArray(rawPage.column_widths) ? rawPage.column_widths : [];
                const objectColumnWidths = Array.isArray(rawPage.columns)
                    ? rawPage.columns.map((col) => (col && typeof col === 'object' ? col.width : undefined))
                    : [];
                const columnWidths = columns.map((_, idx) => {
                    const preferred = Number.parseFloat(sourceColumnWidths[idx]);
                    const fallbackObj = Number.parseFloat(objectColumnWidths[idx]);
                    if (Number.isFinite(preferred) && preferred > 0) return preferred;
                    if (Number.isFinite(fallbackObj) && fallbackObj > 0) return fallbackObj;
                    return defaultWidths[idx];
                });

                const rawTableSettings = rawPage.table_settings && typeof rawPage.table_settings === 'object'
                    ? rawPage.table_settings
                    : {};
                const tableSettings = {
                    x: clampNumber(rawTableSettings.x, 0, 90, fallback.table_settings.x),
                    y: clampNumber(rawTableSettings.y, 0, 95, fallback.table_settings.y),
                    width: clampNumber(rawTableSettings.width, 10, 100, fallback.table_settings.width),
                    row_height: clampNumber(rawTableSettings.row_height, 1, 20, fallback.table_settings.row_height),
                    font_size: clampNumber(rawTableSettings.font_size, 8, 64, fallback.table_settings.font_size),
                    font_family: normalizeFontName(rawTableSettings.font_family, fallback.table_settings.font_family),
                    header_bg: typeof rawTableSettings.header_bg === 'string' && rawTableSettings.header_bg.trim() ? rawTableSettings.header_bg : fallback.table_settings.header_bg,
                    border_color: typeof rawTableSettings.border_color === 'string' && rawTableSettings.border_color.trim() ? rawTableSettings.border_color : fallback.table_settings.border_color,
                    text_color: typeof rawTableSettings.text_color === 'string' && rawTableSettings.text_color.trim() ? rawTableSettings.text_color : fallback.table_settings.text_color,
                    show_total: typeof rawTableSettings.show_total === 'boolean' ? rawTableSettings.show_total : fallback.table_settings.show_total,
                };

                // Restore posisi & visibilitas variabel teks dari template yang tersimpan
                if (rawPage.text_positions && typeof rawPage.text_positions === 'object') {
                    const rp = rawPage.text_positions;
                    window._assignTextPos = {
                        event_x:      typeof rp.event_x === 'number' ? rp.event_x : null,
                        event_y:      typeof rp.event_y === 'number' ? rp.event_y : null,
                        nama_enabled: Boolean(rp.nama_enabled),
                        nama_x:       typeof rp.nama_x  === 'number' ? rp.nama_x  : null,
                        nama_y:       typeof rp.nama_y  === 'number' ? rp.nama_y  : null,
                        npm_enabled:  Boolean(rp.npm_enabled),
                        npm_x:        typeof rp.npm_x   === 'number' ? rp.npm_x   : null,
                        npm_y:        typeof rp.npm_y   === 'number' ? rp.npm_y   : null,
                    };
                }

                return {
                    enabled: Boolean(rawPage.enabled),
                    background_source: rawPage.background_source === 'custom' ? 'custom' : 'main',
                    title: typeof rawPage.title === 'string' ? rawPage.title : fallback.title,
                    subtitle: typeof rawPage.subtitle === 'string' ? rawPage.subtitle : fallback.subtitle,
                    columns,
                    column_widths: columnWidths,
                    table_settings: tableSettings,
                    rows: rows.length ? rows : [columns.map(() => '')],
                };
            }

            function normalizeMainConfig(rawConfig) {
                const normalized = JSON.parse(JSON.stringify(defaultConfig));
                if (!rawConfig || typeof rawConfig !== 'object') {
                    return normalized;
                }

                Object.keys(normalized).forEach((key) => {
                    if (rawConfig[key] && typeof rawConfig[key] === 'object') {
                        normalized[key] = { ...normalized[key], ...rawConfig[key] };
                    }
                    normalized[key].font = normalizeFontName(normalized[key].font, defaultConfig[key].font || 'Open Sans');
                });
                return normalized;
            }

            function refreshAssignmentPanelState() {
                assignmentSettingsPanel.style.display = assignmentPage.enabled ? 'block' : 'none';
                toggleAssignmentPage.checked = assignmentPage.enabled;
                assignmentBgSourceMain.checked = assignmentPage.background_source !== 'custom';
                assignmentBgSourceCustom.checked = assignmentPage.background_source === 'custom';
                assignmentBgUploadWrap.style.display = assignmentPage.enabled && assignmentPage.background_source === 'custom' ? 'block' : 'none';
                refreshDesignerModeButtons();
            }

            function syncAssignmentMetaValues() {
                assignmentPage.title = assignmentTitle.value.trim();
                assignmentPage.subtitle = assignmentSubtitle.value.trim();
            }

            function clampNumber(value, min, max, fallback) {
                const number = Number.parseFloat(value);
                if (!Number.isFinite(number)) return fallback;
                if (number < min) return min;
                if (number > max) return max;
                return number;
            }

            function ensureAssignmentColumnWidthsShape() {
                const totalColumns = assignmentPage.columns.length;
                const sourceWidths = Array.isArray(assignmentPage.column_widths) ? assignmentPage.column_widths : [];

                assignmentPage.column_widths = assignmentPage.columns.map((_, idx) => {
                    const parsed = Number.parseFloat(sourceWidths[idx]);
                    if (Number.isFinite(parsed) && parsed > 0) return parsed;
                    if (totalColumns === 1) return 100;
                    if (idx === 0) return 45;
                    return 55 / (totalColumns - 1);
                });
            }

            function ensureAssignmentTableSettingsShape() {
                const fallback = getDefaultAssignmentPage().table_settings;
                const raw = assignmentPage.table_settings && typeof assignmentPage.table_settings === 'object'
                    ? assignmentPage.table_settings
                    : {};

                assignmentPage.table_settings = {
                    x: clampNumber(raw.x, 0, 90, fallback.x),
                    y: clampNumber(raw.y, 0, 95, fallback.y),
                    width: clampNumber(raw.width, 10, 100, fallback.width),
                    row_height: clampNumber(raw.row_height, 1, 20, fallback.row_height),
                    font_size: clampNumber(raw.font_size, 8, 64, fallback.font_size),
                    font_family: normalizeFontName(raw.font_family, fallback.font_family),
                    header_bg: typeof raw.header_bg === 'string' && raw.header_bg.trim() ? raw.header_bg : fallback.header_bg,
                    border_color: typeof raw.border_color === 'string' && raw.border_color.trim() ? raw.border_color : fallback.border_color,
                    text_color: typeof raw.text_color === 'string' && raw.text_color.trim() ? raw.text_color : fallback.text_color,
                    show_total: typeof raw.show_total === 'boolean' ? raw.show_total : fallback.show_total,
                };
            }

            function applyTableSettingsToForm() {
                ensureAssignmentTableSettingsShape();
                assignmentTableFontSize.value = assignmentPage.table_settings.font_size;
                assignmentTableFontFamily.value = normalizeFontName(assignmentPage.table_settings.font_family, 'Open Sans');
                assignmentTableHeaderBg.value = assignmentPage.table_settings.header_bg;
                assignmentTableBorderColor.value = assignmentPage.table_settings.border_color;
                assignmentTableTextColor.value = assignmentPage.table_settings.text_color;
                assignmentTableShowTotal.checked = assignmentPage.table_settings.show_total;
            }

            function syncTableSettingsFromForm() {
                const fallback = getDefaultAssignmentPage().table_settings;
                const current = assignmentPage.table_settings && typeof assignmentPage.table_settings === 'object'
                    ? assignmentPage.table_settings
                    : fallback;
                assignmentPage.table_settings = {
                    x: clampNumber(current.x, 0, 90, fallback.x),
                    y: clampNumber(current.y, 0, 95, fallback.y),
                    width: clampNumber(current.width, 10, 100, fallback.width),
                    row_height: clampNumber(current.row_height, 1, 20, fallback.row_height),
                    font_size: clampNumber(assignmentTableFontSize.value, 8, 64, current.font_size),
                    font_family: normalizeFontName(assignmentTableFontFamily.value, current.font_family),
                    header_bg: assignmentTableHeaderBg.value || fallback.header_bg,
                    border_color: assignmentTableBorderColor.value || fallback.border_color,
                    text_color: assignmentTableTextColor.value || fallback.text_color,
                    show_total: Boolean(assignmentTableShowTotal.checked),
                };
            }

            function ensureAssignmentRowsShape() {
                if (!Array.isArray(assignmentPage.columns) || !assignmentPage.columns.length) {
                    assignmentPage.columns = ['Kolom 1'];
                }
                ensureAssignmentColumnWidthsShape();
                ensureAssignmentTableSettingsShape();

                if (!Array.isArray(assignmentPage.rows) || !assignmentPage.rows.length) {
                    assignmentPage.rows = [assignmentPage.columns.map(() => '')];
                    return;
                }

                assignmentPage.rows = assignmentPage.rows.map((row) => {
                    const cells = Array.isArray(row) ? row.slice(0, assignmentPage.columns.length).map((cell) => String(cell ?? '')) : [];
                    while (cells.length < assignmentPage.columns.length) cells.push('');
                    return cells;
                });
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function renderAssignmentColumnsEditor() {
                ensureAssignmentColumnWidthsShape();
                assignmentColumnsEditor.innerHTML = assignmentPage.columns.map((label, idx) => {
                    const colWidth = Number.parseFloat(assignmentPage.column_widths[idx]);
                    const widthDisplay = Number.isFinite(colWidth) ? colWidth : 1;
                    return `
                    <div class="input-group input-group-sm mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text">${idx + 1}</span>
                        </div>
                        <input type="text" class="form-control" data-column-index="${idx}" value="${escapeHtml(label)}" placeholder="Nama Kolom">
                        <input type="number" class="form-control assignment-col-width" data-column-width-index="${idx}" value="${widthDisplay}" min="1" step="0.1" title="Lebar Kolom">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-danger" data-remove-column="${idx}" ${assignmentPage.columns.length <= 1 ? 'disabled' : ''}>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                }).join('');
            }

            function renderAssignmentRows() {
                ensureAssignmentRowsShape();
                assignmentRowsHead.innerHTML = `
                    <tr>
                        ${assignmentPage.columns.map((label) => `<th style="min-width: 140px;">${escapeHtml(label || 'Kolom')}</th>`).join('')}
                        <th style="width: 40px;">#</th>
                    </tr>
                `;

                assignmentRowsBody.innerHTML = assignmentPage.rows.map((row, idx) => `
                    <tr>
                        ${assignmentPage.columns.map((_, colIdx) => `
                            <td><input class="form-control form-control-sm" data-row-index="${idx}" data-col-index="${colIdx}" value="${escapeHtml(row[colIdx] ?? '')}"></td>
                        `).join('')}
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-xs btn-outline-danger" data-remove-row="${idx}">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
            }

            function applyAssignmentToForm() {
                assignmentPage = normalizeAssignmentPage(assignmentPage);
                assignmentTitle.value = assignmentPage.title;
                assignmentSubtitle.value = assignmentPage.subtitle;
                renderAssignmentColumnsEditor();
                renderAssignmentRows();
                applyTableSettingsToForm();
                refreshAssignmentPanelState();
            }

            function snapshotAssignmentPage() {
                syncAssignmentMetaValues();
                syncTableSettingsFromForm();
                ensureAssignmentRowsShape();
                const rows = assignmentPage.rows
                    .map((row) => assignmentPage.columns.map((_, idx) => String(row[idx] ?? '').trim()))
                    .filter((row) => row.some((cell) => cell !== ''));

                const _tp = window._assignTextPos || {};
                return {
                    enabled: assignmentPage.enabled,
                    background_source: assignmentPage.background_source === 'custom' ? 'custom' : 'main',
                    title: assignmentPage.title || 'Rekap JP / Point Assignment',
                    subtitle: assignmentPage.subtitle || '',
                    columns: assignmentPage.columns.map((col, idx) => (String(col ?? '').trim() || `Kolom ${idx + 1}`)),
                    column_widths: assignmentPage.column_widths.map((width) => clampNumber(width, 1, 1000, 1)),
                    table_settings: { ...assignmentPage.table_settings },
                    rows: rows.length ? rows : [assignmentPage.columns.map(() => '')],
                    text_positions: (_tp.event_x != null || _tp.nama_x != null || _tp.npm_x != null || _tp.nama_enabled || _tp.npm_enabled)
                        ? {
                            event_x: _tp.event_x, event_y: _tp.event_y,
                            nama_enabled: !!_tp.nama_enabled, nama_x: _tp.nama_x, nama_y: _tp.nama_y,
                            npm_enabled:  !!_tp.npm_enabled,  npm_x:  _tp.npm_x,  npm_y:  _tp.npm_y,
                          }
                        : null,
                };
            }

            function initAssignmentSettings() {
                applyAssignmentToForm();

                [assignmentTitle, assignmentSubtitle].forEach((input) => {
                    input.addEventListener('input', () => {
                        syncAssignmentMetaValues();
                        renderAssignmentDesignerPreview();
                    });
                });

                assignmentColumnsEditor.addEventListener('input', (event) => {
                    const target = event.target;
                    if (!(target instanceof HTMLInputElement)) return;

                    const widthIndex = Number(target.dataset.columnWidthIndex);
                    if (Number.isInteger(widthIndex) && widthIndex >= 0 && widthIndex < assignmentPage.columns.length) {
                        assignmentPage.column_widths[widthIndex] = clampNumber(target.value, 1, 1000, assignmentPage.column_widths[widthIndex] || 1);
                        renderAssignmentDesignerPreview();
                        return;
                    }

                    const colIndex = Number(target.dataset.columnIndex);
                    if (!Number.isInteger(colIndex) || colIndex < 0 || colIndex >= assignmentPage.columns.length) return;
                    assignmentPage.columns[colIndex] = target.value;
                    renderAssignmentRows();
                    renderAssignmentDesignerPreview();
                });

                [assignmentTableFontFamily, assignmentTableFontSize, assignmentTableHeaderBg, assignmentTableBorderColor, assignmentTableTextColor, assignmentTableShowTotal].forEach((input) => {
                    input.addEventListener('input', () => {
                        syncTableSettingsFromForm();
                        ensureFontFamiliesLoaded([assignmentPage.table_settings.font_family]);
                        applyTableSettingsToForm();
                        renderAssignmentDesignerPreview();
                    });
                    input.addEventListener('change', () => {
                        syncTableSettingsFromForm();
                        ensureFontFamiliesLoaded([assignmentPage.table_settings.font_family]);
                        applyTableSettingsToForm();
                        renderAssignmentDesignerPreview();
                    });
                });

                assignmentColumnsEditor.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-remove-column]');
                    if (!button) return;
                    const colIndex = Number(button.dataset.removeColumn);
                    if (!Number.isInteger(colIndex) || assignmentPage.columns.length <= 1) return;

                    assignmentPage.columns.splice(colIndex, 1);
                    assignmentPage.column_widths.splice(colIndex, 1);
                    assignmentPage.rows = assignmentPage.rows.map((row) => {
                        const cells = Array.isArray(row) ? row.slice() : [];
                        cells.splice(colIndex, 1);
                        while (cells.length < assignmentPage.columns.length) cells.push('');
                        return cells;
                    });

                    renderAssignmentColumnsEditor();
                    renderAssignmentRows();
                    renderAssignmentDesignerPreview();
                });

                toggleAssignmentPage.addEventListener('change', () => {
                    assignmentPage.enabled = toggleAssignmentPage.checked;
                    if (!assignmentPage.enabled && activeDesignerMode === 'assignment') {
                        activeDesignerMode = 'main';
                    }
                    refreshAssignmentPanelState();
                    refreshDesignerWorkspace();
                });

                assignmentBgSourceMain.addEventListener('change', () => {
                    if (!assignmentBgSourceMain.checked) return;
                    assignmentPage.background_source = 'main';
                    refreshAssignmentPanelState();
                    renderAssignmentDesignerPreview();
                });

                assignmentBgSourceCustom.addEventListener('change', () => {
                    if (!assignmentBgSourceCustom.checked) return;
                    assignmentPage.background_source = 'custom';
                    refreshAssignmentPanelState();
                    renderAssignmentDesignerPreview();
                });

                assignmentBgFile.addEventListener('change', async function() {
                    const file = this.files && this.files[0] ? this.files[0] : null;
                    assignmentBgFileLabel.textContent = file ? file.name : 'Pilih Gambar...';
                    if (!file) {
                        assignmentCustomBackgroundImage = null;
                        if (assignmentBgObjectUrl) {
                            URL.revokeObjectURL(assignmentBgObjectUrl);
                            assignmentBgObjectUrl = null;
                        }
                        renderAssignmentDesignerPreview();
                        return;
                    }

                    try {
                        const { image, objectUrl } = await loadImageFromFile(file);
                        if (assignmentBgObjectUrl) {
                            URL.revokeObjectURL(assignmentBgObjectUrl);
                        }
                        assignmentBgObjectUrl = objectUrl;
                        assignmentCustomBackgroundImage = image;
                        renderAssignmentDesignerPreview();
                    } catch (error) {
                        assignmentCustomBackgroundImage = null;
                        this.value = '';
                        assignmentBgFileLabel.textContent = 'Pilih Gambar...';
                        alert(error.message || 'Gagal memuat background JP / Assignment.');
                    }
                });

                btnAddAssignmentColumn.addEventListener('click', () => {
                    assignmentPage.columns.push(`Kolom ${assignmentPage.columns.length + 1}`);
                    assignmentPage.column_widths.push(20);
                    assignmentPage.rows = assignmentPage.rows.map((row) => {
                        const cells = Array.isArray(row) ? row.slice() : [];
                        cells.push('');
                        return cells;
                    });
                    renderAssignmentColumnsEditor();
                    renderAssignmentRows();
                    renderAssignmentDesignerPreview();
                });

                btnAddAssignmentRow.addEventListener('click', () => {
                    assignmentPage.rows.push(assignmentPage.columns.map(() => ''));
                    renderAssignmentRows();
                    renderAssignmentDesignerPreview();
                });

                assignmentRowsBody.addEventListener('input', (event) => {
                    const target = event.target;
                    if (!(target instanceof HTMLInputElement)) return;
                    const rowIndex = Number(target.dataset.rowIndex);
                    const colIndex = Number(target.dataset.colIndex);
                    if (!Number.isInteger(rowIndex) || !Number.isInteger(colIndex) || !assignmentPage.rows[rowIndex]) return;
                    assignmentPage.rows[rowIndex][colIndex] = target.value;
                    renderAssignmentDesignerPreview();
                });

                assignmentRowsBody.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-remove-row]');
                    if (!button) return;
                    const rowIndex = Number(button.dataset.removeRow);
                    if (!Number.isInteger(rowIndex)) return;
                    assignmentPage.rows.splice(rowIndex, 1);
                    if (assignmentPage.rows.length === 0) {
                        assignmentPage.rows.push(assignmentPage.columns.map(() => ''));
                    }
                    renderAssignmentRows();
                    renderAssignmentDesignerPreview();
                });

                modeMainDesigner.addEventListener('click', () => setDesignerMode('main'));
                modeAssignmentDesigner.addEventListener('click', () => setDesignerMode('assignment'));
            }

            function refreshDesignerModeButtons() {
                const assignmentEnabled = assignmentPage.enabled;
                modeAssignmentDesigner.disabled = !assignmentEnabled;

                modeMainDesigner.classList.toggle('btn-primary', activeDesignerMode === 'main');
                modeMainDesigner.classList.toggle('btn-outline-primary', activeDesignerMode !== 'main');
                modeAssignmentDesigner.classList.toggle('btn-primary', activeDesignerMode === 'assignment');
                modeAssignmentDesigner.classList.toggle('btn-outline-primary', activeDesignerMode !== 'assignment');
            }

            function setDesignerMode(mode) {
                if (mode === 'assignment' && !assignmentPage.enabled) {
                    return;
                }
                activeDesignerMode = mode === 'assignment' ? 'assignment' : 'main';
                refreshDesignerWorkspace();
            }

            function refreshDesignerWorkspace() {
                refreshDesignerModeButtons();
                const hasMainCanvas = bgPreview.naturalWidth > 0;
                editorContainer.classList.toggle('assignment-mode', activeDesignerMode === 'assignment');

                if (activeDesignerMode === 'assignment') {
                    editorPlaceholder.style.display = 'none';
                    canvasWrapper.style.display = 'none';
                    assignmentCanvasWrapper.style.display = 'block';
                    renderAssignmentDesignerPreview();
                    return;
                }

                assignmentCanvasWrapper.style.display = 'none';
                assignmentTableOverlay.style.display = 'none';
                if (hasMainCanvas) {
                    editorPlaceholder.style.display = 'none';
                    canvasWrapper.style.display = 'block';
                    updateVisualPositions();
                } else {
                    canvasWrapper.style.display = 'none';
                    editorPlaceholder.style.display = 'block';
                }
            }

            function getAssignmentPreviewParticipant() {
                const firstRow     = document.querySelector('tbody tr[id^="row-reg-"] td span.font-weight-bold');
                const firstNpmCell = document.querySelector('tbody tr[id^="row-reg-"] td:nth-child(2)');
                return {
                    nama: firstRow     ? firstRow.textContent.trim()     : 'Nama Peserta',
                    npm:  firstNpmCell ? firstNpmCell.textContent.trim() : '000000000',
                    role: 'Peserta',
                };
            }

            function renderAssignmentDesignerPreview() {
                if (!assignmentPage.enabled) {
                    assignmentTableOverlay.style.display = 'none';
                    return;
                }

                const assignmentBackground = getAssignmentBackgroundImage();
                const width = assignmentBackground ? (assignmentBackground.naturalWidth || assignmentBackground.width || 1600) : (ORIGINAL_WIDTH || bgPreview.naturalWidth || 1600);
                const height = assignmentBackground ? (assignmentBackground.naturalHeight || assignmentBackground.height || 1131) : (ORIGINAL_HEIGHT || bgPreview.naturalHeight || 1131);
                assignmentPreviewCanvas.width = width;
                assignmentPreviewCanvas.height = height;

                const assignmentSnapshot = snapshotAssignmentPage();
                const ctx = assignmentPreviewCanvas.getContext('2d');
                drawAssignmentPage(ctx, assignmentPreviewCanvas, getAssignmentPreviewParticipant(), assignmentSnapshot, assignmentBackground);
                refreshAssignmentTableOverlay(assignmentSnapshot);
            }

            function getAssignmentTableMetrics(canvasWidth, canvasHeight, assignmentConfig) {
                const marginX = Math.round(canvasWidth * 0.08);
                const marginY = Math.round(canvasHeight * 0.09);
                const bottomPadding = Math.round(canvasHeight * 0.05);
                const columns = Array.isArray(assignmentConfig.columns) && assignmentConfig.columns.length
                    ? assignmentConfig.columns.map((col, idx) => String(col ?? '').trim() || `Kolom ${idx + 1}`)
                    : ['Kolom 1'];

                const rows = (Array.isArray(assignmentConfig.rows) ? assignmentConfig.rows : []).map((row) => {
                    if (Array.isArray(row)) {
                        const cells = row.slice(0, columns.length).map((cell) => String(cell ?? ''));
                        while (cells.length < columns.length) cells.push('');
                        return cells;
                    }
                    if (row && typeof row === 'object') {
                        return columns.map((_, idx) => String(row[`col${idx + 1}`] ?? row[idx] ?? ''));
                    }
                    return columns.map(() => '');
                });

                const rawTableSettings = assignmentConfig.table_settings && typeof assignmentConfig.table_settings === 'object'
                    ? assignmentConfig.table_settings
                    : {};
                const tableSettings = {
                    x: clampNumber(rawTableSettings.x, 0, 90, 8),
                    y: clampNumber(rawTableSettings.y, 0, 95, 36),
                    width: clampNumber(rawTableSettings.width, 10, 100, 84),
                    row_height: clampNumber(rawTableSettings.row_height, 1, 20, 5.5),
                    font_size: clampNumber(rawTableSettings.font_size, 8, 64, 14),
                    font_family: normalizeFontName(rawTableSettings.font_family, 'Open Sans'),
                    header_bg: typeof rawTableSettings.header_bg === 'string' && rawTableSettings.header_bg.trim() ? rawTableSettings.header_bg : '#e9ecef',
                    border_color: typeof rawTableSettings.border_color === 'string' && rawTableSettings.border_color.trim() ? rawTableSettings.border_color : '#222222',
                    text_color: typeof rawTableSettings.text_color === 'string' && rawTableSettings.text_color.trim() ? rawTableSettings.text_color : '#111111',
                    show_total: typeof rawTableSettings.show_total === 'boolean' ? rawTableSettings.show_total : true,
                };

                const tableX = Math.round((tableSettings.x / 100) * canvasWidth);
                const maxTableWidth = Math.max(120, canvasWidth - tableX - Math.round(canvasWidth * 0.02));
                const tableWidth = Math.min(Math.round((tableSettings.width / 100) * canvasWidth), maxTableWidth);
                const tableY = Math.round((tableSettings.y / 100) * canvasHeight);
                const rowHeight = Math.max(24, Math.round((tableSettings.row_height / 100) * canvasHeight));

                const columnSourceWidths = Array.isArray(assignmentConfig.column_widths) ? assignmentConfig.column_widths : [];
                const columnWeights = columns.map((_, idx) => {
                    const custom = Number.parseFloat(columnSourceWidths[idx]);
                    if (Number.isFinite(custom) && custom > 0) return custom;
                    if (columns.length === 1) return 100;
                    if (idx === 0) return 45;
                    return 55 / (columns.length - 1);
                });
                const totalWeight = columnWeights.reduce((sum, item) => sum + item, 0) || 1;
                const columnWidths = columnWeights.map((weight) => Math.floor((weight / totalWeight) * tableWidth));
                const usedColumnWidth = columnWidths.reduce((sum, item) => sum + item, 0);
                columnWidths[columnWidths.length - 1] += (tableWidth - usedColumnWidth);

                let tableStartY = marginY;
                tableStartY += Math.round(canvasHeight * 0.05);  // Event & Tanggal header
                const _atpM = (typeof window !== 'undefined' && window._assignTextPos) || {};
                if (_atpM.nama_enabled) tableStartY += Math.round(canvasHeight * 0.028);
                if (_atpM.npm_enabled)  tableStartY += Math.round(canvasHeight * 0.028);
                tableStartY += Math.round(canvasHeight * 0.015); // padding sebelum tabel
                tableStartY = Math.max(tableStartY, tableY);

                const reservedTotalHeight = tableSettings.show_total ? rowHeight : 0;
                const maxRows = Math.max(1, Math.floor((canvasHeight - (tableStartY + rowHeight) - bottomPadding - reservedTotalHeight) / rowHeight));
                const visibleRowsCount = Math.min(rows.length, maxRows);
                const renderedRows = 1 + visibleRowsCount + (tableSettings.show_total ? 1 : 0);
                const tableRenderedHeight = renderedRows * rowHeight;

                return {
                    columns,
                    rows,
                    tableSettings,
                    tableX,
                    tableY,
                    tableStartY,
                    tableWidth,
                    rowHeight,
                    columnWeights,
                    columnWidths,
                    totalWeight,
                    tableRenderedHeight,
                };
            }

            function refreshAssignmentTableOverlay(assignmentSnapshot = null) {
                if (!assignmentPage.enabled || activeDesignerMode !== 'assignment') {
                    assignmentTableOverlay.style.display = 'none';
                    return;
                }
                if (!assignmentPreviewCanvas.width || !assignmentPreviewCanvas.height) {
                    assignmentTableOverlay.style.display = 'none';
                    return;
                }

                const snapshot = assignmentSnapshot || snapshotAssignmentPage();
                const metrics = getAssignmentTableMetrics(assignmentPreviewCanvas.width, assignmentPreviewCanvas.height, snapshot);
                const displayWidth = assignmentPreviewCanvas.clientWidth || assignmentPreviewCanvas.width;
                const displayHeight = assignmentPreviewCanvas.clientHeight || assignmentPreviewCanvas.height;
                if (!displayWidth || !displayHeight) {
                    assignmentTableOverlay.style.display = 'none';
                    return;
                }

                const scaleX = displayWidth / assignmentPreviewCanvas.width;
                const scaleY = displayHeight / assignmentPreviewCanvas.height;
                assignmentTableOverlay.style.display = 'block';
                assignmentTableOverlay.style.left = `${Math.round(metrics.tableX * scaleX)}px`;
                assignmentTableOverlay.style.top = `${Math.round(metrics.tableStartY * scaleY)}px`;
                assignmentTableOverlay.style.width = `${Math.max(80, Math.round(metrics.tableWidth * scaleX))}px`;
                assignmentTableOverlay.style.height = `${Math.max(28, Math.round(metrics.tableRenderedHeight * scaleY))}px`;

                let columnCursor = 0;
                assignmentColumnHandles.innerHTML = metrics.columnWidths.slice(0, -1).map((width, idx) => {
                    columnCursor += width;
                    return `<div class="assignment-column-handle" data-boundary-index="${idx + 1}" style="left:${Math.round(columnCursor * scaleX)}px;"></div>`;
                }).join('');
            }

            function initAssignmentVisualOverlay() {
                assignmentTableOverlay.addEventListener('mousedown', (event) => {
                    if (activeDesignerMode !== 'assignment' || !assignmentPage.enabled) return;

                    const displayWidth = assignmentPreviewCanvas.clientWidth || assignmentPreviewCanvas.width;
                    const displayHeight = assignmentPreviewCanvas.clientHeight || assignmentPreviewCanvas.height;
                    if (!displayWidth || !displayHeight) return;

                    const assignmentSnapshot = snapshotAssignmentPage();
                    const metrics = getAssignmentTableMetrics(assignmentPreviewCanvas.width, assignmentPreviewCanvas.height, assignmentSnapshot);
                    const columnHandle = event.target.closest('.assignment-column-handle');
                    const actionHandle = event.target.closest('[data-overlay-action]');

                    let action = 'move';
                    let boundaryIndex = null;
                    if (columnHandle) {
                        action = 'column-resize';
                        boundaryIndex = Number(columnHandle.dataset.boundaryIndex);
                    } else if (actionHandle) {
                        action = actionHandle.dataset.overlayAction || 'move';
                    }

                    assignmentOverlayState = {
                        action,
                        boundaryIndex,
                        startClientX: event.clientX,
                        startClientY: event.clientY,
                        canvasWidth: assignmentPreviewCanvas.width,
                        canvasHeight: assignmentPreviewCanvas.height,
                        displayWidth,
                        displayHeight,
                        metrics,
                        startTableSettings: { ...assignmentPage.table_settings },
                        startColumnWidths: Array.isArray(assignmentPage.column_widths) ? assignmentPage.column_widths.slice() : [],
                    };

                    document.body.style.userSelect = 'none';
                    document.body.style.cursor = action === 'resize'
                        ? 'nwse-resize'
                        : action === 'row-height'
                            ? 'ns-resize'
                            : action === 'column-resize'
                                ? 'col-resize'
                                : 'move';

                    event.preventDefault();
                });

                document.addEventListener('mousemove', (event) => {
                    if (!assignmentOverlayState) return;

                    const state = assignmentOverlayState;
                    const deltaCanvasX = ((event.clientX - state.startClientX) / (state.displayWidth || 1)) * state.canvasWidth;
                    const deltaCanvasY = ((event.clientY - state.startClientY) / (state.displayHeight || 1)) * state.canvasHeight;

                    if (state.action === 'move') {
                        const newXPercent = (state.metrics.tableX + deltaCanvasX) / state.canvasWidth * 100;
                        const newYPercent = (state.metrics.tableStartY + deltaCanvasY) / state.canvasHeight * 100;
                        assignmentPage.table_settings.x = clampNumber(newXPercent, 0, 95, state.startTableSettings.x);
                        assignmentPage.table_settings.y = clampNumber(newYPercent, 0, 95, state.startTableSettings.y);
                        applyTableSettingsToForm();
                    } else if (state.action === 'resize') {
                        const newWidthPercent = (state.metrics.tableWidth + deltaCanvasX) / state.canvasWidth * 100;
                        assignmentPage.table_settings.width = clampNumber(newWidthPercent, 10, 100, state.startTableSettings.width);
                        applyTableSettingsToForm();
                    } else if (state.action === 'row-height') {
                        const newRowHeightPercent = (state.metrics.rowHeight + deltaCanvasY) / state.canvasHeight * 100;
                        assignmentPage.table_settings.row_height = clampNumber(newRowHeightPercent, 1, 20, state.startTableSettings.row_height);
                        applyTableSettingsToForm();
                    } else if (state.action === 'column-resize' && Number.isInteger(state.boundaryIndex)) {
                        const rightIndex = state.boundaryIndex;
                        const leftIndex = rightIndex - 1;
                        if (leftIndex < 0 || rightIndex >= state.metrics.columnWidths.length) return;

                        const tableWidth = state.metrics.tableWidth;
                        const leftStartPx = state.metrics.columnWidths[leftIndex];
                        const rightStartPx = state.metrics.columnWidths[rightIndex];
                        const mergedWidth = leftStartPx + rightStartPx;
                        const minColumnPx = Math.max(20, Math.round(tableWidth * 0.04));
                        const newLeftPx = clampNumber(leftStartPx + deltaCanvasX, minColumnPx, mergedWidth - minColumnPx, leftStartPx);
                        const newRightPx = mergedWidth - newLeftPx;
                        const totalWeight = state.metrics.totalWeight;

                        const nextWidths = state.startColumnWidths.slice();
                        nextWidths[leftIndex] = (newLeftPx / tableWidth) * totalWeight;
                        nextWidths[rightIndex] = (newRightPx / tableWidth) * totalWeight;
                        assignmentPage.column_widths = nextWidths;
                    }

                    renderAssignmentDesignerPreview();
                    event.preventDefault();
                });

                document.addEventListener('mouseup', () => {
                    if (!assignmentOverlayState) return;

                    const lastAction = assignmentOverlayState.action;
                    assignmentOverlayState = null;
                    document.body.style.userSelect = '';
                    document.body.style.cursor = '';

                    if (lastAction === 'column-resize') {
                        renderAssignmentColumnsEditor();
                    } else {
                        applyTableSettingsToForm();
                    }
                    renderAssignmentDesignerPreview();
                });
            }

            // ============================================
            // 2. LOGIC EDITOR (VISUAL)
            // ============================================

            // A. Upload Background
            bgFile.addEventListener('change', async function() {
                const file = this.files && this.files[0] ? this.files[0] : null;
                bgFileLabel.textContent = file ? file.name : 'Pilih Gambar...';
                if (!file) {
                    if (mainBgObjectUrl) {
                        URL.revokeObjectURL(mainBgObjectUrl);
                        mainBgObjectUrl = null;
                    }
                    bgPreview.src = '#';
                    ORIGINAL_WIDTH = 0;
                    ORIGINAL_HEIGHT = 0;
                    refreshDesignerWorkspace();
                    renderAssignmentDesignerPreview();
                    return;
                }

                try {
                    const { image, objectUrl } = await loadImageFromFile(file);
                    if (mainBgObjectUrl) {
                        URL.revokeObjectURL(mainBgObjectUrl);
                    }
                    mainBgObjectUrl = objectUrl;
                    ORIGINAL_WIDTH = image.naturalWidth;
                    ORIGINAL_HEIGHT = image.naturalHeight;
                    bgPreview.src = objectUrl;
                    refreshDesignerWorkspace();
                    renderAssignmentDesignerPreview();
                } catch (error) {
                    this.value = '';
                    bgFileLabel.textContent = 'Pilih Gambar...';
                    alert(error.message || 'Gagal memuat background utama.');
                }
            });

            window.addEventListener('resize', () => {
                if (activeDesignerMode === 'assignment') {
                    renderAssignmentDesignerPreview();
                    return;
                }
                updateVisualPositions();
            });
            bgPreview.addEventListener('load', () => {
                refreshDesignerWorkspace();
                renderAssignmentDesignerPreview();
            });

            // B. Update CSS Posisi Elemen
            function updateVisualPositions() {
                if(bgPreview.naturalWidth === 0) return;
                const currentWidth = bgPreview.clientWidth;
                SCALE = currentWidth / bgPreview.naturalWidth;

                ['nama', 'role', 'nomor', 'deskripsi'].forEach(key => {
                    const el = document.getElementById('el_' + key);
                    const data = config[key];

                    // Posisi (Persentase)
                    el.style.left = (data.x) + '%';
                    el.style.top  = (data.y) + '%';
                    el.style.transform = 'translate(-50%, -50%)';

                    // Styling
                    el.style.fontSize = (data.size * SCALE) + 'px';
                    el.style.color = data.color;
                    el.style.textAlign = data.align;
                    el.style.fontFamily = getFontStack(data.font);
                    el.style.fontWeight = data.bold ? 'bold' : 'normal';
                    el.style.fontStyle = data.italic ? 'italic' : 'normal';

                    // Khusus deskripsi (multiline wrapping)
                    if(key === 'deskripsi') {
                        el.style.width = (data.width || 60) + '%';
                        el.style.whiteSpace = 'normal';
                    }
                });
            }

            // C. Drag Logic
            const draggables = document.querySelectorAll('.draggable-item');
            let isDragging = false;
            let currentDrag = null;
            let startX, startY, startLeft, startTop;

            draggables.forEach(el => {
                el.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    isDragging = true;
                    currentDrag = el;
                    setActiveElement(el.dataset.key);

                    startX = e.clientX;
                    startY = e.clientY;
                    startLeft = parseFloat(el.style.left); // percentage string
                    startTop  = parseFloat(el.style.top);  // percentage string
                });
            });

            document.addEventListener('mousemove', (e) => {
                if (!isDragging || !currentDrag) return;

                const wrapperRect = canvasWrapper.getBoundingClientRect();
                const dx = e.clientX - startX;
                const dy = e.clientY - startY;

                // Konversi gerakan pixel mouse ke persentase canvas
                const dxPercent = (dx / wrapperRect.width) * 100;
                const dyPercent = (dy / wrapperRect.height) * 100;

                const key = currentDrag.dataset.key;
                config[key].x = startLeft + dxPercent;
                config[key].y = startTop + dyPercent;

                updateVisualPositions();
            });

            document.addEventListener('mouseup', () => {
                isDragging = false;
                currentDrag = null;
            });


            // D. Property Editing
            function setActiveElement(key) {
                activeElementKey = key;
                const data = config[key];

                // Visual Highlight
                document.querySelectorAll('.draggable-item').forEach(el => el.classList.remove('active'));
                document.getElementById('el_' + key).classList.add('active');

                // Tampilkan Panel
                propPlaceholder.style.display = 'none';
                propPanel.style.display = 'block';
                elNameDisplay.innerText = key.toUpperCase();
                setToolSectionState('properties', true);

                // Isi Form
                propSize.value = data.size;
                propColor.value = data.color;
                propAlign.value = data.align;
                propFont.value = normalizeFontName(data.font, 'Open Sans');
                propBold.checked = data.bold;
                propItalic.checked = data.italic;
            }

            // Bind Event Form ke Config
            [propSize, propColor, propAlign, propFont, propBold, propItalic].forEach(input => {
                input.addEventListener('input', () => {
                    if(!activeElementKey) return;
                    config[activeElementKey].size = parseInt(propSize.value);
                    config[activeElementKey].color = propColor.value;
                    config[activeElementKey].align = propAlign.value;
                    config[activeElementKey].font = normalizeFontName(propFont.value, config[activeElementKey].font || 'Open Sans');
                    config[activeElementKey].bold = propBold.checked;
                    config[activeElementKey].italic = propItalic.checked;
                    ensureFontFamiliesLoaded([config[activeElementKey].font]);

                    updateVisualPositions();
                });
            });


            // ============================================
            // 3. GENERATE LOGIC
            // ============================================
            btnGenerate.addEventListener('click', async () => {
                // Validasi Awal
                if(!bgFile.files[0]) return alert("Harap upload background sertifikat terlebih dahulu!");
                if(!confirm("Mulai generate sertifikat untuk semua peserta yang HADIR?\nProses ini tidak bisa dibatalkan.")) return;

                const assignmentSnapshot = snapshotAssignmentPage();
                if (assignmentSnapshot.enabled) {
                    const pdfReady = await ensureJsPdfLibrary();
                    if (!pdfReady) {
                        return alert("Library PDF gagal dimuat. Periksa koneksi internet lalu coba lagi.");
                    }
                }
                await ensureFontFamiliesLoaded(getUsedFontNamesFromState(assignmentSnapshot));

                // UI Loading
                const logArea = document.getElementById('logArea');
                const progressBar = document.getElementById('progressBar');
                document.getElementById('consoleFooter').style.display = 'block';

                logArea.innerHTML = "> Memulai proses generate...<br>";
                btnGenerate.disabled = true;

                // 1. Load Background Image High-Res ke Memory
                let mainTempObjectUrl = null;
                let assignmentTempObjectUrl = null;
                let bgImg = null;
                let assignmentBgImage = null;
                const cleanupTempUrls = () => {
                    if (mainTempObjectUrl) {
                        URL.revokeObjectURL(mainTempObjectUrl);
                        mainTempObjectUrl = null;
                    }
                    if (assignmentTempObjectUrl) {
                        URL.revokeObjectURL(assignmentTempObjectUrl);
                        assignmentTempObjectUrl = null;
                    }
                };

                try {
                    const mainLoaded = await loadImageFromFile(bgFile.files[0]);
                    mainTempObjectUrl = mainLoaded.objectUrl;
                    bgImg = mainLoaded.image;

                    if (assignmentSnapshot.enabled) {
                        if (assignmentSnapshot.background_source === 'custom') {
                            if (!(assignmentBgFile.files && assignmentBgFile.files[0])) {
                                cleanupTempUrls();
                                btnGenerate.disabled = false;
                                return alert("Upload background khusus JP / Assignment terlebih dahulu.");
                            }
                            const assignmentLoaded = await loadImageFromFile(assignmentBgFile.files[0]);
                            assignmentTempObjectUrl = assignmentLoaded.objectUrl;
                            assignmentBgImage = assignmentLoaded.image;
                        } else {
                            assignmentBgImage = bgImg;
                        }
                    }
                } catch (error) {
                    cleanupTempUrls();
                    btnGenerate.disabled = false;
                    return alert(error.message || "Gagal memuat background.");
                }

                // 2. Siapkan Canvas di Memory
                const canvasMain = document.createElement('canvas');
                canvasMain.width = bgImg.naturalWidth;
                canvasMain.height = bgImg.naturalHeight;
                const ctxMain = canvasMain.getContext('2d');

                // 3. Ambil Peserta Hadir via API
                let participants = [];
                try {
                    const res = await fetch("/admin/api/certificates/event/{{ $event->id }}"); // Pastikan route ini return JSON peserta hadir
                    const json = await res.json();
                    participants = json.participants || [];
                } catch(e) {
                    alert("Gagal mengambil data peserta: " + e.message);
                    btnGenerate.disabled = false;
                    cleanupTempUrls();
                    return;
                }

                if(participants.length === 0) {
                    logArea.innerHTML += "<span class='text-danger'>Tidak ada peserta hadir (Attended/Present) untuk digenerate.</span><br>";
                    btnGenerate.disabled = false;
                    cleanupTempUrls();
                    return;
                }

                logArea.innerHTML += `> Ditemukan ${participants.length} peserta hadir.<br>`;

                // 4. Loop Generate
                let done = 0;
                for(const p of participants) {
                    try {
                        // Bersihkan Canvas & Gambar Background
                        ctxMain.clearRect(0, 0, canvasMain.width, canvasMain.height);
                        ctxMain.drawImage(bgImg, 0, 0, canvasMain.width, canvasMain.height);

                        // Render Setiap Elemen Text
                        ['nama', 'role', 'nomor', 'deskripsi'].forEach(key => {
                            const setting = config[key];
                            let textToRender = "";

                            // Tentukan Isi Teks
                            if(key === 'nama')      textToRender = p.nama;
                            else if(key === 'role') textToRender = p.role;
                            else if(key === 'nomor') {
                                // === LOGIKA AUTO INCREMENT ===
                                textToRender = parseCertificateNumber(inputNomor.value, done);
                            }
                            else if(key === 'deskripsi') textToRender = inputDeskripsi.value;

                            // Setup Font Context
                            const fontStyle = setting.italic ? 'italic' : '';
                            const fontWeight = setting.bold ? 'bold' : 'normal';
                            // Note: size harus dalam pixel asli canvas, bukan scale
                            const fontFamily = getFontStack(setting.font);
                            ctxMain.font = `${fontStyle} ${fontWeight} ${setting.size}px ${fontFamily}`;
                            ctxMain.fillStyle = setting.color;
                            ctxMain.textAlign = setting.align;
                            ctxMain.textBaseline = 'middle'; // Agar positioning y center lebih akurat

                            // Kalkulasi Posisi Pixel (Dari Persentase)
                            const xPx = (setting.x / 100) * canvasMain.width;
                            const yPx = (setting.y / 100) * canvasMain.height;

                            // Render Text
                            if(key === 'deskripsi') {
                                // Handle Multiline wrapping
                                const maxWidth = (setting.width / 100) * canvasMain.width;
                                wrapText(ctxMain, textToRender, xPx, yPx, maxWidth, setting.size * 1.2);
                            } else {
                                // Single line
                                ctxMain.fillText(textToRender, xPx, yPx);
                            }
                        });

                        let uploadBlob;
                        let uploadFilename;

                        if (assignmentSnapshot.enabled) {
                            const canvasAssignment = document.createElement('canvas');
                            canvasAssignment.width = assignmentBgImage ? assignmentBgImage.naturalWidth : canvasMain.width;
                            canvasAssignment.height = assignmentBgImage ? assignmentBgImage.naturalHeight : canvasMain.height;
                            const ctxAssignment = canvasAssignment.getContext('2d');
                            drawAssignmentPage(ctxAssignment, canvasAssignment, p, assignmentSnapshot, assignmentBgImage);

                            const pdf = new window.jspdf.jsPDF({
                                orientation: canvasMain.width >= canvasMain.height ? 'landscape' : 'portrait',
                                unit: 'px',
                                format: [canvasMain.width, canvasMain.height],
                            });
                            pdf.addImage(canvasMain.toDataURL('image/jpeg', 0.92), 'JPEG', 0, 0, canvasMain.width, canvasMain.height);
                            const assignmentOrientation = canvasAssignment.width >= canvasAssignment.height ? 'landscape' : 'portrait';
                            pdf.addPage([canvasAssignment.width, canvasAssignment.height], assignmentOrientation);
                            pdf.addImage(canvasAssignment.toDataURL('image/jpeg', 0.92), 'JPEG', 0, 0, canvasAssignment.width, canvasAssignment.height);
                            uploadBlob = pdf.output('blob');
                            uploadFilename = `cert_${p.user_id}.pdf`;
                        } else {
                            uploadBlob = await new Promise(r => canvasMain.toBlob(r, 'image/jpeg', 0.9));
                            uploadFilename = `cert_${p.user_id}.jpg`;
                        }
                        if (!uploadBlob) throw new Error("Gagal membangun file sertifikat");

                        // 6. Upload ke Server
                        const formData = new FormData();
                        formData.append("event_id", "{{ $event->id }}");
                        formData.append("user_id",  p.user_id);
                        formData.append("file", uploadBlob, uploadFilename);

                        const uploadRes = await fetch("/admin/api/certificates/upload", {
                            method: "POST",
                            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            body: formData
                        });

                        if(!uploadRes.ok) throw new Error("Server error");

                        // Update Log
                        logArea.innerHTML += `<span class="text-success small">[OK] ${p.nama}</span><br>`;

                    } catch(err) {
                        console.error(err);
                        logArea.innerHTML += `<span class="text-danger small">[ERROR] ${p.nama}</span><br>`;
                    }

                    // Update Progress Bar
                    done++;
                    const pct = Math.round((done / participants.length) * 100) + "%";
                    progressBar.style.width = pct;
                    progressBar.innerText = pct;
                    logArea.scrollTop = logArea.scrollHeight;
                }

                cleanupTempUrls();
                alert("Proses Generate Selesai!");
                location.reload(); // Refresh halaman untuk melihat link download baru
            });


            // ============================================
            // 4. UTILS: TEXT WRAPPING (CANVAS)
            // ============================================
            function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
                const words = text.split(' ');
                let line = '';
                let lines = [];

                for(let n = 0; n < words.length; n++) {
                    const testLine = line + words[n] + ' ';
                    const metrics = ctx.measureText(testLine);
                    if (metrics.width > maxWidth && n > 0) {
                        lines.push(line);
                        line = words[n] + ' ';
                    } else {
                        line = testLine;
                    }
                }
                lines.push(line);

                // Center vertical block adjustment
                const totalHeight = lines.length * lineHeight;
                let currentY = y - (totalHeight / 2) + (lineHeight/2);

                for(let i=0; i<lines.length; i++) {
                    ctx.fillText(lines[i], x, currentY);
                    currentY += lineHeight;
                }
            }

            function parseNumericValue(value) {
                const cleaned = String(value ?? '').replace(/[^0-9,.-]/g, '').replace(',', '.');
                const num = Number.parseFloat(cleaned);
                return Number.isFinite(num) ? num : 0;
            }

            function formatNumericTotal(value) {
                if (!Number.isFinite(value)) return '';
                if (Number.isInteger(value)) return value.toString();
                return value.toFixed(2).replace(/\.?0+$/, '');
            }

            function drawWrappedCellText(ctx, text, x, y, width, height, align) {
                const value = String(text ?? '');
                const maxTextWidth = width - 16;
                const words = value.split(' ');
                const lines = [];
                let line = '';

                words.forEach((word) => {
                    const test = (line ? line + ' ' : '') + word;
                    if (ctx.measureText(test).width > maxTextWidth && line) {
                        lines.push(line);
                        line = word;
                    } else {
                        line = test;
                    }
                });
                if (line) lines.push(line);

                const maxLines = Math.max(1, Math.floor((height - 10) / 16));
                const visibleLines = lines.slice(0, maxLines);
                if (lines.length > maxLines) {
                    const last = visibleLines[visibleLines.length - 1];
                    visibleLines[visibleLines.length - 1] = last.slice(0, Math.max(0, last.length - 3)) + '...';
                }

                const totalHeight = visibleLines.length * 16;
                let currentY = y + ((height - totalHeight) / 2) + 8;

                visibleLines.forEach((lineText) => {
                    if (align === 'center') {
                        ctx.textAlign = 'center';
                        ctx.fillText(lineText, x + (width / 2), currentY);
                    } else {
                        ctx.textAlign = 'left';
                        ctx.fillText(lineText, x + 8, currentY);
                    }
                    currentY += 16;
                });
            }

            function drawAssignmentPage(ctx, canvas, participant, assignmentConfig, backgroundImage = null) {
                const width = canvas.width;
                const height = canvas.height;
                const marginX = Math.round(width * 0.08);
                const marginY = Math.round(height * 0.09);
                const bottomPadding = Math.round(height * 0.05);
                const columns = Array.isArray(assignmentConfig.columns) && assignmentConfig.columns.length
                    ? assignmentConfig.columns.map((col, idx) => String(col ?? '').trim() || `Kolom ${idx + 1}`)
                    : ['Kolom 1'];
                const rawTableSettings = assignmentConfig.table_settings && typeof assignmentConfig.table_settings === 'object'
                    ? assignmentConfig.table_settings
                    : {};
                const tableSettings = {
                    x: clampNumber(rawTableSettings.x, 0, 90, 8),
                    y: clampNumber(rawTableSettings.y, 0, 95, 36),
                    width: clampNumber(rawTableSettings.width, 10, 100, 84),
                    row_height: clampNumber(rawTableSettings.row_height, 1, 20, 5.5),
                    font_size: clampNumber(rawTableSettings.font_size, 8, 64, 14),
                    font_family: normalizeFontName(rawTableSettings.font_family, 'Open Sans'),
                    header_bg: typeof rawTableSettings.header_bg === 'string' && rawTableSettings.header_bg.trim() ? rawTableSettings.header_bg : '#e9ecef',
                    border_color: typeof rawTableSettings.border_color === 'string' && rawTableSettings.border_color.trim() ? rawTableSettings.border_color : '#222222',
                    text_color: typeof rawTableSettings.text_color === 'string' && rawTableSettings.text_color.trim() ? rawTableSettings.text_color : '#111111',
                    show_total: typeof rawTableSettings.show_total === 'boolean' ? rawTableSettings.show_total : true,
                };

                const rows = (Array.isArray(assignmentConfig.rows) ? assignmentConfig.rows : []).map((row) => {
                    if (Array.isArray(row)) {
                        const cells = row.slice(0, columns.length).map((cell) => String(cell ?? ''));
                        while (cells.length < columns.length) cells.push('');
                        return cells;
                    }

                    if (row && typeof row === 'object') {
                        return columns.map((_, idx) => String(row[`col${idx + 1}`] ?? row[idx] ?? ''));
                    }

                    return columns.map(() => '');
                });

                const tableX = Math.round((tableSettings.x / 100) * width);
                const maxTableWidth = Math.max(120, width - tableX - Math.round(width * 0.02));
                const tableWidth = Math.min(Math.round((tableSettings.width / 100) * width), maxTableWidth);
                const tableY = Math.round((tableSettings.y / 100) * height);
                const rowHeight = Math.max(24, Math.round((tableSettings.row_height / 100) * height));
                const columnSourceWidths = Array.isArray(assignmentConfig.column_widths) ? assignmentConfig.column_widths : [];
                const columnWeights = columns.map((_, idx) => {
                    const custom = Number.parseFloat(columnSourceWidths[idx]);
                    if (Number.isFinite(custom) && custom > 0) return custom;
                    if (columns.length === 1) return 100;
                    if (idx === 0) return 45;
                    return 55 / (columns.length - 1);
                });
                const totalWeight = columnWeights.reduce((sum, item) => sum + item, 0) || 1;
                const columnWidths = columnWeights.map((weight) => Math.floor((weight / totalWeight) * tableWidth));
                const usedColumnWidth = columnWidths.reduce((sum, item) => sum + item, 0);
                columnWidths[columnWidths.length - 1] += (tableWidth - usedColumnWidth);
                const tableFontSize = Math.max(8, Math.round(tableSettings.font_size));
                const assignmentFontFamily = getFontStack(tableSettings.font_family);

                const drawGridRow = (y) => {
                    ctx.strokeRect(tableX, y, tableWidth, rowHeight);
                    let currentX = tableX;
                    for (let i = 0; i < columnWidths.length - 1; i++) {
                        currentX += columnWidths[i];
                        ctx.beginPath();
                        ctx.moveTo(currentX, y);
                        ctx.lineTo(currentX, y + rowHeight);
                        ctx.stroke();
                    }
                };

                if (backgroundImage) {
                    ctx.drawImage(backgroundImage, 0, 0, width, height);
                } else {
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, width, height);
                }

                let cursorY = marginY;
                ctx.textBaseline = 'top';
                ctx.fillStyle = tableSettings.text_color;
                const _atp = (typeof window !== 'undefined' && window._assignTextPos) || {};

                // ── Event & Tanggal (header utama, selalu tampil) ──────────────────
                const _evX = _atp.event_x != null ? Math.round(_atp.event_x * width  / 100) : (width / 2);
                const _evY = _atp.event_y != null ? Math.round(_atp.event_y * height / 100) : cursorY;
                ctx.textAlign = _atp.event_x != null ? 'left' : 'center';
                ctx.font = `700 ${Math.max(24, Math.round(width * 0.018))}px ${assignmentFontFamily}`;
                ctx.fillText(`${eventName}  |  ${eventDate}`, _evX, _evY);
                cursorY += Math.round(height * 0.05);

                ctx.textAlign = 'left';
                ctx.font = `600 ${Math.max(14, Math.round(width * 0.0105))}px ${assignmentFontFamily}`;

                // ── Nama (variabel opsional) ──────────────────────────────────────
                if (_atp.nama_enabled) {
                    const _namaX = _atp.nama_x != null ? Math.round(_atp.nama_x * width  / 100) : marginX;
                    const _namaY = _atp.nama_y != null ? Math.round(_atp.nama_y * height / 100) : cursorY;
                    ctx.fillText(`Nama: ${participant.nama || '-'}`, _namaX, _namaY);
                    if (_atp.nama_x == null) cursorY += Math.round(height * 0.028);
                }

                // ── NPM (variabel opsional) ───────────────────────────────────────
                if (_atp.npm_enabled) {
                    const _npmX = _atp.npm_x != null ? Math.round(_atp.npm_x * width  / 100) : marginX;
                    const _npmY = _atp.npm_y != null ? Math.round(_atp.npm_y * height / 100) : cursorY;
                    ctx.fillText(`NPM: ${participant.npm || '-'}`, _npmX, _npmY);
                    if (_atp.npm_x == null) cursorY += Math.round(height * 0.028);
                }

                cursorY += Math.round(height * 0.015);
                cursorY = Math.max(cursorY, tableY);

                ctx.fillStyle = tableSettings.header_bg;
                ctx.fillRect(tableX, cursorY, tableWidth, rowHeight);
                ctx.strokeStyle = tableSettings.border_color;
                ctx.lineWidth = 1;
                drawGridRow(cursorY);

                ctx.fillStyle = tableSettings.text_color;
                ctx.font = `700 ${Math.max(10, tableFontSize)}px ${assignmentFontFamily}`;
                let currentX = tableX;
                columns.forEach((label, idx) => {
                    const cellWidth = columnWidths[idx];
                    drawWrappedCellText(ctx, label, currentX, cursorY, cellWidth, rowHeight, idx === 0 ? 'left' : 'center');
                    currentX += cellWidth;
                });

                cursorY += rowHeight;
                ctx.font = `${Math.max(9, tableFontSize - 1)}px ${assignmentFontFamily}`;

                const reservedTotalHeight = tableSettings.show_total ? rowHeight : 0;
                const maxRows = Math.max(1, Math.floor((height - cursorY - bottomPadding - reservedTotalHeight) / rowHeight));
                const visibleRows = rows.slice(0, maxRows);
                const totals = columns.map(() => 0);
                const hasNumeric = columns.map(() => false);

                visibleRows.forEach((row) => {
                    drawGridRow(cursorY);
                    let cellX = tableX;

                    columns.forEach((_, idx) => {
                        const cellWidth = columnWidths[idx];
                        const cellValue = String(row[idx] ?? '');
                        drawWrappedCellText(ctx, cellValue, cellX, cursorY, cellWidth, rowHeight, idx === 0 ? 'left' : 'center');

                        if (idx > 0) {
                            const trimmed = cellValue.trim();
                            if (trimmed && /[0-9]/.test(trimmed)) {
                                hasNumeric[idx] = true;
                                totals[idx] += parseNumericValue(trimmed);
                            }
                        }

                        cellX += cellWidth;
                    });

                    cursorY += rowHeight;
                });

                if (tableSettings.show_total) {
                    ctx.fillStyle = '#f8f9fa';
                    ctx.fillRect(tableX, cursorY, tableWidth, rowHeight);
                    drawGridRow(cursorY);
                    ctx.fillStyle = tableSettings.text_color;
                    ctx.font = `700 ${Math.max(10, tableFontSize)}px ${assignmentFontFamily}`;

                    let totalX = tableX;
                    columns.forEach((_, idx) => {
                        const cellWidth = columnWidths[idx];
                        const value = idx === 0 ? 'TOTAL' : (hasNumeric[idx] ? formatNumericTotal(totals[idx]) : '');
                        drawWrappedCellText(ctx, value, totalX, cursorY, cellWidth, rowHeight, idx === 0 ? 'left' : 'center');
                        totalX += cellWidth;
                    });
                }
            }


            // ============================================
            // 5. SERVER SAVE/LOAD TEMPLATE
            // ============================================
            document.getElementById('btnSaveTemplate').addEventListener('click', async () => {
                const btn = document.getElementById('btnSaveTemplate');
                const oldHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                btn.disabled = true;

                try {
                    const payload = {
                        role: 'default',
                        name: 'Custom Layout',
                        canvas_width: ORIGINAL_WIDTH || 2000,
                        canvas_height: ORIGINAL_HEIGHT || 1414,
                        template_json: {
                            version: 2,
                            main_fields: config,
                            inputs: {
                                nomor_template: inputNomor.value,
                                deskripsi: inputDeskripsi.value,
                            },
                            assignment_page: snapshotAssignmentPage(),
                        },
                    };

                    const res = await fetch("{{ route('certificates.store_template', $event->id) }}", {
                        method: "POST",
                        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                        body: JSON.stringify(payload)
                    });

                    const json = await res.json();
                    if(json.success) alert('Template berhasil disimpan!');
                    else throw new Error('Gagal response server');
                } catch(e) {
                    alert('Gagal menyimpan template: ' + e);
                } finally {
                    btn.innerHTML = oldHtml;
                    btn.disabled = false;
                }
            });

            async function loadTemplateFromServer() {
                try {
                    const res = await fetch("{{ route('certificates.show_template', $event->id) }}?role=default");
                    const json = await res.json();
                    if(json.success && json.template && json.template.template_json) {
                        const serverConfig = json.template.template_json;

                        if (serverConfig.main_fields) {
                            config = normalizeMainConfig(serverConfig.main_fields);
                        } else if (serverConfig.nama && serverConfig.nomor) {
                            config = normalizeMainConfig(serverConfig);
                        }

                        if (serverConfig.inputs && typeof serverConfig.inputs === 'object') {
                            if (typeof serverConfig.inputs.nomor_template === 'string') {
                                inputNomor.value = serverConfig.inputs.nomor_template;
                            }
                            if (typeof serverConfig.inputs.deskripsi === 'string') {
                                inputDeskripsi.value = serverConfig.inputs.deskripsi;
                            }
                        }

                        assignmentPage = normalizeAssignmentPage(serverConfig.assignment_page);
                        if (json.template.canvas_width) {
                            ORIGINAL_WIDTH = Number(json.template.canvas_width) || ORIGINAL_WIDTH;
                        }
                        if (json.template.canvas_height) {
                            ORIGINAL_HEIGHT = Number(json.template.canvas_height) || ORIGINAL_HEIGHT;
                        }
                        await ensureFontFamiliesLoaded(getUsedFontNamesFromState({
                            table_settings: assignmentPage.table_settings,
                        }));
                        applyAssignmentToForm();
                        updateNomorPreview();
                        refreshDesignerWorkspace();
                        renderAssignmentDesignerPreview();
                    }
                } catch(e) { console.log("Menggunakan template default (lokal)."); }
            }

            // ============================================
            // 6. FITUR TAMBAHAN (Absensi & Tambah Manual)
            // ============================================

            // Toggle Form Tambah Peserta
            const btnToggleAdd = document.getElementById('btnToggleAddParticipant');
            const panelAdd = document.getElementById('addParticipantPanel');
            if(btnToggleAdd && panelAdd) {
                btnToggleAdd.addEventListener('click', () => {
                    if (panelAdd.classList.contains('show')) {
                        panelAdd.classList.remove('show');
                        panelAdd.style.height = '0';
                    } else {
                        panelAdd.classList.add('show');
                        panelAdd.style.height = 'auto';
                    }
                });
            }

            // Aksi Simpan Peserta Manual
            const btnSavePart = document.getElementById('btnAddParticipant');
            if(btnSavePart) {
                btnSavePart.addEventListener('click', async () => {
                    const npm   = document.getElementById('add_npm').value.trim();
                    const prodi = document.getElementById('add_prodi').value.trim();

                    if (!npm) return alert('NPM wajib diisi');

                    btnSavePart.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';
                    btnSavePart.disabled = true;

                    try {
                        const res = await fetch(`/admin/api/events/{{ $event->id }}/participants/add`, {
                            method: "POST",
                            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            body: JSON.stringify({ npm, prodi })
                        });
                        const json = await res.json();
                        if (!json.success) throw new Error(json.message);

                        alert(json.message);
                        location.reload();
                    } catch (err) {
                        alert(err.message);
                    } finally {
                        btnSavePart.disabled = false;
                        btnSavePart.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan';
                    }
                });
            }

            // Aksi Toggle Switch Kehadiran
            document.querySelectorAll('.attendance-checkbox').forEach(cb => {
                cb.addEventListener('change', async (e) => {
                    const id = e.target.dataset.registrationId;
                    const present = e.target.checked;
                    const badge = document.getElementById(`badge-att-${id}`);

                    e.target.disabled = true; // prevent double click

                    try {
                        const res = await fetch(`/admin/api/event-registrations/${id}/attendance`, {
                            method: "POST",
                            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            body: JSON.stringify({ present })
                        });
                        const json = await res.json();

                        // Update UI visual
                        if(present) {
                            badge.className = 'badge badge-success';
                            badge.innerText = 'Hadir';
                        } else {
                            badge.className = 'badge badge-danger';
                            badge.innerText = 'Absen';
                        }
                    } catch(err) {
                        alert("Gagal update absensi");
                        e.target.checked = !present; // revert
                    } finally {
                        e.target.disabled = false;
                    }
                });
            });

        });
    </script>

    {{-- Context-aware upload + JP draggable overlays (Event, Nama, NPM) --}}
    <script>
    (function () {
        'use strict';

        if (!window._assignTextPos) {
            window._assignTextPos = {
                event_x: null, event_y: null,
                nama_enabled: false, nama_x: null, nama_y: null,
                npm_enabled:  false, npm_x:  null, npm_y:  null,
            };
        }

        document.addEventListener('DOMContentLoaded', function () {

            var mainSection    = document.getElementById('uploadSectionMain');
            var jpSection      = document.getElementById('uploadSectionAssignment');
            var bgSourceGroup  = document.getElementById('assignmentBgSourceGroup');
            var modeMainBtn    = document.getElementById('modeMainDesigner');
            var modeJpBtn      = document.getElementById('modeAssignmentDesigner');
            var directInput    = document.getElementById('assignmentBgFileDirect');
            var directLabel    = document.querySelector('label[for="assignmentBgFileDirect"]');
            var realInput      = document.getElementById('assignmentBgFile');
            var realLabel      = document.querySelector('label[for="assignmentBgFile"]');
            var bgSourceCustom = document.getElementById('assignmentBgSourceCustom');
            var canvas         = document.getElementById('assignmentPreviewCanvas');
            var elEvent        = document.getElementById('at_event');
            var elNama         = document.getElementById('at_nama');
            var elNpm          = document.getElementById('at_npm');
            var toggleNama     = document.getElementById('toggleVarNama');
            var toggleNpm      = document.getElementById('toggleVarNpm');

            function isJpActive() {
                return !!(modeJpBtn && !modeJpBtn.disabled && modeJpBtn.classList.contains('btn-primary'));
            }

            // ── POSISI OVERLAY DARI _assignTextPos ─────────────────
            function applyOverlayPositions() {
                var p = window._assignTextPos || {};
                if (elEvent) {
                    elEvent.style.left = (p.event_x != null ? p.event_x : 35) + '%';
                    elEvent.style.top  = (p.event_y != null ? p.event_y : 9)  + '%';
                }
                if (elNama) {
                    elNama.style.left = (p.nama_x != null ? p.nama_x : 8) + '%';
                    elNama.style.top  = (p.nama_y != null ? p.nama_y : 18) + '%';
                }
                if (elNpm) {
                    elNpm.style.left = (p.npm_x != null ? p.npm_x : 8) + '%';
                    elNpm.style.top  = (p.npm_y != null ? p.npm_y : 22) + '%';
                }
            }

            // ── SYNC SEMUA TAMPILAN ────────────────────────────────
            function syncAll() {
                var jp  = isJpActive();
                var pos = window._assignTextPos || {};

                // Upload context
                if (mainSection)   mainSection.style.display   = jp ? 'none' : '';
                if (jpSection)     jpSection.style.display     = jp ? ''     : 'none';
                if (bgSourceGroup) bgSourceGroup.style.display = 'none'; // selalu hidden

                // Event overlay: selalu tampil di JP mode
                if (elEvent) elEvent.style.display = jp ? '' : 'none';

                // Nama & NPM: hanya tampil jika JP aktif DAN masing-masing di-enable
                if (elNama) elNama.style.display = (jp && pos.nama_enabled) ? '' : 'none';
                if (elNpm)  elNpm.style.display  = (jp && pos.npm_enabled)  ? '' : 'none';

                // Sync toggle checkbox states dari _assignTextPos (mis: setelah template load)
                if (toggleNama) toggleNama.checked = !!pos.nama_enabled;
                if (toggleNpm)  toggleNpm.checked  = !!pos.npm_enabled;

                if (jp) applyOverlayPositions();
            }

            // ── DRAG LOGIC ─────────────────────────────────────────
            var dragging = null; // 'event' | 'nama' | 'npm' | null
            var startMX, startMY, startLeft, startTop;

            function getOverlayEl(key) {
                return key === 'event' ? elEvent : key === 'nama' ? elNama : elNpm;
            }

            function startDrag(e, key) {
                e.preventDefault();
                dragging  = key;
                startMX   = e.clientX;
                startMY   = e.clientY;
                var el    = getOverlayEl(key);
                startLeft = parseFloat(el.style.left) || 0;
                startTop  = parseFloat(el.style.top)  || 0;
                el.classList.add('at-active');
            }

            document.addEventListener('mousemove', function (e) {
                if (!dragging || !canvas) return;
                var rect  = canvas.getBoundingClientRect();
                if (!rect.width || !rect.height) return;
                var el    = getOverlayEl(dragging);
                var newX  = Math.max(0, Math.min(95, startLeft + (e.clientX - startMX) / rect.width  * 100));
                var newY  = Math.max(0, Math.min(95, startTop  + (e.clientY - startMY) / rect.height * 100));
                el.style.left = newX + '%';
                el.style.top  = newY + '%';
            });

            document.addEventListener('mouseup', function () {
                if (!dragging) return;
                var el  = getOverlayEl(dragging);
                var key = dragging;
                el.classList.remove('at-active');
                var newX = Math.round(parseFloat(el.style.left) * 10) / 10;
                var newY = Math.round(parseFloat(el.style.top)  * 10) / 10;
                var p    = window._assignTextPos;
                if      (key === 'event') { p.event_x = newX; p.event_y = newY; }
                else if (key === 'nama')  { p.nama_x  = newX; p.nama_y  = newY; }
                else                      { p.npm_x   = newX; p.npm_y   = newY; }
                dragging = null;
                if (typeof window._reRenderAssignment === 'function') window._reRenderAssignment();
            });

            if (elEvent) elEvent.addEventListener('mousedown', function (e) { startDrag(e, 'event'); });
            if (elNama)  elNama.addEventListener('mousedown',  function (e) { startDrag(e, 'nama');  });
            if (elNpm)   elNpm.addEventListener('mousedown',   function (e) { startDrag(e, 'npm');   });

            // ── TOGGLE VARIABEL NAMA & NPM ─────────────────────────
            function onToggle(key, checked) {
                window._assignTextPos[key + '_enabled'] = checked;
                syncAll();
                if (typeof window._reRenderAssignment === 'function') window._reRenderAssignment();
            }

            if (toggleNama) toggleNama.addEventListener('change', function () { onToggle('nama', this.checked); });
            if (toggleNpm)  toggleNpm.addEventListener('change',  function () { onToggle('npm',  this.checked); });

            // ── FORWARD UPLOAD JP → REAL INPUT ────────────────────
            if (directInput && realInput) {
                directInput.addEventListener('change', function () {
                    var file = this.files && this.files[0];
                    if (!file) return;
                    if (directLabel) directLabel.textContent = file.name;
                    if (bgSourceCustom) {
                        bgSourceCustom.checked = true;
                        bgSourceCustom.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    try {
                        var dt = new DataTransfer();
                        dt.items.add(file);
                        realInput.files = dt.files;
                        if (realLabel) realLabel.textContent = file.name;
                        realInput.dispatchEvent(new Event('change'));
                    } catch (err) {
                        console.warn('[JP Upload] DataTransfer fallback:', err);
                    }
                });
            }

            // ── MODE BUTTON LISTENERS ──────────────────────────────
            if (modeMainBtn) modeMainBtn.addEventListener('click', function () { setTimeout(syncAll, 90); });
            if (modeJpBtn)   modeJpBtn.addEventListener('click',   function () { setTimeout(syncAll, 90); });

            // Sync toggle states setelah template selesai dimuat (loadTemplateFromServer ~async)
            setTimeout(syncAll, 1800);

            // Sync awal
            syncAll();
        });
    })();
    </script>
@endsection
