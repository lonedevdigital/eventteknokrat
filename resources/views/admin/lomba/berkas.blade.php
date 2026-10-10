@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Review Berkas')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
        --accent-red: #e74c3c;
        --bg-light: #ecf0f1;
        --text-grey: #2c3e50;
    }
    .card, .btn, .form-control, .badge { border-radius: 0 !important; box-shadow: none !important; }
    .card-header-flat { background-color: var(--primary-green); color: #fff; padding: 12px 20px; border-bottom: none; }
    .table-flat thead th {
        background-color: var(--bg-light); color: #7f8c8d; border-bottom: 2px solid #bdc3c7;
        text-transform: uppercase; font-size: 0.6rem; letter-spacing: .5px; vertical-align: middle; padding: 9px 10px;
    }
    .table-flat tbody td { vertical-align: middle !important; color: var(--text-grey); padding: 9px 10px; font-size: 0.7rem; }
    .status-pill { font-size: 10px; padding: 2px 8px; font-weight: 700; display: inline-block; }
    .status-approved { background:#f0fdf4; color:#27ae60; border:1px solid #c3e6cb; }
    .status-rejected { background:#fef2f2; color:#e74c3c; border:1px solid #fecaca; }
    .status-pending { background:#fff8e1; color:#b8860b; border:1px solid #ffe08a; }
    .status-none { background:#f8f9fa; color:#6c757d; border:1px solid #dee2e6; }
    .btn-mini { padding: 2px 7px; font-size: 0.6rem; border:none; margin-right:2px; margin-top:3px; }
    .btn-approve { background:var(--primary-green); color:#fff; }
    .btn-reject { background:var(--accent-red); color:#fff; }
</style>

<div class="container-fluid">

    @if(session('success'))
    <div class="alert fade show mb-3" style="background-color:#2ecc71;color:#fff;border:none;border-radius:0;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff;opacity:1"><span>&times;</span></button>
    </div>
    @endif

    <div class="card border-0 mb-3">
        <div class="card-header-flat d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold mb-0" style="font-size: 0.95rem;">
                <i class="fas fa-clipboard-check mr-2"></i> Review Berkas — {{ $event->nama_event }}
                @if($pendingCount > 0)
                    <span style="background:var(--accent-red); color:#fff; font-size: 0.75rem; padding:2px 10px; margin-left:6px; vertical-align:middle;" title="Jumlah berkas yang masih menunggu direview">
                        ({{ $pendingCount }})
                    </span>
                @endif
            </h3>
            <a href="{{ route('lomba.manage', $event->slug) }}" class="btn btn-sm"
               style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        @if($event->branches->isNotEmpty())
        <div class="card-body pb-0" style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
            <form method="GET" action="{{ route('lomba.berkas', $event->slug) }}" class="form-row align-items-end">
                <div class="col-12 col-lg-4 mb-3">
                    <label class="small mb-1 font-weight-bold text-muted text-uppercase">Filter Cabang Lomba</label>
                    <select name="branch_id" class="form-control form-control-sm custom-select" onchange="this.form.submit()">
                        <option value="">- Semua Cabang -</option>
                        @foreach($event->branches as $b)
                            <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto mb-3">
                    <span class="text-muted" style="font-size: 0.7rem;">
                        <i class="fas fa-users mr-1"></i> {{ $registrants->count() }} peserta ditampilkan
                    </span>
                </div>
            </form>
        </div>
        @endif

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-flat mb-0">
                    <thead>
                        <tr>
                            <th style="width:35px;">#</th>
                            <th>Nama</th>
                            <th style="width:100px;">NPM</th>
                            <th>Cabang</th>
                            @foreach($documentRequirements as $docReq)
                                <th>{{ $docReq->nama_dokumen }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($registrants as $reg)
                        @php
                            $nama = $reg->mahasiswa->nama_mahasiswa ?? $reg->user->name ?? '—';
                            $npm = $reg->mahasiswa->npm_mahasiswa ?? '—';
                            $docsByReq = $reg->documents->keyBy('event_document_requirement_id');
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $nama }}</td>
                            <td>{{ $npm }}</td>
                            <td>{{ $reg->branch->nama_cabang ?? '—' }}</td>
                            @foreach($documentRequirements as $docReq)
                                @php $doc = $docReq->branch_id === $reg->branch_id ? $docsByReq->get($docReq->id) : null; @endphp
                                <td>
                                    @if($docReq->branch_id !== $reg->branch_id)
                                        <span class="status-pill status-none">—</span>
                                    @elseif(!$doc)
                                        <span class="status-pill status-none">Belum Upload</span>
                                    @else
                                        @php
                                            $fileUrl = \Illuminate\Support\Str::startsWith($doc->file_path, ['http://', 'https://']) ? $doc->file_path : asset($doc->file_path);
                                        @endphp
                                        <span class="status-pill status-{{ $doc->status }}">
                                            {{ $doc->status === 'approved' ? 'Disetujui' : ($doc->status === 'rejected' ? 'Ditolak' : 'Menunggu') }}
                                        </span>
                                        <a href="{{ $fileUrl }}" target="_blank" class="d-block mt-1" style="font-size: 0.6rem;">{{ $docReq->jenis_file === 'link' ? 'Kunjungi Link' : 'Lihat berkas' }}</a>
                                        <div>
                                            <form action="{{ route('lomba.berkas.review', [$event->slug, $doc->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-mini btn-approve">Approve</button>
                                            </form>
                                            <button type="button" class="btn btn-mini btn-reject" data-toggle="modal" data-target="#rejectModal{{ $doc->id }}">Reject</button>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ 4 + $documentRequirements->count() }}" class="text-center text-muted py-4">Belum ada pendaftar.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal tolak berkas (di luar table agar markup HTML valid) --}}
@foreach($registrants as $reg)
    @foreach($reg->documents as $doc)
        <div class="modal fade" id="rejectModal{{ $doc->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="border-radius:0;">
                    <form action="{{ route('lomba.berkas.review', [$event->slug, $doc->id]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <div class="modal-header">
                            <h6 class="modal-title">Tolak Berkas</h6>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <label class="small mb-1">Catatan (opsional)</label>
                            <textarea name="review_notes" class="form-control form-control-sm" rows="3" placeholder="Alasan penolakan...">{{ $doc->review_notes }}</textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm" style="background:var(--accent-red);color:#fff;">Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endforeach

@endsection
