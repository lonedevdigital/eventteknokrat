<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Juara - {{ $event->nama_event }}</title>

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

<h2>Data Juara: {{ $event->nama_event }}</h2>

@foreach($groups as $group)
    <h3 class="cabang">{{ $group['nama_cabang'] ? 'Cabang Lomba: ' . $group['nama_cabang'] : 'Juara' }}</h3>
    <table>
        <thead>
        <tr>
            <th style="width:18%;">Juara</th>
            <th style="width:27%;">Nama / Tim</th>
            <th style="width:15%;">NPM</th>
            <th style="width:40%;">Universitas / Sekolah</th>
        </tr>
        </thead>
        <tbody>
        @forelse($group['winners'] as $winner)
            <tr>
                <td>{{ $winner['label'] }}</td>
                <td>
                    {{ $winner['nama'] }}
                    @if($winner['is_team'])
                        (Tim)
                    @endif
                </td>
                <td>{{ $winner['is_team'] ? '-' : ($winner['npm'] ?: '-') }}</td>
                <td>{{ $winner['is_team'] ? '-' : $winner['asal'] }}</td>
            </tr>
            @if($winner['is_team'] && !empty($winner['members']))
                <tr>
                    <td></td>
                    <td colspan="3">
                        <ol>
                            @foreach($winner['members'] as $member)
                                <li>{{ $member['nama'] }}{{ $member['npm'] ? ' — ' . $member['npm'] : '' }} — {{ $member['asal'] }}</li>
                            @endforeach
                        </ol>
                    </td>
                </tr>
            @endif
        @empty
            <tr><td colspan="4" class="center">Belum ada juara ditetapkan.</td></tr>
        @endforelse
        </tbody>
    </table>
@endforeach

</body>
</html>
