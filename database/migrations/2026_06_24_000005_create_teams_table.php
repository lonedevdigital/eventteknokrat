<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            // Tim dibuat untuk sebuah lomba (event bertipe lomba)
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            // Ketua tim (mahasiswa pembuat tim)
            $table->unsignedBigInteger('ketua_user_id');
            $table->foreign('ketua_user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->string('nama_tim');
            // Cabang lomba yang diikuti (opsional, untuk lomba berbayar/bercabang)
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('event_competition_branches')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
