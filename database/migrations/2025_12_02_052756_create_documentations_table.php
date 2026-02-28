<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentations', function (Blueprint $table) {
            $table->id();

            // File dokumentasi bisa banyak per event
            $table->foreignId('event_id')
                ->constrained('events')
                ->onDelete('cascade');

            // Lokasi file
            $table->string('file_path');

            // (Optional) type file: image / video / pdf
            $table->string('file_type')->nullable();

            // (Optional) caption/keterangan
            $table->string('caption')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentations');
    }
};
