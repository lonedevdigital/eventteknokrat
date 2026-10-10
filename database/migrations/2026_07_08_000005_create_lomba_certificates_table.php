<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lomba_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->unsignedBigInteger('lomba_certificate_template_id')->nullable();
            $table->foreign('lomba_certificate_template_id', 'lomba_certs_template_fk')
                ->references('id')->on('lomba_certificate_templates')->nullOnDelete();
            $table->string('jenis', 20); // panitia | peserta
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('nama');
            $table->string('npm')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('event_competition_branches')->nullOnDelete();
            $table->string('file_path');
            $table->string('certificate_number')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lomba_certificates');
    }
};
