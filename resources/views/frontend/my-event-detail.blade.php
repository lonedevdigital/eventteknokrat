@extends('layouts.frontend')

@section('content')
    @php
        $branch = $registration->branch;
        $isTim = $registration->tipe_pendaftaran === 'tim';

        $biaya = 'Gratis';
        if ($branch && $branch->tipe_bayar === 'berbayar') {
            $harga = $isTim ? $branch->harga_tim_efektif : $branch->harga_individual_efektif;
            $biaya = 'Rp ' . number_format($harga, 0, ',', '.') . ($isTim ? ' / tim' : ' / individu');
        }

        $isKetua = $isTim && $team && (int) $team->ketua_user_id === (int) auth()->id();
        $canCancel = $registration->status !== 'attended';
        $cancelConfirmText = $isKetua
            ? 'Batalkan pendaftaran ini? Tim "' . $team->nama_tim . '" beserta seluruh anggotanya akan ikut dihapus.'
            : 'Batalkan pendaftaran Anda untuk lomba ini?';
    @endphp

    <section class="p-5 md:p-7">
        <div class="mb-5 flex items-center justify-between gap-3">
            <h1 class="font-['Sora',sans-serif] text-2xl font-extrabold text-[#ab021c] md:text-3xl">
                {{ $event->nama_event }}
            </h1>
            <a href="{{ route('frontend.my-events') }}"
               class="inline-flex rounded-xl bg-[#ab021c]/10 px-4 py-2 text-sm font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white">
                Kembali
            </a>
        </div>

        <div class="grid gap-5">
            <div class="rounded-2xl bg-[#ab021c]/5 p-4 md:p-5">
                <h2 class="font-['Sora',sans-serif] text-lg font-extrabold text-[#ab021c]">Informasi Pendaftaran</h2>
                <dl class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">Cabang Lomba</dt>
                        <dd class="text-sm font-bold text-[#ab021c]">{{ $branch->nama_cabang ?? 'Tidak ada cabang' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">Biaya Pendaftaran</dt>
                        <dd class="text-sm font-bold text-[#ab021c]">{{ $biaya }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">Tipe Pendaftaran</dt>
                        <dd class="text-sm font-bold text-[#ab021c]">{{ $isTim ? 'Tim' : 'Individu' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">Tanggal Daftar</dt>
                        <dd class="text-sm font-bold text-[#ab021c]">{{ $registration->registered_at ? \Carbon\Carbon::parse($registration->registered_at)->translatedFormat('d F Y') : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">Tanggal Pelaksanaan</dt>
                        <dd class="text-sm font-bold text-[#ab021c]">{{ $event->pelaksanaan_range_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">Tempat</dt>
                        <dd class="text-sm font-bold text-[#ab021c]">{{ $event->tempat_pelaksanaan ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            @if($isTim && $team)
            <div class="rounded-2xl bg-[#ab021c]/5 p-4 md:p-5">
                <h2 class="font-['Sora',sans-serif] text-lg font-extrabold text-[#ab021c]">Tim: {{ $team->nama_tim }}</h2>
                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-[11px] font-bold uppercase tracking-wide text-[#ab021c]/60">
                                <th class="pb-2">#</th>
                                <th class="pb-2">Nama</th>
                                <th class="pb-2">NPM</th>
                                <th class="pb-2">Program Studi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($team->members as $m)
                                <tr class="border-t border-[#ab021c]/10">
                                    <td class="py-2 text-[#ab021c]/70">{{ $loop->iteration }}</td>
                                    <td class="py-2 font-semibold text-[#ab021c]">
                                        {{ $m->nama }}
                                        @if((int) $m->user_id === (int) $team->ketua_user_id)
                                            <span class="ml-1 text-[10px] font-bold uppercase text-[#ab021c]/60">(Ketua)</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-[#ab021c]/85">{{ $m->npm ?: '-' }}</td>
                                    <td class="py-2 text-[#ab021c]/85">{{ $m->prodi ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if($event->isLomba() && $documentRequirements->isNotEmpty())
            <div class="rounded-2xl bg-[#ab021c]/5 p-4 md:p-5">
                <h2 class="font-['Sora',sans-serif] text-lg font-extrabold text-[#ab021c]">Berkas yang Diperlukan</h2>
                <div class="mt-3 grid gap-3">
                    @foreach($documentRequirements as $docReq)
                        @php
                            $doc = $myDocuments->get($docReq->id);
                            $statusLabel = match($doc?->status) {
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                'pending' => 'Menunggu Review',
                                default => 'Belum Diupload',
                            };
                            $statusClass = match($doc?->status) {
                                'approved' => 'bg-emerald-100 text-emerald-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                'pending' => 'bg-amber-100 text-amber-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                            $fileUrl = $doc ? (\Illuminate\Support\Str::startsWith($doc->file_path, ['http://', 'https://']) ? $doc->file_path : asset($doc->file_path)) : null;
                        @endphp
                        <div class="rounded-xl bg-white/70 p-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p class="text-sm font-bold text-[#ab021c]">
                                        {{ $docReq->nama_dokumen }}
                                        <span class="ml-1 text-[10px] font-semibold uppercase text-[#ab021c]/50">({{ $docReq->jenis_file === 'pdf' ? 'PDF' : ($docReq->jenis_file === 'link' ? 'Link' : 'Gambar') }})</span>
                                    </p>
                                    @if($fileUrl)
                                        <a href="{{ $fileUrl }}" target="_blank" class="text-xs font-semibold text-[#ab021c] underline">Lihat berkas terupload</a>
                                    @endif
                                    @if($doc && $doc->status === 'rejected' && $doc->review_notes)
                                        <p class="mt-1 text-xs font-semibold text-red-600">Catatan: {{ $doc->review_notes }}</p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                            </div>

                            @if(! $doc || $doc->status !== 'approved')
                                @if(!$isTim || $isKetua)
                                <form method="POST" action="{{ route('frontend.my-event.documents.upload', [$event->slug ?: $event->id, $docReq->id]) }}"
                                      enctype="multipart/form-data" class="mt-2 flex flex-wrap items-center gap-2">
                                    @csrf
                                    @if($docReq->jenis_file === 'link')
                                        <input type="url" name="file_link" required placeholder="https://..."
                                               class="w-full sm:w-64 rounded-lg border-gray-300 bg-[#ab021c]/5 px-3 py-1.5 text-xs text-[#ab021c] focus:border-[#ab021c] focus:ring-[#ab021c]">
                                    @else
                                        <input type="file" name="file" accept="{{ $docReq->jenis_file === 'pdf' ? 'application/pdf' : 'image/*' }}" required
                                               class="text-xs text-[#ab021c] file:mr-2 file:rounded-lg file:border-0 file:bg-[#ab021c]/10 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-[#ab021c]">
                                    @endif
                                    <button type="submit" class="rounded-lg bg-[#ab021c] px-3 py-1.5 text-xs font-bold text-white transition hover:opacity-90">
                                        {{ $doc ? 'Upload Ulang' : 'Upload' }}
                                    </button>
                                </form>
                                @else
                                <p class="mt-2 text-xs font-semibold text-[#ab021c]/60">
                                    Hanya ketua tim yang dapat mengupload berkas ini.
                                </p>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($registration->allDocumentsApproved())
                    <div class="mt-4 rounded-xl bg-emerald-100 px-4 py-4 text-center">
                        <p class="text-sm font-bold text-emerald-700">Seluruh Berkas telah di Approve.</p>
                        @php
                            $waLink = $registration->branch?->wa_group_link ?? $event->wa_group_link;
                        @endphp
                        @if($waLink)
                            <p class="mt-1 mb-2 text-xs font-semibold text-emerald-600">Silakan bergabung ke grup WhatsApp berikut untuk info lebih lanjut:</p>
                            <a href="{{ $waLink }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-bold text-white transition hover:bg-emerald-600">
                                <i class="fab fa-whatsapp text-lg"></i>
                                Bergabung Grup WhatsApp
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            @endif

            @if($event->jadwal_timeline)
            <div class="rounded-2xl bg-[#ab021c]/5 p-4 md:p-5">
                <h2 class="font-['Sora',sans-serif] text-lg font-extrabold text-[#ab021c]">Jadwal &amp; Timeline</h2>
                <p class="mt-2 whitespace-pre-line text-sm font-medium leading-relaxed text-[#ab021c]/85 md:text-base">
                    {{ $event->jadwal_timeline }}
                </p>
            </div>
            @endif

            <div class="rounded-2xl border-2 border-red-200 bg-red-50 p-4 md:p-5">
                <h2 class="font-['Sora',sans-serif] text-lg font-extrabold text-red-700">Batalkan Pendaftaran</h2>
                @if($canCancel)
                    <p class="mt-1 text-sm font-medium text-red-700/80">
                        @if($isKetua)
                            Anda adalah ketua tim. Membatalkan pendaftaran akan ikut menghapus tim beserta seluruh anggotanya.
                        @else
                            Anda tidak akan lagi terdaftar pada cabang lomba ini.
                        @endif
                    </p>
                    <form method="POST" action="{{ route('frontend.my-event.cancel', ['slug' => $event->slug ?: $event->id, 'branch' => $registration->branch_id]) }}"
                          class="mt-3" onsubmit="return confirm({{ \Illuminate\Support\Js::from($cancelConfirmText) }})">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-red-700">
                            Batalkan Pendaftaran Cabang Lomba
                        </button>
                    </form>
                @else
                    <p class="mt-1 text-sm font-medium text-red-700/80">
                        Pendaftaran tidak dapat dibatalkan karena Anda sudah melakukan absensi.
                    </p>
                @endif
            </div>
        </div>
    </section>
@endsection
