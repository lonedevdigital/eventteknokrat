<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // =============== ROLE / LEVEL ===============
    // nilai yang disimpan di kolom "level" pada tabel users
    const LEVEL_BAAK          = 'baak';
    const LEVEL_KEMAHASISWAAN = 'kemahasiswaan';
    const LEVEL_MAHASISWA     = 'mahasiswa';
    const LEVEL_SUPERUSER     = 'superuser';
    const LEVEL_PENANGGUNG_JAWAB = 'penanggung_jawab';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role',
        'type', // dosen, mahasiswa
        'fakultas_kode',
        'email_pribadi',
        'no_telepon'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id', "user_id");
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id', "user_id");
    }

    // =============== HELPER UNTUK LEVEL ===============

    // cek satu level
    public function hasLevel(string $level): bool
    {
        return $this->role === $level;
    }

    // cek beberapa level sekaligus
    public function hasAnyLevel(array $levels): bool
    {
        return in_array($this->role, $levels, true);
    }

    public function isBaak(): bool
    {
        return $this->role === self::LEVEL_BAAK;
    }

    public function isKemahasiswaan(): bool
    {
        return $this->role === self::LEVEL_KEMAHASISWAAN;
    }

    public function isMahasiswa(): bool
    {
        return $this->role === self::LEVEL_MAHASISWA;
    }

    public function isSuperUser(): bool
    {
        return $this->role === self::LEVEL_SUPERUSER;
    }

    public function isPenanggungJawab(): bool
    {
        return $this->role === self::LEVEL_PENANGGUNG_JAWAB;
    }

    // BAAK, Kemahasiswaan, Super User dianggap "staff"
    public function isStaff(): bool
    {
        return in_array($this->role, [
            self::LEVEL_BAAK,
            self::LEVEL_KEMAHASISWAAN,
            self::LEVEL_SUPERUSER,
            self::LEVEL_PENANGGUNG_JAWAB,
        ], true);
    }
}
