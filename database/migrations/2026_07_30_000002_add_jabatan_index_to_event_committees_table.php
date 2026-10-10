<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * event_committees.jabatan dipakai sebagai satu-satunya sumber kebenaran
     * untuk posisi Bendahara/Sekretaris (lihat User::isBendaharaLomba(),
     * Event::bendaharaCommittees()) tapi belum punya index — query filter by
     * jabatan per event jadi full scan tanpa ini.
     */
    public function up(): void
    {
        Schema::table('event_committees', function (Blueprint $table) {
            $table->index(['event_id', 'jabatan']);
        });
    }

    public function down(): void
    {
        Schema::table('event_committees', function (Blueprint $table) {
            $table->dropIndex(['event_id', 'jabatan']);
        });
    }
};
