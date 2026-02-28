@extends('templateAdminLTE.home')

@section('sub-breadcrumb', 'Dokumentasi')
@section('page-title', 'Manajemen Dokumentasi')

@section('content')

    <style>
        /* Efek Hover pada Card Event */
        .event-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid #e9ecef;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
            border-color: #007bff;
        }

        /* Typography Title */
        .event-title {
            font-size: 1.1rem;
            line-height: 1.4;
            min-height: 3.0rem; /* Menjaga tinggi judul agar sejajar */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                {{-- Notifikasi --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card card-primary card-outline shadow-sm">

                    {{-- HEADER: JUDUL & FILTER --}}
                    <div class="card-header bg-white p-3">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-camera mr-2"></i> Pilih Event
                                </h3>
                            </div>

                            {{-- Filter Form --}}
                            <div class="col-md-6">
                                <form method="GET" action="">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0">
                                                <i class="fas fa-filter text-muted"></i>
                                            </span>
                                        </div>
                                        <select name="event_id" class="form-control border-left-0" onchange="this.form.submit()">
                                            <option value="">-- Tampilkan Semua Event --</option>
                                            @foreach($events as $ev)
                                                <option value="{{ $ev->id }}" {{ (isset($filter) && $filter == $ev->id) ? 'selected' : '' }}>
                                                    {{ $ev->nama_event }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- BODY: LIST EVENT GRID --}}
                    <div class="card-body bg-light">

                        @if($events->count() == 0)
                            {{-- EMPTY STATE --}}
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-4x text-muted mb-3 opacity-50"></i>
                                <h5 class="text-muted">Belum ada event yang tersedia.</h5>
                                <p class="small text-muted">Silakan buat event terlebih dahulu di menu Manajemen Event.</p>
                            </div>
                        @else
                            {{-- GRID EVENT --}}
                            <div class="row">
                                @foreach($events as $ev)
                                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">
                                        <div class="card h-100 event-card shadow-sm border-0 rounded-lg">

                                            <div class="card-body d-flex flex-column">
                                                {{-- Tanggal (Badge) --}}
                                                <div class="mb-2">
                                                    <span class="badge badge-info font-weight-normal px-2 py-1">
                                                        <i class="far fa-calendar-alt mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($ev->tanggal_pelaksanaan)->format('d M Y') }}
                                                    </span>
                                                </div>

                                                {{-- Judul --}}
                                                <h5 class="event-title font-weight-bold text-dark mb-2" title="{{ $ev->nama_event }}">
                                                    {{ $ev->nama_event }}
                                                </h5>

                                                {{-- Kategori (Optional, jika ada relasi) --}}
                                                <p class="text-muted small mb-4">
                                                    <i class="fas fa-tag mr-1"></i> {{ $ev->category->nama_kategori ?? 'Umum' }}
                                                </p>

                                                {{-- Spacer agar tombol selalu di bawah --}}
                                                <div class="mt-auto">
                                                    <a href="{{ route('documentations.create', $ev->id) }}"
                                                       class="btn btn-success btn-block shadow-sm font-weight-bold">
                                                        <i class="fas fa-images mr-1"></i> Dokumentasi
                                                    </a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Pagination (Jika ada) --}}
                            @if(method_exists($events, 'links'))
                                <div class="mt-3">
                                    {{ $events->withQueryString()->links() }}
                                </div>
                            @endif

                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
