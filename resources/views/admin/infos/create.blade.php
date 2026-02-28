@extends('templateAdminLTE.home')

@section('sub-breadcrumb', 'Tambah Info')
@section('page-title', 'Tambah Info')

@section('content')

    <div class="row">
        <div class="col-md-8">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Info Terkini</h3>
                </div>

                <div class="card-body">

                    <form action="{{ route('infos.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Isi Informasi</label>
                            <textarea name="isi" class="form-control" rows="6" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Status Publikasi</label>
                            <select name="is_published" class="form-control">
                                <option value="1">Published</option>
                                <option value="0">Draft</option>
                            </select>
                        </div>

                        <button class="btn btn-success">
                            <i class="fa fa-save"></i> Simpan
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
