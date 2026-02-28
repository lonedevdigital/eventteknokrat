<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display mahasiswa login view (/login).
     */
    public function create(): View
    {
        return view('auth.login', [
            'loginMode' => 'mahasiswa',
            'loginTitle' => 'Login Mahasiswa',
            'loginSubtitle' => 'Gunakan NPM sebagai username dan password.',
            'formAction' => route('login.store'),
            'usernameLabel' => 'NPM',
            'usernamePlaceholder' => 'Masukkan NPM',
            'submitLabel' => 'Sign In Mahasiswa',
            'switchUrl' => route('admin.login'),
            'switchText' => 'Login Admin',
        ]);
    }

    /**
     * Display admin login view (/admin/login).
     */
    public function createAdmin(): View
    {
        return view('auth.login', [
            'loginMode' => 'admin',
            'loginTitle' => 'Login Admin',
            'loginSubtitle' => 'Khusus akun admin (BAAK, Kemahasiswaan, Superuser).',
            'formAction' => route('admin.login.store'),
            'usernameLabel' => 'Username / Email Admin',
            'usernamePlaceholder' => 'Masukkan username atau email admin',
            'submitLabel' => 'Sign In Admin',
            'switchUrl' => route('login'),
            'switchText' => 'Login Mahasiswa',
        ]);
    }

    /**
     * Handle mahasiswa authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if ($this->isAdminRole($request->user()?->role)) {
            $this->logoutCurrentSession($request);

            throw ValidationException::withMessages([
                'email' => 'Akun admin harus login melalui /admin/login.',
            ]);
        }

        return redirect()->intended(route('frontend.home'));
    }

    /**
     * Handle admin authentication request.
     */
    public function storeAdmin(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if (! $this->isAdminRole($request->user()?->role)) {
            $this->logoutCurrentSession($request);

            throw ValidationException::withMessages([
                'email' => 'Akun mahasiswa harus login melalui /login.',
            ]);
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function isAdminRole(?string $role): bool
    {
        $normalizedRole = strtolower(trim((string) $role));

        return in_array($normalizedRole, [
            'superuser',
            'baak',
            'kemahasiswaan',
            // backward compatibility role lama
            'admin',
            'super_user',
            'kemasis',
        ], true);
    }

    private function logoutCurrentSession(Request $request): void
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
