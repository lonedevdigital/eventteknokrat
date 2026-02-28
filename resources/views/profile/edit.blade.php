@extends('layouts.frontend')

@section('content')
    @php
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
    @endphp

    <section class="rounded-3xl bg-white p-5 shadow-sm md:p-6">
        <div class="mb-4">
            <h1 class="font-['Sora',sans-serif] text-2xl font-extrabold text-[#ab021c]">My Profile</h1>
            <p class="mt-1 text-sm font-semibold text-[#ab021c]/85">
                Data akun dan biodata mahasiswa dari database.
            </p>
        </div>

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

        <article class="relative overflow-hidden rounded-2xl bg-white p-4 shadow-sm ring-1 ring-[#ab021c]/10">
            <div class="mb-3 h-3 w-full rounded-full bg-[linear-gradient(90deg,_#be0a28_0%,_#be0a28_52%,_#8f0018_52%,_#8f0018_100%)]"></div>
            <h2 class="mb-2 text-base font-extrabold text-[#ab021c]">Biodata Mahasiswa</h2>

            @if(!$mahasiswa)
                <p class="text-sm font-semibold text-[#ab021c]/80">
                    Data mahasiswa belum ditemukan untuk akun ini.
                </p>
            @else
                <div class="space-y-2 text-sm">
                    <p><span class="font-bold text-[#ab021c]">NPM:</span> {{ $mahasiswa->npm_mahasiswa ?: '-' }}</p>
                    <p><span class="font-bold text-[#ab021c]">Nama:</span> {{ $mahasiswa->nama_mahasiswa ?: '-' }}</p>
                    <p><span class="font-bold text-[#ab021c]">Angkatan:</span> {{ $mahasiswa->angkatan ?: '-' }}</p>
                    <p><span class="font-bold text-[#ab021c]">Program Studi:</span> {{ $mahasiswa->nama_program_studi ?: '-' }}</p>
                    <p><span class="font-bold text-[#ab021c]">Fakultas:</span> {{ $mahasiswa->nama_fakultas ?: '-' }}</p>
                    <p><span class="font-bold text-[#ab021c]">Jenis Kelamin:</span> {{ $genderLabel }}</p>
                    <p><span class="font-bold text-[#ab021c]">No. Telepon:</span> {{ $mahasiswa->no_telepon ?: '-' }}</p>
                    <p><span class="font-bold text-[#ab021c]">SKS:</span> {{ $mahasiswa->sks ?: '-' }}</p>
                    <p><span class="font-bold text-[#ab021c]">IPK:</span> {{ $mahasiswa->ipk ?: '-' }}</p>
                    @if($showUsername)
                        <p><span class="font-bold text-[#ab021c]">Username:</span> {{ $user->username }}</p>
                    @endif
                    <p><span class="font-bold text-[#ab021c]">Email:</span> {{ $user->email ?: '-' }}</p>
                    <p><span class="font-bold text-[#ab021c]">Status:</span> {{ strtoupper($user->role ?: '-') }}</p>
                </div>
            @endif
        </article>

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
