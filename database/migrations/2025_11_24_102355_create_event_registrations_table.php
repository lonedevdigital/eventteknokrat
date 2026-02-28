<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();

            // Event yang didaftarkan
            $table->foreignId('event_id')
                ->constrained('events')
                ->onDelete('cascade');

            // User yang mendaftar (pengganti mahasiswa_id)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Waktu pendaftaran
            $table->timestamp('registered_at')->useCurrent();

            // Status registrasi
            // registered = hanya daftar
            // attended = sudah presensi
            $table->enum('status', ['registered', 'attended'])
                ->default('registered');
                
            // Waktu presensi event
            $table->timestamp('attendance_at')->nullable();

            $table->timestamps();

            // Cegah user daftar dua kali event yang sama
            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
