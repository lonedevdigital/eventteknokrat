<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUser = auth()->user();
        
        $query = User::query();

        if ($currentUser->isSuperUser()) {
            // Superuser see: BAAK, Kemahasiswaan, Penanggung Jawab
            $query->whereIn('role', [
                User::LEVEL_BAAK,
                User::LEVEL_KEMAHASISWAAN,
                User::LEVEL_PENANGGUNG_JAWAB
            ]);
        } elseif ($currentUser->isBaak() || $currentUser->isKemahasiswaan()) {
            // BAAK/Kemahasiswaan see: Penanggung Jawab only
            $query->whereIn('role', [
                User::LEVEL_PENANGGUNG_JAWAB
            ]);
        } else {
            abort(403, 'Unauthorized action.');
        }

        $users = $query->latest()->get();

        return view('admin.user_management.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentUser = auth()->user();
        $allowedRoles = [];

        if ($currentUser->isSuperUser()) {
            $allowedRoles = [
                User::LEVEL_BAAK => 'BAAK',
                User::LEVEL_KEMAHASISWAAN => 'Kemahasiswaan',
                User::LEVEL_PENANGGUNG_JAWAB => 'Penanggung Jawab',
            ];
        } elseif ($currentUser->isBaak() || $currentUser->isKemahasiswaan()) {
            $allowedRoles = [
                User::LEVEL_PENANGGUNG_JAWAB => 'Penanggung Jawab',
            ];
        } else {
            abort(403);
        }

        return view('admin.user_management.create', compact('allowedRoles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();
        
        $allowedLevels = [];
        if ($currentUser->isSuperUser()) {
            $allowedLevels = [User::LEVEL_BAAK, User::LEVEL_KEMAHASISWAAN, User::LEVEL_PENANGGUNG_JAWAB];
        } elseif ($currentUser->isBaak() || $currentUser->isKemahasiswaan()) {
            $allowedLevels = [User::LEVEL_PENANGGUNG_JAWAB];
        } else {
            abort(403);
        }

        // Custom Validation
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'], // Removed unique:users to allow upsert
            'email' => ['required', 'string', 'email', 'max:255'], // Removed unique:users to allow upsert logic check below
            'role' => ['required', Rule::in($allowedLevels)],
            'type' => ['nullable', 'string', 'in:dosen,mahasiswa'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ], [
            'username.required' => 'Username/NPM wajib diisi.',
            'role.required' => 'Role wajib dipilih.',
            'name.required' => 'Nama wajib diisi.',
        ]);

        // Additional Validation & Logic for Penanggung Jawab
        if ($request->role === User::LEVEL_PENANGGUNG_JAWAB) {
            if (empty($request->type)) {
                return back()->withErrors(['type' => 'Tipe Penanggung Jawab harus dipilih.'])->withInput();
            }

            if ($request->type === 'mahasiswa') {
                if (empty($request->no_telepon)) {
                    return back()->withErrors(['no_telepon' => 'Nomor HP wajib diisi untuk Mahasiswa.'])->withInput();
                }
                
                // Validate NPM exists in mahasiswas table
                $mahasiswa = \App\Models\Mahasiswa::where('npm_mahasiswa', $request->username)->first();
                if (!$mahasiswa) {
                    return back()->withErrors(['username' => 'NPM tidak ditemukan di data mahasiswa.'])->withInput();
                }
            }
        }

        // Check availability logic manually
        // If Username exists -> We UPDATE it (Promote/Reset).
        // If Email exists for DIFFERENT username -> Error.
        
        $existingUser = User::where('username', $request->username)->first();
        $emailCheck = User::where('email', $request->email)->first();

        if ($emailCheck && (!$existingUser || $emailCheck->id !== $existingUser->id)) {
             return back()->withErrors(['email' => 'Email ini sudah digunakan oleh user lain.'])->withInput();
        }

        // Password = Username (NPM/NIDN)
        $plainPassword = $request->username;
        $dataToSave = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($plainPassword),
            'role' => $request->role,
            'type' => $request->role === User::LEVEL_PENANGGUNG_JAWAB ? $request->type : null,
            'no_telepon' => $request->no_telepon,
        ];

        if ($existingUser) {
            $existingUser->update($dataToSave);
            $user = $existingUser;
            $message = 'User berhasil diperbarui (Password di-reset).';
        } else {
             $dataToSave['username'] = $request->username;
             $user = User::create($dataToSave);
             $message = 'User berhasil ditambahkan.';
        }

        // Link User to Mahasiswa if type is mahasiswa
        if ($request->role === User::LEVEL_PENANGGUNG_JAWAB && $request->type === 'mahasiswa') {
             $mahasiswa = \App\Models\Mahasiswa::where('npm_mahasiswa', $request->username)->first();
             if ($mahasiswa) {
                 $mahasiswa->update(['user_id' => $user->id]);
             }
        }

        return redirect()->route('user-management.index')
            ->with('success', $message)
            ->with('generated_password', $plainPassword);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $userManagement)
    {
        // Parameter binding name defaults to model name (camelCase), but route resource usually uses singular name.
        // We'll use $userManagement as the variable name for the target user.
        $targetUser = $userManagement;
        $currentUser = auth()->user();

        // Authorization check
        if (!$this->canManage($currentUser, $targetUser)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit user ini.');
        }

        $allowedRoles = [];
        if ($currentUser->isSuperUser()) {
            $allowedRoles = [
                User::LEVEL_BAAK => 'BAAK',
                User::LEVEL_KEMAHASISWAAN => 'Kemahasiswaan',
                User::LEVEL_PENANGGUNG_JAWAB => 'Penanggung Jawab',
            ];
        } elseif ($currentUser->isBaak() || $currentUser->isKemahasiswaan()) {
            $allowedRoles = [
                User::LEVEL_PENANGGUNG_JAWAB => 'Penanggung Jawab',
            ];
        }

        return view('admin.user_management.edit', compact('targetUser', 'allowedRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $userManagement)
    {
        $targetUser = $userManagement;
        $currentUser = auth()->user();

        if (!$this->canManage($currentUser, $targetUser)) {
            abort(403);
        }

        $allowedLevels = [];
        if ($currentUser->isSuperUser()) {
            $allowedLevels = [User::LEVEL_BAAK, User::LEVEL_KEMAHASISWAAN, User::LEVEL_PENANGGUNG_JAWAB];
        } elseif ($currentUser->isBaak() || $currentUser->isKemahasiswaan()) {
            $allowedLevels = [User::LEVEL_PENANGGUNG_JAWAB];
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($targetUser->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($targetUser->id)],
            'role' => ['required', Rule::in($allowedLevels)],
            'type' => ['nullable', 'string', 'in:dosen,mahasiswa'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

         // Validation for Penanggung Jawab
         if ($request->role === User::LEVEL_PENANGGUNG_JAWAB) {
            if (empty($request->type)) {
                return back()->withErrors(['type' => 'Tipe Penanggung Jawab harus dipilih.'])->withInput();
            }
            if ($request->type === 'mahasiswa' && empty($request->no_telepon)) {
                return back()->withErrors(['no_telepon' => 'Nomor HP wajib diisi untuk Mahasiswa.'])->withInput();
            }
        }

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'type' => $request->role === User::LEVEL_PENANGGUNG_JAWAB ? $request->type : null,
            'no_telepon' => $request->no_telepon,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $targetUser->update($data);

        return redirect()->route('user-management.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $userManagement)
    {
        $targetUser = $userManagement;
        $currentUser = auth()->user();

        if (!$this->canManage($currentUser, $targetUser)) {
            abort(403);
        }

        if ($targetUser->id === $currentUser->id) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $targetUser->delete();

        return redirect()->route('user-management.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Helper to check if current user can manage target user
     */
    private function canManage($currentUser, $targetUser)
    {
        if ($currentUser->isSuperUser()) {
            // Superuser can manage BAAK, Kemahasiswaan, PJ
            // Cannot manage other Superusers (convention, optional)
            return in_array($targetUser->role, [
                User::LEVEL_BAAK, 
                User::LEVEL_KEMAHASISWAAN, 
                User::LEVEL_PENANGGUNG_JAWAB
            ]);
        }

        if ($currentUser->isBaak() || $currentUser->isKemahasiswaan()) {
            return $targetUser->role === User::LEVEL_PENANGGUNG_JAWAB;
        }

        return false;
    }
}
