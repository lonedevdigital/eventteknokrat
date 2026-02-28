<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('npm_mahasiswa');
            $table->string('nama_mahasiswa')->nullable();
            $table->unsignedBigInteger('kode_program_studi')->nullable();
            $table->string('nama_program_studi')->nullable();
            $table->string('kode_fakultas')->nullable();
            $table->string('nama_fakultas')->nullable();
            $table->string('nama_program_studi_english')->nullable();
            $table->string('nama_fakultas_english')->nullable();
            $table->string('angkatan', 4)->nullable();
            $table->string('no_telepon')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('sks')->nullable();
            $table->string('ipk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
