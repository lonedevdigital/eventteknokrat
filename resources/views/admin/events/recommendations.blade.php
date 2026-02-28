@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Rekomendasi Event')

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h3 class="card-title mb-2 mb-md-0">
                    <i class="fas fa-star mr-1 text-warning"></i> Kelola Rekomendasi Event
                </h3>
                <div class="d-flex flex-wrap">
                    <span class="badge badge-primary mr-2 mb-1">Total: {{ $totalRecommended }}/{{ $maxTotal }}</span>
                    @if(in_array($userRole, ['baak', 'kemahasiswaan'], true))
                        <span class="badge badge-info mr-2 mb-1">{{ strtoupper($userRole) }}: {{ $roleRecommended }}/{{ $maxRole }}</span>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('events.recommendations.index') }}" class="mb-3">
                    <div class="form-row">
                        <div class="col-md-4 mb-2">
                            <input
                                type="text"
                                name="q"
                                class="form-control"
                                placeholder="Cari nama event..."
                                value="{{ request('q') }}"
                            >
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="event_category_id" class="form-control">
                                <option value="">Semua kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string) request('event_category_id') === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5 mb-2">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                                <a href="{{ route('events.recommendations.index') }}" class="btn btn-outline-secondary">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th style="width: 80px;">Cover</th>
                            <th>Event</th>
                            <th style="width: 140px;">Role Event</th>
                            <th style="width: 150px;">Tanggal</th>
                            <th style="width: 200px;">Status Rekomendasi</th>
                            <th style="width: 170px;" class="text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($events as $event)
                            @php
                                $recommendation = $event->recommendation;
                                $isRecommended = $recommendation !== null;
                                $ownerRole = strtoupper($event->owner_role ?: '-');
                                $canAddThisEvent = $isRecommended || $canRecommend;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    @if($event->thumbnail)
                                        <img
                                            src="{{ \Illuminate\Support\Str::startsWith($event->thumbnail, ['http://', 'https://']) ? $event->thumbnail : asset($event->thumbnail) }}"
                                            alt="{{ $event->nama_event }}"
                                            style="width: 56px; height: 56px; object-fit: cover;"
                                        >
                                    @else
                                        <div class="bg-light d-inline-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $event->nama_event }}</div>
                                    <small class="text-muted d-block">{{ $event->category->nama_kategori ?? '-' }}</small>
                                    <small class="text-muted d-block">{{ $event->tempat_pelaksanaan }}</small>
                                </td>
                                <td>{{ $ownerRole }}</td>
                                <td>
                                    {{ $event->tanggal_pelaksanaan ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y') : '-' }}
                                </td>
                                <td>
                                    @if($isRecommended)
                                        <span class="badge badge-success">Direkomendasikan</span>
                                        <small class="d-block text-muted mt-1">
                                            Oleh {{ strtoupper($recommendation->selected_by_role ?? '-') }}
                                            @if($recommendation->selector)
                                                ({{ $recommendation->selector->name }})
                                            @endif
                                        </small>
                                    @else
                                        <span class="badge badge-secondary">Belum</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('events.recommendations.toggle', $event->id) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="btn btn-sm {{ $isRecommended ? 'btn-outline-danger' : 'btn-success' }}"
                                            {{ $canAddThisEvent ? '' : 'disabled' }}
                                        >
                                            {{ $isRecommended ? 'Hapus Highlight' : 'Highlight' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data event.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(method_exists($events, 'links'))
                <div class="card-footer">
                    {{ $events->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
