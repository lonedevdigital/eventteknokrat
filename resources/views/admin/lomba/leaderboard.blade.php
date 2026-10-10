@extends('layouts.admin')
@section('title', 'Leaderboard Lomba: ' . $event->nama_event)

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-trophy mr-2" style="color:#f59e0b;"></i>Leaderboard: {{ $event->nama_event }}
            </h5>
            <small class="text-muted">Rekapitulasi nilai dan peringkat peserta.</small>
        </div>
        <div>
            <a href="{{ route('lomba.manage', $event->slug) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 mb-4" style="border:1px solid #e2e8f0 !important;">
        <div class="card-body p-3 bg-light">
            <form method="GET" action="{{ route('lomba.leaderboard', $event->slug) }}" class="form-row align-items-center">
                <div class="col-md-5">
                    <select name="branch_id" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">-- Pilih Cabang Lomba --</option>
                        @foreach($event->branches as $b)
                            <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0" style="border:1px solid #e2e8f0 !important;">
        <div class="table-responsive">
            <table class="table table-flat mb-0">
                <thead>
                    <tr>
                        <th style="width:60px;" class="text-center">Peringkat</th>
                        <th>Peserta / Tim</th>
                        <th>Cabang</th>
                        <th class="text-center">Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($registrants as $reg)
                    @php
                        $nama = $reg->mahasiswa->nama_mahasiswa ?? $reg->user->name ?? '—';
                    @endphp
                    <tr>
                        <td class="text-center font-weight-bold" style="font-size: 1.1rem; color: {{ $loop->iteration <= 3 ? '#f59e0b' : '#64748b' }};">
                            @if($loop->iteration == 1)
                                <i class="fas fa-medal" style="color: #fbbf24;"></i> 1
                            @elseif($loop->iteration == 2)
                                <i class="fas fa-medal" style="color: #94a3b8;"></i> 2
                            @elseif($loop->iteration == 3)
                                <i class="fas fa-medal" style="color: #b45309;"></i> 3
                            @else
                                {{ $loop->iteration }}
                            @endif
                        </td>
                        <td class="font-weight-bold">{{ $nama }}</td>
                        <td>{{ $reg->branch->nama_cabang ?? '—' }}</td>
                        <td class="text-center font-weight-bold text-success" style="font-size: 1rem;">
                            {{ number_format($reg->total_score, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data nilai peserta.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.table-flat th { background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.65rem; border-bottom: 2px solid #e2e8f0; }
.table-flat td { vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
</style>
@endsection
