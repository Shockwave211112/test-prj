<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegister(): void
    {
        $user = User::factory()->make();
        $response = $this->post('/api/register',
            [
                'name' => $user->getAttributes()['name'],
                'email' => $user->getAttributes()['email'],
                'password' => $user->getAttributes()['password'],
                'password_confirmation' => $user->getAttributes()['password'],
                'pin_code' => $user->getAttributes()['pin_code'],
                'role_id' => $user->getAttributes()['role_id']
            ]
        );
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('data.token', csrf_token());
    }

    public function testAdminAuth(): void
    {
        $user = User::factory()->create(['password' => bcrypt($password = 'abobus1234'), 'role_id' => 1]);
        $response = $this->post('/api/login',
            [
                'email' => $user->email,
                'password' => $password,
            ]
        );
        $response->assertStatus(200);
        $response->assertJsonPath('data.token', csrf_token());
    }

    public function testWaiterAuth(): void
    {
        $user = User::factory()->create(['role_id' => 3]);
        $response = $this->post('/api/login',
            [
                'email' => $user->email,
                'password' => $user->password,
            ]
        );
        $response->assertStatus(401);

        $response = $this->post('/api/login',
            [
                'pin_code' => $user->pin_code
            ]
        );
        $response->assertStatus(200);
        $response->assertJsonPath('data.token', csrf_token());

        $response = $this->actingAs($user)->post('/api/logout');
        $response->assertStatus(200);
    }
    public function testLogout(): void
    {
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->post("/api/logout");
        $response->assertStatus(200);
    }
}
