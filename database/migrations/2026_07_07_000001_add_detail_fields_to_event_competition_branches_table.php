<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_competition_branches', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('nama_cabang');
            $table->text('deskripsi')->nullable()->after('cover_image');
            $table->string('tipe_bayar', 20)->default('gratis')->after('deskripsi');
            $table->unsignedInteger('harga_individual')->nullable()->after('tipe_bayar');
            $table->unsignedInteger('harga_tim')->nullable()->after('harga_individual');
            $table->unsignedInteger('maks_anggota_tim')->default(1)->after('harga_tim');
            $table->string('juknis_file')->nullable()->after('maks_anggota_tim');
            $table->string('contact_person_nama')->nullable()->after('juknis_file');
            $table->string('contact_person_telepon')->nullable()->after('contact_person_nama');
        });
    }

    public function down(): void
    {
        Schema::table('event_competition_branches', function (Blueprint $table) {
            $table->dropColumn([
                'cover_image',
                'deskripsi',
                'tipe_bayar',
                'harga_individual',
                'harga_tim',
                'maks_anggota_tim',
                'juknis_file',
                'contact_person_nama',
                'contact_person_telepon',
            ]);
        });
    }
};
