<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'npm' => 'required',
            'password' => 'required'
        ]);

        // Login berdasarkan username = npm
        $user = User::where('username', $request->npm)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'NPM atau password salah'
            ], 401);
        }

        // Ambil biodata mahasiswa
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();

        // Buat token
        $token = $user->createToken('user_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'mahasiswa' => $mahasiswa,
                'token' => $token
            ]
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();

        return response()->json([
            'status' => true,
            'data' => [
                'user' => $user,
                'mahasiswa' => $mahasiswa
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}
