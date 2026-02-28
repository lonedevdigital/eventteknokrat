@extends('layouts.frontend')

@section('content')
    @php
        $monthOptions = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        $currentYear = now()->year;
        $yearOptions = range($currentYear - 1, $currentYear + 3);
    @endphp

    <section class="p-5 md:p-6">
        <div class="mb-4">
            <h1 class="font-['Sora',sans-serif] text-2xl font-extrabold text-[#ab021c]">List Event</h1>
            <p class="mt-1 text-sm font-semibold text-[#ab021c]/85">
                Menampilkan {{ $events->count() }} dari {{ $events->total() }} event.
            </p>
        </div>

        <form method="GET" action="{{ route('frontend.events') }}" class="mb-4 flex flex-wrap items-center gap-2">
            <details class="group relative">
                <summary class="inline-flex cursor-pointer list-none items-center gap-2 rounded-xl bg-[#ab021c]/10 px-4 py-2 text-sm font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white">
                    Filter
                </summary>
                <div class="absolute left-0 top-full z-20 mt-2 w-[min(92vw,320px)] rounded-2xl border border-[#ab021c]/15 bg-white p-4 shadow-xl">
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
                            <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#ab021c]/80">Status Event</span>
                            <select
                                name="status"
                                class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20"
                            >
                                <option value="">Semua Status</option>
                                <option value="upcoming" @selected(request('status') === 'upcoming')>Akan Datang</option>
                                <option value="ongoing" @selected(request('status') === 'ongoing')>Hari Ini</option>
                                <option value="past" @selected(request('status') === 'past')>Selesai</option>
                            </select>
                        </label>

                        <div class="grid grid-cols-2 gap-2">
                            <label>
                                <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#ab021c]/80">Bulan</span>
                                <select
                                    name="month"
                                    class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20"
                                >
                                    <option value="">Semua</option>
                                    @foreach($monthOptions as $monthValue => $monthName)
                                        <option value="{{ $monthValue }}" @selected((string) request('month') === (string) $monthValue)>
                                            {{ $monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            <label>
                                <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#ab021c]/80">Tahun</span>
                                <select
                                    name="year"
                                    class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20"
                                >
                                    <option value="">Semua</option>
                                    @foreach($yearOptions as $year)
                                        <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <a
                                href="{{ route('frontend.events') }}"
                                class="inline-flex rounded-lg bg-[#ab021c]/10 px-3 py-1.5 text-xs font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white"
                            >
                                Reset
                            </a>
                            <button
                                type="submit"
                                class="inline-flex rounded-lg bg-[#ab021c] px-3 py-1.5 text-xs font-bold text-white transition hover:bg-[#8f0018]"
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
                placeholder="Cari Event"
                class="h-10 min-w-[220px] flex-1 rounded-xl border-0 bg-[#ab021c]/5 px-4 text-sm font-semibold text-[#ab021c] ring-0 placeholder:text-[#ab021c]/55 focus:ring-2 focus:ring-[#ab021c]/20"
            >
            <button
                type="submit"
                class="inline-flex h-10 rounded-xl bg-[#ab021c] px-4 text-sm font-bold text-white transition hover:bg-[#8f0018]"
            >
                Cari
            </button>
        </form>

        <div class="overflow-hidden rounded-2xl border border-[#ab021c]/25">
            <div class="hidden grid-cols-[1fr_2fr_0.7fr] bg-[#ab021c]/10 px-4 py-3 text-sm font-bold text-[#ab021c] md:grid">
                <div>Judul</div>
                <div>Pelaksanaan</div>
                <div>Status</div>
            </div>

            @forelse($events as $event)
                @php
                    $statusLabel = ucfirst((string) ($event->status ?? 'dibuka'));
                    $statusClass = $statusLabel === 'Dibuka'
                        ? 'text-blue-600'
                        : 'text-[#ab021c]/80';
                    $dateLabel = $event->tanggal_pelaksanaan
                        ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->translatedFormat('d F Y')
                        : '-';
                    $timeLabel = $event->waktu_pelaksanaan
                        ? \Carbon\Carbon::parse($event->waktu_pelaksanaan)->format('H:i') . ' WIB'
                        : '-';
                    $locationLabel = $event->tempat_pelaksanaan ?: '-';
                @endphp

                <a
                    href="{{ route('frontend.events.show', $event->slug ?: $event->id) }}"
                    class="grid gap-3 px-4 py-4 text-sm transition hover:bg-[#ab021c]/5 md:grid-cols-[1fr_2fr_0.7fr] {{ $loop->first ? '' : 'border-t border-[#ab021c]/15' }}"
                >
                    <div>
                        <p class="mb-0.5 text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60 md:hidden">Judul</p>
                        <p class="font-semibold leading-snug text-[#ab021c]">{{ $event->nama_event }}</p>
                    </div>

                    <div class="space-y-0.5 text-[#ab021c]/90">
                        <p class="mb-0.5 text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60 md:hidden">Pelaksanaan</p>
                        <p><span class="font-semibold">Tanggal:</span> {{ $dateLabel }}</p>
                        <p><span class="font-semibold">Waktu:</span> {{ $timeLabel }}</p>
                        <p><span class="font-semibold">Tempat:</span> {{ $locationLabel }}</p>
                    </div>

                    <div class="md:pt-0.5">
                        <p class="mb-0.5 text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60 md:hidden">Status</p>
                        <p class="font-bold {{ $statusClass }}">{{ $statusLabel }}</p>
                    </div>
                </a>
            @empty
                <div class="px-4 py-8 text-center text-sm font-semibold text-[#ab021c]">
                    Event tidak ditemukan untuk filter yang dipilih.
                </div>
            @endforelse
        </div>

        <div class="mt-5 flex justify-center">
            @include('frontend.partials.pager', ['paginator' => $events])
        </div>
    </section>
@endsection
