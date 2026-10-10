@extends('layouts.admin')
@section('title', 'Penilaian Lomba: ' . $event->nama_event)

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h5 class="mb-0 font-weight-bold text-dark">
                <i class="fas fa-gavel mr-2" style="color:var(--primary-green);"></i>Penilaian: {{ $event->nama_event }}
            </h5>
            <small class="text-muted">Beri nilai pada karya / jawaban peserta lomba.</small>
        </div>
        <div>
            <a href="{{ route('lomba.manage', $event->slug) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card border-0 mb-4" style="border:1px solid #e2e8f0 !important;">
        <div class="card-body p-3 bg-light">
            <form method="GET" action="{{ route('lomba.penilaian', $event->slug) }}" class="form-row align-items-center">
                <div class="col-md-5">
                    <select name="branch_id" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">-- Pilih Cabang Lomba --</option>
                        @foreach($event->branches as $b)
                            <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
                @if($branchId)
                    @php
                        $selectedBranch = $event->branches->firstWhere('id', $branchId);
                        $isPj = $selectedBranch && $selectedBranch->pj_user_id === auth()->id();
                        $canManage = $event->canBeManagedBy(auth()->user());
                    @endphp
                    @if($canManage || $isPj)
                    <div class="col-md-auto mt-2 mt-md-0">
                        <a href="{{ route('lomba.cabang.edit', [$event->slug, $branchId]) }}" class="btn btn-sm btn-outline-primary bg-white">
                            <i class="fas fa-edit mr-1"></i> Pengaturan Cabang & Kriteria
                        </a>
                    </div>
                    @endif
                @endif
            </form>
        </div>
    </div>

    @if($criteria->isEmpty())
        <div class="alert alert-warning">
            <i class="fas fa-info-circle mr-1"></i> Belum ada kriteria penilaian yang dikonfigurasi untuk cabang ini. Silakan atur kriteria penilaian di menu Kelola Lomba.
        </div>
    @else
        <div class="card border-0" style="border:1px solid #e2e8f0 !important;">
            <div class="table-responsive">
                <table class="table table-flat mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Peserta / Tim</th>
                            <th>Cabang</th>
                            <th>Karya / Berkas</th>
                            <th style="width:300px;">Nilai Kriteria</th>
                            <th style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($registrants as $reg)
                        @php
                            $nama = $reg->mahasiswa->nama_mahasiswa ?? $reg->user->name ?? '—';
                            $docs = $reg->documents; // Collection of ParticipantDocument
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $nama }}</td>
                            <td>{{ $reg->branch->nama_cabang ?? '—' }}</td>
                            <td>
                                @if($docs->isEmpty())
                                    <span class="text-muted small">Belum ada karya yang diunggah.</span>
                                @else
                                    <ul class="list-unstyled mb-0 small">
                                        @foreach($docs as $doc)
                                            @php
                                                $fileUrl = \Illuminate\Support\Str::startsWith($doc->file_path, ['http://', 'https://']) ? $doc->file_path : asset($doc->file_path);
                                            @endphp
                                            <li>
                                                <a href="{{ $fileUrl }}" target="_blank" class="text-primary">
                                                    <i class="fas fa-external-link-alt mr-1"></i> {{ $doc->requirement->nama_dokumen ?? 'Karya' }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <form action="{{ route('lomba.penilaian.store', [$event->slug, $reg->id]) }}" method="POST">
                                @csrf
                                <td>
                                    @foreach($criteria as $c)
                                        @php
                                            $existingScore = $reg->scores->where('assessment_criterion_id', $c->id)->first();
                                        @endphp
                                        <div class="form-group mb-2 d-flex align-items-center">
                                            <label class="mb-0 small mr-2" style="width:120px;">{{ $c->nama_kriteria }} ({{ $c->bobot }}%)</label>
                                            <input type="number" name="scores[{{ $c->id }}]" class="form-control form-control-sm" 
                                                   value="{{ $existingScore->nilai ?? '' }}" min="0" max="100" placeholder="0 - 100" required>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-green btn-sm w-100">
                                        <i class="fas fa-save mr-1"></i> Simpan
                                    </button>
                                </td>
                            </form>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada peserta yang mendaftar.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<style>
.table-flat th { background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.65rem; border-bottom: 2px solid #e2e8f0; }
.table-flat td { vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
</style>
@endsection
