@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Kelola Juara')

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
    .btn-mini { padding: 2px 7px; font-size: 0.6rem; border:none; margin-right:2px; margin-top:3px; }
    .juara-pill { background:#fff8e1; color:#b8860b; border:1px solid #ffe08a; font-size: 10px; padding: 2px 8px; font-weight: 700; display: inline-block; }
    ol.anggota-list { margin: 0; padding-left: 16px; }
</style>

<div class="container-fluid">

    @if(session('success'))
    <div class="alert fade show mb-3" style="background-color:#2ecc71;color:#fff;border:none;border-radius:0;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff;opacity:1"><span>&times;</span></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert fade show mb-3" style="background-color:#e74c3c;color:#fff;border:none;border-radius:0;">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff;opacity:1"><span>&times;</span></button>
    </div>
    @endif

    <div class="card border-0 mb-3">
        <div class="card-header-flat d-flex align-items-center justify-content-between">
            <h3 class="card-title font-weight-bold mb-0" style="font-size: 0.95rem;">
                <i class="fas fa-medal mr-2"></i> Kelola Juara — {{ $event->nama_event }}
            </h3>
            <div>
                <a href="{{ route('lomba.export.juara.pdf', $event->slug) }}" class="btn btn-sm mr-1"
                   style="background:rgba(255,255,255,.9);color:var(--primary-green-dark);border:1px solid rgba(255,255,255,.4);font-weight:600;">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>
                <a href="{{ route('lomba.manage', $event->slug) }}" class="btn btn-sm"
                   style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    @foreach($sections as $section)
        @php $branch = $section['branch']; @endphp
        <div class="card border-0 mb-3" style="border:1px solid #e2e8f0 !important;">
            <div class="card-header bg-white" style="border-bottom:2px solid var(--primary-green);">
                <h6 class="card-title font-weight-bold mb-0" style="font-size: 0.82rem;">
                    <i class="fas fa-trophy mr-1" style="color:var(--primary-green);"></i>
                    {{ $branch ? 'Cabang Lomba: ' . $branch->nama_cabang : 'Juara' }}
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('lomba.juara.store', $event->slug) }}" class="form-inline mb-3 juara-add-form">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branch->id ?? '' }}">
                    <select name="event_registration_id" class="form-control form-control-sm mr-2 mb-2" style="min-width:260px;" required>
                        <option value="">— Pilih Peserta / Tim —</option>
                        @foreach($section['options'] as $option)
                            <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="juara_label" class="form-control form-control-sm mr-2 mb-2"
                           placeholder="Contoh: Juara 1 / Juara Harapan 1" required maxlength="100">
                    <button type="submit" class="btn btn-sm mb-2" style="background:var(--primary-green);color:#fff;">
                        <i class="fas fa-plus mr-1"></i> Tambah Juara
                    </button>
                </form>

                @if(empty($section['options']))
                    <p class="text-muted small mb-3">Belum ada peserta/tim terdaftar untuk {{ $branch ? 'cabang ini' : 'lomba ini' }}.</p>
                @endif

                <div class="table-responsive">
                    <table class="table table-flat mb-0">
                        <thead>
                            <tr>
                                <th style="width:35px;">#</th>
                                <th style="width:140px;">Juara</th>
                                <th>Nama / Tim</th>
                                <th style="width:100px;">NPM</th>
                                <th>Universitas / Asal</th>
                                <th style="width:60px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($section['winners'] as $winner)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td><span class="juara-pill">{{ $winner['label'] }}</span></td>
                                <td>
                                    <span class="font-weight-bold">{{ $winner['nama'] }}</span>
                                    @if($winner['is_team'])
                                        <span class="badge badge-secondary ml-1">Tim</span>
                                        @if(!empty($winner['members']))
                                            <ol class="anggota-list mt-1">
                                                @foreach($winner['members'] as $member)
                                                    <li>{{ $member['nama'] }} @if($member['npm']) ({{ $member['npm'] }}) @endif — {{ $member['asal'] }}</li>
                                                @endforeach
                                            </ol>
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $winner['is_team'] ? '-' : ($winner['npm'] ?: '-') }}</td>
                                <td>{{ $winner['is_team'] ? '-' : $winner['asal'] }}</td>
                                <td>
                                    <form method="POST" action="{{ route('lomba.juara.destroy', [$event->slug, $winner['id']]) }}"
                                          onsubmit="return confirm('Hapus juara ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-mini" style="background:var(--accent-red);color:#fff;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">Belum ada juara ditetapkan.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
    document.querySelectorAll('.juara-add-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            var btn = form.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; }
        });
    });
</script>

@endsection
