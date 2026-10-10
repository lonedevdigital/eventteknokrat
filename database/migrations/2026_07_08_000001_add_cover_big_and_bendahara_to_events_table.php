<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('cover_big')->nullable()->after('flyer');
            $table->unsignedBigInteger('bendahara_user_id')->nullable()->after('ketua_pelaksana_user_id');
            $table->foreign('bendahara_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['bendahara_user_id']);
            $table->dropColumn(['cover_big', 'bendahara_user_id']);
        });
    }
};
