<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Penggunaan di route:
     * ->middleware('role:baak,kemahasiswaan,superuser')
     */
    public function handle(Request $request, Closure $next, ...$levels)
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        // Cek level user (baak / kemahasiswaan / mahasiswa / superuser)
        if (in_array($user->role, $levels, true)) {
            return $next($request);
        }

        // Cek permission khusus jika role tidak match
        
        // 1. Penanggung Jawab
        if (in_array('penanggung_jawab', $levels, true) && $user->boleh_jadi_penanggung_jawab) {
            return $next($request);
        }

        // 2. Ketua Pelaksana
        if (in_array('ketua_pelaksana', $levels, true)) {
            if (\App\Models\Event::where('ketua_pelaksana_user_id', $user->id)->exists()) {
                return $next($request);
            }
            if (\App\Models\EventCommittee::where('user_id', $user->id)->where('jabatan', 'Ketua Pelaksana')->exists()) {
                return $next($request);
            }
        }

        // 3. Panitia
        if (in_array('panitia', $levels, true)) {
            if (\App\Models\EventCommittee::where('user_id', $user->id)->exists()) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki hak akses.');
    }
}
