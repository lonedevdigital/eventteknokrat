@extends('layouts.frontend')

@section('content')
    <div class="grid min-w-0 gap-5">
        <section class="mx-auto w-full min-w-0 max-w-full overflow-hidden px-0.5 pt-2 md:pt-3" id="info-slider">
            <style>
                #info-slider .info-marquee {
                    width: 100%;
                    max-width: 100%;
                    min-width: 0;
                    overflow: hidden;
                }

                #info-slider .info-track {
                    display: flex;
                    min-width: max-content;
                    width: max-content;
                    align-items: center;
                    gap: 1.8rem;
                    padding: 0.7rem 0;
                    animation: info-slide-left 34s linear infinite;
                    will-change: transform;
                }

                @media (hover: hover) and (pointer: fine) {
                    #info-slider .info-marquee:hover .info-track {
                        animation-play-state: paused;
                    }
                }

                #info-slider .info-item {
                    display: inline-flex;
                    flex: 0 0 auto;
                    align-items: center;
                    gap: 0.55rem;
                    white-space: nowrap;
                    min-width: 0;
                    color: #7b0f15;
                }

                #info-slider .info-dot {
                    flex: 0 0 auto;
                    width: 8px;
                    height: 8px;
                    border-radius: 9999px;
                    background: #ab021c;
                }

                #info-slider .info-title {
                    display: inline-block;
                    font-size: 0.69rem;
                    font-weight: 800;
                }

                #info-slider .info-body {
                    display: inline-block;
                    white-space: nowrap;
                    font-size: 0.64rem;
                    font-weight: 600;
                    opacity: 0.9;
                }

                #info-slider .info-date {
                    border-radius: 9999px;
                    background: rgba(171, 2, 28, 0.11);
                    padding: 2px 8px;
                    font-size: 0.6rem;
                    font-weight: 700;
                }

                .section-title-accent {
                    margin-top: 0.62rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.72rem;
                }

                .section-title-accent .accent-line {
                    width: clamp(58px, 9vw, 126px);
                    height: 2px;
                    border-radius: 999px;
                    background: linear-gradient(90deg, rgba(171, 2, 28, 0) 0%, rgba(171, 2, 28, 0.62) 35%, #ab021c 100%);
                }

                .section-title-accent .accent-line:last-child {
                    transform: scaleX(-1);
                }

                .section-title-accent .accent-core {
                    width: 13px;
                    height: 13px;
                    border-radius: 999px;
                    background: #ab021c;
                    box-shadow: 0 0 0 4px rgba(171, 2, 28, 0.16);
                }

                .section-title-accent .accent-core.icon {
                    width: auto;
                    height: auto;
                    background: transparent;
                    box-shadow: none;
                    color: #ab021c;
                    font-size: 12px;
                    line-height: 1;
                }

                @keyframes info-slide-left {
                    from { transform: translateX(0); }
                    to { transform: translateX(-50%); }
                }

                @media (max-width: 767px) {
                    #info-slider .info-track {
                        gap: 1.2rem;
                        padding: 0.58rem 0;
                    }

                    .section-title-accent {
                        gap: 0.5rem;
                    }

                    .section-title-accent .accent-line {
                        width: clamp(44px, 15vw, 78px);
                    }

                    .section-title-accent .accent-core {
                        width: 11px;
                        height: 11px;
                    }
                }

            </style>

            <div class="w-full min-w-0 max-w-full overflow-hidden rounded-2xl border border-[#ab021c]/15 bg-white shadow-sm">
                <div class="flex w-full min-w-0 max-w-full items-stretch overflow-hidden">
                    <div class="flex shrink-0 items-center bg-[#ab021c] px-3 py-2 text-[11px] font-extrabold uppercase tracking-[0.5px] text-white md:px-4 md:text-xs">
                        Info Terkini
                    </div>
                    <div class="min-w-0 flex-1 overflow-hidden px-3 md:px-4">
                        @if($latestInfos->isEmpty())
                            <p class="py-2 text-xs font-semibold text-[#ab021c]/80 md:text-sm">
                                Belum ada informasi terbaru saat ini.
                            </p>
                        @else
                            <div class="info-marquee" aria-label="Informasi terkini berjalan otomatis">
                                <div class="info-track">
                                    @for($tickerLoop = 0; $tickerLoop < 2; $tickerLoop++)
                                        @foreach($latestInfos as $info)
                                            @php
                                                $infoBody = trim(preg_replace('/\s+/', ' ', strip_tags((string) $info->isi)));
                                                $infoDate = $info->published_at
                                                    ? $info->published_at->format('d M Y')
                                                    : ($info->updated_at ? $info->updated_at->format('d M Y') : 'Update');
                                            @endphp
                                            <span class="info-item" aria-hidden="{{ $tickerLoop === 1 ? 'true' : 'false' }}">
                                                <span class="info-dot"></span>
                                                <span class="info-title">{{ $info->judul }}</span>
                                                @if($infoBody !== '')
                                                    <span class="info-body">{{ $infoBody }}</span>
                                                @endif
                                                <span class="info-date">{{ $infoDate }}</span>
                                            </span>
                                        @endforeach
                                    @endfor
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto w-full px-0.5 py-5 md:py-6" id="hot-slider">
            <div class="mb-3 text-center">
                <h2 class="font-['Sora',sans-serif] text-[1rem] sm:text-[1.15rem] md:text-[clamp(1.3rem,2.8vw,2.55rem)] font-extrabold uppercase tracking-[0.6px] md:tracking-[1.2px] text-[#ab021c]">
                    Rekomendasi
                </h2>
                <div class="section-title-accent" aria-hidden="true">
                    <span class="accent-line"></span>
                    <span class="accent-core"></span>
                    <span class="accent-line"></span>
                </div>
            </div>

            @if($hotEvents->isEmpty())
                <div class="rounded-2xl bg-[#ab021c]/5 p-5 text-center text-sm font-semibold text-[#ab021c]">
                    Belum ada data event rekomendasi untuk ditampilkan.
                </div>
            @else
                <div class="relative">
                    @if($hotEvents->count() > 1)
                        <button
                            type="button"
                            class="absolute left-0 top-[40%] z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white text-xl font-extrabold text-[#ab021c] shadow-sm transition hover:bg-[#ab021c] hover:text-white md:left-[1%] md:top-1/2 md:h-14 md:w-14 md:text-3xl"
                            id="hotPrev"
                            aria-label="Geser kiri"
                        >
                            &#8249;
                        </button>
                    @endif

                    <div
                        class="grid grid-flow-col auto-cols-[90%] gap-0 overflow-hidden px-[5%] py-2 select-none md:auto-cols-[70%] md:gap-4 md:px-[15%]"
                        id="hotSlider"
                    >
                        @foreach($hotEvents as $event)
                            @php
                                $thumb = $event->thumbnail;
                                if ($thumb && !\Illuminate\Support\Str::startsWith($thumb, ['http://', 'https://'])) {
                                    $thumb = asset($thumb);
                                }

                                $isUpcoming = $event->tanggal_pelaksanaan && $event->tanggal_pelaksanaan >= now()->toDateString();
                            @endphp
                            <article
                                class="slide overflow-hidden rounded-3xl bg-white shadow-sm opacity-45 scale-90 transition-all duration-500 ease-out will-change-transform {{ $loop->first ? 'opacity-100 scale-100' : '' }}"
                                data-slide-index="{{ $loop->index }}"
                            >
                                <img
                                    src="{{ $thumb ?: 'https://placehold.co/640x360/ffffff/ab021c?text=Event+Teknokrat+University' }}"
                                    alt="{{ $event->nama_event }}"
                                    class="h-[clamp(180px,30vw,430px)] w-full object-cover"
                                >
                                <div class="grid gap-2 p-4 md:p-5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="inline-flex items-center rounded-full bg-[#ab021c] px-3 py-1 text-[11px] font-bold text-white">
                                            {{ $isUpcoming ? 'Akan Datang' : 'Selesai' }}
                                        </span>
                                        <small class="text-xs font-semibold text-[#ab021c]/85">
                                            {{ $event->tanggal_pelaksanaan ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y') : '-' }}
                                        </small>
                                    </div>
                                    <h3 class="text-lg font-extrabold leading-tight text-[#ab021c] md:text-[1.35rem]">
                                        {{ $event->nama_event }}
                                    </h3>
                                    <p class="text-sm font-semibold text-[#ab021c]/85">
                                        {{ $event->tempat_pelaksanaan ?: 'Lokasi belum tersedia' }}
                                    </p>
                                    <div>
                                        <a
                                            href="{{ route('frontend.events.show', $event->slug ?: $event->id) }}"
                                            class="inline-flex rounded-lg bg-[#ab021c]/10 px-3 py-1.5 text-xs font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white"
                                        >
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if($hotEvents->count() > 1)
                        <button
                            type="button"
                            class="absolute right-0 top-[40%] z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white text-xl font-extrabold text-[#ab021c] shadow-sm transition hover:bg-[#ab021c] hover:text-white md:right-[1%] md:top-1/2 md:h-14 md:w-14 md:text-3xl"
                            id="hotNext"
                            aria-label="Geser kanan"
                        >
                            &#8250;
                        </button>
                    @endif
                </div>

                @if($hotEvents->count() > 1)
                    <div class="mt-4 flex justify-center gap-2" id="hotDots">
                        @foreach($hotEvents as $event)
                            <button
                                type="button"
                                class="hot-dot h-[10px] rounded-full bg-[#ab021c] transition-all duration-200 {{ $loop->first ? 'w-7 opacity-100' : 'w-[10px] opacity-35' }}"
                                data-index="{{ $loop->index }}"
                                aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                aria-label="Slide {{ $loop->iteration }}"
                            ></button>
                        @endforeach
                    </div>
                @endif
            @endif
        </section>

        <section class="mx-auto w-full px-0.5 py-5 md:py-6" id="event-list">
            <style>
                #event-list .event-grid {
                    display: grid;
                    grid-template-columns: repeat(1, minmax(0, 1fr));
                    gap: 20px;
                }
                @media (min-width: 640px) {
                    #event-list .event-grid {
                        grid-template-columns: repeat(2, minmax(0, 1fr));
                        gap: 24px;
                    }
                }
                @media (min-width: 1024px) {
                    #event-list .event-grid {
                        grid-template-columns: repeat(3, minmax(0, 1fr));
                        gap: 26px;
                    }
                }
                #event-list .event-card {
                    background: #ffffff;
                    border-radius: 18px;
                    border: 1px solid #eef2f6;
                    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
                    display: flex;
                    flex-direction: column;
                    overflow: hidden;
                    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
                }
                #event-list .event-card:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 14px 30px -4px rgba(15, 23, 42, 0.1);
                    border-color: #e2e8f0;
                }
                #event-list .event-thumb-wrap {
                    position: relative;
                    width: 100%;
                    aspect-ratio: 16 / 9.5;
                    overflow: hidden;
                    background: #f1f5f9;
                    display: block;
                }
                #event-list .event-thumb-img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                    transition: transform 0.4s ease;
                }
                #event-list .event-card:hover .event-thumb-img {
                    transform: scale(1.04);
                }
                #event-list .event-badge-group {
                    position: absolute;
                    top: 12px;
                    left: 12px;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    z-index: 2;
                }
                #event-list .event-badge-red {
                    background: #d60029;
                    color: #ffffff;
                    font-size: 11px;
                    font-weight: 800;
                    text-transform: uppercase;
                    letter-spacing: 0.04em;
                    padding: 4px 10px;
                    border-radius: 6px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
                }
                #event-list .event-badge-light {
                    background: rgba(255, 255, 255, 0.95);
                    color: #1e293b;
                    font-size: 11px;
                    font-weight: 800;
                    text-transform: uppercase;
                    letter-spacing: 0.04em;
                    padding: 4px 10px;
                    border-radius: 6px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    backdrop-filter: blur(4px);
                }
                #event-list .event-img-footer {
                    position: absolute;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: linear-gradient(to top, rgba(0, 0, 0, 0.88) 0%, rgba(0, 0, 0, 0.45) 55%, rgba(0, 0, 0, 0) 100%);
                    padding: 24px 14px 10px 14px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 10px;
                    z-index: 2;
                }
                #event-list .event-location {
                    display: flex;
                    align-items: center;
                    gap: 5px;
                    color: #ffffff;
                    font-size: 12px;
                    font-weight: 600;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    min-width: 0;
                }
                #event-list .event-date {
                    color: #ffffff;
                    font-size: 12px;
                    font-weight: 700;
                    white-space: nowrap;
                    flex-shrink: 0;
                }
                #event-list .event-body {
                    padding: 18px 18px 20px 18px;
                    display: flex;
                    flex-direction: column;
                    flex: 1;
                    justify-content: space-between;
                    gap: 14px;
                }
                #event-list .event-category-row {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    color: #ab021c;
                    font-size: 12.5px;
                    font-weight: 700;
                }
                #event-list .event-title {
                    color: #0f172a;
                    font-size: 16.5px;
                    font-weight: 800;
                    line-height: 1.35;
                    margin: 0;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    transition: color 0.15s ease;
                }
                #event-list .event-card:hover .event-title {
                    color: #ab021c;
                }
                #event-list .event-desc {
                    color: #64748b;
                    font-size: 12.5px;
                    line-height: 1.55;
                    margin: 0;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                #event-list .event-format-pill {
                    background: #f4f5f9;
                    border-radius: 12px;
                    padding: 10px 14px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 8px;
                    margin-top: 2px;
                }
                #event-list .event-format-left {
                    display: flex;
                    align-items: center;
                    gap: 7px;
                    color: #1e293b;
                    font-size: 12px;
                    font-weight: 700;
                    min-width: 0;
                }
                #event-list .event-format-left span {
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
                #event-list .event-format-right {
                    color: #ab021c;
                    font-size: 12px;
                    font-weight: 800;
                    white-space: nowrap;
                    flex-shrink: 0;
                }
                #event-list .event-actions {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-top: 4px;
                }
                #event-list .event-btn-register {
                    flex: 1;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    background: #d60029;
                    color: #ffffff;
                    font-size: 13px;
                    font-weight: 700;
                    padding: 11px 16px;
                    border-radius: 12px;
                    text-decoration: none;
                    transition: background 0.2s ease, transform 0.15s ease;
                    text-align: center;
                    letter-spacing: 0.01em;
                }
                #event-list .event-btn-register:hover {
                    background: #b80022;
                    color: #ffffff;
                }
                #event-list .event-btn-bookmark {
                    width: 44px;
                    height: 44px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    background: #eef2f8;
                    color: #475569;
                    border-radius: 12px;
                    border: none;
                    cursor: pointer;
                    flex-shrink: 0;
                    transition: background 0.2s ease, color 0.2s ease, transform 0.15s ease;
                }
                #event-list .event-btn-bookmark:hover {
                    background: #e2e8f0;
                    color: #1e293b;
                }
                #event-list .event-btn-bookmark.active {
                    background: #fee2e2;
                    color: #dc2626;
                }
            </style>

            <div class="mb-5 text-center">
                <h2 class="font-['Sora',sans-serif] text-[0.9rem] sm:text-[1.05rem] md:text-[1.3rem] lg:text-[1.5rem] font-extrabold uppercase tracking-[0.55px] md:tracking-[0.85px] text-[#ab021c]">
                    Event Terbaru
                </h2>
                <div class="section-title-accent" aria-hidden="true">
                    <span class="accent-line"></span>
                    <span class="accent-core"></span>
                    <span class="accent-line"></span>
                </div>
            </div>

            @if($events->isEmpty())
                <div class="rounded-2xl bg-[#ab021c]/5 p-5 text-center text-sm font-semibold text-[#ab021c]">
                    Belum ada event untuk ditampilkan.
                </div>
            @else
                <div class="event-grid">
                    @foreach($events as $event)
                        @php
                            $thumb = $event->thumbnail;
                            if ($thumb && !\Illuminate\Support\Str::startsWith($thumb, ['http://', 'https://'])) {
                                $thumb = asset($thumb);
                            }
                            $thumb = $thumb ?: 'https://placehold.co/640x360/ffffff/ab021c?text=Event+Teknokrat+University';

                            $dateLabel = $event->tanggal_pelaksanaan
                                ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y')
                                : null;
                            $placeLabel = $event->tempat_pelaksanaan;

                            $badgeType = $event->category ? strtoupper($event->category->nama_kategori) : ($event->type === 'lomba' ? 'LOMBA' : 'EVENT');
                            $badgeSecondary = $event->partisipasi_skala ? strtoupper($event->partisipasi_skala) : null;

                            $categoryLabel = $event->partisipasi_prodi ?: ($event->category ? $event->category->nama_kategori : null);

                            $descText = trim(strip_tags((string) $event->deskripsi));

                            $waktuPelaksanaan = $event->waktu_pelaksanaan
                                ? \Carbon\Carbon::parse($event->waktu_pelaksanaan)->format('H:i') . ($event->waktu_pelaksanaan_selesai ? ' - ' . \Carbon\Carbon::parse($event->waktu_pelaksanaan_selesai)->format('H:i') : '') . ' WIB'
                                : null;

                            $tipeBayarLabel = null;
                            if ($event->tipe_bayar) {
                                if (strtolower($event->tipe_bayar) === 'berbayar') {
                                    $tipeBayarLabel = (isset($event->biaya) && $event->biaya > 0)
                                        ? 'Rp ' . number_format($event->biaya, 0, ',', '.')
                                        : 'Berbayar';
                                } else {
                                    $tipeBayarLabel = 'Gratis';
                                }
                            }

                            $actionLabel = $event->is_registration_closed
                                ? 'Lihat Detail'
                                : ($event->type === 'lomba' ? 'Daftar Lomba' : 'Daftar Event');
                            $eventUrl = route('frontend.events.show', $event->slug ?: $event->id);
                        @endphp
                        <article class="event-card">
                            {{-- Image and Badges --}}
                            <a href="{{ $eventUrl }}" class="event-thumb-wrap" title="{{ $event->nama_event }}">
                                <img
                                    src="{{ $thumb }}"
                                    alt="{{ $event->nama_event }}"
                                    class="event-thumb-img"
                                    loading="lazy"
                                >

                                <div class="event-badge-group">
                                    <span class="event-badge-red">{{ $badgeType }}</span>
                                    @if($badgeSecondary)
                                        <span class="event-badge-light">{{ $badgeSecondary }}</span>
                                    @endif
                                </div>

                                @if($placeLabel || $dateLabel)
                                    <div class="event-img-footer">
                                        <div class="event-location">
                                            @if($placeLabel)
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                <span>{{ $placeLabel }}</span>
                                            @endif
                                        </div>
                                        @if($dateLabel)
                                            <span class="event-date">{{ $dateLabel }}</span>
                                        @endif
                                    </div>
                                @endif
                            </a>

                            {{-- Card Body --}}
                            <div class="event-body">
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    @if($categoryLabel)
                                        <div class="event-category-row">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ab021c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                            </svg>
                                            <span>{{ $categoryLabel }}</span>
                                        </div>
                                    @endif

                                    <h3 class="event-title">
                                        <a href="{{ $eventUrl }}" style="color: inherit; text-decoration: none;">
                                            {{ $event->nama_event }}
                                        </a>
                                    </h3>

                                    @if($descText !== '')
                                        <p class="event-desc">
                                            {{ $descText }}
                                        </p>
                                    @endif
                                </div>

                                <div>
                                    @if($waktuPelaksanaan || $tipeBayarLabel)
                                        <div class="event-format-pill">
                                            @if($waktuPelaksanaan)
                                                <div class="event-format-left">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ab021c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="13" r="8"/>
                                                        <path d="M12 9v4l2.5 2"/>
                                                        <path d="M12 5V2"/>
                                                        <path d="M10 2h4"/>
                                                    </svg>
                                                    <span>{{ $waktuPelaksanaan }}</span>
                                                </div>
                                            @else
                                                <div></div>
                                            @endif

                                            @if($tipeBayarLabel)
                                                <span class="event-format-right">{{ $tipeBayarLabel }}</span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="event-actions">
                                        <a href="{{ $eventUrl }}" class="event-btn-register">
                                            {{ $actionLabel }}
                                        </a>
                                        <button
                                            type="button"
                                            class="event-btn-bookmark"
                                            data-event-id="{{ $event->id }}"
                                            aria-label="Simpan Event"
                                            title="Simpan Event"
                                        >
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-center">
                    @include('frontend.partials.pager', ['paginator' => $events])
                </div>
            @endif

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    try {
                        const saved = JSON.parse(localStorage.getItem('saved_events') || '[]');
                        document.querySelectorAll('#event-list .event-btn-bookmark').forEach(function (btn) {
                            const id = btn.getAttribute('data-event-id');
                            const svg = btn.querySelector('svg');
                            if (saved.includes(id)) {
                                btn.classList.add('active');
                                if (svg) svg.setAttribute('fill', 'currentColor');
                            }
                            btn.addEventListener('click', function (e) {
                                e.preventDefault();
                                e.stopPropagation();
                                let list = JSON.parse(localStorage.getItem('saved_events') || '[]');
                                if (list.includes(id)) {
                                    list = list.filter(function (item) { return item !== id; });
                                    btn.classList.remove('active');
                                    if (svg) svg.setAttribute('fill', 'none');
                                } else {
                                    list.push(id);
                                    btn.classList.add('active');
                                    if (svg) svg.setAttribute('fill', 'currentColor');
                                }
                                localStorage.setItem('saved_events', JSON.stringify(list));
                            });
                        });
                    } catch (e) {}
                });
            </script>
        </section>

        @include('frontend.partials.lomba-section')

        <section class="mx-auto w-full px-0.5 py-5 md:py-6" id="event-gallery">
            <div class="mb-4 text-center">
                <h2 class="font-['Sora',sans-serif] text-[0.96rem] sm:text-[1.1rem] md:text-[clamp(1.2rem,2.2vw,1.95rem)] font-extrabold uppercase tracking-[0.7px] md:tracking-[1px] text-[#ab021c]">
                    Galeri
                </h2>
                <div class="section-title-accent" aria-hidden="true">
                    <span class="accent-line"></span>
                    <span class="accent-core"></span>
                    <span class="accent-line"></span>
                </div>
            </div>

            @if($galleryEvents->isEmpty())
                <div class="rounded-2xl bg-[#ab021c]/5 p-5 text-center text-sm font-semibold text-[#ab021c]">
                    Belum ada dokumentasi galeri untuk ditampilkan.
                </div>
            @else
                @php
                    $galleryEventItems = $galleryEvents->map(function ($event) {
                        $photos = $event->documentations->map(function ($doc) use ($event) {
                            $docImage = $doc->file_path;
                            if ($docImage && !\Illuminate\Support\Str::startsWith($docImage, ['http://', 'https://'])) {
                                $docImage = asset($docImage);
                            }
                            $docImage = $docImage ?: 'https://placehold.co/900x600/ffffff/ab021c?text=Dokumentasi+Event';

                            $captionLabel = $doc->caption ?: $event->nama_event;

                            return [
                                'src' => $docImage,
                                'alt' => $captionLabel,
                                'caption' => $captionLabel,
                            ];
                        })->values();

                        return [
                            'event_id' => $event->id,
                            'event_name' => $event->nama_event,
                            'event_date' => $event->tanggal_pelaksanaan
                                ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y')
                                : '-',
                            'cover' => $photos->first()['src'] ?? 'https://placehold.co/900x600/ffffff/ab021c?text=Dokumentasi+Event',
                            'total_photos' => $photos->count(),
                            'photos' => $photos,
                        ];
                    })->filter(function ($item) {
                        return $item['total_photos'] > 0;
                    })->values();
                @endphp

                @if($galleryEventItems->isEmpty())
                    <div class="rounded-2xl bg-[#ab021c]/5 p-5 text-center text-sm font-semibold text-[#ab021c]">
                        Belum ada dokumentasi gambar untuk ditampilkan.
                    </div>
                @else
                    <div class="grid gap-3 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                        @foreach($galleryEventItems as $galleryEvent)
                            <button
                                type="button"
                                class="gallery-item group relative block aspect-[4/3] overflow-hidden rounded-2xl bg-[#ab021c]/10 text-left"
                                data-gallery-event-index="{{ $loop->index }}"
                            >
                                <img
                                    src="{{ $galleryEvent['cover'] }}"
                                    alt="{{ $galleryEvent['event_name'] }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-transparent"></div>
                                <div class="absolute left-2 top-2 rounded-full bg-black/55 px-2 py-1 text-[11px] font-bold text-white">
                                    {{ $galleryEvent['total_photos'] }} Foto
                                </div>
                                <div class="absolute inset-x-0 bottom-0 px-3 pb-2 pt-4 text-white">
                                    <p class="text-xs font-semibold text-white/80">{{ $galleryEvent['event_date'] }}</p>
                                    <p class="text-xs font-bold leading-snug">
                                        {{ \Illuminate\Support\Str::limit($galleryEvent['event_name'], 44) }}
                                    </p>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    <script type="application/json" id="galleryEventsData">
                        @json($galleryEventItems)
                    </script>

                    <div
                        id="galleryLightbox"
                        class="fixed inset-0 z-[120] hidden items-center justify-center bg-black/90 p-4"
                        role="dialog"
                        aria-modal="true"
                        aria-hidden="true"
                    >
                        <button
                            type="button"
                            id="galleryClose"
                            class="absolute right-3 top-3 flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-3xl font-bold text-white transition hover:bg-white/20"
                            aria-label="Tutup galeri"
                        >
                            &times;
                        </button>

                        <button
                            type="button"
                            id="galleryPrev"
                            class="absolute left-2 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-3xl font-bold text-white transition hover:bg-white/20 md:left-5 md:h-12 md:w-12"
                            aria-label="Foto sebelumnya"
                        >
                            &#8249;
                        </button>

                        <div id="galleryViewport" class="relative w-full max-w-5xl">
                            <img
                                id="galleryImage"
                                src=""
                                alt=""
                                class="max-h-[78vh] w-full rounded-2xl object-contain"
                            >
                            <p
                                id="galleryMeta"
                                class="mt-3 text-center text-sm font-bold text-white"
                            ></p>
                            <p
                                id="galleryCaption"
                                class="mt-1 text-center text-sm font-semibold text-white/85"
                            ></p>
                        </div>

                        <button
                            type="button"
                            id="galleryNext"
                            class="absolute right-2 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-3xl font-bold text-white transition hover:bg-white/20 md:right-5 md:h-12 md:w-12"
                            aria-label="Foto berikutnya"
                        >
                            &#8250;
                        </button>
                    </div>
                @endif
            @endif
        </section>

        <section class="mx-auto w-full px-0.5 py-5 md:py-6" id="sponsor-logos">
            <style>
                #sponsor-logos .partner-shell {
                    border-radius: 20px;
                    background: #f0f1f3;
                    padding: clamp(1.1rem, 2.4vw, 1.8rem) clamp(0.7rem, 2.6vw, 1.8rem);
                }

                #sponsor-logos .partner-head {
                    text-align: center;
                }

                #sponsor-logos .partner-title {
                    font-family: 'Sora', sans-serif;
                    font-size: clamp(1.35rem, 2.45vw, 2.1rem);
                    font-weight: 800;
                    line-height: 1.1;
                    color: #5f0d17;
                }

                #sponsor-logos .partner-marquee {
                    overflow: hidden;
                    margin-top: clamp(0.9rem, 2vw, 1.4rem);
                }

                #sponsor-logos .partner-track {
                    display: flex;
                    width: max-content;
                    align-items: center;
                    gap: clamp(1.5rem, 3.8vw, 3.2rem);
                    padding: 0.45rem 0;
                }

                #sponsor-logos .partner-track.is-animated {
                    animation: sponsor-scroll-left 34s linear infinite;
                }

                #sponsor-logos .partner-marquee:hover .partner-track.is-animated {
                    animation-play-state: paused;
                }

                #sponsor-logos .partner-track:not(.is-animated) {
                    width: 100%;
                    justify-content: center;
                    flex-wrap: wrap;
                    gap: 1rem 2rem;
                }

                #sponsor-logos .partner-item {
                    flex: 0 0 auto;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: clamp(120px, 13vw, 195px);
                    min-height: 76px;
                    text-decoration: none;
                }

                #sponsor-logos .partner-item img {
                    max-height: clamp(44px, 5.2vw, 82px);
                    width: auto;
                    max-width: 100%;
                    object-fit: contain;
                    opacity: 0.96;
                    transition: transform 180ms ease, opacity 180ms ease;
                }

                #sponsor-logos .partner-item:hover img {
                    transform: translateY(-1px);
                    opacity: 1;
                }

                @keyframes sponsor-scroll-left {
                    from { transform: translateX(0); }
                    to { transform: translateX(-50%); }
                }

                @media (max-width: 767px) {
                    #sponsor-logos .partner-track {
                        gap: 1.15rem;
                    }

                    #sponsor-logos .partner-item {
                        width: clamp(88px, 23vw, 120px);
                        min-height: 58px;
                    }
                }

                @media (prefers-reduced-motion: reduce) {
                    #sponsor-logos .partner-track,
                    #sponsor-logos .partner-track.is-animated {
                        animation: none;
                        width: 100%;
                        justify-content: center;
                        flex-wrap: wrap;
                        gap: 1rem 1.25rem;
                    }
                }
            </style>

            <div class="partner-shell">
                <div class="partner-head">
                    <h2 class="partner-title">Our Partners</h2>
                    <div class="section-title-accent" aria-hidden="true">
                        <span class="accent-line"></span>
                        <span class="accent-core icon">
                            <i class="fas fa-graduation-cap"></i>
                        </span>
                        <span class="accent-line"></span>
                    </div>
                </div>

                @if($sponsors->isEmpty())
                    <div class="rounded-xl bg-white/65 p-4 text-center text-sm font-semibold text-[#7b0f15]">
                        Belum ada logo sponsor yang ditampilkan.
                    </div>
                @else
                    @php $shouldAnimateSponsor = $sponsors->count() > 1; @endphp
                    <div class="partner-marquee">
                        <div class="partner-track {{ $shouldAnimateSponsor ? 'is-animated' : '' }}" aria-label="Logo partner berjalan otomatis">
                            @foreach($sponsors as $sponsor)
                                @php
                                    $logo = $sponsor->logo_path;
                                    if ($logo && !\Illuminate\Support\Str::startsWith($logo, ['http://', 'https://'])) {
                                        $logo = asset($logo);
                                    }
                                @endphp

                                @if($sponsor->link_url)
                                    <a href="{{ $sponsor->link_url }}" target="_blank" rel="noopener noreferrer" class="partner-item" title="{{ $sponsor->nama }}">
                                        <img src="{{ $logo }}" alt="{{ $sponsor->nama }}" loading="lazy">
                                    </a>
                                @else
                                    <div class="partner-item" title="{{ $sponsor->nama }}">
                                        <img src="{{ $logo }}" alt="{{ $sponsor->nama }}" loading="lazy">
                                    </div>
                                @endif
                            @endforeach

                            @if($shouldAnimateSponsor)
                                @foreach($sponsors as $sponsor)
                                    @php
                                        $logo = $sponsor->logo_path;
                                        if ($logo && !\Illuminate\Support\Str::startsWith($logo, ['http://', 'https://'])) {
                                            $logo = asset($logo);
                                        }
                                    @endphp

                                    @if($sponsor->link_url)
                                        <a href="{{ $sponsor->link_url }}" target="_blank" rel="noopener noreferrer" class="partner-item" title="{{ $sponsor->nama }}" aria-hidden="true" tabindex="-1">
                                            <img src="{{ $logo }}" alt="{{ $sponsor->nama }}" loading="lazy">
                                        </a>
                                    @else
                                        <div class="partner-item" title="{{ $sponsor->nama }}" aria-hidden="true">
                                            <img src="{{ $logo }}" alt="{{ $sponsor->nama }}" loading="lazy">
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const slider = document.getElementById('hotSlider');
        const prev = document.getElementById('hotPrev');
        const next = document.getElementById('hotNext');
        const initialSlides = slider ? Array.from(slider.querySelectorAll('.slide')) : [];
        const dots = Array.from(document.querySelectorAll('#hotDots .hot-dot'));
        let slides = initialSlides;
        let currentSlideIndex = 0;
        let autoplayTimer = null;
        let isAnimating = false;
        let touchStartX = 0;
        let touchDeltaX = 0;

        if (!slider || initialSlides.length === 0) {
            return;
        }

        const realCount = initialSlides.length;
        const cloneBuffer = Math.min(realCount, 2);
        const transitionMs = 700;
        const autoplayDelayMs = 4500;

        if (realCount > 1) {
            const prependSources = initialSlides.slice(-cloneBuffer);
            const appendSources = initialSlides.slice(0, cloneBuffer);

            prependSources.slice().reverse().forEach(function (source) {
                const clone = source.cloneNode(true);
                clone.dataset.clone = 'true';
                slider.insertBefore(clone, slider.firstChild);
            });

            appendSources.forEach(function (source) {
                const clone = source.cloneNode(true);
                clone.dataset.clone = 'true';
                slider.appendChild(clone);
            });

            slides = Array.from(slider.querySelectorAll('.slide'));
        }

        function clampSlideIndex(slideIndex) {
            return Math.max(0, Math.min(slideIndex, slides.length - 1));
        }

        function getRealIndexBySlideIndex(slideIndex) {
            const raw = slides[slideIndex]?.dataset.slideIndex ?? '0';
            const parsed = Number(raw);
            return Number.isNaN(parsed) ? 0 : parsed;
        }

        function setActive(activeSlideIndex) {
            const activeRealIndex = getRealIndexBySlideIndex(activeSlideIndex);

            slides.forEach(function (slide, slideIndex) {
                const isActive = slideIndex === activeSlideIndex;
                slide.classList.toggle('opacity-100', isActive);
                slide.classList.toggle('scale-100', isActive);
                slide.classList.toggle('opacity-45', !isActive);
                slide.classList.toggle('scale-90', !isActive);
            });

            dots.forEach(function (dot, dotIndex) {
                const isActive = dotIndex === activeRealIndex;
                dot.classList.toggle('w-7', isActive);
                dot.classList.toggle('opacity-100', isActive);
                dot.classList.toggle('w-[10px]', !isActive);
                dot.classList.toggle('opacity-35', !isActive);
                dot.setAttribute('aria-current', isActive ? 'true' : 'false');
            });
        }

        function getTargetLeft(target) {
            return target.offsetLeft - ((slider.clientWidth - target.offsetWidth) / 2);
        }

        function getSlideLeft(slideIndex) {
            const target = slides[slideIndex];
            if (!target) {
                return 0;
            }

            return Math.max(0, getTargetLeft(target));
        }

        function jumpTo(slideIndex) {
            const safeIndex = clampSlideIndex(slideIndex);
            slider.scrollLeft = getSlideLeft(safeIndex);
            currentSlideIndex = safeIndex;
            setActive(safeIndex);
        }

        function normalizeClonePosition(slideIndex) {
            if (realCount <= 1) {
                return slideIndex;
            }

            const minRealIndex = cloneBuffer;
            const maxRealIndex = cloneBuffer + realCount - 1;

            if (slideIndex < minRealIndex) {
                return slideIndex + realCount;
            }

            if (slideIndex > maxRealIndex) {
                return slideIndex - realCount;
            }

            return slideIndex;
        }

        function easeInOutCubic(t) {
            return t < 0.5
                ? 4 * t * t * t
                : 1 - Math.pow(-2 * t + 2, 3) / 2;
        }

        function moveTo(slideIndex) {
            const safeIndex = clampSlideIndex(slideIndex);
            if (isAnimating) {
                return;
            }

            const startLeft = slider.scrollLeft;
            const endLeft = getSlideLeft(safeIndex);
            const distance = endLeft - startLeft;

            if (Math.abs(distance) < 1) {
                currentSlideIndex = safeIndex;
                setActive(safeIndex);
                return;
            }

            isAnimating = true;
            currentSlideIndex = safeIndex;
            setActive(safeIndex);

            const startAt = performance.now();
            function animate(now) {
                const elapsed = now - startAt;
                const progress = Math.min(elapsed / transitionMs, 1);
                slider.scrollLeft = startLeft + (distance * easeInOutCubic(progress));

                if (progress < 1) {
                    requestAnimationFrame(animate);
                    return;
                }

                isAnimating = false;
                const normalizedIndex = normalizeClonePosition(currentSlideIndex);
                if (normalizedIndex !== currentSlideIndex) {
                    jumpTo(normalizedIndex);
                }
            }

            requestAnimationFrame(animate);
        }

        function stopAutoplay() {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                autoplayTimer = null;
            }
        }

        function startAutoplay() {
            if (realCount <= 1) {
                return;
            }

            stopAutoplay();
            autoplayTimer = setInterval(function () {
                moveTo(currentSlideIndex + 1);
            }, autoplayDelayMs);
        }

        function restartAutoplay() {
            stopAutoplay();
            startAutoplay();
        }

        if (prev) {
            prev.addEventListener('click', function () {
                moveTo(currentSlideIndex - 1);
                restartAutoplay();
            });
        }

        if (next) {
            next.addEventListener('click', function () {
                moveTo(currentSlideIndex + 1);
                restartAutoplay();
            });
        }

        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                const realIndex = Number(dot.dataset.index || 0);
                if (Number.isNaN(realIndex)) {
                    return;
                }

                const targetSlideIndex = realCount > 1 ? realIndex + cloneBuffer : realIndex;
                moveTo(targetSlideIndex);
                restartAutoplay();
            });
        });

        slider.addEventListener('mouseenter', stopAutoplay);
        slider.addEventListener('mouseleave', startAutoplay);
        slider.addEventListener('touchstart', function (event) {
            stopAutoplay();
            touchStartX = event.touches[0]?.clientX || 0;
            touchDeltaX = 0;
        }, { passive: true });

        slider.addEventListener('touchmove', function (event) {
            const x = event.touches[0]?.clientX || 0;
            touchDeltaX = x - touchStartX;
        }, { passive: true });

        slider.addEventListener('touchend', function () {
            if (Math.abs(touchDeltaX) > 40) {
                if (touchDeltaX < 0) {
                    moveTo(currentSlideIndex + 1);
                } else {
                    moveTo(currentSlideIndex - 1);
                }
            }
            startAutoplay();
        }, { passive: true });

        let isMouseDown = false;
        let mouseStartX = 0;
        let mouseDeltaX = 0;
        let wasDragAction = false;

        slider.style.cursor = 'grab';

        slider.addEventListener('mousedown', function (event) {
            if (event.button !== 0) { return; }
            isMouseDown = true;
            mouseStartX = event.clientX;
            mouseDeltaX = 0;
            wasDragAction = false;
            stopAutoplay();
            slider.style.cursor = 'grabbing';
            event.preventDefault();
        });

        window.addEventListener('mousemove', function (event) {
            if (!isMouseDown) { return; }
            mouseDeltaX = event.clientX - mouseStartX;
            if (Math.abs(mouseDeltaX) > 5) {
                wasDragAction = true;
            }
        });

        window.addEventListener('mouseup', function () {
            if (!isMouseDown) { return; }
            isMouseDown = false;
            slider.style.cursor = 'grab';
            if (Math.abs(mouseDeltaX) > 40) {
                if (mouseDeltaX < 0) {
                    moveTo(currentSlideIndex + 1);
                } else {
                    moveTo(currentSlideIndex - 1);
                }
            }
            restartAutoplay();
        });

        slider.addEventListener('click', function (event) {
            if (wasDragAction) {
                event.preventDefault();
                event.stopPropagation();
                wasDragAction = false;
            }
        }, true);

        function getInitialRealIndex() {
            const isDesktop = window.matchMedia('(min-width: 768px)').matches;
            if (isDesktop && realCount >= 3) {
                return 1;
            }

            return 0;
        }

        window.addEventListener('resize', function () {
            const normalized = normalizeClonePosition(currentSlideIndex);
            jumpTo(normalized);
            setActive(normalized);
        });

        const initialRealIndex = getInitialRealIndex();
        const initialSlideIndex = realCount > 1 ? initialRealIndex + cloneBuffer : initialRealIndex;
        jumpTo(initialSlideIndex);
        setActive(initialSlideIndex);
        startAutoplay();
    })();

    (function () {
        const items = Array.from(document.querySelectorAll('.gallery-item'));
        const dataElement = document.getElementById('galleryEventsData');
        const lightbox = document.getElementById('galleryLightbox');
        const image = document.getElementById('galleryImage');
        const meta = document.getElementById('galleryMeta');
        const caption = document.getElementById('galleryCaption');
        const closeButton = document.getElementById('galleryClose');
        const prevButton = document.getElementById('galleryPrev');
        const nextButton = document.getElementById('galleryNext');
        const viewport = document.getElementById('galleryViewport');

        if (!items.length || !dataElement || !lightbox || !image || !meta || !caption || !closeButton) {
            return;
        }

        let events = [];
        try {
            events = JSON.parse(dataElement.textContent || '[]');
        } catch (error) {
            events = [];
        }

        if (!events.length) {
            return;
        }

        let activeEventIndex = 0;
        let activePhotoIndex = 0;
        let touchStartX = 0;
        let touchDeltaX = 0;

        function normalizeEventIndex(index) {
            const total = events.length;
            if (!total) {
                return 0;
            }

            return (index + total) % total;
        }

        function currentPhotos() {
            return events[activeEventIndex]?.photos || [];
        }

        function normalizePhotoIndex(index) {
            const photos = currentPhotos();
            if (!photos.length) {
                return 0;
            }

            return (index + photos.length) % photos.length;
        }

        function updateNavigationState() {
            const hasMultiplePhotos = currentPhotos().length > 1;
            if (prevButton) {
                prevButton.classList.toggle('hidden', !hasMultiplePhotos);
            }

            if (nextButton) {
                nextButton.classList.toggle('hidden', !hasMultiplePhotos);
            }
        }

        function render(photoIndex) {
            const photos = currentPhotos();
            if (!photos.length) {
                return;
            }

            activePhotoIndex = normalizePhotoIndex(photoIndex);
            const activeSlide = photos[activePhotoIndex];
            const currentEvent = events[activeEventIndex];
            image.src = activeSlide.src;
            image.alt = activeSlide.alt;
            caption.textContent = activeSlide.caption;
            meta.textContent = currentEvent.event_name + ' (' + (activePhotoIndex + 1) + '/' + photos.length + ')';
            updateNavigationState();
        }

        function open(eventIndex) {
            activeEventIndex = normalizeEventIndex(eventIndex);
            activePhotoIndex = 0;
            render(activePhotoIndex);
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        }

        function close() {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }

        function showNext() {
            if (currentPhotos().length < 2) {
                return;
            }

            render(activePhotoIndex + 1);
        }

        function showPrev() {
            if (currentPhotos().length < 2) {
                return;
            }

            render(activePhotoIndex - 1);
        }

        items.forEach(function (item, index) {
            item.addEventListener('click', function () {
                const clickedEventIndex = Number(item.dataset.galleryEventIndex ?? index);
                open(Number.isNaN(clickedEventIndex) ? index : clickedEventIndex);
            });
        });

        closeButton.addEventListener('click', close);

        if (nextButton) {
            nextButton.addEventListener('click', showNext);
        }

        if (prevButton) {
            prevButton.addEventListener('click', showPrev);
        }

        lightbox.addEventListener('click', function (event) {
            if (event.target === lightbox) {
                close();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (lightbox.classList.contains('hidden')) {
                return;
            }

            if (event.key === 'Escape') {
                close();
            } else if (event.key === 'ArrowRight') {
                showNext();
            } else if (event.key === 'ArrowLeft') {
                showPrev();
            }
        });

        if (viewport) {
            viewport.addEventListener('touchstart', function (event) {
                touchStartX = event.touches[0]?.clientX || 0;
                touchDeltaX = 0;
            }, { passive: true });

            viewport.addEventListener('touchmove', function (event) {
                const currentX = event.touches[0]?.clientX || 0;
                touchDeltaX = currentX - touchStartX;
            }, { passive: true });

            viewport.addEventListener('touchend', function () {
                if (Math.abs(touchDeltaX) < 45) {
                    return;
                }

                if (touchDeltaX < 0) {
                    showNext();
                } else {
                    showPrev();
                }
            }, { passive: true });
        }
    })();
</script>
@endpush
