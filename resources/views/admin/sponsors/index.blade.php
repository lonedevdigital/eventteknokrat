@extends('templateAdminLTE.home')

@section('sub-breadcrumb', 'Sponsor')
@section('page-title', 'Sponsor')

@section('content')
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Sponsor</h3>
                    <a href="{{ route('sponsors.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah Sponsor
                    </a>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                        <tr>
                            <th style="width: 60px">#</th>
                            <th style="width: 170px">Logo</th>
                            <th>Nama</th>
                            <th>Link</th>
                            <th style="width: 90px">Urutan</th>
                            <th style="width: 110px">Status</th>
                            <th style="width: 140px">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($sponsors as $sponsor)
                            @php
                                $logo = $sponsor->logo_path;
                                if ($logo && !\Illuminate\Support\Str::startsWith($logo, ['http://', 'https://'])) {
                                    $logo = asset($logo);
                                }
                            @endphp
                            <tr>
                                <td>{{ $sponsors->firstItem() + $loop->index }}</td>
                                <td>
                                    @if($logo)
                                        <img src="{{ $logo }}" alt="{{ $sponsor->nama }}" style="height:48px; width:auto; max-width:140px; object-fit:contain;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $sponsor->nama }}</td>
                                <td>
                                    @if($sponsor->link_url)
                                        <a href="{{ $sponsor->link_url }}" target="_blank" rel="noopener noreferrer">
                                            {{ \Illuminate\Support\Str::limit($sponsor->link_url, 34) }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $sponsor->urutan }}</td>
                                <td>
                                    @if($sponsor->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('sponsors.edit', $sponsor->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('sponsors.destroy', $sponsor->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus sponsor ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    Belum ada data sponsor.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sponsors->hasPages())
                    <div class="card-footer">
                        {{ $sponsors->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

