@extends('templateAdminLTE.home')

@section('sub-breadcrumb', 'Halaman Mahasiswa')

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
        .card, .btn, .form-control, .input-group-text,
        .custom-file-label, .alert, .badge, .pagination .page-item .page-link {
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

        /* --- FORM & COMPONENT STYLING --- */
        .form-control:focus, .custom-select:focus {
            border-color: var(--primary-green);
            box-shadow: none;
        }

        /* Override warna border bottom filter agar lebih halus */
        .filter-section {
            background-color: #f8fafc !important; /* Abu sangat muda flat */
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 15px;
        }
    </style>

    <div class="container-fluid">

        <div class="row">
            <div class="col-12">

                {{-- ALERT / NOTIFIKASI --}}
                <div id="respon">
                    @include('components.message')
                </div>

                {{-- CARD UTAMA (FLAT) --}}
                <div class="card border-0">

                    {{-- HEADER HIJAU FLAT --}}
                    <div class="card-header-flat">
                        <h3 class="card-title font-weight-bold mb-0">
                            <i class="fas fa-users mr-2"></i> Data Mahasiswa
                        </h3>
                    </div>

                    {{-- FILTER SECTION --}}
                    {{-- Menggunakan class custom .filter-section agar background flat --}}
                    <div class="filter-section">
                        @include('admin.mahasiswa.filter')
                    </div>

                    {{-- TABLE SECTION --}}
                    <div class="card-body p-0">
                        @include('admin.mahasiswa.table')
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    @include('admin.mahasiswa.javascript')

@endsection
