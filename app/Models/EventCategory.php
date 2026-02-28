<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kategori',
    ];

    public function events()
    {
        // relasi ke tabel events pakai kolom event_category_id
        return $this->hasMany(Event::class, 'event_category_id');
        // karena namespace-nya sama (App\Models), Event::class aman dipakai begini
    }
}
