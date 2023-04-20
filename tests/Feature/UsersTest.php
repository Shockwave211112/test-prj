<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use DatabaseMigrations;
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }
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
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email
        ]);
        $response->assertStatus(200);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->post('/api/users');
        $response->assertStatus(302);
    }
    public function testShow(): void
    {
        $randomUser = User::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/users/{$randomUser->id}");
        $response->assertStatus(200);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/users/99999999");
        $response->assertStatus(404);
    }
    public function testEdit(): void
    {
        $randomUser = User::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/users/{$randomUser->id}/edit");
        $response->assertStatus(200);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/users/99999999/edit");
        $response->assertStatus(404);
    }
    public function testUpdate(): void
    {
        $randomUser = User::all()->random();
        $user = User::factory()->make();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->put("/api/users/{$randomUser->id}/update", [
            "name" => $user->name,
            "email" => $user->email
        ]);
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email
        ]);
        $response->assertStatus(200);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->put("/api/users/99999999/update");
        $response->assertStatus(404);
    }
    public function testDelete(): void
    {
        $randomUser = User::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->delete("/api/users/{$randomUser->id}/delete");
        $this->assertDatabaseMissing('users', [
            'name' => $randomUser->name,
            'email' => $randomUser->email
        ]);
        $response->assertStatus(200);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->delete("/api/users/99999999/delete");
        $response->assertStatus(404);
    }
}
