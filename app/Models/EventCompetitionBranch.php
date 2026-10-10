<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCompetitionBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'pj_user_id',
        'nama_cabang',
        'cover_image',
        'deskripsi',
        'tipe_bayar',
        'harga_individual',
        'harga_tim',
        'maks_anggota_tim',
        'juknis_file',
        'contact_person_nama',
        'contact_person_telepon',
        'harga_pendaftaran',
    ];

    protected $casts = [
        'harga_pendaftaran' => 'integer',
        'harga_individual'  => 'integer',
        'harga_tim'         => 'integer',
        'maks_anggota_tim'  => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'pj_user_id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'branch_id');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'branch_id');
    }

    public function assessmentCriteria()
    {
        return $this->hasMany(AssessmentCriterion::class, 'branch_id')->orderBy('id');
    }

    public function isBerbayar(): bool
    {
        return $this->tipe_bayar === 'berbayar';
    }

    // Harga efektif per tim, fallback ke kolom lama untuk cabang yang dibuat sebelum split harga.
    public function getHargaTimEfektifAttribute()
    {
        return $this->harga_tim ?? $this->harga_pendaftaran ?? 0;
    }

    // Harga efektif per individu, fallback ke kolom lama untuk cabang yang dibuat sebelum split harga.
    public function getHargaIndividualEfektifAttribute()
    {
        return $this->harga_individual ?? $this->harga_pendaftaran ?? 0;
    }
}
