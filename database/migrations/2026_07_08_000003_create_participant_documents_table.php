<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participant_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_document_requirement_id')->constrained('event_document_requirements')->cascadeOnDelete();
            $table->foreignId('event_registration_id')->constrained('event_registrations')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('status', 20)->default('pending'); // pending | approved | rejected
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            $table->unique(['event_document_requirement_id', 'event_registration_id'], 'participant_documents_req_reg_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participant_documents');
    }
};
