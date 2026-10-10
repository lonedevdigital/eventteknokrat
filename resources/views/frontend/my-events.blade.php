@extends('layouts.frontend')

@section('content')
    <section class="p-4 md:p-7 max-w-6xl mx-auto">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="font-['Sora',sans-serif] text-2xl md:text-3xl font-extrabold text-[#ab021c]">My Events</h1>
                <p class="mt-1 text-sm font-semibold text-[#ab021c]/80">
                    Menampilkan {{ $events->count() }} dari {{ $events->total() }} event yang Anda ikuti.
                </p>
            </div>
            @if(session('success'))
                <div class="w-full rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm font-bold text-emerald-800">
                    <i class="fas fa-check-circle mr-1 text-emerald-600"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="w-full rounded-xl bg-red-50 border border-red-200 px-4 py-2.5 text-sm font-bold text-red-800">
                    <i class="fas fa-exclamation-circle mr-1 text-red-600"></i> {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- FORM FILTER & PENCARIAN --}}
        <form method="GET" action="{{ route('frontend.my-events') }}" class="mb-6 flex flex-wrap items-center gap-2">
            <details class="group relative">
                <summary class="inline-flex cursor-pointer list-none items-center gap-2 rounded-xl bg-[#ab021c]/10 px-4 py-2 text-sm font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white">
                    <i class="fas fa-filter text-xs"></i> Filter
                </summary>
                <div class="absolute left-0 top-full z-20 mt-2 w-[min(92vw,340px)] rounded-2xl border border-[#ab021c]/15 bg-white p-4 shadow-xl">
                    <div class="grid gap-3">
                        <label>
                            <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#ab021c]/80">Kategori</span>
                            <select
                                name="event_category_id"
                                class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20"
                            >
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) request('event_category_id') === (string) $category->id)>
                                        {{ $category->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#ab021c]/80">Jenis Event</span>
                            <select
                                name="type"
                                class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20"
                            >
                                <option value="">Semua Jenis</option>
                                <option value="lomba" @selected(request('type') === 'lomba')>Perlombaan</option>
                                <option value="event" @selected(request('type') === 'event')>Seminar / Event Umum</option>
                            </select>
                        </label>

                        <label>
                            <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#ab021c]/80">Waktu Pelaksanaan</span>
                            <select
                                name="event_status"
                                class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20"
                            >
                                <option value="">Semua Waktu</option>
                                <option value="upcoming" @selected(request('event_status') === 'upcoming')>Akan Datang</option>
                                <option value="ongoing" @selected(request('event_status') === 'ongoing')>Hari Ini</option>
                                <option value="past" @selected(request('event_status') === 'past')>Selesai</option>
                            </select>
                        </label>

                        <div class="flex items-center justify-between gap-2 pt-1 border-t border-gray-100">
                            <a
                                href="{{ route('frontend.my-events') }}"
                                class="inline-flex rounded-lg bg-[#ab021c]/10 px-3 py-1.5 text-xs font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white"
                            >
                                Reset
                            </a>
                            <button
                                type="submit"
                                class="inline-flex rounded-lg bg-[#ab021c] px-3.5 py-1.5 text-xs font-bold text-white transition hover:bg-[#8f0018]"
                            >
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </details>

            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Cari Event yang Anda ikuti..."
                class="h-10 min-w-[220px] flex-1 rounded-xl border-0 bg-[#ab021c]/5 px-4 text-sm font-semibold text-[#ab021c] ring-0 placeholder:text-[#ab021c]/55 focus:ring-2 focus:ring-[#ab021c]/20"
            >
            <button
                type="submit"
                class="inline-flex h-10 items-center gap-1.5 rounded-xl bg-[#ab021c] px-4 text-sm font-bold text-white transition hover:bg-[#8f0018]"
            >
                <i class="fas fa-search text-xs"></i> Cari
            </button>
        </form>

        {{-- DAFTAR EVENT (GROUPING PER INDUK EVENT) --}}
        <div class="grid gap-5">
            @forelse($events as $event)
                @php
                    $isLomba = $event->isLomba();
                    $regs = $event->registrations;
                    $dateLabel = $event->tanggal_pelaksanaan
                        ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->translatedFormat('d F Y')
                        : '-';
                    $timeLabel = $event->waktu_pelaksanaan
                        ? \Carbon\Carbon::parse($event->waktu_pelaksanaan)->format('H:i') . ' WIB'
                        : '-';
                    $locationLabel = $event->tempat_pelaksanaan ?: '-';

                    $today = now()->toDateString();
                    $eventDate = $event->tanggal_pelaksanaan ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->toDateString() : null;
                    $isPast = $eventDate && $eventDate < $today;
                    $isToday = $eventDate && $eventDate === $today;
                @endphp

                <div class="rounded-2xl border border-[#ab021c]/20 bg-white shadow-sm overflow-hidden transition hover:shadow-md">
                    {{-- ── CARD HEADER: Induk Event ── --}}
                    <div class="border-b border-[#ab021c]/15 bg-gradient-to-r from-[#ab021c]/10 via-[#ab021c]/5 to-transparent p-4 md:p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex-1 min-w-[240px]">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    @if($isLomba)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-[#ab021c] px-3 py-0.5 text-xs font-bold text-white uppercase tracking-wider">
                                            <i class="fas fa-trophy text-[10px]"></i> Perlombaan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-600 px-3 py-0.5 text-xs font-bold text-white uppercase tracking-wider">
                                            <i class="fas fa-calendar-alt text-[10px]"></i> Seminar / Event
                                        </span>
                                    @endif

                                    @if($event->category)
                                        <span class="inline-flex rounded-full bg-[#ab021c]/10 px-2.5 py-0.5 text-xs font-bold text-[#ab021c]">
                                            {{ $event->category->nama_kategori }}
                                        </span>
                                    @endif

                                    @if($isToday)
                                        <span class="inline-flex rounded-full bg-amber-500 px-2.5 py-0.5 text-xs font-bold text-white animate-pulse">
                                            Hari Ini
                                        </span>
                                    @elseif($isPast)
                                        <span class="inline-flex rounded-full bg-gray-400 px-2.5 py-0.5 text-xs font-bold text-white">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-emerald-600 px-2.5 py-0.5 text-xs font-bold text-white">
                                            Akan Datang
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('frontend.events.show', $event->slug ?: $event->id) }}" class="group">
                                    <h2 class="font-['Sora',sans-serif] text-lg md:text-xl font-bold text-gray-900 group-hover:text-[#ab021c] transition leading-snug">
                                        {{ $event->nama_event }}
                                    </h2>
                                </a>

                                <div class="mt-2.5 flex flex-wrap items-center gap-x-5 gap-y-1 text-xs text-gray-600 font-medium">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-calendar-day text-[#ab021c]"></i>
                                        <span>{{ $dateLabel }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-clock text-[#ab021c]"></i>
                                        <span>{{ $timeLabel }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-map-marker-alt text-[#ab021c]"></i>
                                        <span>{{ $locationLabel }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="shrink-0">
                                <a href="{{ route('frontend.events.show', $event->slug ?: $event->id) }}"
                                   class="inline-flex items-center gap-1.5 rounded-xl border border-[#ab021c]/25 bg-white px-3.5 py-1.5 text-xs font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white">
                                    <i class="fas fa-external-link-alt text-[10px]"></i> Info Event
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- ── CARD BODY: List Cabang Lomba yang Diikuti ── --}}
                    <div class="p-4 md:p-5">
                        @if($isLomba)
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-[#ab021c]">
                                    <i class="fas fa-layer-group mr-1"></i> Cabang Lomba yang Anda Ikuti ({{ $regs->count() }} Cabang)
                                </span>
                            </div>

                            <div class="divide-y divide-gray-100 rounded-xl border border-gray-100 bg-gray-50/50 overflow-hidden">
                                @forelse($regs as $reg)
                                    @php
                                        $branch = $reg->branch;
                                        $isTim = $reg->tipe_pendaftaran === 'tim';

                                        // Cari tim yang cocok untuk branch ini
                                        $team = $userTeams->first(function ($t) use ($event, $branch) {
                                            return $t->event_id === $event->id && (!$branch || $t->branch_id === $branch->id);
                                        });

                                        $isKetua = $team && (int) $team->ketua_user_id === (int) auth()->id();

                                        // Status dokumen/berkas
                                        $allApproved = $reg->allDocumentsApproved();
                                        $hasRejected = $reg->documents->where('status', 'rejected')->isNotEmpty();
                                        $hasPending = $reg->documents->where('status', 'pending')->isNotEmpty();
                                        $totalRequirements = $event->documentRequirementsForBranch($reg->branch_id)->count();

                                        // Ikon cabang dinamis
                                        $cabangName = $branch->nama_cabang ?? 'Cabang Lomba';
                                        $iconClass = 'fas fa-medal';
                                        if (preg_match('/(game|legend|mlbb|valorant|pubg|free fire|esport)/i', $cabangName)) {
                                            $iconClass = 'fas fa-gamepad';
                                        } elseif (preg_match('/(desain|poster|ui|ux|animasi|video|fotografi)/i', $cabangName)) {
                                            $iconClass = 'fas fa-palette';
                                        } elseif (preg_match('/(web|code|koding|programming|science|iot|robotik|aplikasi)/i', $cabangName)) {
                                            $iconClass = 'fas fa-laptop-code';
                                        }
                                    @endphp

                                    <div class="p-3.5 md:p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white hover:bg-red-50/20 transition">
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-[#ab021c]/10 text-[#ab021c] flex items-center justify-center font-bold text-base shrink-0 mt-0.5">
                                                <i class="{{ $iconClass }}"></i>
                                            </div>

                                            <div>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h3 class="font-bold text-sm md:text-base text-gray-900">
                                                        {{ $cabangName }}
                                                    </h3>

                                                    @if($isTim || $team)
                                                        <span class="inline-flex items-center gap-1 rounded-md bg-indigo-50 border border-indigo-200 px-2 py-0.5 text-[11px] font-bold text-indigo-700">
                                                            <i class="fas fa-users text-[10px]"></i> Tim: {{ $team->nama_tim ?? 'Terdaftar Tim' }}
                                                            @if($isKetua)
                                                                <span class="text-[9px] bg-indigo-200 text-indigo-900 px-1 py-0.2 rounded font-bold">Ketua</span>
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[11px] font-bold text-emerald-700">
                                                            <i class="fas fa-user text-[10px]"></i> Individu
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                                    {{-- Status Dokumen / Berkas --}}
                                                    @if($totalRequirements > 0)
                                                        @if($allApproved)
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                                                                <i class="fas fa-check-circle"></i> Berkas Lengkap & Disetujui
                                                            </span>
                                                        @elseif($hasRejected)
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-[11px] font-bold text-red-800">
                                                                <i class="fas fa-exclamation-circle"></i> Berkas Perlu Diperbaiki
                                                            </span>
                                                        @elseif($hasPending)
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold text-amber-800">
                                                                <i class="fas fa-clock"></i> Menunggu Review Berkas
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-[11px] font-bold text-gray-700">
                                                                <i class="fas fa-file-upload"></i> Belum Lengkapi Berkas
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800">
                                                            <i class="fas fa-check"></i> Terdaftar
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 self-end md:self-center">
                                            <a href="{{ route('frontend.my-event.show', ['slug' => $event->slug ?: $event->id, 'branch' => $reg->branch_id]) }}"
                                               class="inline-flex items-center gap-1.5 rounded-xl bg-[#ab021c] px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#8f0018]">
                                                @if($isTim || $team)
                                                    <i class="fas fa-users-cog"></i> Kelola Tim / Berkas
                                                @else
                                                    <i class="fas fa-file-alt"></i> Detail & Berkas
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-xs text-gray-500">
                                        Belum ada cabang lomba yang terdaftar untuk akun ini.
                                    </div>
                                @endforelse
                            </div>

                        @else
                            {{-- Untuk Event Seminar / Umum --}}
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-100 bg-gray-50/70 p-4">
                                <div class="space-y-1">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">Status Kepesertaan:</span>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                                            <i class="fas fa-check-circle"></i> Terdaftar Resmi
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('frontend.events.show', $event->slug ?: $event->id) }}"
                                       class="inline-flex items-center gap-1.5 rounded-xl bg-[#ab021c] px-4 py-2 text-xs font-bold text-white transition hover:bg-[#8f0018]">
                                        <i class="fas fa-info-circle"></i> Lihat Halaman Event
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-[#ab021c]/20 bg-white p-12 text-center shadow-sm">
                    <div class="mx-auto w-16 h-16 rounded-full bg-[#ab021c]/10 text-[#ab021c] flex items-center justify-center text-2xl mb-3">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3 class="font-['Sora',sans-serif] text-lg font-bold text-gray-900">Belum Ada Event Terdaftar</h3>
                    <p class="mt-1 text-sm text-gray-500">Anda belum mendaftar pada event atau cabang perlombaan manapun.</p>
                    <a href="{{ route('frontend.events.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-[#ab021c] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#8f0018]">
                        <i class="fas fa-compass"></i> Jelajahi Event Sekarang
                    </a>
                </div>
            @endforelse
        </div>

        {{-- PAGINATOR --}}
        <div class="mt-8 flex justify-center">
            @include('frontend.partials.pager', ['paginator' => $events])
        </div>
    </section>
@endsection
