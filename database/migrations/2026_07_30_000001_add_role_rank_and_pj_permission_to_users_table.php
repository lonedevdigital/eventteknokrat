<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fase 0 dari rencana Level/Permission/Assignment role:
     * - role_rank: representasi numerik Level (1=Administrator, 2=Baak/Kemahasiswaan,
     *   3=Dosen/Mahasiswa) supaya middleware bisa cascading, bukan exact-match.
     * - boleh_jadi_penanggung_jawab: permission terpisah dari role, khusus dicek
     *   sebelum user Level 3 boleh di-assign jadi Penanggung Jawab event/lomba.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('role_rank')->nullable()->after('role');
            $table->boolean('boleh_jadi_penanggung_jawab')->default(false)->after('type');
        });

        // Backfill rank dari role yang sudah ada. Role assignment lama
        // (penanggung_jawab/ketua_pelaksana/panitia/bendahara) hari ini cuma
        // pernah diisi dosen/mahasiswa (lihat kolom `type`), jadi masuk rank 3.
        DB::table('users')->where('role', 'superuser')->update(['role_rank' => 1]);
        DB::table('users')->whereIn('role', ['baak', 'kemahasiswaan'])->update(['role_rank' => 2]);
        DB::table('users')->whereIn('role', [
            'mahasiswa', 'penanggung_jawab', 'ketua_pelaksana', 'panitia', 'bendahara',
        ])->update(['role_rank' => 3]);

        // Penanggung Jawab existing otomatis dianggap sudah "layak" —
        // supaya tidak kehilangan hak assign begitu Fase 3 mulai memfilter
        // berdasarkan flag ini alih-alih kolom role.
        DB::table('users')->where('role', 'penanggung_jawab')->update([
            'boleh_jadi_penanggung_jawab' => true,
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role_rank', 'boleh_jadi_penanggung_jawab']);
        });
    }
};
