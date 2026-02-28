<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('users')->insert([
            [
                'username'   => 'admin',
                'name'       => 'Admin',
                'email'      => 'admin@gmail.com',
                'password'   => bcrypt('admin123'),
                'role'       => 'superuser',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'kemahasiswaan',
                'name'       => 'Kemahasiswaan',
                'email'      => 'kemahasiswaan@gmail.com',
                'password'   => bcrypt('admin123'),
                'role'       => 'kemahasiswaan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username'   => 'baak',
                'name'       => 'BAAK',
                'email'      => 'baak@gmail.com',
                'password'   => bcrypt('admin123'),
                'role'       => 'baak',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
