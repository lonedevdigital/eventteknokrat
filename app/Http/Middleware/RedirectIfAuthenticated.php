<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $isAdminUser = $this->isAdminRole($user?->role);

                // User yang sudah login tidak boleh membuka halaman login lagi.
                if ($isAdminUser) {
                    return redirect('/dashboard');
                }

                return redirect()->route('frontend.home');
            }
        }

        return $next($request);
    }

    private function isAdminRole(?string $role): bool
    {
        $normalizedRole = strtolower(trim((string) $role));

        return in_array($normalizedRole, [
            'superuser',
            'baak',
            'kemahasiswaan',
            'admin',
            'super_user',
            'kemasis',
        ], true);
    }
}
