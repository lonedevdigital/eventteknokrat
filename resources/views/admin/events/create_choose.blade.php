@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Tambah Baru')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
        --accent-blue: #2980b9;
    }
    .card, .btn { border-radius: 0 !important; box-shadow: none !important; }

    .card-header-flat {
        background-color: var(--primary-green);
        color: #fff;
        padding: 12px 20px;
        border-bottom: none;
    }

    .choose-card {
        border: 1px solid #e2e8f0;
        background: #fff;
        padding: 30px 24px;
        text-align: center;
        height: 100%;
        transition: all .18s ease;
        cursor: pointer;
        display: block;
        color: inherit;
    }
    .choose-card:hover {
        border-color: var(--primary-green);
        box-shadow: 0 8px 24px rgba(39,174,96,.15) !important;
        transform: translateY(-3px);
        text-decoration: none;
        color: inherit;
    }
    .choose-icon {
        width: 76px; height: 76px;
        margin: 0 auto 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; color: #fff;
    }
    .choose-icon.green { background: var(--primary-green); }
    .choose-icon.blue  { background: var(--accent-blue); }
    .choose-card h4 { font-weight: 700; font-size: 1.15rem; margin-bottom: 8px; color: #2c3e50; }
    .choose-card p  { font-size: .86rem; color: #7f8c8d; margin-bottom: 16px; min-height: 56px; }
    .choose-tag {
        display: inline-block;
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .5px; padding: 6px 16px; color: #fff;
    }
    .choose-tag.green { background: var(--primary-green); }
    .choose-tag.blue  { background: var(--accent-blue); }
</style>

<div class="container-fluid">
    <div class="card border-0" style="border:1px solid #e2e8f0 !important;">
        <div class="card-header-flat d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold mb-0" style="font-size:1.1rem;">
                <i class="fas fa-plus-circle mr-2"></i> Pilih Jenis yang Ingin Dibuat
            </h3>
            <a href="{{ route('events.index') }}" class="btn btn-sm"
               style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        <div class="card-body p-4">
            <p class="text-muted mb-4">
                Tentukan dulu jenis kegiatan yang akan dibuat. Setiap jenis memiliki form dan pengelolaan yang berbeda.
            </p>

            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-6 mb-3">
                    <a href="{{ route('events.create', ['type' => 'event']) }}" class="choose-card shadow-sm">
                        <div class="choose-icon blue"><i class="fas fa-calendar-alt"></i></div>
                        <h4>Event</h4>
                        <p>Seminar, workshop, kuliah umum, dan kegiatan dengan pendaftaran &amp; presensi peserta individual.</p>
                        <span class="choose-tag blue">Buat Event</span>
                    </a>
                </div>

                <div class="col-lg-5 col-md-6 mb-3">
                    <a href="{{ route('events.create', ['type' => 'lomba']) }}" class="choose-card shadow-sm">
                        <div class="choose-icon green"><i class="fas fa-trophy"></i></div>
                        <h4>Perlombaan</h4>
                        <p>Kompetisi dengan cabang lomba, tim peserta, kepanitiaan, dan opsi pendaftaran berbayar/gratis.</p>
                        <span class="choose-tag green">Buat Perlombaan</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
