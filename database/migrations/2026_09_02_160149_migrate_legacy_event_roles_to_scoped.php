<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan kolom permissions di event_committees
        Schema::table('event_committees', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('jabatan');
        });

        // 2. Update role user dari ketua_pelaksana, panitia, bendahara, penanggung_jawab menjadi mahasiswa / dosen
        $legacyRoles = ['ketua_pelaksana', 'panitia', 'bendahara', 'penanggung_jawab'];

        // Jika mereka adalah penanggung_jawab, pastikan flag boleh_jadi_penanggung_jawab diset
        DB::table('users')->where('role', 'penanggung_jawab')->update([
            'boleh_jadi_penanggung_jawab' => true,
        ]);

        // Kembalikan role ke mahasiswa
        // Jika type == dosen, seharusnya role = dosen, tapi default 'mahasiswa'
        DB::table('users')->whereIn('role', $legacyRoles)->update([
            'role' => DB::raw("IF(type = 'dosen', 'dosen', 'mahasiswa')"),
            'role_rank' => 3 // RANK_BASE
        ]);

        // 3. Hapus role tersebut dari tabel roles
        DB::table('roles')->whereIn('name', $legacyRoles)->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scoped', function (Blueprint $table) {
            //
        });
    }
};
