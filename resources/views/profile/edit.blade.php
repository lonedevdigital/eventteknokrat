@extends('layouts.frontend')

@section('title', 'Profil Saya')
@section('robots', 'noindex, nofollow')

@section('content')
    @php
        $isAdmin = $isAdmin ?? ($user?->isStaff() ?? false);
        $isExternal = $isExternal ?? ($user?->isExternalStudent() ?? false);

        $genderLabel = match ($mahasiswa?->gender) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
        $showUsername = !$mahasiswa || ($user->username !== ($mahasiswa->npm_mahasiswa ?? null));
        $shouldOpenPasswordForm = $errors->has('current-password')
            || $errors->has('new_password')
            || $errors->has('new_password_confirm')
            || (bool) session('error');

        $initial = strtoupper(mb_substr(trim((string) $user->name) ?: '?', 0, 1));

        if ($isAdmin) {
            $badgeLabel = 'Status Administrator';
            $badgeClasses = 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-300';
            $dotClasses = 'bg-emerald-500';
        } elseif ($isExternal) {
            $badgeLabel = 'Mahasiswa Diluar Teknokrat';
            $badgeClasses = 'bg-slate-100 text-slate-700 ring-1 ring-slate-300';
            $dotClasses = 'bg-slate-400';
        } else {
            $badgeLabel = 'Mahasiswa Teknokrat';
            $badgeClasses = 'bg-[#ab021c]/10 text-[#ab021c] ring-1 ring-[#ab021c]/25';
            $dotClasses = 'bg-[#ab021c]';
        }

        // Susun field biodata sesuai jenis akun — lihat ProfileController::edit() untuk alasan pemisahannya.
        if ($mahasiswa) {
            $bioTitle = $isAdmin ? 'Biodata Mahasiswa (Tertaut ke Akun Admin)' : 'Biodata Mahasiswa';
            $bioFields = [
                ['NPM', $mahasiswa->npm_mahasiswa ?: '-'],
                ['Nama', $mahasiswa->nama_mahasiswa ?: '-'],
                ['Angkatan', $mahasiswa->angkatan ?: '-'],
                ['Program Studi', $mahasiswa->nama_program_studi ?: '-'],
                ['Fakultas', $mahasiswa->nama_fakultas ?: '-'],
                ['Jenis Kelamin', $genderLabel],
                ['No. Telepon', $mahasiswa->no_telepon ?: '-'],
                ['SKS', $mahasiswa->sks ?: '-'],
                ['IPK', $mahasiswa->ipk ?: '-'],
            ];
            if ($showUsername) {
                $bioFields[] = ['Username', $user->username ?: '-'];
            }
            $bioFields[] = ['Email', $user->email ?: '-'];
        } elseif ($isExternal) {
            $bioTitle = 'Data Pendaftaran';
            $bioFields = [
                ['Nama', $user->name ?: '-'],
                ['Email', $user->email ?: '-'],
                ['NPM', $user->npm ?: '- (bukan mahasiswa)'],
                ['Asal Sekolah / Universitas', $user->institution->nama ?? '-'],
            ];
        } elseif ($isAdmin) {
            $bioTitle = 'Data Akun';
            $bioFields = [
                ['Nama', $user->name ?: '-'],
                ['Username', $user->username ?: '-'],
                ['Email', $user->email ?: '-'],
                ['Role', strtoupper(str_replace('_', ' ', $user->role ?: '-'))],
            ];
        } else {
            $bioTitle = 'Biodata Mahasiswa';
            $bioFields = [];
        }
    @endphp

    <section class="rounded-3xl bg-white p-5 shadow-sm md:p-6">
        @if(session('error'))
            <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- HEADER PROFIL --}}
        <article class="relative overflow-hidden rounded-2xl bg-[linear-gradient(135deg,_#ab021c_0%,_#8f0018_100%)] p-5 shadow-sm md:p-6">
            <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -bottom-14 right-16 h-32 w-32 rounded-full bg-white/5"></div>
            <div class="relative flex flex-col items-center gap-4 text-center sm:flex-row sm:text-left">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/15 font-['Sora',sans-serif] text-2xl font-extrabold text-white ring-1 ring-white/30">
                    {{ $initial }}
                </div>
                <div class="min-w-0">
                    <h1 class="truncate font-['Sora',sans-serif] text-xl font-extrabold text-white md:text-2xl">
                        {{ $user->name ?: 'Pengguna' }}
                    </h1>
                    <p class="mt-0.5 truncate text-sm font-semibold text-white/80">{{ $user->email ?: '-' }}</p>
                    <span class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-extrabold {{ $badgeClasses }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $dotClasses }}"></span>
                        {{ $badgeLabel }}
                    </span>
                </div>
            </div>
        </article>

        {{-- BIODATA --}}
        <article class="relative mt-4 overflow-hidden rounded-2xl bg-white p-4 shadow-sm ring-1 ring-[#ab021c]/10 md:p-5">
            <div class="mb-4 h-3 w-full rounded-full bg-[linear-gradient(90deg,_#be0a28_0%,_#be0a28_52%,_#8f0018_52%,_#8f0018_100%)]"></div>
            <h2 class="mb-3 text-base font-extrabold text-[#ab021c]">{{ $bioTitle }}</h2>

            @if(empty($bioFields))
                <p class="text-sm font-semibold text-[#ab021c]/80">
                    Data mahasiswa belum ditemukan untuk akun ini.
                </p>
            @else
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach($bioFields as [$label, $value])
                        <div class="rounded-xl bg-[#ab021c]/5 px-4 py-3">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">{{ $label }}</p>
                            <p class="mt-0.5 truncate text-sm font-bold text-[#ab021c]" title="{{ $value }}">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>

        {{-- UBAH PASSWORD --}}
        <article class="relative mt-4 overflow-hidden rounded-2xl bg-white p-4 shadow-sm ring-1 ring-[#ab021c]/10">
            <div class="mb-3 h-3 w-full rounded-full bg-[linear-gradient(90deg,_#be0a28_0%,_#be0a28_52%,_#8f0018_52%,_#8f0018_100%)]"></div>
            <details class="group" @if($shouldOpenPasswordForm) open @endif>
                <summary class="flex cursor-pointer list-none items-center justify-between rounded-xl bg-[#ab021c]/8 px-4 py-3 text-sm font-extrabold text-[#ab021c] transition hover:bg-[#ab021c]/15">
                    <span>Ubah Password</span>
                    <span class="text-base leading-none transition group-open:rotate-180">&#9662;</span>
                </summary>

                <div class="mt-3">
                    <form method="POST" action="{{ route('profile.update') }}" class="grid gap-3 md:grid-cols-3">
                        @csrf
                        @method('PATCH')

                        <label>
                            <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#ab021c]/80">Password Lama</span>
                            <input
                                type="password"
                                name="current-password"
                                class="w-full rounded-xl border-0 bg-[#ab021c]/5 px-4 py-2.5 text-sm font-semibold text-[#ab021c] outline-none ring-0 focus:ring-2 focus:ring-[#ab021c]/20"
                                required
                            >
                            @error('current-password')
                                <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>
                            @enderror
                        </label>

                        <label>
                            <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#ab021c]/80">Password Baru</span>
                            <input
                                type="password"
                                name="new_password"
                                class="w-full rounded-xl border-0 bg-[#ab021c]/5 px-4 py-2.5 text-sm font-semibold text-[#ab021c] outline-none ring-0 focus:ring-2 focus:ring-[#ab021c]/20"
                                required
                            >
                            @error('new_password')
                                <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>
                            @enderror
                        </label>

                        <label>
                            <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#ab021c]/80">Konfirmasi Password Baru</span>
                            <input
                                type="password"
                                name="new_password_confirm"
                                class="w-full rounded-xl border-0 bg-[#ab021c]/5 px-4 py-2.5 text-sm font-semibold text-[#ab021c] outline-none ring-0 focus:ring-2 focus:ring-[#ab021c]/20"
                                required
                            >
                            @error('new_password_confirm')
                                <span class="mt-1 block text-xs font-bold text-red-600">{{ $message }}</span>
                            @enderror
                        </label>

                        <div class="md:col-span-3">
                            <button
                                type="submit"
                                class="inline-flex rounded-xl bg-[#ab021c] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#8f0018]"
                            >
                                Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </details>
        </article>
    </section>
@endsection
