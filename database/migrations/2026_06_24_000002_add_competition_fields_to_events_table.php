<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Jenis: event biasa atau perlombaan
            $table->string('type', 20)->default('event')->after('id')->index();

            // === Field khusus LOMBA ===
            // Flyer lomba (events biasa pakai kolom thumbnail)
            $table->string('flyer')->nullable()->after('thumbnail');

            // Skala partisipasi: 'prodi' (terbatas 1 prodi) atau 'universitas' (seluruh UTI)
            $table->string('partisipasi_skala', 20)->nullable()->after('deskripsi');
            // Nama program studi target (jika skala = prodi)
            $table->string('partisipasi_prodi')->nullable()->after('partisipasi_skala');

            // Tipe lomba: 'gratis' atau 'berbayar'
            $table->string('tipe_bayar', 20)->nullable()->after('partisipasi_prodi');

            // Informasi kontak panitia (array of {jabatan, nama, kontak})
            $table->json('kontak')->nullable()->after('tipe_bayar');

            // Ketua Pelaksana yang diberi akses kelola oleh Penanggung Jawab
            $table->unsignedBigInteger('ketua_pelaksana_user_id')->nullable()->after('created_by_user_id');
            $table->foreign('ketua_pelaksana_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['ketua_pelaksana_user_id']);
            $table->dropColumn([
                'type',
                'flyer',
                'partisipasi_skala',
                'partisipasi_prodi',
                'tipe_bayar',
                'kontak',
                'ketua_pelaksana_user_id',
            ]);
        });
    }
};
