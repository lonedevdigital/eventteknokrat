<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LombaJuara extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'branch_id',
        'event_registration_id',
        'juara_label',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function branch()
    {
        return $this->belongsTo(EventCompetitionBranch::class, 'branch_id');
    }

    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }
}
