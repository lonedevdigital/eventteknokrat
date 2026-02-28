@extends('layouts.frontend')

@section('content')
    <section class="p-5 md:p-6">
        <div class="mb-4">
            <h1 class="font-['Sora',sans-serif] text-2xl font-extrabold text-[#ab021c]">My Events</h1>
            <p class="mt-1 text-sm font-semibold text-[#ab021c]/85">
                Menampilkan {{ $registrations->count() }} dari {{ $registrations->total() }} event yang Anda ikuti.
            </p>
        </div>

        <form method="GET" action="{{ route('frontend.my-events') }}" class="mb-4 flex flex-wrap items-center gap-2">
            <details class="group relative">
                <summary class="inline-flex cursor-pointer list-none items-center gap-2 rounded-xl bg-[#ab021c]/10 px-4 py-2 text-sm font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white">
                    Filter
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

                        <div class="grid grid-cols-2 gap-2">
                            <label>
                                <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#ab021c]/80">Registrasi</span>
                                <select
                                    name="status"
                                    class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20"
                                >
                                    <option value="">Semua</option>
                                    <option value="registered" @selected(request('status') === 'registered')>Terdaftar</option>
                                    <option value="attended" @selected(request('status') === 'attended')>Sudah Absen</option>
                                </select>
                            </label>

                            <label>
                                <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#ab021c]/80">Kehadiran</span>
                                <select
                                    name="attendance_status"
                                    class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20"
                                >
                                    <option value="">Semua</option>
                                    <option value="pending" @selected(request('attendance_status') === 'pending')>Pending</option>
                                    <option value="present" @selected(request('attendance_status') === 'present')>Hadir</option>
                                    <option value="absent" @selected(request('attendance_status') === 'absent')>Tidak Hadir</option>
                                </select>
                            </label>
                        </div>

                        <label>
                            <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#ab021c]/80">Status Event</span>
                            <select
                                name="event_status"
                                class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20"
                            >
                                <option value="">Semua Status</option>
                                <option value="upcoming" @selected(request('event_status') === 'upcoming')>Akan Datang</option>
                                <option value="ongoing" @selected(request('event_status') === 'ongoing')>Hari Ini</option>
                                <option value="past" @selected(request('event_status') === 'past')>Selesai</option>
                            </select>
                        </label>

                        <div class="flex items-center justify-between gap-2">
                            <a
                                href="{{ route('frontend.my-events') }}"
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

            @forelse($registrations as $registration)
                @php
                    $event = $registration->event;
                    $statusLabel = $event ? ucfirst((string) ($event->status ?? 'dibuka')) : '-';
                    $statusClass = $statusLabel === 'Dibuka'
                        ? 'text-blue-600'
                        : 'text-[#ab021c]/80';
                    $dateLabel = $event?->tanggal_pelaksanaan
                        ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->translatedFormat('d F Y')
                        : '-';
                    $timeLabel = $event?->waktu_pelaksanaan
                        ? \Carbon\Carbon::parse($event->waktu_pelaksanaan)->format('H:i') . ' WIB'
                        : '-';
                    $locationLabel = $event?->tempat_pelaksanaan ?: '-';
                    $registrationLabel = $registration->status === 'attended' ? 'Sudah Absen' : 'Terdaftar';
                @endphp

                <div class="grid gap-3 px-4 py-4 text-sm md:grid-cols-[1fr_2fr_0.7fr] {{ $loop->first ? '' : 'border-t border-[#ab021c]/15' }}">
                    <div>
                        <p class="mb-0.5 text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60 md:hidden">Judul</p>
                        @if($event)
                            <a
                                href="{{ route('frontend.events.show', $event->slug ?: $event->id) }}"
                                class="font-semibold leading-snug text-[#ab021c] transition hover:underline"
                            >
                                {{ $event->nama_event }}
                            </a>
                        @else
                            <p class="font-semibold leading-snug text-[#ab021c]">Event tidak tersedia</p>
                        @endif
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
                        <p class="text-xs font-semibold text-[#ab021c]/75">{{ $registrationLabel }}</p>

                        @if($event && $registration->status !== 'attended')
                            <a
                                href="{{ route('frontend.attendance.scan') }}"
                                class="mt-2 inline-flex rounded-lg bg-[#ab021c] px-3 py-1.5 text-xs font-bold text-white transition hover:bg-[#8f0018]"
                            >
                                Absensi
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm font-semibold text-[#ab021c]">
                    Event tidak ditemukan untuk filter yang dipilih.
                </div>
            @endforelse
        </div>

        <div class="mt-5 flex justify-center">
            @include('frontend.partials.pager', ['paginator' => $registrations])
        </div>
    </section>
@endsection
