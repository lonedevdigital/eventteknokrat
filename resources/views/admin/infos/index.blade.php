@extends('templateAdminLTE.home')

@section('sub-breadcrumb', 'Info Terkini')
@section('page-title', 'Info Terkini')

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
                    <h3 class="card-title">Daftar Info Terkini</h3>
                    <a href="{{ route('infos.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah Info
                    </a>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                        <tr>
                            <th style="width: 60px">#</th>
                            <th>Judul</th>
                            <th>Cuplikan</th>
                            <th>Status</th>
                            <th>Update</th>
                            <th style="width: 140px">Aksi</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($infos as $info)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $info->judul }}</td>
                                <td>{{ \Illuminate\Support\Str::limit(strip_tags($info->isi), 60) }}</td>

                                <td>
                                    @if($info->is_published)
                                        <span class="badge badge-success">Published</span>
                                    @else
                                        <span class="badge badge-secondary">Draft</span>
                                    @endif
                                </td>

                                <td>{{ $info->updated_at->format('d/m/Y H:i') }}</td>

                                <td>
                                    <a href="{{ route('infos.edit', $info->id) }}"
                                       class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form action="{{ route('infos.destroy', $info->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus info ini?')">
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
                                <td colspan="6" class="text-center text-muted py-3">
                                    Belum ada info.
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
