@if (!empty($prodiId) && !empty($angkatan))
    {{-- TAMPILAN JIKA DATA ADA --}}
    <div class="table-responsive">
        {{-- Hapus 'table-bordered', tambahkan style header manual agar rapi --}}
        <table id="dataTable" class="table table-hover table-striped mb-0" style="border-top: 1px solid #dee2e6;">
            <thead style="background-color: #f8fafc;">
            <tr>
                <th class="text-center align-middle py-3 text-secondary text-uppercase font-weight-bold small" width="5%" style="border-bottom: 2px solid #e2e8f0;">No</th>
                <th class="align-middle py-3 text-secondary text-uppercase font-weight-bold small" style="border-bottom: 2px solid #e2e8f0;">NPM</th>
                <th class="align-middle py-3 text-secondary text-uppercase font-weight-bold small" style="border-bottom: 2px solid #e2e8f0;">Nama Mahasiswa</th>
                <th class="align-middle py-3 text-secondary text-uppercase font-weight-bold small" style="border-bottom: 2px solid #e2e8f0;">Program Studi</th>
                <th class="text-center align-middle py-3 text-secondary text-uppercase font-weight-bold small" width="10%" style="border-bottom: 2px solid #e2e8f0;">Aksi</th>
            </tr>
            </thead>

            <tbody>
            @forelse ($dataMahasiswa as $mhs)
                <tr>
                    <td class="text-center align-middle">{{ $loop->iteration }}</td>

                    <td class="align-middle font-weight-bold text-dark">{{ $mhs->npm_mahasiswa }}</td>
                    <td class="align-middle">{{ $mhs->nama_mahasiswa }}</td>
                    <td class="align-middle">
                        <span class="badge badge-light border" style="font-weight: 500;">
                            {{ $mhs->nama_program_studi }}
                        </span>
                    </td>

                    {{-- Reset Password --}}
                    <td class="text-center align-middle">
                        <form method="POST" action="{{ route('data-mahasiswa.update', $mhs->user_id) }}"
                              class="reset-password-form d-inline-block">
                            @csrf
                            @method('PATCH')

                            {{-- Tombol Reset Flat (Kuning/Oranye) --}}
                            <button type="button" class="btn btn-warning btn-xs reset-password-btn font-weight-bold text-white shadow-none"
                                    title="Reset Password"
                                    style="background-color: #f39c12; border: none; border-radius: 0;">
                                <i class="fa fa-key mr-1"></i> Reset
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="py-4">
                            {{-- Ikon Hijau Pudar --}}
                            <i class="fas fa-folder-open fa-3x mb-3" style="color: #27ae60; opacity: 0.3;"></i>
                            <p class="text-muted font-weight-bold mb-0">Data mahasiswa tidak ditemukan untuk filter ini.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@else
    {{-- TAMPILAN JIKA FILTER BELUM DIPILIH --}}
    <div class="text-center py-5 bg-white">
        <div class="py-5">
            {{-- Ikon Hijau Pudar --}}
            <i class="fas fa-search fa-4x mb-3" style="color: #27ae60; opacity: 0.2;"></i>
            <h5 class="text-dark font-weight-bold">Filter Data Belum Dipilih</h5>
            <p class="text-muted mb-0">Silakan pilih <strong style="color: #27ae60;">Angkatan</strong> dan <strong style="color: #27ae60;">Program Studi</strong> di atas untuk menampilkan data.</p>
        </div>
    </div>
@endif
