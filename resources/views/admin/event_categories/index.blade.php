@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Kategori Event')

@section('content')

    <style>
        /* --- FLAT GREEN THEME --- */

        /* 1. Global Flat Reset */
        .card, .table, .btn, .form-control {
            border-radius: 0 !important; /* Sudut kotak/lancip */
            box-shadow: none !important; /* Hilangkan bayangan 3D */
            border: none !important; /* Hilangkan border */
        }

        /* 2. Header Card (Warna Utama: Hijau Flat) */
        .card-header-flat {
            background-color: #27ae60; /* Hijau sesuai request (Nephritis Flat) */
            color: #ffffff;
            padding: 15px 20px;
            border-bottom: none;
        }

        /* 3. Tombol Flat */
        .btn {
            background-image: none !important;
            transition: all 0.2s ease-in-out;
            font-weight: 500;
        }

        /* Tombol Tambah (Di dalam Header Hijau) */
        /* Kita buat Putih agar kontras dan bersih di atas background hijau */
        .btn-add-header {
            background-color: #ffffff;
            color: #27ae60; /* Teks hijau */
            padding: 6px 15px;
        }
        .btn-add-header:hover {
            background-color: #f0fdf4; /* Putih agak kehijauan saat hover */
            color: #1e8449;
        }

        /* Tombol Edit (Kuning Flat) */
        .btn-edit-flat {
            background-color: #f1c40f;
            color: #fff;
        }
        .btn-edit-flat:hover {
            background-color: #d4ac0d;
            color: #fff;
        }

        /* Tombol Hapus (Merah Flat) */
        .btn-delete-flat {
            background-color: #e74c3c;
            color: white;
        }
        .btn-delete-flat:hover {
            background-color: #c0392b;
            color: white;
        }

        /* 4. Tabel Flat */
        .table-flat thead th {
            background-color: #ecf0f1; /* Abu sangat muda */
            color: #7f8c8d; /* Text abu tua */
            border-bottom: 2px solid #bdc3c7;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            vertical-align: middle;
            padding-left: 15px;
        }
        .table-flat td {
            vertical-align: middle !important;
            border-top: 1px solid #ecf0f1;
            padding: 12px 15px;
            color: #2c3e50;
        }

        /* Highlight baris saat di-hover */
        .table-hover tbody tr:hover {
            background-color: #f0fdf4; /* Hijau sangat pudar saat hover baris */
        }
    </style>

    <div class="container-fluid">

        {{-- Notifikasi (Hijau Flat) --}}
        @if (session('success'))
            <div class="alert fade show mb-3" role="alert" style="background-color: #2ecc71; color: white; border: none; border-radius: 0;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle mr-2" style="font-size: 1.2rem;"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close" style="color: white; opacity: 1; text-shadow: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-12">

                <div class="card">
                    {{-- HEADER HIJAU --}}
                    <div class="card-header-flat d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold mb-0" style="font-size: 1.1rem;">
                            <i class="fas fa-list-ul mr-2"></i> Daftar Kategori
                        </h3>

                        {{-- Tombol Tambah: Putih (agar kontras dengan header hijau) --}}
                        <a href="{{ route('event-categories.create') }}" class="btn btn-add-header btn-sm shadow-none">
                            <i class="fas fa-plus mr-1"></i> Tambah Kategori
                        </a>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-flat mb-0">
                                <thead>
                                <tr>
                                    <th style="width: 60px;" class="text-center">No</th>
                                    <th>Nama Kategori</th>
                                    <th style="width: 150px;" class="text-center">Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($categories as $cat)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>

                                        <td class="font-weight-bold">
                                            {{ $cat->nama_kategori }}
                                        </td>

                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('event-categories.edit', $cat->id) }}"
                                                   class="btn btn-edit-flat btn-sm"
                                                   title="Edit"
                                                   data-toggle="tooltip">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>

                                                {{-- Tombol Hapus --}}
                                                <form action="{{ route('event-categories.destroy', $cat->id) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Hapus kategori &quot;{{ $cat->nama_kategori }}&quot;?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-delete-flat btn-sm"
                                                            title="Hapus"
                                                            data-toggle="tooltip">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="fas fa-leaf fa-3x mb-3 opacity-25" style="color: #27ae60;"></i><br>
                                            Belum ada data kategori.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    @if(method_exists($categories, 'links'))
                        <div class="card-footer bg-white p-3">
                            <div class="float-right">
                                {{ $categories->links() }}
                            </div>
                        </div>
                    @endif

                </div>

            </div>
        </div>

    </div>

    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>

@endsection
