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
        Schema::create('participant_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_criterion_id')->constrained('assessment_criteria')->cascadeOnDelete();
            $table->foreignId('event_registration_id')->constrained('event_registrations')->cascadeOnDelete();
            $table->decimal('nilai', 5, 2)->default(0);
            $table->foreignId('scored_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Seorang peserta/tim hanya memiliki satu nilai untuk satu kriteria tertentu
            $table->unique(['assessment_criterion_id', 'event_registration_id'], 'participant_scores_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_scores');
    }
};
