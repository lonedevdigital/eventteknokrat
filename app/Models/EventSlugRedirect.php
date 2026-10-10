<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSlugRedirect extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'old_slug',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
