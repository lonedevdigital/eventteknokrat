<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('infos', function (Blueprint $table) {
            $table->id();

            $table->string('judul');
            $table->string('slug')->unique();

            // Isi info terkini (teks saja)
            $table->text('isi')->nullable();

            // Kontrol publish
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();

            // Penulis
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infos');
    }
};
