@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Rekomendasi Event')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
        --accent-yellow: #f1c40f;
        --accent-red: #e74c3c;
        --bg-light: #ecf0f1;
        --text-grey: #2c3e50;
    }

    .card, .btn, .form-control, .input-group-text,
    .badge, .custom-select {
        border-radius: 0 !important;
        box-shadow: none !important;
        border-color: #dee2e6;
    }

    .card-header-flat {
        background-color: var(--primary-green);
        color: #fff;
        padding: 12px 20px;
        border-bottom: none;
    }

    .btn-green {
        background-color: var(--primary-green);
        color: #fff;
        border: none;
        font-weight: 600;
    }
    .btn-green:hover { background-color: var(--primary-green-dark); color: #fff; }

    .btn-action-flat {
        border: none;
        color: #fff;
        padding: 5px 12px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-action-flat:hover { color: #fff; opacity: 0.88; }
    .btn-action-flat:disabled { opacity: 0.45; cursor: default; }
    .btn-green-flat  { background-color: var(--primary-green); }
    .btn-danger-flat { background-color: var(--accent-red); }
    .btn-grey-flat   { background-color: #95a5a6; }

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
        padding: 10px 12px;
    }
    .table-hover tbody tr:hover { background-color: #f9fbfb; }

    .img-cover {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border: 1px solid #dee2e6;
        background: #fff;
    }

    .quota-badge {
        background: rgba(255,255,255,0.18);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.35);
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        letter-spacing: 0.3px;
    }

    .badge-recommended {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        font-size: 11px;
        padding: 3px 8px;
        font-weight: 600;
    }
    .badge-not-yet {
        background-color: #f8f9fa;
        color: #6c757d;
        border: 1px solid #dee2e6;
        font-size: 11px;
        padding: 3px 8px;
    }
</style>

<div class="container-fluid">

    @if(session('success'))
    <div class="alert fade show mb-3" style="background-color:#2ecc71;color:#fff;border:none;border-radius:0;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff;opacity:1"><span>&times;</span></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible mb-3" style="border-radius:0;">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('error') }}
    </div>
    @endif

    <div class="card border-0">

        {{-- Header --}}
        <div class="card-header-flat d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
            <h3 class="card-title font-weight-bold mb-0" style="font-size:1.1rem;">
                <i class="fas fa-star mr-2"></i> Kelola Rekomendasi Event
            </h3>
            <div class="d-flex align-items-center" style="gap:6px;">
                <span class="quota-badge">
                    Total: {{ $totalRecommended }}/{{ $maxTotal }}
                </span>
                @if(in_array($userRole, ['baak', 'kemahasiswaan'], true))
                <span class="quota-badge">
                    {{ strtoupper($userRole) }}: {{ $roleRecommended }}/{{ $maxRole }}
                </span>
                @endif
            </div>
        </div>

        <div class="card-body p-0">

            {{-- Filter --}}
            <div class="p-3" style="background-color:#f8fafc; border-bottom:1px solid #e2e8f0;">
                <form method="GET" action="{{ route('events.recommendations.index') }}">
                    <div class="form-row align-items-center">
                        <div class="col-12 col-lg-4 mb-2 mb-lg-0">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white text-muted border-right-0">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" name="q"
                                       class="form-control border-left-0"
                                       placeholder="Cari nama event..."
                                       value="{{ request('q') }}">
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 mb-2 mb-lg-0">
                            <select name="event_category_id" class="form-control form-control-sm custom-select">
                                <option value="">— Semua Kategori —</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ (string) request('event_category_id') === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-lg-2 mb-2 mb-lg-0">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-green btn-sm" style="width:75%;">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                                <a href="{{ route('events.recommendations.index') }}"
                                   class="btn btn-default btn-sm border bg-white text-center"
                                   style="width:25%;" title="Reset">
                                    <i class="fas fa-sync-alt text-muted"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="table-responsive">
                <table class="table table-hover table-flat mb-0">
                    <thead>
                        <tr>
                            <th class="text-center pl-3" style="width:60px;">Cover</th>
                            <th style="min-width:220px;">Event</th>
                            <th style="width:120px;">Role Pembuat</th>
                            <th style="width:130px;">Tanggal</th>
                            <th style="width:190px;">Status</th>
                            <th class="text-center" style="width:160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($events as $event)
                        @php
                            $recommendation  = $event->recommendation;
                            $isRecommended   = $recommendation !== null;
                            $ownerRole       = strtoupper($event->owner_role ?: '-');
                            $canAddThisEvent = $isRecommended || $canRecommend;
                        @endphp
                        <tr>
                            <td class="text-center pl-3">
                                @if($event->thumbnail)
                                    <img src="{{ \Illuminate\Support\Str::startsWith($event->thumbnail, ['http://', 'https://']) ? $event->thumbnail : asset($event->thumbnail) }}"
                                         alt="{{ $event->nama_event }}" class="img-cover">
                                @else
                                    <div class="img-cover d-flex align-items-center justify-content-center text-muted mx-auto bg-light">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>

                            <td>
                                <div class="font-weight-bold" style="font-size:.93rem;">{{ $event->nama_event }}</div>
                                <small class="text-muted d-block">
                                    <i class="fas fa-tags mr-1"></i>{{ $event->category->nama_kategori ?? '-' }}
                                </small>
                                <small class="text-muted d-block">
                                    <i class="fas fa-map-marker-alt mr-1" style="color:#e74c3c;"></i>{{ $event->tempat_pelaksanaan }}
                                </small>
                            </td>

                            <td>
                                <span class="text-monospace" style="font-size:.85rem;">{{ $ownerRole }}</span>
                            </td>

                            <td style="font-size:.85rem; color:#555;">
                                {{ $event->tanggal_pelaksanaan
                                    ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y')
                                    : '-' }}
                            </td>

                            <td>
                                @if($isRecommended)
                                    <span class="badge-recommended">
                                        <i class="fas fa-star mr-1"></i>Direkomendasikan
                                    </span>
                                    <small class="d-block text-muted mt-1" style="font-size:11px;">
                                        Oleh {{ strtoupper($recommendation->selected_by_role ?? '-') }}
                                        @if($recommendation->selector)
                                            ({{ $recommendation->selector->name }})
                                        @endif
                                    </small>
                                @else
                                    <span class="badge-not-yet">Belum direkomendasikan</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <form method="POST" action="{{ route('events.recommendations.toggle', $event->id) }}">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm btn-action-flat {{ $isRecommended ? 'btn-danger-flat' : ($canAddThisEvent ? 'btn-green-flat' : 'btn-grey-flat') }}"
                                            {{ $canAddThisEvent ? '' : 'disabled' }}>
                                        {{ $isRecommended ? 'HAPUS HIGHLIGHT' : 'HIGHLIGHT' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-star fa-3x mb-3 d-block" style="opacity:.2; color:#27ae60;"></i>
                                Belum ada data event.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        @if(method_exists($events, 'links') && $events->hasPages())
        <div class="card-footer bg-white border-top p-3">
            <div class="d-flex justify-content-end">{{ $events->withQueryString()->links() }}</div>
        </div>
        @endif

    </div>
</div>

<script>
    $(function () { $('[data-toggle="tooltip"]').tooltip() });
</script>

@endsection
