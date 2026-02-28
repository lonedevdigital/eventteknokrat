<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return Redirect::route('login');
            }
            return $next($request);
        });
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        $mahasiswa = Mahasiswa::query()
            ->where('user_id', $user->id)
            ->first();

        // Fallback bila data belum ter-link ke user_id tapi username = NPM.
        if (!$mahasiswa && $user->role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::query()
                ->where('npm_mahasiswa', $user->username)
                ->first();
        }

        return view('profile.edit', [
            'user' => $user,
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current-password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6'],
            'new_password_confirm' => ['required', 'string', 'same:new_password'],
        ]);

        if (!Hash::check($validated['current-password'], Auth::user()->password)) {
            return redirect()->back()->with('error', 'Password lama salah.');
        }

        if (strcmp($validated['current-password'], $validated['new_password']) === 0) {
            return redirect()->back()->with('error', 'Password baru harus berbeda dari password lama.');
        }

        $user = User::findOrFail(Auth::user()->id);
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil diperbarui.');
    }
}
