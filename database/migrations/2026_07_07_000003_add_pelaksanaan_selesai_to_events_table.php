<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->date('tanggal_pelaksanaan_selesai')->nullable()->after('tanggal_pelaksanaan');
            $table->time('waktu_pelaksanaan_selesai')->nullable()->after('waktu_pelaksanaan');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['tanggal_pelaksanaan_selesai', 'waktu_pelaksanaan_selesai']);
        });
    }
};
