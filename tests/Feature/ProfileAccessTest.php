<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ProfileAccessTest extends TestCase
{
    /**
     * @test
     */
    public function profile_page_loads_successfully_for_superuser()
    {
        $user = User::factory()->create([
            'role' => User::LEVEL_SUPERUSER,
            'username' => 'admin_profile_test',
        ]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Profil Saya');
        $response->assertSee('Status Administrator');
    }

    /**
     * @test
     */
    public function profile_page_loads_successfully_for_mahasiswa()
    {
        $user = User::factory()->create([
            'role' => User::LEVEL_MAHASISWA,
            'username' => 'mahasiswa_profile_test',
        ]);

        $this->withoutExceptionHandling();
        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Profil Saya');
    }
}
