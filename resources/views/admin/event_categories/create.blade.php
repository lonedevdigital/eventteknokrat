@extends('templateAdminLTE.home')

@section('content')

    <div class="container-fluid">

        <h3 class="mb-3">Tambah Kategori Event</h3>

        <form action="{{ route('event-categories.store') }}" method="POST">
            @csrf

            <div class="card">
                <div class="card-header">
                    Form Tambah Kategori
                </div>

                <div class="card-body">
                    <div class="form-group mb-3">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control"
                               placeholder="Contoh: Seminar, Workshop, Lomba"
                               value="{{ old('nama_kategori') }}" required>

                        @error('nama_kategori')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button class="btn btn-success">Simpan</button>
                    <a href="{{ route('event-categories.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>

        </form>

    </div>

@endsection
