@extends('templateAdminLTE.home')
@section('sub-breadcrumb', 'Event Terlaksana')

@section('content')

    <div class="card">
        <div class="card-header">
            <h5>Event Terlaksana</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Nama Event</th>
                    <th>Tanggal</th>
                    <th>Registrasi</th>
                    <th>Hadir</th>
                    <th>Detail</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($events as $ev)
                    <tr>
                        <td>{{ $ev->nama_event }}</td>
                        <td>{{ $ev->tanggal_pelaksanaan }}</td>
                        <td>{{ $ev->registrations()->count() }}</td>
                        <td>{{ $ev->registrations()->where('status', 'attended')->count() }}</td>
                        <td>
                            <a href="{{ route('events.detail', $ev->id) }}"
                               class="btn btn-info btn-sm">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
