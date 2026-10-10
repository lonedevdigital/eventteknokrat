<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LombaCertificate extends Model
{
    use HasFactory;

    protected $table = 'lomba_certificates';

    protected $fillable = [
        'event_id',
        'lomba_certificate_template_id',
        'jenis',
        'user_id',
        'nama',
        'npm',
        'branch_id',
        'file_path',
        'certificate_number',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function template()
    {
        return $this->belongsTo(LombaCertificateTemplate::class, 'lomba_certificate_template_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(EventCompetitionBranch::class, 'branch_id');
    }
}
