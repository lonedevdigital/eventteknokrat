<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah index baru dulu SEBELUM drop yang lama — index lama masih dipakai untuk
        // menopang foreign key "event_id" (MySQL butuh index dengan kolom itu di posisi awal).
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->unique(['event_id', 'user_id', 'branch_id'], 'event_registrations_event_user_branch_unique');
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropUnique('event_registrations_event_id_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->unique(['event_id', 'user_id'], 'event_registrations_event_id_user_id_unique');
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropUnique('event_registrations_event_user_branch_unique');
        });
    }
};
