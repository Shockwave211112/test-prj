<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsersTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testIndex(): void
    {
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get('/api/users');
        $response->assertStatus(200);
    }
    public function testCreation(): void
    {
        $user = User::factory()->make();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->post('/api/users', [
            'name' => $user->name,
            'pin_code' => $user->pin_code,
            'email' => $user->email,
            'password' => $user->password,
            'password_confirmation' => $user->password,
            'role_id' => $user->role_id,
        ]);
        $response->assertStatus(200);
    }
    public function testShow(): void
    {
        $randomUser = User::first();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/users/{$randomUser->id}");
        $response->assertStatus(200);
    }
    public function testEdit(): void
    {
        $randomUser = User::first();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/users/{$randomUser->id}/edit");
        $response->assertStatus(200);
    }
    public function testUpdate(): void
    {
        $randomUser = User::first();
        $user = User::factory()->make();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->put("/api/users/{$randomUser->id}/update", [$user]);
        $response->assertStatus(200);
    }
    public function testDelete(): void
    {
        $randomUser = User::first();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->delete("/api/users/{$randomUser->id}/delete");
        $response->assertStatus(200);
    }
}
