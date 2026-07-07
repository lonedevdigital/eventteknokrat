<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // === Detail tambahan khusus LOMBA ===
            $table->text('syarat_ketentuan')->nullable()->after('kontak');
            $table->text('jadwal_timeline')->nullable()->after('syarat_ketentuan');
            $table->text('hadiah_penghargaan')->nullable()->after('jadwal_timeline');
            $table->text('cara_pendaftaran')->nullable()->after('hadiah_penghargaan');

            // Juknis (Petunjuk Teknis) berupa file PDF
            $table->string('juknis_file')->nullable()->after('cara_pendaftaran');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'syarat_ketentuan',
                'jadwal_timeline',
                'hadiah_penghargaan',
                'cara_pendaftaran',
                'juknis_file',
            ]);
        });
    }
};
