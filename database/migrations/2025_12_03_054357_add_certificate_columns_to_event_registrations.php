<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('certificate_url')->nullable()->after('attendance_at');
            $table->timestamp('certificate_uploaded_at')->nullable()->after('certificate_url');
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn(['certificate_url', 'certificate_uploaded_at']);
        });
    }
};
