<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventDocumentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'branch_id',
        'nama_dokumen',
        'jenis_file',
        'format_diizinkan',
        'is_required',
        'is_default',
        'urutan',
    ];

    protected $casts = [
        'format_diizinkan' => 'array',
        'is_required' => 'boolean',
        'is_default' => 'boolean',
        'urutan' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function branch()
    {
        return $this->belongsTo(EventCompetitionBranch::class, 'branch_id');
    }

    public function participantDocuments()
    {
        return $this->hasMany(ParticipantDocument::class, 'event_document_requirement_id');
    }
}
