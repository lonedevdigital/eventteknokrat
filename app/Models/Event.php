<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Event extends Model
{
    public function getRouteKeyName()
    {
        return 'slug';
    }

    use HasFactory;

    // === JENIS EVENT ===
    const TYPE_EVENT = 'event';
    const TYPE_LOMBA = 'lomba';

    protected $fillable = [
        'type',
        'slug',
        'thumbnail',
        'flyer',
        'cover_big',
        'nama_event',
        'created_by_user_id',
        'ketua_pelaksana_user_id',
        'owner_role', // ✅ BARU: role pemilik event (baak / kemahasiswaan)
        'event_category_id',
        'tempat_pelaksanaan',
        'waktu_pelaksanaan',
        'waktu_pelaksanaan_selesai',
        'tanggal_event',
        'tanggal_pendaftaran',
        'tanggal_pelaksanaan',
        'tanggal_pelaksanaan_selesai',
        'deskripsi',
        'partisipasi_skala',
        'partisipasi_prodi',
        'tipe_bayar',
        'kontak',
        'syarat_ketentuan',
        'jadwal_timeline',
        'hadiah_penghargaan',
        'cara_pendaftaran',
        'juknis_file',
        'informasi_lainnya',
        'qr_token',
    ];

    protected $casts = [
        'kontak' => 'array',
    ];

    // agar status & jumlah_peserta ikut muncul kalau di-JSON-kan
    protected $appends = [
        'status',
        'jumlah_peserta',
        'is_registration_closed',
        'pelaksanaan_range_label',
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
     * -------------------------
     *   RELASI LOMBA
     * -------------------------
     */

    // Ketua Pelaksana yang diberi akses kelola oleh Penanggung Jawab
    public function ketuaPelaksana()
    {
        return $this->belongsTo(User::class, 'ketua_pelaksana_user_id');
    }

    /**
     * Boleh mengelola lomba ini: admin core, PJ pembuat, atau Ketua Pelaksana yang ditugaskan.
     * Logic sebenarnya ada di EventPolicy::manage() — lihat Fase 1 di
     * analisis-bagian-role-ini-gleaming-clock.md.
     */
    public function canBeManagedBy(User $user): bool
    {
        return app(\App\Policies\EventPolicy::class)->manage($user, $this);
    }

    /**
     * Anggota Kepanitiaan dengan jabatan "Bendahara Lomba" — inilah satu-satunya jalur
     * penetapan Bendahara untuk lomba ini (bukan field terpisah).
     */
    public function bendaharaCommittees()
    {
        return $this->committees()->where('jabatan', 'Bendahara Lomba');
    }

    /**
     * Boleh mengelola pembayaran lomba ini: admin core, atau siapa pun yang tercatat
     * sebagai "Bendahara Lomba" di Kepanitiaan lomba ini — cukup Ketua Pelaksana yang
     * menambahkannya di Kepanitiaan, TIDAK perlu admin mengubah role user secara terpisah.
     * Logic sebenarnya ada di EventPolicy::manageBendahara().
     */
    public function canBeManagedByBendahara(User $user): bool
    {
        return app(\App\Policies\EventPolicy::class)->manageBendahara($user, $this);
    }

    /**
     * Boleh mengelola modul administratif lomba ini (berkas, data peserta): admin core,
     * atau siapa pun yang tercatat sebagai "Sekretaris Lomba" di Kepanitiaan lomba ini.
     * Logic sebenarnya ada di EventPolicy::manageSekretaris() — pola yang sama dengan
     * Bendahara, ditambahkan sebagai bagian Fase 1 (dulu belum ada method scoped ini).
     */
    public function canBeManagedBySekretaris(User $user): bool
    {
        return app(\App\Policies\EventPolicy::class)->manageSekretaris($user, $this);
    }

    // Daftar berkas yang wajib diupload peserta lomba (semua cabang, dipakai konteks admin "lihat semua")
    public function documentRequirements()
    {
        return $this->hasMany(EventDocumentRequirement::class, 'event_id')->orderBy('urutan');
    }

    /**
     * Berkas wajib untuk satu cabang tertentu (atau untuk lomba tanpa cabang kalau $branchId null) —
     * dipakai di semua tempat yang butuh "berkas milik SATU cabang", bukan gabungan semua cabang.
     */
    public function documentRequirementsForBranch(?int $branchId)
    {
        $query = $this->documentRequirements();

        return $branchId ? $query->where('branch_id', $branchId) : $query->whereNull('branch_id');
    }

    /**
     * Pastikan berkas default "Bukti Pembayaran" ada untuk satu cabang tertentu ($branchId null =
     * lomba tanpa cabang) — dipanggil di titik-titik yang bergantung padanya (Kelola Pembayaran,
     * Kelola Perlombaan) supaya lomba/cabang lama tetap otomatis punya baris default-nya.
     * Kalau admin sempat menambah "Bukti Pembayaran" manual (is_default masih false) di cabang yang
     * sama, baris itu DIPROMOSIKAN jadi default — bukan dibuat duplikat.
     */
    public function ensureDefaultDocumentRequirement(?int $branchId = null): EventDocumentRequirement
    {
        $existing = $this->documentRequirementsForBranch($branchId)->where('is_default', true)->first();
        if ($existing) {
            return $existing;
        }

        $manual = $this->documentRequirementsForBranch($branchId)
            ->whereRaw('LOWER(nama_dokumen) = ?', ['bukti pembayaran'])
            ->first();
        if ($manual) {
            $manual->update(['is_default' => true]);

            return $manual->fresh();
        }

        $maxUrutan = $this->documentRequirementsForBranch($branchId)->max('urutan');

        return EventDocumentRequirement::create([
            'event_id'     => $this->id,
            'branch_id'    => $branchId,
            'nama_dokumen' => 'Bukti Pembayaran',
            'jenis_file'   => 'gambar',
            'is_default'   => true,
            'urutan'       => ($maxUrutan ?? -1) + 1,
        ]);
    }

    /**
     * Pastikan berkas default ada di SETIAP cabang lomba (atau sekali untuk lomba tanpa cabang) —
     * pengganti pemanggilan ensureDefaultDocumentRequirement() polos di titik-titik yang butuh
     * jaminan untuk semua cabang sekaligus, bukan cuma satu.
     */
    public function ensureDefaultDocumentRequirementsForAllBranches(): void
    {
        $branches = $this->branches()->get();

        if ($branches->isEmpty()) {
            $this->ensureDefaultDocumentRequirement(null);

            return;
        }

        foreach ($branches as $branch) {
            $this->ensureDefaultDocumentRequirement($branch->id);
        }
    }

    // Cabang lomba (untuk lomba berbayar)
    public function branches()
    {
        return $this->hasMany(EventCompetitionBranch::class, 'event_id');
    }

    // Panitia event/lomba
    public function committees()
    {
        return $this->hasMany(EventCommittee::class, 'event_id');
    }

    // Tim peserta lomba
    public function teams()
    {
        return $this->hasMany(Team::class, 'event_id');
    }

    public function assessmentCriteria()
    {
        return $this->hasMany(AssessmentCriterion::class, 'event_id')->orderBy('id');
    }

    /**
     * Helper jenis event
     */
    public function getIsLombaAttribute(): bool
    {
        return $this->type === self::TYPE_LOMBA;
    }

    public function isLomba(): bool
    {
        return $this->type === self::TYPE_LOMBA;
    }

    public function scopeLomba(Builder $q): Builder
    {
        return $q->where('type', self::TYPE_LOMBA);
    }

    public function scopeEventBiasa(Builder $q): Builder
    {
        return $q->where('type', self::TYPE_EVENT);
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
     * Label tanggal pelaksanaan, dirender sebagai rentang jika tanggal_pelaksanaan_selesai
     * berbeda dari tanggal_pelaksanaan (dipakai terutama untuk Lomba yang bisa berlangsung
     * beberapa hari).
     */
    public function getPelaksanaanRangeLabelAttribute(): string
    {
        if (!$this->tanggal_pelaksanaan) {
            return '—';
        }

        $start = Carbon::parse((string) $this->tanggal_pelaksanaan);
        $end = Carbon::parse((string) ($this->tanggal_pelaksanaan_selesai ?: $this->tanggal_pelaksanaan));

        return $start->isSameDay($end)
            ? $start->translatedFormat('d M Y')
            : $start->translatedFormat('d M Y') . ' – ' . $end->translatedFormat('d M Y');
    }

    /**
     * Deskripsi singkat untuk meta tag SEO (meta description, og:description, dll).
     * Fallback ke ringkasan otomatis jika field "deskripsi" masih kosong.
     */
    public function metaDescription(int $limit = 155): string
    {
        $text = trim((string) Str::squish((string) $this->deskripsi));

        if ($text === '') {
            $parts = array_filter([
                $this->nama_event,
                $this->category->nama_kategori ?? null,
                $this->tempat_pelaksanaan,
            ]);

            $text = implode(' — ', $parts);
        }

        return Str::limit($text, $limit);
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
        $timezone = config('app.timezone');

        // tanggal_pendaftaran adalah batas pendaftaran yang sebenarnya diisi admin.
        if ($this->tanggal_pendaftaran) {
            return Carbon::parse((string) $this->tanggal_pendaftaran, $timezone)->endOfDay();
        }

        if (!$this->tanggal_pelaksanaan) {
            return null;
        }

        $eventDate = Carbon::parse((string) $this->tanggal_pelaksanaan, $timezone);

        // Fallback lama: kalau tanggal_pendaftaran belum diisi, pakai tanggal pelaksanaan.
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
                $newSlug = static::generateUniqueSlug($event->nama_event, $event->id);
                $oldSlug = $event->getOriginal('slug');

                // Catat slug lama supaya URL yang sudah terindeks Google tetap bisa di-redirect.
                if ($oldSlug && $oldSlug !== $newSlug) {
                    EventSlugRedirect::create([
                        'event_id' => $event->id,
                        'old_slug' => $oldSlug,
                    ]);
                }

                $event->slug = $newSlug;
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






