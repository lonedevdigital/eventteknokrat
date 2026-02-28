@extends('layouts.frontend')

@section('content')
    <section class="p-5 md:p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="font-['Sora',sans-serif] text-2xl font-extrabold text-[#ab021c]">Absensi Event</h1>
                <p class="mt-1 text-sm font-semibold text-[#ab021c]/85">
                    Akses kamera lalu scan QR code dari panitia. Setelah berhasil, status event Anda menjadi Sudah Absen.
                </p>
            </div>
            <a
                href="{{ route('frontend.my-events') }}"
                class="inline-flex rounded-xl bg-[#ab021c]/10 px-3 py-2 text-xs font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white"
            >
                Kembali ke My Event
            </a>
        </div>

        <div class="mt-4">
            <article class="rounded-2xl bg-[#ab021c]/5 p-4">
                <h2 class="mb-2 text-sm font-extrabold uppercase tracking-wide text-[#ab021c]">Scan via Kamera</h2>
                <div id="reader" class="min-h-[280px] overflow-hidden rounded-xl bg-white p-2"></div>
                <p id="cameraStatus" class="mt-2 text-xs font-semibold text-[#ab021c]/75">
                    Klik "Mulai Kamera" untuk mulai scan.
                </p>

                <div class="mt-3 flex flex-wrap gap-2">
                    <button
                        type="button"
                        id="startScanBtn"
                        class="inline-flex rounded-lg bg-[#ab021c] px-3 py-1.5 text-xs font-bold text-white transition hover:bg-[#8f0018]"
                    >
                        Mulai Kamera
                    </button>
                    <button
                        type="button"
                        id="stopScanBtn"
                        class="inline-flex rounded-lg bg-[#ab021c]/10 px-3 py-1.5 text-xs font-bold text-[#ab021c] transition hover:bg-[#ab021c] hover:text-white"
                    >
                        Berhenti
                    </button>
                </div>
                <div id="scanResultBox" class="mt-3 hidden rounded-xl px-3 py-2 text-sm font-semibold"></div>
            </article>
        </div>

        <article class="mt-4 rounded-2xl bg-[#ab021c]/5 p-4">
            <h2 class="mb-3 text-sm font-extrabold uppercase tracking-wide text-[#ab021c]">Absensi Terakhir</h2>
            @if($recentAttendances->isEmpty())
                <p class="text-sm font-semibold text-[#ab021c]/75">Belum ada riwayat absensi.</p>
            @else
                <div class="grid gap-2">
                    @foreach($recentAttendances as $item)
                        <div class="rounded-xl bg-white px-3 py-2 text-sm font-semibold text-[#ab021c]">
                            {{ $item->event?->nama_event ?? 'Event' }}
                            <span class="text-[#ab021c]/70">- {{ $item->attendance_at?->format('d M Y H:i') ?? '-' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        (function () {
            const endpoint = @json(route('frontend.attendance.submit'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const readerId = 'reader';
            const startBtn = document.getElementById('startScanBtn');
            const stopBtn = document.getElementById('stopScanBtn');
            const cameraStatus = document.getElementById('cameraStatus');
            const resultBox = document.getElementById('scanResultBox');

            if (!startBtn || !stopBtn || !cameraStatus || !resultBox) {
                return;
            }

            let scanner = null;
            let isScanning = false;
            let isSubmitting = false;
            let lastToken = '';
            let lastScanAt = 0;

            function showResult(success, message) {
                resultBox.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'bg-emerald-50', 'text-emerald-700');
                resultBox.classList.add(success ? 'bg-emerald-50' : 'bg-red-50');
                resultBox.classList.add(success ? 'text-emerald-700' : 'text-red-700');
                resultBox.textContent = message;
            }

            async function submitAttendanceToken(rawToken) {
                const token = String(rawToken || '').trim();
                if (!token || isSubmitting) {
                    return;
                }

                isSubmitting = true;
                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            qr_token: token,
                        }),
                    });

                    const payload = await response.json().catch(function () {
                        return {};
                    });

                    if (!response.ok || !payload.success) {
                        showResult(false, payload.message || 'Absensi gagal diproses.');
                        return;
                    }

                    const eventName = payload.data?.event ? (' Event: ' + payload.data.event + '.') : '';
                    showResult(true, (payload.message || 'Absensi berhasil.') + eventName);
                } catch (error) {
                    showResult(false, 'Terjadi gangguan saat mengirim absensi.');
                } finally {
                    isSubmitting = false;
                }
            }

            async function startScanning() {
                if (isScanning) {
                    return;
                }

                if (typeof Html5Qrcode === 'undefined') {
                    cameraStatus.textContent = 'Library scanner tidak tersedia.';
                    showResult(false, 'Scanner tidak dapat dimuat.');
                    return;
                }

                scanner = new Html5Qrcode(readerId);
                cameraStatus.textContent = 'Mencari kamera...';

                try {
                    const devices = await Html5Qrcode.getCameras();
                    if (!devices || devices.length === 0) {
                        cameraStatus.textContent = 'Kamera tidak ditemukan.';
                        showResult(false, 'Perangkat kamera tidak ditemukan.');
                        return;
                    }

                    const selectedCamera = devices[0].id;
                    await scanner.start(
                        selectedCamera,
                        { fps: 10, qrbox: { width: 240, height: 240 } },
                        async function onScanSuccess(decodedText) {
                            const now = Date.now();
                            if (decodedText === lastToken && (now - lastScanAt) < 3000) {
                                return;
                            }

                            lastToken = decodedText;
                            lastScanAt = now;
                            await submitAttendanceToken(decodedText);
                        },
                        function () {
                            // Ignore scan errors per frame.
                        }
                    );

                    isScanning = true;
                    cameraStatus.textContent = 'Kamera aktif. Arahkan QR code ke frame.';
                } catch (error) {
                    cameraStatus.textContent = 'Izin kamera ditolak atau kamera tidak dapat diakses.';
                    showResult(false, 'Gagal mengakses kamera.');
                }
            }

            async function stopScanning() {
                if (!scanner || !isScanning) {
                    return;
                }

                try {
                    await scanner.stop();
                    await scanner.clear();
                } catch (error) {
                    // no-op
                } finally {
                    isScanning = false;
                    cameraStatus.textContent = 'Scanner berhenti.';
                }
            }

            startBtn.addEventListener('click', function () {
                startScanning();
            });

            stopBtn.addEventListener('click', function () {
                stopScanning();
            });

            window.addEventListener('beforeunload', function () {
                stopScanning();
            });
        })();
    </script>
@endpush
