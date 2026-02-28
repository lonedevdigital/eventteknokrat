<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique()->nullable(false);
            $table->string('email')->unique();
            $table->string('no_telepon')->nullable();
            
            // type = dosen, mahasiswa
            $table->string('type')->nullable();
            
            // Role: admin, mahasiswa, panitia, dll
            $table->string('role')->default('mahasiswa');
            
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
