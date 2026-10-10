<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lomba_juaras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('event_competition_branches')->cascadeOnDelete();
            $table->foreignId('event_registration_id')->constrained('event_registrations')->cascadeOnDelete();
            $table->string('juara_label');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lomba_juaras');
    }
};
