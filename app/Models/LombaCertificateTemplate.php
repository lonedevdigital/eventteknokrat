<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LombaCertificateTemplate extends Model
{
    use HasFactory;

    protected $table = 'lomba_certificate_templates';

    protected $fillable = [
        'event_id',
        'jenis',
        'name',
        'canvas_width',
        'canvas_height',
        'template_json',
    ];

    protected $casts = [
        'canvas_width' => 'integer',
        'canvas_height' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
