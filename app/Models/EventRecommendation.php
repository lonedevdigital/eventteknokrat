<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'selected_by_user_id',
        'selected_by_role',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function selector()
    {
        return $this->belongsTo(User::class, 'selected_by_user_id');
    }
}
