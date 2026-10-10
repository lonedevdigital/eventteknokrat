<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentCriterion extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'branch_id',
        'nama_kriteria',
        'bobot',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function branch()
    {
        return $this->belongsTo(EventCompetitionBranch::class, 'branch_id');
    }
}
