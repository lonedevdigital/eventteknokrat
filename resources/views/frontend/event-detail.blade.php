@extends('layouts.frontend')

@section('content')
    @php
        $thumb = $event->thumbnail;
        if ($thumb && !\Illuminate\Support\Str::startsWith($thumb, ['http://', 'https://'])) {
            $thumb = asset($thumb);
        }

        $creatorName = $event->creator->name ?? '-';
    @endphp

    <section class="p-5 md:p-7">
        <h1 class="text-center font-['Sora',sans-serif] text-2xl font-extrabold text-[#ab021c] md:text-3xl">
            {{ $event->nama_event }}
        </h1>

        <p class="mt-2 text-center text-sm font-semibold text-[#ab021c]/75 md:text-base">
            Pengadaan Acara: <span class="font-bold">{{ $creatorName }}</span>
        </p>

        @if (session('success'))
            <div class="mt-5 rounded-xl bg-emerald-100 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('info'))
            <div class="mt-5 rounded-xl bg-amber-100 px-4 py-3 text-sm font-semibold text-amber-700">
                {{ session('info') }}
            </div>
        @endif

        <div class="mt-6 overflow-hidden rounded-2xl bg-[#ab021c]/5">
            <img
                src="{{ $thumb ?: 'https://placehold.co/1280x720/ffffff/ab021c?text=Event+Teknokrat+University' }}"
                alt="{{ $event->nama_event }}"
                class="h-[220px] w-full object-cover md:h-[360px]"
            >
        </div>

        <div class="mt-6 grid gap-5">
            <div class="rounded-2xl bg-[#ab021c]/5 p-4 md:p-5">
                <h2 class="font-['Sora',sans-serif] text-lg font-extrabold text-[#ab021c]">Deskripsi Acara</h2>
                <p class="mt-2 whitespace-pre-line text-sm font-medium leading-relaxed text-[#ab021c]/85 md:text-base">
                    {{ $event->deskripsi ?: 'Belum ada deskripsi acara.' }}
                </p>
            </div>

            <div class="rounded-2xl bg-[#ab021c]/5 p-4 md:p-5">
                <h2 class="font-['Sora',sans-serif] text-lg font-extrabold text-[#ab021c]">Informasi Tambahan</h2>
                <p class="mt-2 whitespace-pre-line text-sm font-medium leading-relaxed text-[#ab021c]/85 md:text-base">
                    {{ $event->informasi_lainnya ?: 'Belum ada informasi tambahan.' }}
                </p>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            @auth
                @if($hasRegistered)
                    <span class="inline-flex items-center rounded-xl bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-700">
                        Sudah mendaftar event ini
                    </span>
                @elseif($isRegistrationClosed)
                    <span class="inline-flex items-center rounded-xl bg-[#ab021c]/15 px-4 py-2 text-sm font-bold text-[#ab021c]">
                        Pendaftaran sudah ditutup
                    </span>
                @else
                    <form method="POST" action="{{ route('frontend.events.register', $event->slug ?: $event->id) }}">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-xl bg-[#ab021c] px-5 py-2.5 text-sm font-bold text-white transition hover:opacity-90"
                        >
                            Daftar Event
                        </button>
                    </form>
                @endif
            @else
                @if($isRegistrationClosed)
                    <span class="inline-flex items-center rounded-xl bg-[#ab021c]/15 px-4 py-2 text-sm font-bold text-[#ab021c]">
                        Pendaftaran sudah ditutup
                    </span>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center rounded-xl bg-[#ab021c] px-5 py-2.5 text-sm font-bold text-white transition hover:opacity-90"
                    >
                        Login untuk Daftar
                    </a>
                @endif
            @endauth
        </div>
    </section>
@endsection
