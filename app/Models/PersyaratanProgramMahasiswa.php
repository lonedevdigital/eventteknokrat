<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersyaratanProgramMahasiswa extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function persyaratan_program()
    {
        return $this->belongsTo(PersyaratanProgram::class, 'persyaratan_program_id', "id");
    }
}
