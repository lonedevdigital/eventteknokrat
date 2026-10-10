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
        Schema::table('events', function (Blueprint $table) {
            $table->string('wa_group_link')->nullable()->after('informasi_lainnya');
        });

        Schema::table('event_competition_branches', function (Blueprint $table) {
            $table->string('wa_group_link')->nullable()->after('contact_person_telepon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events_and_branches', function (Blueprint $table) {
            //
        });
    }
};
