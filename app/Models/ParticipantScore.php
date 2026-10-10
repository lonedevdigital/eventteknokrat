<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_criterion_id',
        'event_registration_id',
        'nilai',
        'scored_by',
    ];

    public function criterion()
    {
        return $this->belongsTo(AssessmentCriterion::class, 'assessment_criterion_id');
    }

    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }

    public function scorer()
    {
        return $this->belongsTo(User::class, 'scored_by');
    }
}
