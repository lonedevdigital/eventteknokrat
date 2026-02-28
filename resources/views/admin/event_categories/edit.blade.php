@extends('templateAdminLTE.home')

@section('content')

    <div class="container-fluid">

        <h3 class="mb-3">Edit Kategori Event</h3>

        <form action="{{ route('event-categories.update', $eventCategory->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-header">
                    Form Edit Kategori
                </div>

                <div class="card-body">
                    <div class="form-group mb-3">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control"
                               value="{{ old('nama_kategori', $eventCategory->nama_kategori) }}" required>

                        @error('nama_kategori')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button class="btn btn-primary">Update</button>
                    <a href="{{ route('event-categories.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>

        </form>

    </div>

@endsection
