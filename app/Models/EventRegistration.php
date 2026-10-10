<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    use HasFactory;

    protected $table = 'event_registrations';

    protected $fillable = [
        'event_id',
        'branch_id',
        'tipe_pendaftaran',
        'user_id',
        'status',
        'attendance_status',
        'registered_at',
        'attendance_at',
        'certificate_url',
        'certificate_uploaded_at',
    ];


    protected $casts = [
        'event_id'  => 'integer',
        'branch_id' => 'integer',
        'user_id'   => 'integer',
        'registered_at' => 'datetime',
        'attendance_at' => 'datetime',
    ];

    /**
     * -------------------------
     *   RELASI MODEL
     * -------------------------
     */

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Ambil biodata mahasiswa via user_id
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, 'user_id', 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(EventCompetitionBranch::class, 'branch_id');
    }

    /**
     * Registrasi yang merepresentasikan satu "pendaftar/pembayar" — individu apa adanya,
     * atau ketua tim yang mewakili seluruh anggotanya. Dipakai untuk daftar peserta &
     * pembayaran supaya anggota tim non-ketua tidak muncul sebagai baris terpisah
     * (karena satu tim = satu pendaftaran/pembayaran).
     */
    public function scopePayers($query, int $eventId)
    {
        return $query->where('event_id', $eventId)
            ->where(function ($q) use ($eventId) {
                $q->where('tipe_pendaftaran', 'individu')
                    ->orWhere(function ($qq) use ($eventId) {
                        $qq->where('tipe_pendaftaran', 'tim')
                            ->whereExists(function ($sub) use ($eventId) {
                                $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                                    ->from('teams')
                                    ->whereColumn('teams.ketua_user_id', 'event_registrations.user_id')
                                    ->where('teams.event_id', $eventId);
                            });
                    });
            });
    }

    // Berkas (dokumen lomba) yang diupload untuk registrasi ini
    public function documents()
    {
        return $this->hasMany(ParticipantDocument::class, 'event_registration_id');
    }

    public function scores()
    {
        return $this->hasMany(ParticipantScore::class, 'event_registration_id');
    }

    /**
     * True hanya jika SEMUA berkas yang diwajibkan lomba ini (termasuk Bukti Pembayaran)
     * sudah diupload dan berstatus approved. Dipakai untuk notifikasi ke mahasiswa
     * dan sebagai gate kelayakan generate sertifikat.
     */
    public function allDocumentsApproved(): bool
    {
        $requirementIds = $this->event?->documentRequirementsForBranch($this->branch_id)->pluck('id') ?? collect();

        if ($requirementIds->isEmpty()) {
            return false;
        }

        $approvedCount = $this->documents()
            ->whereIn('event_document_requirement_id', $requirementIds)
            ->where('status', 'approved')
            ->count();

        return $approvedCount >= $requirementIds->count();
    }

    /**
     * -------------------------
     *   CEGAH DUPLIKASI DAFTAR
     * -------------------------
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registration) {
            $query = static::where('event_id', $registration->event_id)
                ->where('user_id', $registration->user_id);

            $registration->branch_id
                ? $query->where('branch_id', $registration->branch_id)
                : $query->whereNull('branch_id');

            if ($query->exists()) {
                throw new \Exception("Anda sudah terdaftar pada cabang ini.");
            }
        });
    }

    /**
     * -------------------------
     *   HELPER METHODS
     * -------------------------
     */

    public static function isRegistered($eventId, $userId)
    {
        return static::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();
    }

    public static function countParticipants($eventId)
    {
        return static::where('event_id', $eventId)->count();
    }

    public static function getParticipants($eventId)
    {
        return static::where('event_id', $eventId)
            ->with(['user', 'mahasiswa'])
            ->get();
    }
}
