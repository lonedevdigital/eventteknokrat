<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Info extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'slug',
        'isi',
        'is_published',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (Info $info) {
            if (empty($info->slug)) {
                $info->slug = Str::slug($info->judul) . '-' . Str::random(5);
            }
        });
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
