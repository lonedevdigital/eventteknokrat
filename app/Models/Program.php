<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function program_detail()
    {
        return $this->hasMany(ProgramDetail::class, 'program_id', "id");
    }

    public function program_persyaratan()
    {
        return $this->hasMany(PersyaratanProgram::class, 'program_id', "id");
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->slug = $model->generateUniqueSlug(Str::slug($model->nama_program));
        });

        static::updating(function ($model) {
            if ($model->isDirty('nama_program')) {
                $model->slug = $model->generateUniqueSlug(Str::slug($model->nama_program));
            }
        });
    }

    public function generateUniqueSlug($slug)
    {
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$original}-" . $count++;
        }

        return $slug;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'koordinator_id', "id");
    }
}
