<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('event_id');
            $table->foreign('branch_id')->references('id')->on('event_competition_branches')->nullOnDelete();
            $table->string('tipe_pendaftaran', 20)->nullable()->after('branch_id');
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['branch_id', 'tipe_pendaftaran']);
        });
    }
};
