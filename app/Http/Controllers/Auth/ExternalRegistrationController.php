<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ExternalRegistrationController extends Controller
{
    /**
     * Display registration view for mahasiswa diluar Teknokrat.
     */
    public function create(): View
    {
        return view('auth.register-external', [
            'institutions' => Institution::orderBy('nama')->pluck('nama'),
        ]);
    }

    /**
     * Handle registration for mahasiswa diluar Teknokrat.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'npm' => ['nullable', 'string', 'max:50'],
            'institution' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $institution = Institution::findOrCreateByName($validated['institution']);

        $user = null;
        $attempts = 0;

        while (! $user) {
            $attempts++;

            try {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'username' => $this->generateUsername($validated['email']),
                    'password' => Hash::make($validated['password']),
                    'role' => User::LEVEL_MAHASISWA,
                    'is_external' => true,
                    'npm' => $validated['npm'] ?? null,
                    'institution_id' => $institution->id,
                ]);
            } catch (QueryException $e) {
                if ($attempts >= 5) {
                    throw $e;
                }
            }
        }

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('frontend.home')
            ->with('success', 'Pendaftaran berhasil. Selamat datang, ' . $user->name . '!');
    }

    /**
     * Generate unique username placeholder for external accounts (not used for login).
     */
    private function generateUsername(string $email): string
    {
        $local = Str::before($email, '@');
        $base = Str::of($local)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', '')->substr(0, 20);

        if ($base->isEmpty()) {
            $base = Str::of('ext');
        }

        do {
            $candidate = $base . Str::lower(Str::random(6));
        } while (User::where('username', $candidate)->exists());

        return (string) $candidate;
    }
}
