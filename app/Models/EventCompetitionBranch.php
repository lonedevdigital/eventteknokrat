<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCompetitionBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'nama_cabang',
        'harga_pendaftaran',
    ];

    protected $casts = [
        'harga_pendaftaran' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'branch_id');
    }
}
