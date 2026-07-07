<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ketua_user_id',
        'nama_tim',
        'branch_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_user_id');
    }

    public function branch()
    {
        return $this->belongsTo(EventCompetitionBranch::class, 'branch_id');
    }

    public function members()
    {
        return $this->hasMany(TeamMember::class, 'team_id');
    }
}
