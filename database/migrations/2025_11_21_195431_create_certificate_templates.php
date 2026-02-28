<?php

// database/migrations/xxxx_xx_xx_create_certificate_templates.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            
            $table->string('role', 50)->default('default')->index();
            $table->string('role_key')->default('default'); // contoh: peserta, panitia, pemateri, baak, etc
            $table->string('name')->default('Template');

            $table->unsignedInteger('canvas_width')->default(2000);
            $table->unsignedInteger('canvas_height')->default(1414);

            $table->string('background_path')->nullable();  // simpan background per role
            $table->longText('template_json')->nullable();  // fabric canvas json

            $table->timestamps();

            // 1 event hanya boleh punya 1 template per role_key dan role
            $table->unique(['event_id', 'role_key']);
            $table->unique(['event_id', 'role'], 'cert_tpl_event_role_unique');
        });
    }

    public function down(): void {
        Schema::dropIfExists('certificate_templates');
    }
};
