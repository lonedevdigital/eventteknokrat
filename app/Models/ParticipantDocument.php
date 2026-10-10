<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_document_requirement_id',
        'event_registration_id',
        'file_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'uploaded_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'uploaded_at' => 'datetime',
    ];

    public function requirement()
    {
        return $this->belongsTo(EventDocumentRequirement::class, 'event_document_requirement_id');
    }

    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', 'approved');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
