<?php

namespace App\Constants;

class CommitteePermissions
{
    const MANAGE_ALL = 'all';
    
    // Keuangan
    const KELOLA_PEMBAYARAN = 'kelola_pembayaran';
    
    // Peserta & Pendaftaran
    const LIHAT_PESERTA = 'lihat_peserta';
    const KELOLA_BERKAS = 'kelola_berkas';
    
    // Lomba
    const INPUT_NILAI = 'input_nilai';
    const TETAPKAN_JUARA = 'tetapkan_juara';

    /**
     * Dapatkan semua list permissions untuk keperluan UI checkbox
     */
    public static function all(): array
    {
        return [
            self::MANAGE_ALL        => 'Akses Penuh (Ketua Pelaksana)',
            self::KELOLA_PEMBAYARAN => 'Kelola Pembayaran Lomba',
            self::LIHAT_PESERTA     => 'Lihat Data Peserta Lomba',
            self::KELOLA_BERKAS     => 'Review Berkas Pendaftaran',
            self::INPUT_NILAI       => 'Input & Kelola Nilai',
            self::TETAPKAN_JUARA    => 'Tetapkan Juara Lomba',
        ];
    }
}
