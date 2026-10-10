<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_document_requirements', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('event_id')
                ->constrained('event_competition_branches')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('event_document_requirements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
    }
};
