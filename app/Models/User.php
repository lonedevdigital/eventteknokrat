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

    // Cache properties for Memoization to prevent N+1 Queries
    protected ?bool $_isKetuaPelaksana = null;
    protected ?bool $_isPanitia = null;
    protected ?bool $_isBendaharaLomba = null;

    // =============== ROLE / LEVEL ===============
    // nilai yang disimpan di kolom "role" pada tabel users.
    // Hanya 3 tingkat Level (cascading, lihat role_rank di bawah):
    //   1. Administrator (superuser)
    //   2. Baak / Kemahasiswaan
    //   3. Dosen / Mahasiswa
    const LEVEL_BAAK             = 'baak';
    const LEVEL_KEMAHASISWAAN    = 'kemahasiswaan';
    const LEVEL_MAHASISWA        = 'mahasiswa';
    const LEVEL_DOSEN            = 'dosen';
    const LEVEL_SUPERUSER        = 'superuser';
    const LEVEL_PENANGGUNG_JAWAB = 'penanggung_jawab';
    const LEVEL_KETUA_PELAKSANA  = 'ketua_pelaksana';

    // Rank numerik Level — dipakai middleware cascading (rank user <= rank minimum route).
    const RANK_ADMINISTRATOR = 1;
    const RANK_STAFF         = 2; // Baak / Kemahasiswaan
    const RANK_BASE          = 3; // Dosen / Mahasiswa

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
        'role_rank',
        'type', // dosen, mahasiswa
        'boleh_jadi_penanggung_jawab',
        'fakultas_kode',
        'email_pribadi',
        'no_telepon',
        'is_external',
        'npm',
        'institution_id',
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
        'is_external' => 'boolean',
        'role_rank' => 'integer',
        'boleh_jadi_penanggung_jawab' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->isDirty('role') || is_null($user->role_rank)) {
                $user->role_rank = self::rankForRole($user->role);
            }
        });
    }

    /**
     * Rank numerik untuk sebuah nilai role — dipakai untuk backfill/auto-sync
     * role_rank supaya kolom itu tidak pernah basi walau role diubah manual.
     */
    public static function rankForRole(?string $role): int
    {
        return match ($role) {
            self::LEVEL_SUPERUSER => self::RANK_ADMINISTRATOR,
            self::LEVEL_BAAK, self::LEVEL_KEMAHASISWAAN => self::RANK_STAFF,
            default => self::RANK_BASE, // mahasiswa, dosen, dan sisa role Event Panitia lama
        };
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id', "user_id");
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id', "user_id");
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
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

    public function isDosen(): bool
    {
        return $this->role === self::LEVEL_DOSEN;
    }

    public function isSuperUser(): bool
    {
        return $this->role === self::LEVEL_SUPERUSER;
    }

    public function isPenanggungJawab(): bool
    {
        return (bool) $this->boleh_jadi_penanggung_jawab;
    }

    public function isKetuaPelaksana(): bool
    {
        if ($this->_isKetuaPelaksana !== null) return $this->_isKetuaPelaksana;

        return $this->_isKetuaPelaksana = \App\Models\Event::where('ketua_pelaksana_user_id', $this->id)->exists() 
            || EventCommittee::where('user_id', $this->id)->where('jabatan', 'Ketua Pelaksana')->exists();
    }

    public function isPanitia(): bool
    {
        if ($this->_isPanitia !== null) return $this->_isPanitia;
        return $this->_isPanitia = EventCommittee::where('user_id', $this->id)->exists();
    }

    public function isBendahara(): bool
    {
        return $this->isBendaharaLomba();
    }

    /**
     * Bendahara Lomba ditentukan murni dari Kepanitiaan (jabatan "Bendahara Lomba"),
     * bukan dari kolom role — Ketua Pelaksana cukup menambahkannya di Kepanitiaan,
     * tidak perlu admin mengubah role user secara terpisah.
     */
    public function isBendaharaLomba(): bool
    {
        if ($this->_isBendaharaLomba !== null) return $this->_isBendaharaLomba;
        return $this->_isBendaharaLomba = EventCommittee::where('user_id', $this->id)
            ->where('jabatan', 'Bendahara Lomba')
            ->exists();
    }

    /**
     * Akun mahasiswa yang mendaftar sendiri (bukan sinkronisasi BAAK) —
     * tidak punya baris di tabel "mahasiswas".
     */
    public function isExternalStudent(): bool
    {
        return $this->role === self::LEVEL_MAHASISWA && (bool) $this->is_external;
    }

    // BAAK, Kemahasiswaan, Super User dianggap "staff".
    // Penanggung Jawab dan Panitia juga diizinkan mengakses area dashboard.
    public function isStaff(): bool
    {
        if (in_array($this->role, [
            self::LEVEL_BAAK,
            self::LEVEL_KEMAHASISWAAN,
            self::LEVEL_SUPERUSER,
        ], true)) {
            return true;
        }

        if ($this->isPenanggungJawab()) {
            return true;
        }

        if ($this->isKetuaPelaksana()) {
            return true;
        }

        if ($this->isPanitia()) {
            return true;
        }

        return false;
    }
}
