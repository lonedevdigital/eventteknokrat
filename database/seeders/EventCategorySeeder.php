<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventCategory;

class EventCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['nama_kategori' => 'Seminar'],
            ['nama_kategori' => 'Workshop'],
            ['nama_kategori' => 'Lomba'],
            ['nama_kategori' => 'Pelatihan'],
            ['nama_kategori' => 'Kampus Merdeka'],
        ];

        foreach ($categories as $cat) {
            EventCategory::create($cat);
        }
    }
}
