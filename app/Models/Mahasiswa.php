<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // <- WAJIB
use Laravel\Sanctum\HasApiTokens; // <- WAJIB

class Mahasiswa extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', "id");
    }

    public function persyaratan_program_mahasiswa()
    {
        return $this->hasMany(PersyaratanProgramMahasiswa::class, 'mahasiswa_id', "id");
    }
}
