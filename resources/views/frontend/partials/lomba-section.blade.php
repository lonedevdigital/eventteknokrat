<section class="mx-auto w-full px-0.5 py-5 md:py-6" id="lomba-list">

    <style>
        @keyframes fadeUpLomba {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        #lomba-list .comp-card {
            transition: box-shadow 0.22s ease, transform 0.22s ease;
            animation: fadeUpLomba 0.45s ease both;
        }
        #lomba-list .comp-card:nth-child(2) { animation-delay: 0.10s; }
        #lomba-list .comp-card:nth-child(3) { animation-delay: 0.18s; }
        #lomba-list .comp-card:nth-child(4) { animation-delay: 0.26s; }
        #lomba-list .comp-card:nth-child(5) { animation-delay: 0.32s; }
        #lomba-list .comp-card:nth-child(6) { animation-delay: 0.38s; }
        #lomba-list .comp-card:hover {
            box-shadow: 0 16px 48px rgba(0,0,0,0.14) !important;
            transform: translateY(-4px);
        }
        #lomba-list .btn-detail-lomba {
            transition: background 0.15s ease, transform 0.12s ease;
        }
        #lomba-list .btn-detail-lomba:hover {
            background: #a00e24 !important;
            transform: scale(1.03);
        }

        /* ── Mobile: stack vertically ── */
        @media (max-width: 639px) {
            #lomba-list .comp-card   { flex-direction: column !important; }
            #lomba-list .comp-thumb  { width: 100% !important; height: 130px !important; flex-direction: row !important; }
            #lomba-list .comp-thumb .thumb-icon { margin-top: 0 !important; }
            #lomba-list .comp-body   { padding: 18px 16px 20px !important; gap: 10px !important; }
            #lomba-list .comp-desc   { display: none; }
            #lomba-list .comp-info-row { gap: 12px !important; flex-wrap: wrap !important; }
            #lomba-list .comp-footer {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 8px !important;
            }
        }
    </style>

    {{-- ── Section Header ── --}}
    <div style="margin-bottom: 28px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
            <div style="width:4px; height:22px; background:#C0112B; border-radius:99px;"></div>
            <span style="font-size:12px; font-weight:700; color:#C0112B; letter-spacing:0.12em; text-transform:uppercase;">Terbaru</span>
        </div>
        <h2 style="font-size:clamp(1.4rem,3vw,2rem); font-weight:800; color:#0F1117; letter-spacing:-0.025em; line-height:1.15;">
            Perlombaan Terbaru
        </h2>
        <p style="font-size:14px; color:#6B7280; margin-top:6px; font-weight:400; max-width:520px; line-height:1.6;">
            Temukan kompetisi pilihan dan raih prestasi terbaikmu bersama ribuan peserta dari seluruh Indonesia.
        </p>
    </div>

    @if($latestLomba->isEmpty())
        <div style="background:#FFF0F2; border-radius:14px; padding:24px; text-align:center; font-size:14px; font-weight:600; color:#C0112B;">
            Belum ada perlombaan untuk ditampilkan.
        </div>
    @else
        <div style="display:flex; flex-direction:column; gap:20px;">
            @foreach($latestLomba as $lomba)
                @php
                    $cover = $lomba->flyer ?: $lomba->thumbnail;
                    if ($cover && !\Illuminate\Support\Str::startsWith($cover, ['http://', 'https://'])) {
                        $cover = asset($cover);
                    }

                    $isFree   = ($lomba->tipe_bayar !== 'berbayar');
                    $skala    = $lomba->partisipasi_skala ?? '';
                    $prodi    = $lomba->partisipasi_prodi ?? '';
                    $deadline = $lomba->tanggal_pendaftaran
                                    ? \Carbon\Carbon::parse($lomba->tanggal_pendaftaran)->translatedFormat('d M Y')
                                    : null;
                    $openedAt = $lomba->created_at->translatedFormat('d M Y');
                    $desc     = \Illuminate\Support\Str::limit(strip_tags((string)($lomba->deskripsi ?? '')), 140);

                    // Warna badge skala
                    $skalaBg = match(strtolower($skala)) {
                        'internasional' => 'linear-gradient(90deg,#0055CC,#0099FF)',
                        'regional'      => 'linear-gradient(90deg,#B45309,#D97706)',
                        'universitas'   => 'linear-gradient(90deg,#6D28D9,#7C3AED)',
                        default         => '#C0112B',
                    };
                @endphp

                <div class="comp-card"
                     style="background:#ffffff; border-radius:18px; overflow:hidden; display:flex; flex-direction:row; box-shadow:0 4px 16px rgba(0,0,0,0.08);">

                    {{-- ── Panel Kiri ── --}}
                    <div class="comp-thumb"
                         style="width:220px; flex-shrink:0; position:relative; overflow:hidden; background:#F3F4F6;">

                        @if($cover)
                            <img src="{{ $cover }}" alt="{{ $lomba->nama_event }}"
                                 style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover;" loading="lazy">
                        @else
                            {{-- Placeholder jika tidak ada gambar --}}
                            <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:#E5E7EB;">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" style="opacity:0.3;">
                                    <rect x="3" y="3" width="18" height="18" rx="3" stroke="#6B7280" stroke-width="1.5"/>
                                    <circle cx="8.5" cy="8.5" r="1.5" fill="#6B7280"/>
                                    <path d="M21 15l-5-5L5 21" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Badge skala --}}
                        @if($skala)
                            <div style="position:absolute; top:12px; left:12px; background:{{ $skalaBg }}; color:#fff; font-size:10px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; padding:4px 10px; border-radius:20px; box-shadow:0 2px 6px rgba(0,0,0,0.18);">
                                {{ $skala }}
                            </div>
                        @endif
                    </div>

                    {{-- ── Panel Kanan ── --}}
                    <div class="comp-body"
                         style="flex:1; padding:28px 32px; display:flex; flex-direction:column; gap:14px; min-width:0;">

                        {{-- Tags --}}
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            @if($lomba->category)
                                <span style="display:inline-block; background:#FFF0F2; color:#C0112B; font-size:12px; font-weight:700; padding:4px 12px; border-radius:20px;">
                                    {{ $lomba->category->nama_kategori }}
                                </span>
                            @endif
                            @if($prodi)
                                <span style="display:inline-block; background:#FEF3C7; color:#92400E; font-size:12px; font-weight:600; padding:4px 12px; border-radius:20px;">
                                    {{ \Illuminate\Support\Str::limit($prodi, 26) }}
                                </span>
                            @endif
                            @if($isFree)
                                <span style="display:inline-block; background:#F0FDF4; color:#166534; font-size:12px; font-weight:600; padding:4px 12px; border-radius:20px;">Gratis</span>
                            @else
                                <span style="display:inline-block; background:#FFF7ED; color:#9A3412; font-size:12px; font-weight:600; padding:4px 12px; border-radius:20px;">Berbayar</span>
                            @endif
                        </div>

                        {{-- Judul + Deskripsi --}}
                        <div>
                            <h3 style="font-size:20px; font-weight:800; color:#0F1117; line-height:1.3; letter-spacing:-0.02em; margin-bottom:6px;">
                                {{ $lomba->nama_event }}
                            </h3>
                            @if($desc)
                                <p class="comp-desc" style="font-size:14px; color:#6B7280; line-height:1.65; font-weight:400; max-width:520px;">
                                    {{ $desc }}
                                </p>
                            @endif
                        </div>

                        {{-- Info: deadline + penyelenggara --}}
                        <div class="comp-info-row" style="display:flex; flex-direction:row; gap:24px; flex-wrap:wrap;">
                            @if($deadline)
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="width:32px; height:32px; background:#FFF0F2; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                            <rect x="3" y="4" width="18" height="18" rx="3" stroke="#C0112B" stroke-width="2"/>
                                            <path d="M16 2v4M8 2v4M3 10h18" stroke="#C0112B" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div style="font-size:11px; color:#9CA3AF; font-weight:500; line-height:1;">Deadline Pendaftaran</div>
                                        <div style="font-size:13.5px; color:#C0112B; font-weight:700; margin-top:3px;">{{ $deadline }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($prodi)
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="width:32px; height:32px; background:#FEF3C7; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke="#92400E" stroke-width="2" stroke-linejoin="round"/>
                                            <path d="M9 22V12h6v10" stroke="#92400E" stroke-width="2" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div style="min-width:0;">
                                        <div style="font-size:11px; color:#9CA3AF; font-weight:500; line-height:1;">Penyelenggara</div>
                                        <div style="font-size:13.5px; color:#374151; font-weight:700; margin-top:3px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:220px;">{{ $prodi }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Footer: tombol + dibuka sejak --}}
                        <div class="comp-footer"
                             style="display:flex; align-items:center; justify-content:space-between; padding-top:6px; border-top:1px solid #F3F4F6; margin-top:2px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <a href="{{ route('frontend.events.show', $lomba->slug ?: $lomba->id) }}"
                                   class="btn-detail-lomba"
                                   style="background:#C0112B; color:#fff; font-size:13px; font-weight:700; padding:10px 24px; border:none; border-radius:9px; cursor:pointer; letter-spacing:0.01em; text-decoration:none; display:inline-block;">
                                    Lihat Detail
                                </a>
                            </div>
                            <span style="font-size:12px; color:#9CA3AF; font-weight:400;">
                                Dibuka sejak {{ $openedAt }}
                            </span>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif

</section>
