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
        Schema::table('event_document_requirements', function (Blueprint $table) {
            $table->json('format_diizinkan')->nullable()->after('jenis_file');
            $table->boolean('is_required')->default(true)->after('format_diizinkan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_document_requirements', function (Blueprint $table) {
            $table->dropColumn(['format_diizinkan', 'is_required']);
        });
    }
};
