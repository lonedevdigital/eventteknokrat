<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekapitulasi Event</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 16pt; }
        .header p { margin: 5px 0 0; font-size: 9pt; color: #555; }

        .meta { margin-bottom: 15px; font-size: 9pt; }
        .meta table { width: 100%; border: none; }
        .meta td { padding: 2px; }

        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #444; padding: 6px 8px; text-align: left; vertical-align: middle; }
        table.data th { background-color: #f2f2f2; font-weight: bold; text-align: center; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8pt; color: #fff; background: #555; }
        .bg-success { color: #006400; font-weight: bold; } /* Hijau tua untuk cetak */
        .bg-danger { color: #8b0000; } /* Merah tua untuk cetak */

        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 8pt; color: #777; border-top: 1px solid #ddd; padding-top: 5px; }
    </style>
</head>
<body>

<div class="header">
    <h2>Laporan Rekapitulasi Event</h2>
    <p>Sistem Manajemen Event & Kemahasiswaan</p>
</div>

<div class="meta">
    <table>
        <tr>
            <td width="15%"><strong>Dicetak Oleh</strong></td>
            <td width="35%">: {{ $user->name }} ({{ strtoupper($user->role) }})</td>
            <td width="15%"><strong>Tanggal Cetak</strong></td>
            <td>: {{ now()->translatedFormat('d F Y, H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Filter</strong></td>
            <td colspan="3">:
                @if(request('year') || request('month'))
                    Custom ({{ request('day') }} {{ request('month') }} {{ request('year') }})
                @else
                    {{ ucfirst($filter) }} Range
                @endif
            </td>
        </tr>
    </table>
</div>

<table class="data">
    <thead>
    <tr>
        <th width="5%">No</th>
        <th>Nama Event</th>
        <th width="15%">Kategori</th>
        <th width="15%">Tanggal</th>
        <th width="8%">Reg</th>
        <th width="8%">Hadir</th>
        <th width="8%">Absen</th>
        <th width="10%">%</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rekapEvents as $ev)
        @php
            $reg = $ev->registrations_count;
            $att = $ev->attended_count;
            $absent = max(0, $reg - $att);
            $percent = $reg > 0 ? round(($att/$reg)*100) : 0;
        @endphp
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>
                {{ $ev->nama_event }}
            </td>
            <td>{{ $ev->category->nama_kategori ?? '-' }}</td>
            <td class="text-center">
                {{ \Carbon\Carbon::parse($ev->tanggal_pelaksanaan)->translatedFormat('d M Y') }}
            </td>
            <td class="text-center">{{ $reg }}</td>
            <td class="text-center bg-success">{{ $att }}</td>
            <td class="text-center bg-danger">{{ $absent }}</td>
            <td class="text-center">
                {{ $percent }}%
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data event.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    Dicetak otomatis oleh sistem. Dokumen ini valid tanpa tanda tangan basah.
</div>

</body>
</html>
