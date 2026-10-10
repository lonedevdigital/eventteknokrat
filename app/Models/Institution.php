<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];

    /**
     * Cari institusi berdasarkan nama (case-insensitive, whitespace dirapikan), atau buat baru
     * kalau belum ada — supaya "Universitas Indonesia" dan "universitas indonesia" tidak jadi
     * dua entri berbeda.
     */
    public static function findOrCreateByName(string $name): self
    {
        $normalized = trim((string) Str::squish($name));

        $existing = static::whereRaw('LOWER(nama) = ?', [Str::lower($normalized)])->first();
        if ($existing) {
            return $existing;
        }

        return static::create(['nama' => $normalized]);
    }
}
