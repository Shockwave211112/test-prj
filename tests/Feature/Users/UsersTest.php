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
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email
        ]);
        $response->assertStatus(200);
    }
    public function testShow(): void
    {
        $randomUser = User::find(random_int(2, User::count()));
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/users/{$randomUser->id}");
        $response->assertStatus(200);
    }
    public function testEdit(): void
    {
        $randomUser = User::find(random_int(2, User::count()));
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/users/{$randomUser->id}/edit");
        $response->assertStatus(200);
    }
    public function testUpdate(): void
    {
        $randomUser = User::find(random_int(2, User::count()));
        $user = User::factory()->make();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->put("/api/users/{$randomUser->id}/update", [$user]);
        $this->assertDatabaseHas('users', [
            'name' => $randomUser->name,
            'email' => $randomUser->email
        ]);
        $response->assertStatus(200);
    }
    public function testDelete(): void
    {
        $randomUser = User::find(random_int(2, User::count()));
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->delete("/api/users/{$randomUser->id}/delete");
        $this->assertSoftDeleted('users', [
            'name' => $randomUser->name,
            'email' => $randomUser->email
        ]);
        $response->assertStatus(200);
    }
}
