<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranPerusahaan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id', "id");
    }

    public function pendaftaran_program()
    {
        return $this->belongsTo(PendaftaranProgram::class, 'pendaftaran_id', "id");
    }
}
