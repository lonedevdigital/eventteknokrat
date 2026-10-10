<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->string('nama')->nullable()->change();
            $table->string('npm')->nullable()->change();
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->string('invited_email')->nullable()->after('team_id');
            $table->string('status')->default('confirmed')->after('user_id');
            $table->foreignId('institution_id')->nullable()->after('prodi')->constrained('institutions')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
        });

        // Anggota lama (dibuat sebelum sistem undangan) dianggap sudah confirmed sejak awal.
        DB::table('team_members')->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        Schema::table('team_members', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
            $table->dropColumn(['invited_email', 'status', 'confirmed_at']);
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->string('nama')->nullable(false)->change();
            $table->string('npm')->nullable(false)->change();
        });
    }
};
