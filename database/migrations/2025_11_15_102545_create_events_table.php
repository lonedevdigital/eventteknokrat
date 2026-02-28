<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            
            // role pemilik event: baak | kemahasiswaan
            $table->string('owner_role', 30)->nullable()->index();
            
            // siapa yang membuat event (optional tapi bagus buat audit)
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->foreign('created_by_user_id')->references('id')->on('users')->nullOnDelete();

            /**
             * Slug digunakan sebagai URL unik event,
             * BUKAN ID, dan akan di-generate otomatis melalui Model Event.
             */
            $table->string('slug')->unique()->index();

            // Thumbnail event (path/URL gambar)
            $table->string('thumbnail')->nullable();

            // Nama event
            $table->string('nama_event');

            /**
             * Kategori event
             * Terkait dengan tabel event_categories
             */
            $table->foreignId('event_category_id')
                ->nullable()
                ->constrained('event_categories')
                ->nullOnDelete();

            // Tempat dan waktu pelaksanaan
            $table->string('tempat_pelaksanaan');
            $table->time('waktu_pelaksanaan')->nullable();

            // Tanggal-tanggal penting
            $table->date('tanggal_event')->nullable();
            $table->date('tanggal_pendaftaran')->nullable();
            $table->date('tanggal_pelaksanaan')->nullable();

            // Deskripsi dan informasi tambahan
            $table->text('deskripsi')->nullable();
            $table->text('informasi_lainnya')->nullable();

            /**
             * Token QR → untuk presensi QR
             * Dibuat unik
             */
            $table->string('qr_token')->nullable()->unique();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
