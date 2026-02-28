@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Manajemen Event')

@section('content')

    <style>
        /* --- 1. THEME VARIABLES & RESET --- */
        :root {
            --primary-green: #27ae60;
            --primary-green-dark: #219150;
            --accent-yellow: #f1c40f;
            --accent-red: #e74c3c;
            --accent-blue: #3498db;
            --bg-light: #ecf0f1;
            --text-grey: #2c3e50;
        }

        /* Reset Global */
        .card, .btn, .form-control, .input-group-text,
        .alert, .badge, .img-cover, .pagination .page-item .page-link,
        .custom-select {
            border-radius: 0 !important;
            box-shadow: none !important;
            border-color: #dee2e6;
        }

        /* --- 2. COMPONENT STYLING --- */
        .card-header-flat {
            background-color: var(--primary-green);
            color: #ffffff;
            padding: 12px 20px;
            border-bottom: none;
        }

        /* Tombol Filter Hijau */
        .btn-green {
            background-color: var(--primary-green);
            color: #fff;
            border: none;
        }
        .btn-green:hover { background-color: var(--primary-green-dark); color: #fff; }

        /* Tombol Header Putih */
        .btn-white-action {
            background-color: #fff;
            color: var(--primary-green);
            font-weight: 600;
            border: 1px solid #fff;
        }
        .btn-white-action:hover { background-color: #f0fdf4; color: var(--primary-green-dark); }

        .btn-ghost-white {
            background-color: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255, 0.6);
        }
        .btn-ghost-white:hover { background-color: rgba(255,255,255, 0.1); color: #fff; border-color: #fff; }

        /* --- 3. TOMBOL AKSI FLAT (MODIFIKASI TEXT) --- */
        .btn-action-flat {
            border: none;
            color: #fff;
            padding: 5px 10px; /* Padding pas untuk teks */
            font-size: 0.75rem; /* Font agak kecil agar muat */
            font-weight: 700;   /* Teks tebal */
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-action-flat:hover { color: #fff; opacity: 0.9; }

        /* Warna-warni tombol aksi */
        .btn-warning-flat { background-color: var(--accent-yellow); color: #fff; }
        .btn-danger-flat  { background-color: var(--accent-red); color: #fff; }
        .btn-dark-flat    { background-color: #34495e; color: #fff; }
        .btn-blue-flat    { background-color: var(--accent-blue); color: #fff; }
        .btn-green-flat   { background-color: var(--primary-green); color: #fff; }

        /* Badge & Cover */
        .badge-flat-green {
            background-color: #e8f5e9;
            color: var(--primary-green);
            border: 1px solid var(--primary-green);
            font-weight: 600;
        }
        .img-cover {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border: 1px solid #dee2e6;
            padding: 2px;
            background: #fff;
        }

        /* Tabel Flat */
        .table-flat thead th {
            background-color: var(--bg-light);
            color: #7f8c8d;
            border-bottom: 2px solid #bdc3c7;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }
        .table-flat tbody td {
            vertical-align: middle !important;
            color: var(--text-grey);
            padding: 10px;
        }
        .table-hover tbody tr:hover { background-color: #f9fbfb; }
    </style>

    <div class="container-fluid">

        @if (session('success'))
            <div class="alert fade show mb-3" role="alert" style="background-color: #2ecc71; color: white; border: none;">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: white; opacity: 1;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card border-0">
            <div class="card-header-flat d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold mb-0" style="font-size: 1.1rem;">
                    <i class="fas fa-calendar-alt mr-2"></i> Daftar Event
                </h3>
                <div>
                    <a href="{{ route('event-categories.create') }}" class="btn btn-ghost-white btn-sm mr-2">
                        <i class="fas fa-tags mr-1"></i> Kategori
                    </a>
                    <a href="{{ route('events.create') }}" class="btn btn-white-action btn-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Event
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                {{-- FILTER --}}
                <div class="p-3" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                    <form method="GET" action="{{ route('events.index') }}">
                        <div class="form-row align-items-center">
                            <div class="col-12 col-lg-3 mb-2 mb-lg-0">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white text-muted border-right-0"><i class="fas fa-search"></i></span>
                                    </div>
                                    <input type="text" name="q" class="form-control border-left-0" placeholder="Cari event..." value="{{ request('q') }}">
                                </div>
                            </div>
                            <div class="col-6 col-lg-2 mb-2 mb-lg-0">
                                <select name="event_category_id" class="form-control form-control-sm custom-select">
                                    <option value="">- Kategori -</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('event_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-lg-2 mb-2 mb-lg-0">
                                <select name="status" class="form-control form-control-sm custom-select">
                                    <option value="">- Status -</option>
                                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Akan datang</option>
                                    <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Hari ini</option>
                                    <option value="past" {{ request('status') === 'past' ? 'selected' : '' }}>Lewat</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-3 mb-2 mb-lg-0">
                                <div class="d-flex">
                                    @php $currentYear = now()->year; @endphp
                                    <select name="year" class="form-control form-control-sm mr-1 custom-select">
                                        <option value="">Thn</option>
                                        @for ($y = $currentYear - 5; $y <= $currentYear + 1; $y++)
                                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                    <select name="month" class="form-control form-control-sm mr-1 custom-select">
                                        <option value="">Bln</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                        @endfor
                                    </select>
                                    <select name="day" class="form-control form-control-sm custom-select">
                                        <option value="">Tgl</option>
                                        @for ($d = 1; $d <= 31; $d++)
                                            <option value="{{ $d }}" {{ request('day') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-lg-2">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-green btn-sm w-75 font-weight-bold"><i class="fas fa-filter mr-1"></i> Filter</button>
                                    <a href="{{ route('events.index') }}" class="btn btn-default btn-sm w-25 border bg-white" title="Reset"><i class="fas fa-sync-alt text-muted"></i></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-flat mb-0">
                        <thead>
                        <tr>
                            <th class="text-center pl-3" style="width: 70px;">Cover</th>
                            <th style="min-width: 250px;">Detail Event</th>
                            <th>Kategori</th>
                            <th>Pembuat</th>
                            <th>Jadwal</th>
                            {{-- Kolom Aksi diperlebar agar teks muat --}}
                            <th class="text-center" style="min-width: 320px;">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($events as $event)
                            <tr>
                                <td class="text-center pl-3">
                                    @if ($event->thumbnail)
                                        <a href="{{ Str::startsWith($event->thumbnail, ['http']) ? $event->thumbnail : asset($event->thumbnail) }}" target="_blank">
                                            <img src="{{ Str::startsWith($event->thumbnail, ['http']) ? $event->thumbnail : asset($event->thumbnail) }}" class="img-cover" alt="Thumb">
                                        </a>
                                    @else
                                        <div class="img-cover d-flex align-items-center justify-content-center text-muted mx-auto bg-light"><i class="fas fa-image"></i></div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('events.edit', $event->id) }}" class="font-weight-bold text-dark d-block text-wrap" style="font-size: 1rem; line-height: 1.2;">{{ $event->nama_event }}</a>
                                    <small class="text-muted mt-1 d-block"><i class="fas fa-map-marker-alt mr-1" style="color: #e74c3c;"></i> {{ $event->tempat_pelaksanaan }}</small>
                                </td>
                                <td><span class="badge badge-flat-green px-2 py-1" style="font-size: 0.75rem;">{{ $event->category->nama_kategori ?? 'Umum' }}</span></td>
                                <td><small class="text-muted">{{ $event->creator->name ?? '-' }}</small></td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <span class="text-muted mb-1" style="font-size: 0.8rem;"><i class="fas fa-pen-square mr-1"></i> Reg: {{ \Carbon\Carbon::parse($event->tanggal_pendaftaran)->format('d/m/Y') }}</span>
                                        <span style="color: #27ae60; font-weight: 700; font-size: 0.85rem;"><i class="fas fa-calendar-check mr-1"></i> {{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y') }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    {{-- Grouping Button: Gunakan spasi manual (mr-1) agar tidak dempet --}}
                                    <div class="d-flex justify-content-center" style="gap: 5px;">

                                        {{-- 1. QR Code --}}
                                        <a href="{{ route('admin.events.qr', $event->slug) }}"
                                           class="btn btn-sm btn-action-flat btn-dark-flat"
                                           title="Generate QR">
                                            QR
                                        </a>

                                        {{-- 2. Sertifikat --}}
                                        <a href="{{ route('certificates.event-detail', $event->id) }}"
                                           class="btn btn-sm btn-action-flat btn-blue-flat"
                                           title="Kelola Sertifikat">
                                            SERTIFIKAT
                                        </a>

                                        {{-- 3. Dokumentasi --}}
                                        <a href="{{ route('documentations.create', $event->id) }}"
                                           class="btn btn-sm btn-action-flat btn-green-flat"
                                           title="Kelola Dokumentasi">
                                            DOKUMENTASI
                                        </a>

                                        {{-- 4. Edit --}}
                                        <a href="{{ route('events.edit', $event->id) }}"
                                           class="btn btn-sm btn-action-flat btn-warning-flat"
                                           title="Edit Data">
                                            EDIT
                                        </a>

                                        {{-- 5. Hapus --}}
                                        <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="d-inline-block"
                                              onsubmit="return confirm('Hapus event &quot;{{ $event->nama_event }}&quot;?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-action-flat btn-danger-flat" title="Hapus">
                                                HAPUS
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-leaf fa-3x mb-3 opacity-25" style="color: #27ae60;"></i><br>
                                    Data event tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($events, 'links'))
                    <div class="card-footer bg-white border-top p-3">
                        <div class="d-flex justify-content-end">{{ $events->withQueryString()->links() }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('js')
        <script>
            $(function () { $('[data-toggle="tooltip"]').tooltip() })
        </script>
    @endpush
@endsection
