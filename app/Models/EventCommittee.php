<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCommittee extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'mahasiswa_id',
        'nama',
        'npm',
        'jabatan',
        'is_custom',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
    ];

    /**
     * Daftar jabatan panitia bawaan.
     * Ketua Pelaksana dapat menambah jenis lain (is_custom = true).
     */
    const DEFAULT_JABATAN = [
        'Ketua Pelaksana',
        'Sekretaris Lomba',
        'Bendahara Lomba',
        'Panitia Acara',
        'Panitia Korlap',
        'Panitia Perlengkapan',
        'Panitia Pubdedok',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}
