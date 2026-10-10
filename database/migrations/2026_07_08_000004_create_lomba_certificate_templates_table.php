<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lomba_certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('jenis', 20); // panitia | peserta
            $table->string('name')->default('Template');
            $table->unsignedInteger('canvas_width')->default(2000);
            $table->unsignedInteger('canvas_height')->default(1414);
            $table->longText('template_json')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'jenis'], 'lomba_cert_tpl_event_jenis_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lomba_certificate_templates');
    }
};
