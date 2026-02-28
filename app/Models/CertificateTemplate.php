<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $table = 'certificate_templates';

    protected $fillable = [
        'event_id',
        'role',
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
        return $this->belongsTo(\App\Models\Event::class, 'event_id');
    }
}
