@extends('layouts.frontend')

@section('title', 'Kelola Tim')
@section('robots', 'noindex, nofollow')

@section('content')
    <section class="p-5 md:p-6">
        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <h1 class="font-['Sora',sans-serif] text-2xl font-extrabold text-[#ab021c]">{{ $team->nama_tim }}</h1>
                <p class="mt-1 text-sm font-semibold text-[#ab021c]/85">
                    Lomba: {{ $team->event->nama_event ?? '—' }}
                    @if($team->branch)
                        <span class="ml-1 inline-flex rounded-full bg-[#ab021c]/15 px-2 py-0.5 text-[11px]">{{ $team->branch->nama_cabang }}</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('frontend.teams.index') }}"
               class="inline-flex rounded-xl bg-[#ab021c]/10 px-4 py-2 text-sm font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white">
                Kembali
            </a>
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
        @if($errors->any())
            <div class="mb-4 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-[#ab021c]/25 p-5">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">
                    Anggota ({{ $team->members->count() }}{{ $remaining !== null ? ' — sisa ' . $remaining . ' slot' : '' }})
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">
                            <th class="py-1 pr-3">#</th>
                            <th class="py-1 pr-3">Nama / Email</th>
                            <th class="py-1 pr-3">NPM</th>
                            <th class="py-1 pr-3">Status</th>
                            <th class="py-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-[#ab021c]/90">
                        @foreach($team->members as $m)
                            <tr class="border-t border-[#ab021c]/10">
                                <td class="py-1.5 pr-3 text-[#ab021c]/60">{{ $loop->iteration }}</td>
                                <td class="py-1.5 pr-3 font-semibold">{{ $m->nama ?: $m->invited_email }}</td>
                                <td class="py-1.5 pr-3">{{ $m->npm ?: '—' }}</td>
                                <td class="py-1.5 pr-3">
                                    @if($m->isConfirmed())
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-700">Confirmed</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-700">Pending</span>
                                    @endif
                                </td>
                                <td class="py-1.5">
                                    @if($m->user_id && (int) $m->user_id === (int) $team->ketua_user_id)
                                        <span class="text-[11px] font-semibold text-[#ab021c]/50">Ketua Tim</span>
                                    @else
                                        <form method="POST" action="{{ route('frontend.teams.member.remove', [$team->id, $m->id]) }}"
                                              onsubmit="return confirm('Hapus anggota ini dari tim?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex rounded-lg bg-white px-3 py-1 text-xs font-bold text-red-600 ring-1 ring-red-300 transition hover:bg-red-600 hover:text-white">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($remaining === null || $remaining > 0)
            <div class="mt-4 rounded-2xl border border-[#ab021c]/25 p-5">
                <h2 class="mb-3 text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">Undang Anggota Baru</h2>
                <form method="POST" action="{{ route('frontend.teams.invite', $team->id) }}" class="space-y-2">
                    @csrf
                    <div id="inviteRows" class="space-y-2"></div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="addInviteBtn" onclick="addInviteRow()"
                                class="inline-flex items-center gap-1 rounded-lg bg-[#ab021c]/10 px-3 py-1.5 text-xs font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white">
                            + Tambah Anggota
                        </button>
                        <button type="submit"
                                class="inline-flex rounded-xl bg-[#ab021c] px-5 py-2 text-sm font-bold text-white transition hover:bg-[#8f0018]">
                            Kirim Undangan
                        </button>
                    </div>
                    <p class="text-xs font-semibold text-[#ab021c]/60">
                        Masukkan NPM (mahasiswa Teknokrat) atau Email (mahasiswa diluar Teknokrat) — harus sudah punya akun terdaftar di sistem ini.
                    </p>
                </form>
            </div>
        @endif
    </section>

    <script>
        const maksTambahan = {{ $remaining === null ? 'null' : (int) $remaining }};

        function updateAddInviteButton() {
            const btn = document.getElementById('addInviteBtn');
            if (!btn) return;
            const count = document.querySelectorAll('#inviteRows .invite-row').length;
            if (maksTambahan !== null && count >= maksTambahan) {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        function addInviteRow() {
            if (maksTambahan !== null && document.querySelectorAll('#inviteRows .invite-row').length >= maksTambahan) {
                return;
            }
            const wrap = document.getElementById('inviteRows');
            const div = document.createElement('div');
            div.innerHTML = `
            <div class="invite-row grid items-center gap-2 md:grid-cols-[1fr_auto]">
                <input type="text" name="invite_identifiers[]" placeholder="NPM atau Email anggota"
                       class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20">
                <button type="button" onclick="this.closest('.invite-row').remove(); updateAddInviteButton();"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 text-white transition hover:bg-red-600" title="Hapus">
                    &times;
                </button>
            </div>`;
            wrap.appendChild(div.firstElementChild);
            updateAddInviteButton();
        }

        document.addEventListener('DOMContentLoaded', function () {
            addInviteRow();
        });
    </script>
@endsection
