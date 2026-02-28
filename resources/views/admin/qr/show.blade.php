@extends('templateAdminLTE.home')

@section('content')
    <div class="container-fluid">
        <h3 class="mb-3">QR Presensi - {{ $event->nama_event }}</h3>

        <div class="row">
            <div class="col-lg-7 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <iframe
                            id="qrFrame"
                            src="{{ $qrLink }}"
                            title="QR Absensi"
                            style="width: 100%; min-height: 560px; border: 0; border-radius: 8px; background: #fff;"
                        ></iframe>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="font-weight-bold mb-1">Link QR Aktif</label>
                            <input
                                id="qrLinkInput"
                                type="text"
                                class="form-control"
                                value="{{ $qrLink }}"
                                readonly
                            >
                        </div>

                        <div class="mb-3">
                            <span class="badge badge-warning p-2">
                                Regenerate otomatis setiap {{ $lifetimeMinutes }} menit
                            </span>
                        </div>

                        <div class="mb-2">
                            <span class="font-weight-bold">Berlaku sampai:</span>
                            <span id="expiresAtText">{{ $expires?->format('d M Y H:i:s') }} WIB</span>
                        </div>
                        <div class="mb-3">
                            <span class="font-weight-bold">Sisa waktu:</span>
                            <span id="countdownText">--:--</span>
                        </div>

                        <div class="d-flex flex-wrap">
                            <a id="openQrBtn" href="{{ $qrLink }}" target="_blank" class="btn btn-primary mr-2 mb-2">
                                Buka QR
                            </a>
                            <button id="manualRefreshBtn" type="button" class="btn btn-success mb-2">
                                Regenerate Sekarang
                            </button>
                        </div>

                        <small id="qrStatusText" class="text-muted d-block mt-2"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const refreshEndpoint = @json($refreshEndpoint);
            const csrfToken = @json(csrf_token());

            const qrFrame = document.getElementById('qrFrame');
            const qrLinkInput = document.getElementById('qrLinkInput');
            const openQrBtn = document.getElementById('openQrBtn');
            const expiresAtText = document.getElementById('expiresAtText');
            const countdownText = document.getElementById('countdownText');
            const statusText = document.getElementById('qrStatusText');
            const manualRefreshBtn = document.getElementById('manualRefreshBtn');

            let expiresAtMs = Date.parse(@json($expires?->toIso8601String()));
            let refreshTimerId = null;
            let isRefreshing = false;

            function formatCountdown(totalSeconds) {
                const safeSeconds = Math.max(0, totalSeconds);
                const minutes = Math.floor(safeSeconds / 60);
                const seconds = safeSeconds % 60;
                return String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            }

            function renderCountdown() {
                if (!expiresAtMs || Number.isNaN(expiresAtMs)) {
                    countdownText.textContent = '--:--';
                    return;
                }

                const remainingMs = expiresAtMs - Date.now();
                const remainingSeconds = Math.ceil(remainingMs / 1000);
                countdownText.textContent = formatCountdown(remainingSeconds);
            }

            function scheduleAutoRefresh() {
                if (refreshTimerId) {
                    clearTimeout(refreshTimerId);
                }

                if (!expiresAtMs || Number.isNaN(expiresAtMs)) {
                    return;
                }

                const delay = Math.max(0, expiresAtMs - Date.now());
                refreshTimerId = setTimeout(function () {
                    refreshQrToken(true);
                }, delay + 200);
            }

            function setRefreshingState(active) {
                isRefreshing = active;
                manualRefreshBtn.disabled = active;
                manualRefreshBtn.textContent = active ? 'Meregenerate...' : 'Regenerate Sekarang';
            }

            async function refreshQrToken(isAuto) {
                if (isRefreshing) {
                    return;
                }

                setRefreshingState(true);
                statusText.textContent = isAuto ? 'Regenerate otomatis...' : 'Regenerate manual...';

                try {
                    const response = await fetch(refreshEndpoint, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });

                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }

                    const result = await response.json();
                    if (!result || result.success !== true) {
                        throw new Error('Payload tidak valid');
                    }

                    qrLinkInput.value = result.qr_link;
                    openQrBtn.href = result.qr_link;
                    qrFrame.src = result.qr_link + '?v=' + Date.now();
                    expiresAtText.textContent = (result.expires_at_human || '-') + ' WIB';
                    expiresAtMs = Date.parse(result.expires_at_iso);

                    renderCountdown();
                    scheduleAutoRefresh();
                    statusText.textContent = 'QR berhasil diregenerate.';
                } catch (error) {
                    statusText.textContent = 'Gagal regenerate QR. Coba lagi.';
                    refreshTimerId = setTimeout(function () {
                        refreshQrToken(true);
                    }, 10000);
                } finally {
                    setRefreshingState(false);
                }
            }

            manualRefreshBtn.addEventListener('click', function () {
                refreshQrToken(false);
            });

            setInterval(renderCountdown, 1000);
            renderCountdown();
            scheduleAutoRefresh();
        })();
    </script>
@endsection
