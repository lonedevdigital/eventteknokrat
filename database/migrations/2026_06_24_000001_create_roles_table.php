<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();       // slug: baak, superuser, dll
            $table->string('label');                // Nama tampil: BAAK, Superuser
            $table->json('permissions')->nullable(); // array slug permission
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        $now = now();
        DB::table('roles')->insert([
            [
                'name'        => 'superuser',
                'label'       => 'Superuser',
                'permissions' => json_encode([
                    'dashboard', 'analytics_event',
                    'manajemen_user', 'manajemen_role',
                    'data_mahasiswa', 'kategori_event', 'info_terkini', 'sponsor',
                    'data_event', 'rekomendasi_event', 'sertifikat',
                ]),
                'is_system'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'baak',
                'label'       => 'BAAK',
                'permissions' => json_encode([
                    'dashboard', 'analytics_event',
                    'manajemen_user',
                    'data_mahasiswa', 'kategori_event', 'info_terkini', 'sponsor',
                    'data_event', 'rekomendasi_event', 'sertifikat',
                ]),
                'is_system'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'kemahasiswaan',
                'label'       => 'Kemahasiswaan',
                'permissions' => json_encode([
                    'dashboard', 'analytics_event',
                    'manajemen_user',
                    'data_mahasiswa', 'kategori_event', 'info_terkini', 'sponsor',
                    'data_event', 'rekomendasi_event', 'sertifikat',
                ]),
                'is_system'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'penanggung_jawab',
                'label'       => 'Penanggung Jawab',
                'permissions' => json_encode(['data_event', 'sertifikat']),
                'is_system'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'mahasiswa',
                'label'       => 'Mahasiswa',
                'permissions' => json_encode([]),
                'is_system'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
