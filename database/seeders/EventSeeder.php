<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run()
    {
        $events = [
            [
                'nama_event'           => 'Seminar Nasional Teknologi AI 2025',
                'event_category_id'    => 1,
                'thumbnail'            => 'storage/event_covers/sample1.jpg',
                'tempat_pelaksanaan'   => 'Aula Gedung A',
                'tanggal_pelaksanaan'  => '2025-12-12',
                'waktu_pelaksanaan'    => '09:00:00',
                'tanggal_pendaftaran'  => '2025-11-30',
                'deskripsi'            => 'Seminar nasional mengenai perkembangan AI di Indonesia.',
                'created_by_user_id'           => 1,
            ],
            [
                'nama_event'           => 'Workshop UI/UX Design Bootcamp',
                'event_category_id'    => 2,
                'thumbnail'            => 'storage/event_covers/sample2.jpg',
                'tempat_pelaksanaan'   => 'Lab Multimedia',
                'tanggal_pelaksanaan'  => '2025-12-20',
                'waktu_pelaksanaan'    => '13:00:00',
                'tanggal_pendaftaran'  => '2025-12-05',
                'deskripsi'            => 'Pelatihan intensif UI/UX untuk mahasiswa Teknokrat.',
                'created_by_user_id'           => 1,
            ],
            [
                'nama_event'           => 'Lomba Web Development 2025',
                'event_category_id'    => 3,
                'thumbnail'            => 'storage/event_covers/sample3.jpg',
                'tempat_pelaksanaan'   => 'Gedung E Hall',
                'tanggal_pelaksanaan'  => '2025-12-25',
                'waktu_pelaksanaan'    => '08:00:00',
                'tanggal_pendaftaran'  => '2025-12-10',
                'deskripsi'            => 'Kompetisi website nasional antar mahasiswa.',
                'created_by_user_id'           => 1,
            ],
            [
                'nama_event'           => 'Pelatihan Laravel Backend Development',
                'event_category_id'    => 4,
                'thumbnail'            => 'storage/event_covers/sample4.jpg',
                'tempat_pelaksanaan'   => 'Ruang Coding Center',
                'tanggal_pelaksanaan'  => '2025-12-22',
                'waktu_pelaksanaan'    => '10:00:00',
                'tanggal_pendaftaran'  => '2025-12-01',
                'deskripsi'            => 'Pelatihan Laravel dasar hingga menengah.',
                'created_by_user_id'           => 1,
            ],
            [
                'nama_event'           => 'Sosialisasi Kampus Merdeka 2025',
                'event_category_id'    => 5,
                'thumbnail'            => 'storage/event_covers/sample5.jpg',
                'tempat_pelaksanaan'   => 'Auditorium Utama',
                'tanggal_pelaksanaan'  => '2025-12-05',
                'waktu_pelaksanaan'    => '14:00:00',
                'tanggal_pendaftaran'  => '2025-11-29',
                'deskripsi'            => 'Sosialisasi program kampus merdeka untuk mahasiswa baru.',
                'created_by_user_id'           => 1,
            ]
        ];

        foreach ($events as $ev) {
            $ev['slug'] = Str::slug($ev['nama_event']); // Generate slug SEO-friendly
            Event::create($ev);
        }
    }
}

