<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class cvDataMahasiswaToUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua mahasiswa
        $mahasiswaList = Mahasiswa::all();

        foreach ($mahasiswaList as $mhs) {

            // Cek apakah user sudah ada (berdasarkan username = npm)
            $existingUser = User::where('username', $mhs->npm_mahasiswa)->first();

            if ($existingUser) {
                // Update hubungan user_id kalau kosong
                if (!$mhs->user_id) {
                    $mhs->update([
                        'user_id' => $existingUser->id
                    ]);
                }
                continue;
            }

            // Buat user baru berdasarkan mahasiswa
            $user = User::create([
                'name'      => $mhs->nama_mahasiswa ?? $mhs->npm_mahasiswa,
                'username'  => $mhs->npm_mahasiswa,
                'email'     => $mhs->email ?? $mhs->npm_mahasiswa.'@student.local',
                'password'  => Hash::make($mhs->npm_mahasiswa),
                'role'      => 'mahasiswa',  // kalau tabel users kamu ada kolom role
                'remember_token' => Str::random(10),
            ]);

            // Masukkan user_id ke tabel mahasiswas
            $mhs->update([
                'user_id' => $user->id
            ]);
        }

        echo "Seeder selesai: semua mahasiswa sudah dibuatkan akun user.\n";
    }
}
