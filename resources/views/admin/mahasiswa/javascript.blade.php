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
