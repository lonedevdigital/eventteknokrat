<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'label', 'permissions', 'is_system'];

    protected $casts = [
        'permissions' => 'array',
        'is_system'   => 'boolean',
    ];

    const PERMISSION_GROUPS = [
        'Akses Dashboard' => [
            'dashboard'       => 'Halaman Utama & Statistik',
            'analytics_event' => 'Analytics & Laporan',
        ],
        'Manajemen User' => [
            'manajemen_user'  => 'Kelola Pengguna',
            'manajemen_role'  => 'Kelola Role & Permissions',
        ],
        'Data Master' => [
            'data_mahasiswa'  => 'Data Mahasiswa',
            'kategori_event'  => 'Kategori Event',
            'info_terkini'    => 'Info Terkini',
            'sponsor'         => 'Sponsor & Partner',
        ],
        'Event & Kegiatan' => [
            'data_event'        => 'Data Event (CRUD)',
            'rekomendasi_event' => 'Rekomendasi Event',
            'sertifikat'        => 'Sertifikat & Template',
        ],
        'Lomba & Panitia' => [
            'data_lomba'        => 'Kelola Lomba',
            'manajemen_panitia' => 'Kelola Panitia',
        ],
    ];

    public static function allPermissions(): array
    {
        $flat = [];
        foreach (self::PERMISSION_GROUPS as $permissions) {
            foreach ($permissions as $key => $label) {
                $flat[$key] = $label;
            }
        }
        return $flat;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? [], true);
    }
}
