<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginAccessTest extends TestCase
{
    /**
     * @test
     */
    public function login_page_can_be_rendered_without_doors_error()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Login Mahasiswa');
    }

    /**
     * @test
     */
    public function admin_login_page_can_be_rendered_without_doors_error()
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Login Admin');
    }
}
