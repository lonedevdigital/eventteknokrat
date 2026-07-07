@extends('layouts.frontend')

@section('content')
    <section class="p-5 md:p-6">
        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="font-['Sora',sans-serif] text-2xl font-extrabold text-[#ab021c]">My Teams</h1>
                <p class="mt-1 text-sm font-semibold text-[#ab021c]/85">
                    Tim yang Anda buat untuk mengikuti perlombaan.
                </p>
            </div>
            @if($teams->isNotEmpty())
                <a href="{{ route('frontend.teams.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-[#ab021c] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#8f0018]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg>
                    Buat Tim
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-green-300 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @forelse($teams as $team)
            <div class="mb-4 overflow-hidden rounded-2xl border border-[#ab021c]/25">
                <div class="flex flex-wrap items-center justify-between gap-2 bg-[#ab021c]/10 px-4 py-3">
                    <div>
                        <p class="font-['Sora',sans-serif] text-base font-extrabold text-[#ab021c]">{{ $team->nama_tim }}</p>
                        <p class="text-xs font-semibold text-[#ab021c]/80">
                            <span class="font-bold">Lomba:</span> {{ $team->event->nama_event ?? '—' }}
                            @if($team->branch)
                                <span class="ml-2 inline-flex rounded-full bg-[#ab021c]/15 px-2 py-0.5 text-[11px]">{{ $team->branch->nama_cabang }}</span>
                            @endif
                        </p>
                    </div>
                    <form method="POST" action="{{ route('frontend.teams.destroy', $team->id) }}"
                          onsubmit="return confirm('Hapus tim ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="inline-flex rounded-lg bg-white px-3 py-1.5 text-xs font-bold text-[#ab021c] ring-1 ring-[#ab021c]/30 transition hover:bg-[#ab021c] hover:text-white">
                            Hapus
                        </button>
                    </form>
                </div>

                <div class="px-4 py-3">
                    <p class="mb-2 text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">
                        Anggota ({{ $team->members->count() }})
                    </p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">
                                    <th class="py-1 pr-3">#</th>
                                    <th class="py-1 pr-3">Nama</th>
                                    <th class="py-1 pr-3">NPM</th>
                                    <th class="py-1">Program Studi</th>
                                </tr>
                            </thead>
                            <tbody class="text-[#ab021c]/90">
                                @foreach($team->members as $m)
                                    <tr class="border-t border-[#ab021c]/10">
                                        <td class="py-1.5 pr-3 text-[#ab021c]/60">{{ $loop->iteration }}</td>
                                        <td class="py-1.5 pr-3 font-semibold">{{ $m->nama }}</td>
                                        <td class="py-1.5 pr-3">{{ $m->npm ?: '—' }}</td>
                                        <td class="py-1.5">{{ $m->prodi ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-[#ab021c]/30 bg-[#ab021c]/[0.03] px-6 py-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-[#ab021c]/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#ab021c]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z"/></svg>
                </div>
                <h2 class="font-['Sora',sans-serif] text-lg font-extrabold text-[#ab021c]">Belum ada tim</h2>
                <p class="mx-auto mt-1 max-w-md text-sm font-semibold text-[#ab021c]/75">
                    Anda belum memiliki tim. Buat tim baru untuk mengikuti perlombaan — pastikan Anda sudah mendaftar ke lombanya terlebih dahulu.
                </p>
                <a href="{{ route('frontend.teams.create') }}"
                   class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#ab021c] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#8f0018]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg>
                    Buat Tim Baru
                </a>
            </div>
        @endforelse
    </section>
@endsection
