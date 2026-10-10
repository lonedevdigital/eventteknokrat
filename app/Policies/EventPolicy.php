<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

/**
 * Fase 1 dari rencana Level/Permission/Assignment role (lihat
 * analisis-bagian-role-ini-gleaming-clock.md): satu sumber kebenaran untuk
 * semua pengecekan otorisasi Event/Lomba yang sebelumnya tersebar &
 * terduplikasi di Event::canBeManagedBy(), Event::canBeManagedByBendahara(),
 * CompetitionController::canAssignKetua()/isAdminCore(), dan
 * AdminEventController::canAssignKetuaPelaksana()/authorizeEventByRole().
 *
 * Perilaku otorisasi TIDAK berubah dari sebelumnya — method-method lama itu
 * sekarang jadi wrapper tipis yang delegasi ke sini.
 */
class EventPolicy
{
    /**
     * Admin core: superuser, BAAK, atau Kemahasiswaan — selalu boleh mengelola apa pun.
     */
    public function isAdminCore(User $user): bool
    {
        return $user->isSuperUser() || $user->isBaak() || $user->isKemahasiswaan();
    }

    /**
     * Boleh mengelola lomba ini: admin core, PJ pembuat, atau Ketua Pelaksana yang ditugaskan.
     */
    public function manage(User $user, Event $event): bool
    {
        if ($this->isAdminCore($user)) {
            return true;
        }

        if ($user->boleh_jadi_penanggung_jawab && (int) $event->created_by_user_id === (int) $user->id) {
            return true;
        }

        if ((int) $event->ketua_pelaksana_user_id === (int) $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Boleh mengelola event NON-lomba: admin core, atau owner_role user cocok dengan
     * owner_role event (fallback ke creator kalau owner_role event lama masih null).
     * Untuk lomba, delegasi ke manage() di atas.
     */
    public function manageEvent(User $user, Event $event): bool
    {
        if ($this->isAdminCore($user)) {
            return true;
        }

        if ($event->isLomba()) {
            return $this->manage($user, $event);
        }

        $userRole = Event::normalizeRole($user->role ?? 'superuser');
        $eventRole = Event::normalizeRole($event->owner_role ?? '');

        if ($eventRole === '') {
            return (int) $event->created_by_user_id === (int) $user->id;
        }

        return $eventRole === $userRole;
    }

    /**
     * Boleh menetapkan/mengubah Ketua Pelaksana: admin core, atau PJ pembuat lomba ini
     * (Ketua Pelaksana sendiri TIDAK boleh reassign dirinya/orang lain).
     */
    public function assignKetua(User $user, Event $event): bool
    {
        if ($this->isAdminCore($user)) {
            return true;
        }

        return $user->boleh_jadi_penanggung_jawab && (int) $event->created_by_user_id === (int) $user->id;
    }

    /**
     * Boleh mengelola pembayaran lomba ini: admin core, atau siapa pun yang tercatat
     * sebagai "Bendahara Lomba" di Kepanitiaan lomba ini.
     */
    public function manageBendahara(User $user, Event $event): bool
    {
        if ($this->isAdminCore($user)) {
            return true;
        }

        return $event->bendaharaCommittees()->where('user_id', $user->id)->exists();
    }

    /**
     * Boleh mengelola modul administratif lomba ini (berkas, data peserta): admin core,
     * atau siapa pun yang tercatat sebagai "Sekretaris Lomba" di Kepanitiaan lomba ini.
     * Pola identik dengan manageBendahara() — jabatan committee, bukan role user.
     */
    public function manageSekretaris(User $user, Event $event): bool
    {
        if ($this->isAdminCore($user)) {
            return true;
        }

        return $event->committees()
            ->where('jabatan', 'Sekretaris Lomba')
            ->where('user_id', $user->id)
            ->exists();
    }
}
