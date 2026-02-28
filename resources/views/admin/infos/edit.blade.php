@extends('templateAdminLTE.home')

@section('sub-breadcrumb', 'Edit Info')
@section('page-title', 'Edit Info')

@section('content')

    <div class="row">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Info Terkini</h3>
                </div>

                <div class="card-body">

                    <form action="{{ route('infos.update', $info->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" name="judul" class="form-control"
                                   value="{{ $info->judul }}" required>
                        </div>

                        <div class="form-group">
                            <label>Isi Informasi</label>
                            <textarea name="isi" class="form-control" rows="6" required>{{ $info->isi }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Status Publikasi</label>
                            <select name="is_published" class="form-control">
                                <option value="1" {{ $info->is_published ? 'selected' : '' }}>Published</option>
                                <option value="0" {{ !$info->is_published ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>

                        <button class="btn btn-primary">
                            <i class="fa fa-save"></i> Update
                        </button>

                        <a href="{{ route('infos.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </div>
    </div>

@endsection
