<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranProgram extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', "id");
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', "id");
    }

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id', "id");
    }

    public function persyaratan_pendaftaran()
    {
        return $this->hasMany(PersyaratanProgramMahasiswa::class, 'pendaftaran_program_id', "id");
    }

    public function history()
    {
        return $this->hasMany(HistoryPendaftaranProgram::class, 'pendaftaran_id', "id");
    }

    public function last_history()
    {
        return $this->hasOne(HistoryPendaftaranProgram::class, 'pendaftaran_id', 'id')->latestOfMany();
    }

    public function pendaftaran_perusahaan()
    {
        return $this->hasOne(PendaftaranPerusahaan::class, 'pendaftaran_id', 'id')->latestOfMany();
    }

    public function pembimbing_lapangan()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_lapangan_id', "id");
    }

    public function pembimbing_laporan()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_laporan_id', "id");
    }
}
