<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Absensi Event</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f7f7f7;
            color: #ab021c;
            font-family: 'Manrope', sans-serif;
            padding: 20px;
        }
        .card {
            width: min(92vw, 460px);
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        }
        .title {
            margin: 0;
            font-size: 1.45rem;
            font-weight: 800;
        }
        .subtitle {
            margin: 8px 0 16px;
            font-size: 0.94rem;
            font-weight: 700;
            color: rgba(171, 2, 28, 0.8);
        }
        .qr {
            display: inline-flex;
            padding: 14px;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 0 0 2px rgba(171, 2, 28, 0.08);
        }
        .meta {
            margin-top: 12px;
            font-size: 0.82rem;
            font-weight: 700;
            color: rgba(171, 2, 28, 0.78);
        }
        .token {
            margin-top: 10px;
            font-size: 0.75rem;
            color: rgba(171, 2, 28, 0.65);
            word-break: break-all;
        }
    </style>
</head>
<body>
    <section class="card">
        <h1 class="title">QR Absensi Event</h1>
        <p class="subtitle">{{ $event?->nama_event ?? 'Event' }}</p>

        <div class="qr">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(320)->margin(1)->generate($qrToken) !!}
        </div>

        <p class="meta">Berlaku sampai: {{ $qr->expires_at?->format('d M Y H:i') ?? '-' }} WIB</p>
        <p class="token">Token: {{ $qrToken }}</p>
    </section>
</body>
</html>
