<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCommittee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    /**
     * @test
     */
    public function mahasiswa_biasa_tidak_bisa_masuk_halaman_admin()
    {
        // 1. Arrange: Buat user mahasiswa biasa
        $mahasiswa = User::factory()->create([
            'role' => User::LEVEL_MAHASISWA,
            'boleh_jadi_penanggung_jawab' => false,
            'username' => 'mahasiswa_test',
        ]);

        // 2. Act: Coba akses rute /admin/lomba
        $response = $this->actingAs($mahasiswa)->get('/admin/lomba');

        // 3. Assert: Harus dilarang (403 Forbidden atau redirect)
        // Di aplikasi ini, middleware RoleMiddleware melempar 403 atau 404.
        $this->assertTrue(
            in_array($response->status(), [403, 404, 302]),
            'Mahasiswa biasa harusnya ditolak dari /admin/lomba. Status: ' . $response->status()
        );
    }

    /**
     * @test
     */
    public function ketua_pelaksana_bisa_masuk_halaman_admin()
    {
        // 1. Arrange: Buat user mahasiswa biasa
        $ketua = User::factory()->create([
            'role' => User::LEVEL_MAHASISWA, 
            'boleh_jadi_penanggung_jawab' => false,
            'username' => 'ketua_test',
        ]);

        // Jadikan dia ketua pelaksana di sebuah event
        $creator = User::factory()->create(['username' => 'creator_test']);
        $event = Event::create([
            'nama_event' => 'Lomba Test',
            'slug' => 'lomba-test-123',
            'created_by_user_id' => $creator->id,
            'ketua_pelaksana_user_id' => $ketua->id,
            'tipe_bayar' => 'gratis',
            'partisipasi_skala' => 'prodi',
            'tempat_pelaksanaan' => 'Gedung A',
        ]);

        // 2. Act: Akses rute /admin/lomba
        $response = $this->actingAs($ketua)->get('/admin/lomba');

        // 3. Assert: Harus berhasil (200 OK) karena dia diakui sebagai staff/Ketua Pelaksana
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function panitia_bendahara_bisa_masuk_halaman_payment()
    {
        $bendahara = User::factory()->create([
            'role' => User::LEVEL_MAHASISWA,
            'boleh_jadi_penanggung_jawab' => false,
            'username' => 'bendahara_test',
        ]);

        $creator2 = User::factory()->create(['username' => 'creator_test_2']);
        $event = Event::create([
            'nama_event' => 'Lomba Test 2',
            'slug' => 'lomba-test-456',
            'created_by_user_id' => $creator2->id,
            'tipe_bayar' => 'gratis',
            'partisipasi_skala' => 'prodi',
            'tempat_pelaksanaan' => 'Gedung B',
        ]);

        // Jadikan dia Bendahara
        EventCommittee::create([
            'event_id' => $event->id,
            'user_id' => $bendahara->id,
            'jabatan' => 'Bendahara Lomba',
            'is_custom' => false,
            'permissions' => ['kelola_pembayaran']
        ]);


        // 2. Act: Akses rute /admin/payment
        $response = $this->actingAs($bendahara)->get('/admin/payment');

        // 3. Assert: 200 OK
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function administrator_dan_pengurus_melihat_tombol_dashboard_admin_di_navigasi()
    {
        $admin = User::factory()->create([
            'role' => User::LEVEL_SUPERUSER,
            'username' => 'superuser_nav_test',
        ]);

        $response = $this->actingAs($admin)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
    }

    /**
     * @test
     */
    public function mahasiswa_biasa_tidak_melihat_tombol_dashboard_admin_di_navigasi()
    {
        $mahasiswa = User::factory()->create([
            'role' => User::LEVEL_MAHASISWA,
            'boleh_jadi_penanggung_jawab' => false,
            'username' => 'mahasiswa_nav_test',
        ]);

        $response = $this->actingAs($mahasiswa)->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Dashboard Admin');
    }
}
