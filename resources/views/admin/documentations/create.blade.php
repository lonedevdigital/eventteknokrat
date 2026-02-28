@extends('templateAdminLTE.home')

@section('sub-breadcrumb', 'Tambah Dokumentasi')
@section('page-title', 'Tambah Dokumentasi')

@section('content')

    <div class="row">
        <div class="col-md-8">

            {{-- FORM UPLOAD MULTI FILE --}}
            <div class="card">
                <div class="card-header">
                    <strong>Tambah Dokumentasi — {{ $event->nama_event }}</strong>
                </div>

                <div class="card-body">

                    <form action="{{ route('documentations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="event_id" value="{{ $event->id }}">

                        <div class="form-group mb-3">
                            <label><strong>Upload File Dokumentasi</strong></label>
                            <input type="file" name="files[]" class="form-control" multiple required>
                            <small class="text-muted">
                                Bisa upload banyak file sekaligus. Format: JPG, PNG, MP4, PDF — max 10MB per file.
                            </small>
                        </div>

                        <button class="btn btn-success">Upload</button>
                        <a href="{{ route('documentations.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>

                </div>
            </div>

        </div>
    </div>


    {{-- LIST DOKUMENTASI YANG SUDAH DIUPLOAD --}}
    <div class="row mt-4">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <strong>Daftar Dokumentasi</strong>
                </div>

                <div class="card-body">

                    @if($event->documentations->isEmpty())
                        <p class="text-muted">Belum ada dokumentasi di event ini.</p>
                    @else

                        <div class="row">
                            @foreach($event->documentations as $doc)
                                <div class="col-md-3 mb-4">

                                    <div class="border rounded p-2 text-center">

                                        {{-- PREVIEW FILE --}}
                                        @php
                                            $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                                        @endphp

                                        {{-- Gambar --}}
                                        @if(in_array($ext, ['jpg','jpeg','png','gif']))
                                            <img src="{{ asset($doc->file_path) }}"
                                                 class="img-fluid rounded mb-2" style="max-height: 150px;">
                                        @endif

                                        {{-- Video --}}
                                        @if(in_array($ext, ['mp4','mov','avi']))
                                            <video controls style="max-width: 100%; max-height: 150px;">
                                                <source src="{{ asset($doc->file_path) }}">
                                            </video>
                                        @endif

                                        {{-- PDF --}}
                                        @if($ext === 'pdf')
                                            <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                Lihat PDF
                                            </a>
                                        @endif

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('documentations.destroy', $doc->id) }}"
                                              method="POST" class="mt-2">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Hapus dokumentasi ini?')">
                                                Hapus
                                            </button>
                                        </form>

                                    </div>

                                </div>
                            @endforeach
                        </div>

                    @endif

                </div>
            </div>

        </div>
    </div>

@endsection
