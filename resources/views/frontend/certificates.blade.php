@extends('layouts.frontend')

@section('content')
    <section class="p-5 md:p-6">
        <h1 class="font-['Sora',sans-serif] text-2xl font-extrabold text-[#ab021c]">Sertifikat</h1>
        <p class="mt-1 text-sm font-semibold text-[#ab021c]/85">Daftar sertifikat yang sudah tersedia untuk akun Anda.</p>

        <div class="mt-4">
            @if($certificates->isEmpty())
                <div class="rounded-2xl bg-[#ab021c]/5 p-5 text-center text-sm font-semibold text-[#ab021c]">
                    Belum ada sertifikat yang tersedia.
                </div>
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($certificates as $item)
                        @php
                            $event = $item->event;
                            $url = $item->certificate_url;

                            if ($url && !\Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])) {
                                $url = url($url);
                            }
                        @endphp
                        <article class="grid gap-3 rounded-2xl bg-white p-4 shadow-sm">
                            <h3 class="text-base font-extrabold leading-tight text-[#ab021c]">
                                {{ $event?->nama_event ?? 'Event tidak tersedia' }}
                            </h3>
                            <p class="text-sm font-semibold text-[#ab021c]/85">
                                Upload Sertifikat:
                                <strong>{{ $item->certificate_uploaded_at ? \Carbon\Carbon::parse($item->certificate_uploaded_at)->format('d M Y H:i') : '-' }}</strong>
                            </p>
                            <p class="text-sm font-semibold text-[#ab021c]/85">
                                Tanggal Event:
                                <strong>{{ $event?->tanggal_pelaksanaan ? \Carbon\Carbon::parse($event->tanggal_pelaksanaan)->format('d M Y') : '-' }}</strong>
                            </p>
                            @if($url)
                                <a
                                    href="{{ $url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex w-fit items-center justify-center gap-2 rounded-lg bg-[#ab021c] px-3 py-2 text-sm font-extrabold text-white transition hover:opacity-85"
                                >
                                    Lihat Sertifikat
                                </a>
                            @endif
                        </article>
                    @endforeach
                </div>

                <div class="mt-5 flex justify-center">
                    @include('frontend.partials.pager', ['paginator' => $certificates])
                </div>
            @endif
        </div>
    </section>
@endsection
