<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    protected $fillable = [
        'event_id',
        'file_path'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
