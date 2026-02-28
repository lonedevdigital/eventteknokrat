<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PersyaratanProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', "id");
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->slug = $model->generateUniqueSlug(Str::slug($model->nama_persyaratan));
        });

        static::updating(function ($model) {
            if ($model->isDirty('nama_persyaratan')) {
                $model->slug = $model->generateUniqueSlug(Str::slug($model->nama_persyaratan));
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
}
