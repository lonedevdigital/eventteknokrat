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
        'user_id',
        'status',
        'attendance_status',
        'registered_at',
        'attendance_at',
        'certificate_url',
        'certificate_uploaded_at',
    ];


    protected $casts = [
        'event_id' => 'integer',
        'user_id'  => 'integer',
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

    /**
     * -------------------------
     *   CEGAH DUPLIKASI DAFTAR
     * -------------------------
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($registration) {
            if (static::where('event_id', $registration->event_id)
                ->where('user_id', $registration->user_id)
                ->exists()
            ) {
                throw new \Exception("Anda sudah terdaftar pada event ini.");
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
