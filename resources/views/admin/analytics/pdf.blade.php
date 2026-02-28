<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Event - {{ $event->nama_event }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            margin-bottom: 5px;
            text-align: center;
        }

        p {
            margin-top: 0;
            margin-bottom: 15px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        table th {
            background: #efefef;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>

<h2>Rekap Event: {{ $event->nama_event }}</h2>
<p>Tanggal Pelaksanaan: {{ $event->tanggal_pelaksanaan }}</p>

<table>
    <thead>
    <tr>
        <th>Nama Mahasiswa</th>
        <th>NPM</th>
        <th>Program Studi</th>
        <th>Status Kehadiran</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($registrations as $r)
        <tr>
            <td>{{ $r->mahasiswa->nama_mahasiswa }}</td>
            <td>{{ $r->mahasiswa->npm_mahasiswa }}</td>
            <td>{{ $r->mahasiswa->nama_program_studi }}</td>
            <td>{{ strtoupper($r->status) }}</td>
        </tr>
    @endforeach

    @if($registrations->count() === 0)
        <tr>
            <td colspan="4" class="center">Tidak ada data peserta.</td>
        </tr>
    @endif
    </tbody>
</table>

</body>
</html>
