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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();

            // Relasi ke event
            $table->foreignId('event_id')
                ->constrained('events')
                ->onDelete('cascade');
                
            // Template yang digunakan
            $table->foreignId('certificate_template_id')
                ->nullable()
                ->constrained('certificate_templates')
                ->nullOnDelete();

            // Relasi ke mahasiswa
            $table->foreignId('mahasiswa_id')
                ->constrained('mahasiswas')
                ->onDelete('cascade');

            // Lokasi file PDF di storage
            $table->string('file_path');

            // (Opsional) Nama file untuk ditampilkan
            $table->string('file_name')->nullable();

            // Tipe file (default PDF)
            $table->string('mime_type')->default('application/pdf');

            // Opsional nomor sertifikat
            $table->string('certificate_number')->nullable();

            // Tanggal terbit
            $table->timestamp('issued_at')->nullable();
            
            // Tanggal event (disimpan biar sertifikat konsisten walau event diedit)
            $table->date('event_date')->nullable();

            // Status sertifikat
            $table->enum('status', ['generated', 'downloaded'])
                ->default('generated');

            $table->timestamps();

            // Unique per event untuk mahasiswa
            $table->unique(['event_id', 'mahasiswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
