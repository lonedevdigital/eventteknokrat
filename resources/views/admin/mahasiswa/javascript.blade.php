<div id="tempat-modal"></div>

@push('js')

    {{-- KONFIRMASI SINKRONISASI --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const syncBtn = document.getElementById('sync-button');

            if (syncBtn) {
                syncBtn.addEventListener('click', function () {
                    const angkatan = syncBtn.getAttribute('data-angkatan') || '-';
                    const prodi = syncBtn.getAttribute('data-prodi') || '';
                    const scopeText = prodi
                        ? `angkatan ${angkatan} dan prodi ${prodi}`
                        : `angkatan ${angkatan}`;

                    Swal.fire({
                        title: 'Yakin ingin sinkronisasi?',
                        text: `Data akan diambil ulang dari API pusat untuk ${scopeText}.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, sinkronkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('sync-form').submit();
                        }
                    });
                });
            }

        });
    </script>

    {{-- KONFIRMASI + PROGRESS SINKRONISASI ALL (SELURUH PRODI & ANGKATAN) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const syncAllBtn = document.getElementById('sync-all-button');
            if (!syncAllBtn) {
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function renderProgress(text, doneCount, totalCount) {
                const pct = totalCount > 0 ? Math.round((doneCount / totalCount) * 100) : 0;
                Swal.update({
                    html: `
                        <div class="text-left mb-2" id="sync-all-progress-text">${text}</div>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: ${pct}%;">${pct}%</div>
                        </div>
                    `
                });
            }

            async function runSyncAll() {
                let tahunAwal = 2015;
                let tahunAkhir = new Date().getFullYear();

                try {
                    const rangeRes = await fetch('{{ route('data-mahasiswa.sync-all-range') }}', {
                        headers: { 'Accept': 'application/json' },
                    });
                    if (rangeRes.ok) {
                        const range = await rangeRes.json();
                        tahunAwal = range.from;
                        tahunAkhir = range.to;
                    }
                } catch (e) {
                    // Fallback ke rentang default di atas kalau gagal ambil range.
                }

                const tahunList = [];
                for (let y = tahunAwal; y <= tahunAkhir; y++) {
                    tahunList.push(y);
                }

                Swal.fire({
                    title: 'Sinkronisasi All sedang berjalan',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => renderProgress('Mempersiapkan sinkronisasi...', 0, tahunList.length),
                });

                let totalSynced = 0;
                let totalCreatedUser = 0;
                let totalLinked = 0;
                const tahunTanpaData = [];
                const tahunGagal = [];

                for (let i = 0; i < tahunList.length; i++) {
                    const tahun = tahunList[i];
                    renderProgress(`Mengambil data Prodi dari tahun ${tahun}... (${i + 1}/${tahunList.length})`, i, tahunList.length);

                    try {
                        const res = await fetch(`/master/mahasiswa/sync-all/${tahun}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                        });

                        if (!res.ok) {
                            tahunGagal.push(tahun);
                            continue;
                        }

                        const json = await res.json();

                        if (!json.has_data) {
                            tahunTanpaData.push(tahun);
                            continue;
                        }

                        totalSynced += json.synced;
                        totalCreatedUser += json.created_user;
                        totalLinked += json.linked;
                    } catch (e) {
                        tahunGagal.push(tahun);
                    }
                }

                renderProgress('Selesai.', tahunList.length, tahunList.length);

                let catatan = '';
                if (tahunTanpaData.length) {
                    catatan += ` Angkatan tanpa data: ${tahunTanpaData.join(', ')}.`;
                }
                if (tahunGagal.length) {
                    catatan += ` Angkatan gagal diproses: ${tahunGagal.join(', ')}.`;
                }

                Swal.fire({
                    title: 'Sinkronisasi All selesai',
                    html: `Diproses: ${totalSynced}. User baru: ${totalCreatedUser}. Link user_id: ${totalLinked}.${catatan}`,
                    icon: 'success',
                    confirmButtonText: 'OK',
                }).then(() => {
                    window.location.reload();
                });
            }

            syncAllBtn.addEventListener('click', function () {
                Swal.fire({
                    title: 'Yakin ingin Sinkronisasi All?',
                    text: 'Seluruh data mahasiswa (semua program studi & semua angkatan) akan diambil ulang dari API pusat, satu per satu per angkatan. Proses ini bisa memakan waktu cukup lama.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, sinkronkan semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        runSyncAll();
                    }
                });
            });

        });
    </script>

    {{-- KONFIRMASI RESET PASSWORD (AMAN UNTUK TABEL DINAMIS) --}}
    <script>
        document.addEventListener('click', function (event) {

            // Pastikan yang diklik adalah tombol reset password
            if (event.target.closest('.reset-password-btn')) {

                const button = event.target.closest('.reset-password-btn');

                Swal.fire({
                    title: 'Reset Password?',
                    text: "Password akan dikembalikan ke NPM.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            }

        });
    </script>

@endpush
