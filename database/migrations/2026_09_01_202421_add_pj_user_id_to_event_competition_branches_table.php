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
        Schema::table('event_competition_branches', function (Blueprint $table) {
            $table->unsignedBigInteger('pj_user_id')->nullable()->after('event_id');
            $table->foreign('pj_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_competition_branches', function (Blueprint $table) {
            $table->dropForeign(['pj_user_id']);
            $table->dropColumn('pj_user_id');
        });
    }
};
