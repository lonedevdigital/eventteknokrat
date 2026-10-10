<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_external')->default(false)->after('type');
            $table->string('npm')->nullable()->after('is_external');
            $table->string('asal_instansi')->nullable()->after('npm');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_external', 'npm', 'asal_instansi']);
        });
    }
};
