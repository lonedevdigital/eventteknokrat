<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProgramDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', "id");
    }

    public function penanggung_jawab()
    {
        return $this->belongsTo(User::class, 'pj_user_id', "id");
    }
}
