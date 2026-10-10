<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    /**
     * @test
     */
    public function superuser_can_access_dashboard()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'role' => User::LEVEL_SUPERUSER,
            'username' => 'superuser_dash_test',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function baak_can_access_dashboard()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'role' => User::LEVEL_BAAK,
            'username' => 'baak_dash_test',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function kemahasiswaan_can_access_dashboard()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'role' => User::LEVEL_KEMAHASISWAAN,
            'username' => 'kemahasiswaan_dash_test',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }
}
