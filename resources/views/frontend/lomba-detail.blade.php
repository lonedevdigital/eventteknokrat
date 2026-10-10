@extends('layouts.frontend')

@php
    $thumb = $event->flyer ?: $event->thumbnail;
    if ($thumb && !\Illuminate\Support\Str::startsWith($thumb, ['http://', 'https://'])) {
        $thumb = asset($thumb);
    }
    $thumb = $thumb ?: 'https://placehold.co/1280x720/ffffff/ab021c?text=Event+Teknokrat+University';

    $coverBig = $event->cover_big ?: ($event->flyer ?: $event->thumbnail);
    if ($coverBig && !\Illuminate\Support\Str::startsWith($coverBig, ['http://', 'https://'])) {
        $coverBig = asset($coverBig);
    }
    $coverBig = $coverBig ?: $thumb;

    $creatorName = $event->creator->name ?? 'Universitas Teknokrat Indonesia';
    $categoryName = $event->category->nama_kategori ?? 'Lomba';

    $juknisUrl = $event->juknis_file
        ? (\Illuminate\Support\Str::startsWith($event->juknis_file, ['http://', 'https://']) ? $event->juknis_file : asset($event->juknis_file))
        : null;

    $branchesCount = $event->branches ? $event->branches->count() : 0;
    $registeredBranchIds = $registeredBranchIds ?? [];

    // Pisahkan nama judul agar formatnya seperti referensi (contoh: "GEMATIK 2026 - FAKULTAS..." -> Title: "GEMATIK 2026", Subtitle: "Fakultas...")
    $rawName = $event->nama_event;
    if (\Illuminate\Support\Str::contains($rawName, ' - ')) {
        $parts = explode(' - ', $rawName, 2);
        $titleText = trim($parts[0]);
        $subTitleText = trim($parts[1]);
    } else {
        $titleText = $rawName;
        $subTitleText = $event->partisipasi_prodi ?: 'Gelaran Mahasiswa Teknologi Informasi & Komunikasi Teknokrat Sang Juara';
    }

    // Pisahkan kata terakhir pada judul untuk warna merah (contoh: "GEMATIK" [dark] + "2026" [red])
    $words = explode(' ', $titleText);
    if (count($words) > 1) {
        $lastWord = array_pop($words);
        $firstWords = implode(' ', $words);
    } else {
        $firstWords = $titleText;
        $lastWord = '';
    }

    $shortEventName = $firstWords ?: 'GEMATIK';
@endphp

@section('title', $event->nama_event)

@section('content')
<div class="lomba-detail-wrapper">
    <style>
        .lomba-detail-wrapper {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 12px 16px 60px;
            font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #0f172a;
        }

        /* ── HERO GRID ── */
        .lomba-hero {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
            align-items: center;
            margin-bottom: 50px;
        }
        @media (min-width: 1024px) {
            .lomba-hero {
                grid-template-columns: 1.15fr 0.85fr;
                gap: 40px;
            }
        }

        /* ── BADGES ROW ── */
        .lomba-badge-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .lomba-badge-red-outline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            border: 1px solid #ffd0d5;
            background: #fff5f6;
            color: #d60029;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .lomba-badge-gray {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 9999px;
            background: #eef1f6;
            color: #334155;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        /* ── HERO TYPOGRAPHY ── */
        .lomba-title {
            font-family: 'Sora', sans-serif;
            font-size: 2.5rem;
            font-weight: 900;
            line-height: 1.06;
            letter-spacing: -0.03em;
            color: #0f172a;
            margin: 0 0 12px 0;
        }
        @media (min-width: 640px) {
            .lomba-title { font-size: 3.25rem; }
        }
        @media (min-width: 1024px) {
            .lomba-title { font-size: 3.75rem; }
        }
        .lomba-title .title-red {
            color: #d60029;
        }
        .lomba-subtitle {
            font-size: 1.15rem;
            font-weight: 700;
            color: #7b1828;
            line-height: 1.4;
            margin: 0 0 16px 0;
        }
        .lomba-desc {
            font-size: 0.96rem;
            line-height: 1.65;
            color: #475569;
            margin: 0 0 24px 0;
            white-space: pre-line;
        }

        /* ── ACTION BUTTONS ── */
        .lomba-actions-row {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }
        .btn-lomba-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            background: #d60029;
            color: #ffffff !important;
            font-size: 14.5px;
            font-weight: 800;
            padding: 13px 26px;
            border-radius: 14px;
            text-decoration: none;
            box-shadow: 0 4px 18px rgba(214, 0, 41, 0.28);
            transition: background 0.2s, transform 0.15s;
        }
        .btn-lomba-primary:hover {
            background: #b80022;
            transform: translateY(-1px);
        }
        .btn-lomba-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            background: #f0f3fa;
            color: #1e293b !important;
            font-size: 14.5px;
            font-weight: 700;
            padding: 13px 24px;
            border-radius: 14px;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-lomba-secondary:hover {
            background: #e2e7f2;
            transform: translateY(-1px);
        }

        /* ── 3 METRIC BOXES ── */
        .lomba-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }
        @media (max-width: 480px) {
            .lomba-stats-grid {
                grid-template-columns: 1fr;
            }
        }
        .lomba-stat-box {
            background: #ffffff;
            border: 1px solid #eef2f6;
            border-radius: 16px;
            padding: 16px 18px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }
        .stat-value {
            font-family: 'Sora', sans-serif;
            font-size: 1.55rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 6px;
            display: block;
        }
        .stat-value.red { color: #d60029; }
        .stat-value.dark { color: #0f172a; }
        .stat-label {
            font-size: 11.5px;
            font-weight: 600;
            color: #64748b;
            line-height: 1.3;
            display: block;
        }

        /* ── RIGHT SHOWCASE CARD ── */
        .lomba-right-card {
            background: #ffffff;
            border: 1px solid #eef2f6;
            border-radius: 22px;
            padding: 14px;
            box-shadow: 0 10px 32px rgba(15, 23, 42, 0.06);
        }
        .lomba-card-img-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 10;
            border-radius: 16px;
            overflow: hidden;
            background: #0f172a;
        }
        .lomba-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .lomba-card-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.92) 0%, rgba(0, 0, 0, 0.45) 45%, rgba(0, 0, 0, 0.05) 75%);
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            padding: 18px;
            color: #ffffff;
            gap: 12px;
        }
        .overlay-tag {
            display: inline-block;
            font-size: 9.5px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 4px;
        }
        .overlay-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
            margin: 0;
        }
        .overlay-sub {
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.85);
            margin-top: 3px;
        }
        .overlay-badge-red {
            background: #d60029;
            color: #ffffff;
            font-size: 10px;
            font-weight: 900;
            padding: 5px 10px;
            border-radius: 7px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            flex-shrink: 0;
        }

        /* ── BOTTOM PILLS UNDER PHOTO ── */
        .lomba-pill-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 12px;
        }
        .lomba-pill-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafd;
            border: 1px solid #eef2f8;
            border-radius: 14px;
            padding: 10px 12px;
        }
        .lomba-pill-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: #fee2e2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .lomba-pill-text-1 {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }
        .lomba-pill-text-2 {
            font-size: 10.5px;
            font-weight: 600;
            color: #64748b;
            margin-top: 2px;
        }

        /* ── SECTION CABANG LOMBA ── */
        .cabang-section {
            margin-top: 50px;
            scroll-margin-top: 80px;
        }
        .cabang-header {
            text-align: center;
            max-width: 680px;
            margin: 0 auto 36px;
        }
        .cabang-header-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #d60029;
            background: #fee2e2;
            padding: 4px 12px;
            border-radius: 9999px;
            margin-bottom: 10px;
        }
        .cabang-header-title {
            font-family: 'Sora', sans-serif;
            font-size: 2rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin: 0 0 10px 0;
        }
        .cabang-header-desc {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.6;
            margin: 0;
        }

        .cabang-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 24px;
        }
        @media (min-width: 640px) {
            .cabang-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            .cabang-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }

        .cabang-card {
            background: #ffffff;
            border: 1px solid #eef2f6;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
        }
        .cabang-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }

        .cabang-img-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #0f172a;
            overflow: hidden;
        }
        .cabang-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }
        .cabang-card:hover .cabang-img {
            transform: scale(1.05);
        }
        .cabang-img-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.2) 50%, rgba(0,0,0,0) 100%);
            display: flex;
            align-items: flex-end;
            padding: 14px 16px;
        }
        .cabang-img-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            color: #ffffff;
            margin: 0;
            line-height: 1.25;
        }
        .cabang-badges-top {
            position: absolute;
            top: 10px;
            left: 10px;
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            z-index: 2;
        }
        .cabang-badge {
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 3px 8px;
            border-radius: 6px;
        }
        .cabang-badge.free { background: #059669; color: #fff; }
        .cabang-badge.paid { background: #f59e0b; color: #fff; }
        .cabang-badge.team { background: rgba(255, 255, 255, 0.95); color: #1e293b; }

        .cabang-body {
            padding: 16px 18px 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
            justify-content: space-between;
            gap: 16px;
        }
        .cabang-desc {
            font-size: 12.5px;
            line-height: 1.55;
            color: #64748b;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .cabang-fee-pill {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .fee-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .fee-label { color: #64748b; font-weight: 600; font-size: 11.5px; }
        .fee-val { font-weight: 800; color: #0f172a; }
        .fee-val.red { color: #d60029; }

        .cabang-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11.5px;
        }
        .link-juknis {
            color: #d60029;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .link-juknis:hover { text-decoration: underline; }
        .cp-text { color: #64748b; font-size: 11px; }

        .cabang-btn-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .btn-branch-reg {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-branch-reg.primary { background: #d60029; color: #ffffff !important; }
        .btn-branch-reg.primary:hover { background: #b80022; }
        .btn-branch-reg.secondary { background: #eef2f8; color: #1e293b !important; }
        .btn-branch-reg.secondary:hover { background: #e2e8f0; }

        .btn-branch-done {
            background: #ecfdf5;
            color: #047857;
            font-size: 12px;
            font-weight: 800;
            padding: 11px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #a7f3d0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-branch-closed {
            background: #f1f5f9;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 700;
            padding: 11px;
            border-radius: 10px;
            text-align: center;
        }

        /* ── RESPONSIVE TOGGLES HERO ── */
        @media (max-width: 767px) {
            .lomba-hero-desktop { display: none !important; }
            .lomba-hero-mobile { display: block !important; }
        }
        @media (min-width: 768px) {
            .lomba-hero-desktop { display: grid !important; }
            .lomba-hero-mobile { display: none !important; }
        }

        /* ── RESPONSIVE CABANG (DESKTOP GRID VS MOBILE LIST CARD) ── */
        .cabang-card-desktop {
            display: flex;
        }
        .cabang-card-mobile {
            display: none;
        }

        @media (max-width: 639px) {
            .cabang-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
            }
            .cabang-card-desktop {
                display: none !important;
            }
            .cabang-card-mobile {
                display: flex !important;
                background: #ffffff;
                border: 1px solid #eef2f6;
                border-radius: 18px;
                padding: 12px;
                box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
                flex-direction: column;
                gap: 10px;
                transition: border-color 0.15s, box-shadow 0.15s;
            }
            .cabang-header {
                margin-bottom: 20px;
            }
            .cabang-header-title {
                font-size: 1.35rem;
            }
            .cabang-header-desc {
                font-size: 12px;
            }
        }
        @media (min-width: 640px) {
            .cabang-card-desktop {
                display: flex !important;
            }
            .cabang-card-mobile {
                display: none !important;
            }
        }

        /* ── STYLING KHUSUS LIST CARD MOBILE ── */
        .c-mobile-top {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .c-mobile-img-wrap {
            width: 80px;
            height: 80px;
            min-width: 80px;
            border-radius: 12px;
            overflow: hidden;
            background: #0f172a;
            position: relative;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            flex-shrink: 0;
        }
        .c-mobile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .c-mobile-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .c-mobile-badges {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }
        .c-badge-m {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 2px 6px;
            border-radius: 5px;
        }
        .c-badge-m.free { background: #059669; color: #fff; }
        .c-badge-m.paid { background: #f59e0b; color: #fff; }
        .c-badge-m.team { background: #f1f5f9; color: #1e293b; border: 1px solid #e2e8f0; }
        .c-badge-m.ind { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

        .c-mobile-title {
            font-family: 'Sora', sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .c-mobile-pricing {
            font-size: 12px;
            font-weight: 700;
            color: #d60029;
            display: flex;
            align-items: baseline;
            gap: 4px;
            flex-wrap: wrap;
        }
        .c-unit {
            font-size: 10px;
            font-weight: 600;
            color: #64748b;
        }
        .c-sub-price {
            font-size: 10px;
            font-weight: 600;
            color: #64748b;
        }
        .c-free-text {
            color: #059669;
            font-weight: 700;
        }
        .c-mobile-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 10.5px;
            margin-top: 2px;
            flex-wrap: wrap;
        }
        .c-juknis-link {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            color: #d60029;
            font-weight: 700;
            text-decoration: none;
        }
        .c-juknis-link:hover { text-decoration: underline; }
        .c-cp-text {
            color: #64748b;
            font-size: 10px;
        }

        .c-mobile-actions {
            padding-top: 8px;
            border-top: 1px solid #f1f5f9;
        }
        .c-btn-grid-m {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }
        .c-btn-m {
            padding: 7px 10px !important;
            font-size: 11.5px !important;
            border-radius: 8px !important;
            min-height: 32px !important;
        }

        /* ── MOBILE CARD STYLING (SESUAI GAMBAR REFERENSI USER) ── */
        .mobile-card-container {
            background: #ffffff;
            border: 1px solid #eef2f6;
            border-radius: 22px;
            padding: 12px;
            box-shadow: 0 4px 24px rgba(15, 23, 42, 0.05);
            margin-bottom: 36px;
        }
        .mobile-img-box {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 10;
            border-radius: 16px;
            overflow: hidden;
            background: #0f172a;
        }
        .mobile-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .mobile-badges-top {
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 2;
        }
        .mobile-badge-red {
            background: #d60029;
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 9999px;
            letter-spacing: 0.04em;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        .mobile-badge-white {
            background: rgba(255, 255, 255, 0.95);
            color: #0f172a;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 9999px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .mobile-content-box {
            padding: 14px 6px 6px 6px;
        }
        .mobile-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.35rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin: 0;
        }
        .mobile-title .title-red {
            color: #d60029;
        }
        .mobile-subtitle {
            font-size: 12.5px;
            font-weight: 700;
            color: #7b1828;
            margin: 4px 0 0 0;
            line-height: 1.35;
        }
        .mobile-desc {
            font-size: 12px;
            color: #475569;
            line-height: 1.55;
            margin: 10px 0 0 0;
        }
        .mobile-metrics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 16px;
        }
        .mobile-metric-item {
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.02);
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .metric-header-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
        }
        .metric-header-row svg {
            color: #d60029;
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }
        .metric-big-val {
            font-family: 'Sora', sans-serif;
            font-size: 1.15rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.2;
            margin-top: 3px;
        }
        .metric-big-val.red {
            color: #d60029;
        }
        .metric-sub-val {
            font-size: 10px;
            color: #64748b;
            font-weight: 600;
            line-height: 1.2;
        }
        .mobile-btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            background: #d60029;
            color: #ffffff !important;
            font-size: 13.5px;
            font-weight: 800;
            padding: 13px;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(214, 0, 41, 0.25);
            margin-top: 16px;
            box-sizing: border-box;
        }
        .mobile-btn-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }
        .mobile-btn-sub {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #edf2f8;
            color: #1e293b !important;
            font-size: 12px;
            font-weight: 700;
            padding: 10px 12px;
            border-radius: 12px;
            text-decoration: none;
            text-align: center;
            box-sizing: border-box;
        }
    </style>

    {{-- ============================================================ --}}
    {{-- TAMPILAN KHUSUS MOBILE (SESUAI GAMBAR REFERENSI USER)         --}}
    {{-- ============================================================ --}}
    <div class="lomba-hero-mobile">
        <div class="mobile-card-container">
            {{-- Foto Poster dengan Badges --}}
            <div class="mobile-img-box">
                <img src="{{ $coverBig ?: $thumb }}" alt="{{ $event->nama_event }}">
                <div class="mobile-badges-top">
                    <span class="mobile-badge-red">
                        {{ $branchesCount > 0 ? $branchesCount . ' CABANG KEJUARAAN' : 'KOMPETISI UTAMA' }}
                    </span>
                    <span class="mobile-badge-white">
                        {{ $categoryName ?: 'Kampus Sang Juara' }}
                    </span>
                </div>
            </div>

            <div class="mobile-content-box">
                {{-- Judul Utama --}}
                <h1 class="mobile-title">
                    {{ $firstWords }} @if($lastWord)<span class="title-red">{{ $lastWord }}</span>@endif
                </h1>

                @if(!empty($subTitleText))
                    <p class="mobile-subtitle">{{ $subTitleText }}</p>
                @endif

                @if(!empty($event->deskripsi))
                    <p class="mobile-desc">{{ $event->deskripsi }}</p>
                @endif

                {{-- 4 Kotak Metrik 2x2 (100% Real Data dari Database) --}}
                <div class="mobile-metrics-grid">
                    {{-- 1. Total Hadiah / Biaya --}}
                    <div class="mobile-metric-item">
                        <div class="metric-header-row">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                            <span>Total Hadiah</span>
                        </div>
                        <div class="metric-big-val red">
                            {{ $event->hadiah_penghargaan ? \Illuminate\Support\Str::limit($event->hadiah_penghargaan, 14) : ($event->tipe_bayar === 'berbayar' ? 'Berbayar' : 'Gratis') }}
                        </div>
                        <div class="metric-sub-val">
                            {{ $event->hadiah_penghargaan ? 'Hadiah Kejuaraan' : 'Biaya Pendaftaran' }}
                        </div>
                    </div>

                    {{-- 2. Kategori Lomba --}}
                    <div class="mobile-metric-item">
                        <div class="metric-header-row">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m21 16-4 4-4-4"/><path d="M17 20V4"/><path d="m3 8 4-4 4 4"/><path d="M7 4v16"/></svg>
                            <span>Kategori Lomba</span>
                        </div>
                        <div class="metric-big-val">
                            {{ $branchesCount }} Cabang
                        </div>
                        <div class="metric-sub-val">
                            Tingkat {{ ucfirst($event->partisipasi_skala ?: 'Universitas') }}
                        </div>
                    </div>

                    {{-- 3. Delegasi Kontingen / Peserta --}}
                    <div class="mobile-metric-item">
                        <div class="metric-header-row">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span>Delegasi Kontingen</span>
                        </div>
                        <div class="metric-big-val">
                            {{ $event->jumlah_peserta ?? 0 }} Peserta
                        </div>
                        <div class="metric-sub-val">
                            Terverifikasi Aktif
                        </div>
                    </div>

                    {{-- 4. Trofi / Penghargaan --}}
                    <div class="mobile-metric-item">
                        <div class="metric-header-row">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                            <span>Trofi Bergengsi</span>
                        </div>
                        <div class="metric-big-val">
                            Piala Bergilir
                        </div>
                        <div class="metric-sub-val">
                            {{ \Illuminate\Support\Str::limit($creatorName, 18) }}
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi Mobile --}}
                <a href="#daftar-cabang" class="mobile-btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Daftar Delegasi / Kontingen
                </a>

                <div class="mobile-btn-grid">
                    @if($juknisUrl)
                        <a href="{{ $juknisUrl }}" target="_blank" class="mobile-btn-sub">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Juknis &amp; Rulebook
                        </a>
                    @else
                        <a href="#daftar-cabang" class="mobile-btn-sub">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            Info Lomba
                        </a>
                    @endif

                    <a href="#daftar-cabang" class="mobile-btn-sub">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>
                        Pilih {{ $branchesCount }} Cabang
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- HERO SECTION DESKTOP (100% REAL DATA DARI DATABASE)          --}}
    {{-- ============================================================ --}}
    <section class="lomba-hero lomba-hero-desktop">
        
        {{-- KOLOM KIRI --}}
        <div>
            {{-- Badge Header --}}
            <div class="lomba-badge-row">
                <div class="lomba-badge-red-outline">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/><line x1="7" y1="2" x2="7" y2="22"/><line x1="17" y1="2" x2="7" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/></svg>
                    <span>{{ strtoupper($event->partisipasi_skala ? 'SKALA ' . $event->partisipasi_skala : ($event->category->nama_kategori ?? 'LOMBA')) }}</span>
                </div>
                @if($branchesCount > 0)
                    <div class="lomba-badge-gray">
                        {{ $branchesCount }} DIVISI CABANG LOMBA
                    </div>
                @endif
            </div>

            {{-- Judul Utama (Contoh: "GEMATIK 2026") --}}
            <h1 class="lomba-title">
                {{ $firstWords }} @if($lastWord)<span class="title-red">{{ $lastWord }}</span>@endif
            </h1>

            {{-- Subtitle Slogan --}}
            @if(!empty($subTitleText))
                <p class="lomba-subtitle">
                    {{ $subTitleText }}
                </p>
            @endif

            {{-- Deskripsi --}}
            @if(!empty($event->deskripsi))
                <p class="lomba-desc">{{ $event->deskripsi }}</p>
            @endif

            {{-- Tombol CTA --}}
            <div class="lomba-actions-row">
                @if($branchesCount > 0)
                    <a href="#daftar-cabang" class="btn-lomba-primary">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Eksplorasi {{ $branchesCount }} Cabang Lomba
                    </a>
                @endif

                @if($juknisUrl)
                    <a href="{{ $juknisUrl }}" target="_blank" class="btn-lomba-secondary">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                        Buku Panduan &amp; Silabus
                    </a>
                @endif
            </div>

            {{-- Kotak Metrik Riil --}}
            <div class="lomba-stats-grid">
                @if($branchesCount > 0)
                    <div class="lomba-stat-box">
                        <span class="stat-value red">{{ $branchesCount }}</span>
                        <span class="stat-label">Divisi Cabang Lomba</span>
                    </div>
                @endif

                @if($event->tanggal_pelaksanaan)
                    <div class="lomba-stat-box">
                        <span class="stat-value dark">
                            {{ \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y') }}
                        </span>
                        <span class="stat-label">Tanggal Pelaksanaan</span>
                    </div>
                @endif

                @if($event->tipe_bayar)
                    <div class="lomba-stat-box">
                        <span class="stat-value red">
                            {{ ucfirst($event->tipe_bayar) }}
                        </span>
                        <span class="stat-label">Biaya Pendaftaran</span>
                    </div>
                @endif
            </div>

        </div>

        {{-- KOLOM KANAN (KARTU POSTER & HIGHLIGHTS) --}}
        <div>
            <div class="lomba-right-card">
                <div class="lomba-card-img-wrap">
                    <img src="{{ $coverBig }}" alt="{{ $event->nama_event }}" class="lomba-card-img">
                    <div class="lomba-card-overlay">
                        <div>
                            @if($event->tempat_pelaksanaan)
                                <span class="overlay-tag">{{ strtoupper($event->tempat_pelaksanaan) }}</span>
                            @endif
                            <h3 class="overlay-title">{{ $event->nama_event }}</h3>
                            @if($event->tempat_pelaksanaan)
                                <div class="overlay-sub">{{ $event->tempat_pelaksanaan }}</div>
                            @endif
                        </div>
                        <span class="overlay-badge-red">{{ $categoryName }}</span>
                    </div>
                </div>

                {{-- Pill Highlights Data Riil --}}
                <div class="lomba-pill-grid">
                    @if($event->tanggal_pendaftaran)
                        <div class="lomba-pill-box">
                            <div class="lomba-pill-icon">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </div>
                            <div>
                                <div class="lomba-pill-text-1">{{ \Carbon\Carbon::parse($event->tanggal_pendaftaran)->format('d M Y') }}</div>
                                <div class="lomba-pill-text-2">Batas Pendaftaran</div>
                            </div>
                        </div>
                    @endif

                    @if($event->waktu_pelaksanaan)
                        <div class="lomba-pill-box">
                            <div class="lomba-pill-icon">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <div>
                                <div class="lomba-pill-text-1">{{ \Carbon\Carbon::parse($event->waktu_pelaksanaan)->format('H:i') }} WIB</div>
                                <div class="lomba-pill-text-2">Waktu Pelaksanaan</div>
                            </div>
                        </div>
                    @elseif($event->partisipasi_skala)
                        <div class="lomba-pill-box">
                            <div class="lomba-pill-icon">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <div>
                                <div class="lomba-pill-text-1">Skala {{ ucfirst($event->partisipasi_skala) }}</div>
                                <div class="lomba-pill-text-2">Tingkat Partisipasi</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </section>

    {{-- ============================================================ --}}
    {{-- SECTION LIST SELURUH CABANG LOMBA & PENDAFTARAN              --}}
    {{-- ============================================================ --}}
    <section id="daftar-cabang" class="cabang-section">
        
        <div class="cabang-header">
            <div class="cabang-header-tag">
                <span style="width:6px; height:6px; border-radius:99px; background:#d60029;"></span>
                Divisi &amp; Cabang Perlombaan
            </div>
            <h2 class="cabang-header-title">Pilih Cabang Lomba &amp; Daftarkan Dirimu</h2>
            <p class="cabang-header-desc">
                Tersedia {{ $branchesCount }} divisi kejuaraan. Daftarkan diri secara individu atau bentuk tim terbaikmu bersama mahasiswa lainnya.
            </p>
        </div>

        @if($event->branches->isEmpty())
            <div style="background:#ffffff; border:1px dashed #cbd5e1; border-radius:18px; padding:40px; text-align:center; color:#64748b; font-weight:600; font-size:14px;">
                Belum ada cabang lomba yang terdaftar pada event ini.
            </div>
        @else
            <div class="cabang-grid">
                @foreach($event->branches as $b)
                    @php
                        $coverBranch = $b->cover_image;
                        if ($coverBranch && !\Illuminate\Support\Str::startsWith($coverBranch, ['http://', 'https://'])) {
                            $coverBranch = asset($coverBranch);
                        }
                        $coverBranch = $coverBranch ?: $coverBig;

                        $branchJuknis = $b->juknis_file
                            ? (\Illuminate\Support\Str::startsWith($b->juknis_file, ['http://', 'https://']) ? $b->juknis_file : asset($b->juknis_file))
                            : null;

                        $isUserRegisteredInBranch = in_array($b->id, $registeredBranchIds);
                        $isTeamAllowed = ($b->maks_anggota_tim > 1);

                        $hargaIndividu = $b->harga_individual ?? $b->harga_pendaftaran;
                        $hargaTim = $b->harga_tim ?? $b->harga_pendaftaran;
                    @endphp

                    {{-- KARTU DESKTOP --}}
                    <div class="cabang-card cabang-card-desktop">
                        {{-- Gambar & Badge --}}
                        <div class="cabang-img-wrap">
                            <img src="{{ $coverBranch }}" alt="{{ $b->nama_cabang }}" class="cabang-img">
                            
                            <div class="cabang-badges-top">
                                @if($b->tipe_bayar === 'berbayar')
                                    <span class="cabang-badge paid">Berbayar</span>
                                @else
                                    <span class="cabang-badge free">Gratis</span>
                                @endif

                                @if($isTeamAllowed)
                                    <span class="cabang-badge team">Maks. {{ $b->maks_anggota_tim }} Anggota / Tim</span>
                                @else
                                    <span class="cabang-badge team">Individu</span>
                                @endif
                            </div>

                            <div class="cabang-img-overlay">
                                <h3 class="cabang-img-title">{{ $b->nama_cabang }}</h3>
                            </div>
                        </div>

                        {{-- Body Cabang --}}
                        <div class="cabang-body">
                            <div style="display:flex; flex-direction:column; gap:12px;">
                                @if(!empty($b->deskripsi))
                                    <p class="cabang-desc">
                                        {{ $b->deskripsi }}
                                    </p>
                                @endif

                                <div class="cabang-fee-pill">
                                    <div class="fee-row">
                                        <span class="fee-label">Biaya Individu:</span>
                                        <span class="fee-val">
                                            {{ $b->tipe_bayar === 'berbayar' && $hargaIndividu > 0 ? 'Rp ' . number_format($hargaIndividu, 0, ',', '.') : 'Gratis' }}
                                        </span>
                                    </div>
                                    @if($isTeamAllowed)
                                        <div class="fee-row">
                                            <span class="fee-label">Biaya Tim:</span>
                                            <span class="fee-val red">
                                                {{ $b->tipe_bayar === 'berbayar' && $hargaTim > 0 ? 'Rp ' . number_format($hargaTim, 0, ',', '.') : 'Gratis' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <div class="cabang-meta-row">
                                    @if($branchJuknis)
                                        <a href="{{ $branchJuknis }}" target="_blank" class="link-juknis">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                            Juknis Cabang
                                        </a>
                                    @else
                                        <span></span>
                                    @endif

                                    @if($b->contact_person_nama)
                                        <span class="cp-text">CP: <strong>{{ $b->contact_person_nama }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            {{-- Tombol Aksi Registrasi --}}
                            <div style="padding-top:10px; border-top:1px solid #f1f5f9;">
                                @if($isUserRegisteredInBranch)
                                    <div class="btn-branch-done">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                                        Kamu Sudah Terdaftar
                                    </div>
                                @elseif($isRegistrationClosed)
                                    <div class="btn-branch-closed">
                                        Pendaftaran Ditutup
                                    </div>
                                @else
                                    @auth
                                        @if($isTeamAllowed)
                                            <div class="cabang-btn-grid">
                                                <a href="{{ route('frontend.teams.create', ['event' => $event->slug, 'branch_id' => $b->id]) }}" class="btn-branch-reg primary">
                                                    Daftar Tim
                                                </a>
                                                <form method="POST" action="{{ route('frontend.events.register', $event->slug ?: $event->id) }}" style="margin:0;">
                                                    @csrf
                                                    <input type="hidden" name="branch_id" value="{{ $b->id }}">
                                                    <button type="submit" class="btn-branch-reg secondary" onclick="return confirm('Mendaftar sebagai individu di cabang {{ $b->nama_cabang }}?')">
                                                        Individu
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <form method="POST" action="{{ route('frontend.events.register', $event->slug ?: $event->id) }}" style="margin:0;">
                                                @csrf
                                                <input type="hidden" name="branch_id" value="{{ $b->id }}">
                                                <button type="submit" class="btn-branch-reg primary">
                                                    Daftar Sekarang (Individu)
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn-branch-reg secondary">
                                            Login untuk Mendaftar
                                        </a>
                                    @endauth
                                @endif
                            </div>

                        </div>
                    </div>

                    {{-- KARTU KHUSUS MOBILE (COMPACT LIST CARD) --}}
                    <div class="cabang-card-mobile">
                        <div class="c-mobile-top">
                            <div class="c-mobile-img-wrap">
                                <img src="{{ $coverBranch }}" alt="{{ $b->nama_cabang }}" class="c-mobile-img">
                            </div>
                            <div class="c-mobile-info">
                                <div class="c-mobile-badges">
                                    @if($b->tipe_bayar === 'berbayar')
                                        <span class="c-badge-m paid">Berbayar</span>
                                    @else
                                        <span class="c-badge-m free">Gratis</span>
                                    @endif

                                    @if($isTeamAllowed)
                                        <span class="c-badge-m team">Tim (Maks {{ $b->maks_anggota_tim }})</span>
                                    @else
                                        <span class="c-badge-m ind">Individu</span>
                                    @endif
                                </div>

                                <h3 class="c-mobile-title">{{ $b->nama_cabang }}</h3>

                                <div class="c-mobile-pricing">
                                    @if($b->tipe_bayar === 'berbayar')
                                        @if($isTeamAllowed && $hargaTim > 0)
                                            <span>Rp {{ number_format($hargaTim, 0, ',', '.') }}</span>
                                            <span class="c-unit">/tim</span>
                                            @if($hargaIndividu > 0 && $hargaIndividu != $hargaTim)
                                                <span class="c-sub-price">(Individu: Rp {{ number_format($hargaIndividu, 0, ',', '.') }})</span>
                                            @endif
                                        @elseif($hargaIndividu > 0)
                                            <span>Rp {{ number_format($hargaIndividu, 0, ',', '.') }}</span>
                                            <span class="c-unit">/org</span>
                                        @else
                                            <span class="c-free-text">Gratis</span>
                                        @endif
                                    @else
                                        <span class="c-free-text">Gratis</span>
                                    @endif
                                </div>

                                @if($branchJuknis || $b->contact_person_nama)
                                    <div class="c-mobile-meta">
                                        @if($branchJuknis)
                                            <a href="{{ $branchJuknis }}" target="_blank" class="c-juknis-link">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                                Juknis
                                            </a>
                                        @endif
                                        @if($b->contact_person_nama)
                                            <span class="c-cp-text">CP: {{ $b->contact_person_nama }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Tombol Aksi Registrasi Mobile --}}
                        <div class="c-mobile-actions">
                            @if($isUserRegisteredInBranch)
                                <div class="btn-branch-done c-btn-m">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                                    Kamu Sudah Terdaftar
                                </div>
                            @elseif($isRegistrationClosed)
                                <div class="btn-branch-closed c-btn-m">
                                    Pendaftaran Ditutup
                                </div>
                            @else
                                @auth
                                    @if($isTeamAllowed)
                                        <div class="c-btn-grid-m">
                                            <a href="{{ route('frontend.teams.create', ['event' => $event->slug, 'branch_id' => $b->id]) }}" class="btn-branch-reg primary c-btn-m">
                                                Daftar Tim
                                            </a>
                                            <form method="POST" action="{{ route('frontend.events.register', $event->slug ?: $event->id) }}" style="margin:0;">
                                                @csrf
                                                <input type="hidden" name="branch_id" value="{{ $b->id }}">
                                                <button type="submit" class="btn-branch-reg secondary c-btn-m" onclick="return confirm('Mendaftar sebagai individu di cabang {{ $b->nama_cabang }}?')">
                                                    Individu
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('frontend.events.register', $event->slug ?: $event->id) }}" style="margin:0;">
                                            @csrf
                                            <input type="hidden" name="branch_id" value="{{ $b->id }}">
                                            <button type="submit" class="btn-branch-reg primary c-btn-m">
                                                Daftar Sekarang (Individu)
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn-branch-reg secondary c-btn-m">
                                        Login untuk Mendaftar
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </section>
</div>
@endsection
