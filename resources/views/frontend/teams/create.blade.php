@extends('layouts.frontend')

@section('content')
    <section class="p-5 md:p-6">
        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <h1 class="font-['Sora',sans-serif] text-2xl font-extrabold text-[#ab021c]">Buat Tim Baru</h1>
                <p class="mt-1 text-sm font-semibold text-[#ab021c]/85">Daftarkan tim Anda untuk mengikuti perlombaan.</p>
            </div>
            <a href="{{ route('frontend.teams.index') }}"
               class="inline-flex rounded-xl bg-[#ab021c]/10 px-4 py-2 text-sm font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white">
                Kembali
            </a>
        </div>

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

        @if($lombaList->isEmpty())
            <div class="rounded-2xl border border-dashed border-[#ab021c]/30 bg-[#ab021c]/[0.03] px-6 py-12 text-center">
                <h2 class="font-['Sora',sans-serif] text-lg font-extrabold text-[#ab021c]">Belum mendaftar lomba</h2>
                <p class="mx-auto mt-1 max-w-md text-sm font-semibold text-[#ab021c]/75">
                    Anda harus mendaftar ke sebuah perlombaan terlebih dahulu sebelum bisa membuat tim.
                </p>
                <a href="{{ route('frontend.events') }}"
                   class="mt-5 inline-flex rounded-xl bg-[#ab021c] px-5 py-2.5 text-sm font-bold text-white transition hover:bg-[#8f0018]">
                    Lihat Daftar Event &amp; Lomba
                </a>
            </div>
        @else
        <form method="POST" action="{{ route('frontend.teams.store') }}" class="space-y-5">
            @csrf

            <div class="rounded-2xl border border-[#ab021c]/25 p-5">
                <h2 class="mb-4 text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">Informasi Tim</h2>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="mb-1 block text-sm font-bold text-[#ab021c]">Nama Tim</span>
                        <input type="text" name="nama_tim" value="{{ old('nama_tim') }}" required
                               placeholder="Contoh: Tim Garuda"
                               class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20">
                    </label>

                    <label class="block">
                        <span class="mb-1 block text-sm font-bold text-[#ab021c]">Perlombaan</span>
                        <select name="event_id" id="eventSelect" required onchange="onLombaChange()"
                                class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20">
                            <option value="">— Pilih Lomba —</option>
                            @foreach($lombaList as $lomba)
                                <option value="{{ $lomba->id }}" {{ old('event_id')==$lomba->id?'selected':'' }}>{{ $lomba->nama_event }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <label class="mt-4 block" id="branchWrap" style="display:none;">
                    <span class="mb-1 block text-sm font-bold text-[#ab021c]">Cabang Lomba</span>
                    <select name="branch_id" id="branchSelect"
                            class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20">
                        <option value="">— Pilih Cabang —</option>
                    </select>
                </label>
            </div>

            <div class="rounded-2xl border border-[#ab021c]/25 p-5">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">Anggota Tim</h2>
                    <button type="button" onclick="addMember()"
                            class="inline-flex items-center gap-1 rounded-lg bg-[#ab021c]/10 px-3 py-1.5 text-xs font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white">
                        + Tambah Anggota
                    </button>
                </div>
                <div id="memberRows" class="space-y-2"></div>
                <p class="mt-2 text-xs font-semibold text-[#ab021c]/60">
                    Isi data tiap anggota tim. Baris pertama otomatis terisi data Anda sebagai ketua tim.
                </p>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('frontend.teams.index') }}"
                   class="inline-flex rounded-xl bg-[#ab021c]/10 px-5 py-2.5 text-sm font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex rounded-xl bg-[#ab021c] px-6 py-2.5 text-sm font-bold text-white transition hover:bg-[#8f0018]">
                    Simpan Tim
                </button>
            </div>
        </form>
        @endif
    </section>

    @if($lombaList->isNotEmpty())
    <script>
        // Map lomba → cabang (disiapkan di controller)
        const LOMBA_BRANCHES = @json($lombaBranches);
        const CAPTAIN = @json($captain);

        function onLombaChange() {
            const id = document.getElementById('eventSelect').value;
            const wrap = document.getElementById('branchWrap');
            const sel = document.getElementById('branchSelect');
            const info = LOMBA_BRANCHES[id];
            sel.innerHTML = '<option value="">— Pilih Cabang —</option>';

            if (info && info.tipe_bayar === 'berbayar' && info.branches.length > 0) {
                info.branches.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.id;
                    const harga = Number(b.harga || 0).toLocaleString('id-ID');
                    opt.textContent = b.nama + ' — Rp ' + harga;
                    sel.appendChild(opt);
                });
                wrap.style.display = 'block';
            } else {
                wrap.style.display = 'none';
            }
        }

        function memberRowHtml(nama, npm, prodi, isCaptain) {
            const ph = isCaptain ? ' (Ketua Tim)' : '';
            return `
            <div class="member-row grid items-center gap-2 md:grid-cols-[2fr_1.2fr_1.5fr_auto]">
                <input type="text" name="member_nama[]" value="${nama||''}" placeholder="Nama${ph}"
                       class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20">
                <input type="text" name="member_npm[]" value="${npm||''}" placeholder="NPM"
                       class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20">
                <input type="text" name="member_prodi[]" value="${prodi||''}" placeholder="Program Studi"
                       class="w-full rounded-lg border-0 bg-[#ab021c]/5 px-3 py-2 text-sm font-semibold text-[#ab021c] ring-0 focus:ring-2 focus:ring-[#ab021c]/20">
                <button type="button" onclick="this.closest('.member-row').remove()"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 text-white transition hover:bg-red-600" title="Hapus">
                    &times;
                </button>
            </div>`;
        }

        function addMember(nama, npm, prodi, isCaptain) {
            const wrap = document.getElementById('memberRows');
            const div = document.createElement('div');
            div.innerHTML = memberRowHtml(nama, npm, prodi, isCaptain);
            wrap.appendChild(div.firstElementChild);
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Baris pertama = ketua tim (prefill)
            addMember(CAPTAIN.nama, CAPTAIN.npm, CAPTAIN.prodi, true);
            addMember('', '', '', false);
            onLombaChange();
        });
    </script>
    @endif
@endsection
