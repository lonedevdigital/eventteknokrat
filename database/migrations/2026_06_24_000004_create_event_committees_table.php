<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_committees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();

            // Mahasiswa yang di-assign (opsional, bisa diisi manual via nama)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->unsignedBigInteger('mahasiswa_id')->nullable();
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswas')->nullOnDelete();

            // Snapshot identitas (agar tetap terbaca walau relasi berubah)
            $table->string('nama')->nullable();
            $table->string('npm')->nullable();

            // Jabatan panitia: Sekretaris, Bendahara, Panitia Acara, dll.
            $table->string('jabatan');
            // true jika jenis jabatan ditambahkan custom oleh Ketua Pelaksana
            $table->boolean('is_custom')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_committees');
    }
};
