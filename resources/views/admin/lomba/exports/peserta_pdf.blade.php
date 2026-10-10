<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Peserta - {{ $event->nama_event }}</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h2 { margin-bottom: 5px; text-align: center; }
        p.total { margin-top: 0; margin-bottom: 15px; text-align: center; font-weight: bold; }
        h3.cabang { background: #ecf0f1; padding: 6px 8px; margin-top: 20px; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table th, table td { border: 1px solid #000; padding: 5px; text-align: left; vertical-align: top; }
        table th { background: #efefef; font-weight: bold; }
        .center { text-align: center; }
        ol { margin: 0; padding-left: 16px; }
    </style>
</head>

<body>

<h2>Data Peserta: {{ $event->nama_event }}</h2>
<p class="total">Total Keseluruhan Peserta: {{ $totalPeserta }}</p>

@foreach($groups as $group)
    <h3 class="cabang">{{ $group['nama_cabang'] ? 'Cabang Lomba: ' . $group['nama_cabang'] : 'Peserta' }}</h3>
    <table>
        <thead>
        <tr>
            <th style="width:25%;">Nama Tim / Mahasiswa</th>
            <th style="width:15%;">NPM</th>
            <th style="width:40%;">Anggota Tim</th>
            <th style="width:20%;">Cabang Lomba</th>
        </tr>
        </thead>
        <tbody>
        @forelse($group['rows'] as $row)
            <tr>
                <td>{{ $row['nama'] }}</td>
                <td>{{ $row['npm'] ?: '-' }}</td>
                <td>
                    @if(!empty($row['anggota']))
                        <ol>
                            @foreach(explode("\n", $row['anggota']) as $line)
                                <li>{{ preg_replace('/^\d+\.\s*/', '', $line) }}</li>
                            @endforeach
                        </ol>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $row['cabang'] }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="center">Belum ada peserta.</td></tr>
        @endforelse
        </tbody>
    </table>
@endforeach

</body>
</html>
