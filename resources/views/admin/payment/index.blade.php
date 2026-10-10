@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Kelola Pembayaran')

@section('content')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-green-dark: #219150;
        --bg-light: #ecf0f1;
        --text-grey: #2c3e50;
    }
    .card, .btn, .badge { border-radius: 0 !important; box-shadow: none !important; }
    .card-header-flat { background-color: var(--primary-green); color: #fff; padding: 12px 20px; border-bottom: none; }
    .btn-action-flat {
        border: none; color: #fff; padding: 5px 12px;
        font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    }
    .btn-action-flat:hover { color: #fff; opacity: .9; }
    .btn-green-flat { background-color: var(--primary-green); }
    .table-flat thead th {
        background-color: var(--bg-light); color: #7f8c8d;
        border-bottom: 2px solid #bdc3c7; text-transform: uppercase;
        font-size: 0.62rem; letter-spacing: .5px; vertical-align: middle; padding: 9px 12px;
    }
    .table-flat tbody td { vertical-align: middle !important; color: var(--text-grey); padding: 10px 12px; font-size: 0.775rem; }
    .table-hover tbody tr:hover { background-color: #f9fbfb; }
</style>

<div class="container-fluid">

    @if(session('success'))
    <div class="alert fade show mb-3" style="background-color:#2ecc71;color:#fff;border:none;border-radius:0;">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" style="color:#fff;opacity:1"><span>&times;</span></button>
    </div>
    @endif

    <div class="card border-0">
        <div class="card-header-flat">
            <h3 class="card-title font-weight-bold mb-0" style="font-size: 1rem;">
                <i class="fas fa-money-bill-wave mr-2"></i> Kelola Pembayaran Lomba
            </h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-flat mb-0">
                    <thead>
                        <tr>
                            <th class="text-center pl-3" style="width:50px;">No</th>
                            <th>Nama Lomba</th>
                            <th class="text-center" style="width:120px;">Peserta</th>
                            <th class="text-center" style="width:130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($lombaList as $lomba)
                        <tr>
                            <td class="text-center pl-3 text-muted">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold" style="font-size: 0.83rem;">{{ $lomba->nama_event }}</td>
                            <td class="text-center">{{ $lomba->registrations_count }}</td>
                            <td class="text-center">
                                <a href="{{ route('payment.show', $lomba->slug) }}" class="btn btn-sm btn-action-flat btn-green-flat">
                                    <i class="fas fa-cog mr-1"></i> Kelola
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-money-bill-wave fa-3x mb-3 d-block" style="opacity:.2; color:#27ae60;"></i>
                                Belum ada lomba yang ditugaskan kepada Anda.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
