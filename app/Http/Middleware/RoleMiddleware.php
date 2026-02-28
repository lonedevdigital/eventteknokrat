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

        // cek level user (baak / kemahasiswaan / mahasiswa / superuser)
        if (! in_array($user->role, $levels, true)) {
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        return $next($request);
    }
}
