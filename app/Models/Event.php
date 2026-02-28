<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'thumbnail',
        'nama_event',
        'created_by_user_id',
        'owner_role', // ✅ BARU: role pemilik event (baak / kemahasiswaan)
        'event_category_id',
        'tempat_pelaksanaan',
        'waktu_pelaksanaan',
        'tanggal_event',
        'tanggal_pendaftaran',
        'tanggal_pelaksanaan',
        'deskripsi',
        'informasi_lainnya',
        'qr_token',
    ];

    // agar status & jumlah_peserta ikut muncul kalau di-JSON-kan
    protected $appends = [
        'status',
        'jumlah_peserta',
        'is_registration_closed',
    ];

    /**
     * -------------------------
     *   RELASI
     * -------------------------
     */

    // pembuat event
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // kategori event  ➜ PENTING: pakai event_category_id
    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    // pendaftaran peserta
    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'event_id');
    }

    public function documentations()
    {
        return $this->hasMany(Documentation::class);
    }

    public function recommendation()
    {
        return $this->hasOne(EventRecommendation::class, 'event_id');
    }

    /**
     * ✅ TEMPLATE SERTIFIKAT
     * Migration kamu: certificate_templates punya event_id
     * Jadi relasinya di Event HARUS hasMany / hasOne
     */
    public function certificateTemplates()
    {
        return $this->hasMany(\App\Models\CertificateTemplate::class, 'event_id');
    }

    /**
     * (Opsional) Ambil template terbaru untuk role tertentu
     * contoh: $event->certificateTemplateForRole('baak')->first();
     */
    public function certificateTemplateForRole(string $role)
    {
        $role = static::normalizeRole($role);

        return $this->hasOne(\App\Models\CertificateTemplate::class, 'event_id')
            ->where('role', $role)          // butuh kolom role di certificate_templates
            ->latestOfMany();               // ambil yang paling baru
    }

    /**
     * Jumlah peserta yang mendaftar
     */
    public function getJumlahPesertaAttribute()
    {
        return $this->registrations()->count();
    }

    /**
     * -------------------------
     *   STATUS OTOMATIS
     * -------------------------
     */

    public function getStatusAttribute()
    {
        return $this->isRegistrationClosed() ? 'ditutup' : 'dibuka';
    }

    public function getIsOpenAttribute()
    {
        return $this->status === 'dibuka';
    }

    public function getIsClosedAttribute()
    {
        return $this->status === 'ditutup';
    }

    public function getIsRegistrationClosedAttribute(): bool
    {
        return $this->isRegistrationClosed();
    }

    public function isRegistrationClosed(): bool
    {
        $deadline = $this->registrationDeadlineAt();
        if (!$deadline) {
            return false;
        }

        return now()->greaterThanOrEqualTo($deadline);
    }

    public function registrationDeadlineAt(): ?Carbon
    {
        if (!$this->tanggal_pelaksanaan) {
            return null;
        }

        $timezone = config('app.timezone');
        $eventDate = Carbon::parse((string) $this->tanggal_pelaksanaan, $timezone);

        // If event time is empty, registration is closed at end of event day.
        if (!$this->waktu_pelaksanaan) {
            return $eventDate->copy()->endOfDay();
        }

        try {
            $time = Carbon::parse((string) $this->waktu_pelaksanaan, $timezone)->format('H:i:s');
            return Carbon::parse($eventDate->format('Y-m-d') . ' ' . $time, $timezone);
        } catch (\Throwable $exception) {
            return $eventDate->copy()->endOfDay();
        }
    }

    /**
     * -------------------------
     *   ROLE / VISIBILITY
     * -------------------------
     * Role yang kamu pakai:
     * - superuser (admin)
     * - baak
     * - kemahasiswaan
     */
    public static function normalizeRole(?string $role): string
    {
        $role = strtolower(trim((string) $role));

        // mapping biar aman kalau ada variasi penulisan
        if ($role === 'admin') return 'superuser';
        if ($role === 'super_user') return 'superuser';
        if ($role === 'kemasis') return 'kemahasiswaan';

        return $role ?: 'superuser';
    }

    /**
     * Scope: event yang boleh terlihat oleh user tertentu
     * - superuser: lihat semua
     * - lainnya: hanya owner_role = role user
     * Catatan: event lama yang owner_role null -> hanya superuser yang bisa lihat (aman)
     */
    public function scopeVisibleTo(Builder $q, $user): Builder
    {
        $userRole = static::normalizeRole($user->role ?? 'superuser');

        if ($userRole === 'superuser') {
            return $q;
        }

        return $q->where('owner_role', $userRole);
    }

    /**
     * -------------------------
     *   SLUG OTOMATIS + AUTO SET OWNER_ROLE
     * -------------------------
     */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            // Generate slug saat create
            $event->slug = static::generateUniqueSlug($event->nama_event);

            // ✅ Auto set created_by_user_id kalau belum diisi
            if (empty($event->created_by_user_id) && auth()->check()) {
                $event->created_by_user_id = auth()->id();
            }

            // ✅ Auto set owner_role berdasarkan role pembuat (BAAK/Kemahasiswaan)
            // superuser boleh null? -> aku set superuser juga biar konsisten
            if (empty($event->owner_role) && auth()->check()) {
                $event->owner_role = static::normalizeRole(auth()->user()->role ?? 'superuser');
            }
        });

        static::updating(function ($event) {
            // Update slug saat nama_event berubah
            if ($event->isDirty('nama_event')) {
                $event->slug = static::generateUniqueSlug($event->nama_event, $event->id);
            }
        });
    }

    /**
     * Generator slug unik
     */
    protected static function generateUniqueSlug($namaEvent, $ignoreId = null)
    {
        $slug     = Str::slug($namaEvent);
        $original = $slug;
        $counter  = 1;

        while (
        static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}






