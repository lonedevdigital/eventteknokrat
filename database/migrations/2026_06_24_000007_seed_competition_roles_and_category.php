<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // === 2 ROLE BARU ===
        $roles = [
            [
                'name'        => 'ketua_pelaksana',
                'label'       => 'Ketua Pelaksana',
                'permissions' => json_encode(['data_lomba', 'manajemen_panitia', 'sertifikat']),
            ],
            [
                'name'        => 'panitia',
                'label'       => 'Panitia',
                'permissions' => json_encode(['data_lomba']),
            ],
        ];

        foreach ($roles as $role) {
            $exists = DB::table('roles')->where('name', $role['name'])->exists();
            if (! $exists) {
                DB::table('roles')->insert(array_merge($role, [
                    'is_system'  => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }

        // Penanggung Jawab boleh kelola lomba & panitia
        $pj = DB::table('roles')->where('name', 'penanggung_jawab')->first();
        if ($pj) {
            $perms = json_decode($pj->permissions ?? '[]', true) ?: [];
            foreach (['data_lomba', 'manajemen_panitia'] as $p) {
                if (! in_array($p, $perms, true)) {
                    $perms[] = $p;
                }
            }
            DB::table('roles')->where('name', 'penanggung_jawab')->update([
                'permissions' => json_encode(array_values($perms)),
                'updated_at'  => $now,
            ]);
        }

        // === Pastikan kategori "Lomba" ada (pusat perlombaan) ===
        $hasLomba = DB::table('event_categories')->whereRaw('LOWER(nama_kategori) = ?', ['lomba'])->exists();
        if (! $hasLomba) {
            DB::table('event_categories')->insert([
                'nama_kategori' => 'Lomba',
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('roles')->whereIn('name', ['ketua_pelaksana', 'panitia'])->delete();
    }
};
