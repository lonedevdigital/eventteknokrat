<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Pembayaran - {{ $event->nama_event }}</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h2 { margin-bottom: 5px; text-align: center; }
        p { margin-top: 0; margin-bottom: 15px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #000; padding: 6px; text-align: left; }
        table th { background: #efefef; font-weight: bold; }
        .center { text-align: center; }
    </style>
</head>

<body>

<h2>Rekap Pembayaran: {{ $event->nama_event }}</h2>
<p>Total Peserta: {{ $rows->count() }}</p>

<table>
    <thead>
    <tr>
        <th>Jenis Pendaftaran</th>
        <th>Cabang Lomba</th>
        <th>Nama Mahasiswa / Nama Tim</th>
        <th>NPM</th>
        <th>Status Pembayaran</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($rows as $row)
        <tr>
            <td>{{ $row['jenis_pendaftaran'] }}</td>
            <td>{{ $row['cabang_lomba'] }}</td>
            <td>{{ $row['nama'] }}</td>
            <td>{{ $row['npm'] ?: '-' }}</td>
            <td>{{ $row['status_pembayaran'] }}</td>
        </tr>
    @endforeach

    @if($rows->count() === 0)
        <tr>
            <td colspan="5" class="center">Tidak ada data pembayaran.</td>
        </tr>
    @endif
    </tbody>
</table>

</body>
</html>
