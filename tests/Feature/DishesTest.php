<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DishesTest extends TestCase
{
    use DatabaseMigrations;
    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function testIndex(): void
    {
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get('/api/dishes');
        $response->assertStatus(200);
    }
    public function testCreation(): void
    {
        Storage::fake('local');
        $dish = Dish::factory()->make();
        $fakeImg = File::create('test-image.jpeg', 100);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->post('/api/dishes', [
            'name' => $dish->name,
            'img' => $fakeImg,
            'category_id' => $dish->category_id,
            'calories' => $dish->calories,
            'price' => $dish->price,
            'composition' => $dish->composition
        ]);
        $this->assertDatabaseHas('dishes', [
            'name' => $dish->name
        ]);
        $dish = Dish::all()->last();
        Storage::disk('local')->assertExists($dish->img);
        $response->assertStatus(200);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->post('/api/dishes', []);
        $response->assertStatus(302);
        $response = $this->actingAs(User::factory()->create(['role_id' => 3]))->post('/api/dishes', [
            'name' => "testName",
            'img' => $fakeImg,
            'category_id' => $dish->category_id,
            'calories' => $dish->calories,
            'price' => $dish->price,
            'composition' => $dish->composition
        ]);
        $response->assertStatus(403);
    }
    public function testShow(): void
    {
        $randDish = Dish::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/dishes/{$randDish->id}");
        $response->assertStatus(200);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/dishes/99999999");
        $response->assertStatus(404);
    }
    public function testEdit(): void
    {
        $randDish = Dish::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/dishes/{$randDish->id}/edit");
        $response->assertStatus(200);
        $response = $this->actingAs(User::factory()->create(['role_id' => 3]))->get("/api/dishes/{$randDish->id}/edit");
        $response->assertStatus(403);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/dishes/99999999/edit");
        $response->assertStatus(404);
    }
    public function testUpdate(): void
    {
        Storage::fake('local');
        $dish = Dish::factory()->make();
        $fakeImg = File::create('test-image.jpeg', 100);
        $randDish = Dish::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->put("/api/dishes/{$randDish->id}/update", [
            'name' => $dish->name,
            'img' => $fakeImg,
            'category_id' => $dish->category_id,
            'calories' => $dish->calories,
            'price' => $dish->price,
            'composition' => $dish->composition
            ]);
        $this->assertDatabaseHas('dishes', [
            'name' => $dish->name,
        ]);
        $dish = Dish::where('name', '=', $dish->name)->first();
        Storage::disk('local')->assertExists($dish->img);
        $response->assertStatus(200);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->put("/api/dishes/999999/update");
        $response->assertStatus(404);
        $response = $this->actingAs(User::factory()->create(['role_id' => 3]))->put("/api/dishes/{$randDish->id}/update");
        $response->assertStatus(403);
    }
    public function testDelete(): void
    {
        $randDish = Dish::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->delete("/api/dishes/{$randDish->id}/delete");
        $this->assertDatabaseMissing('dishes', [
            'name' => $randDish->name
        ]);
        $response->assertStatus(200);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->delete("/api/dishes/9999999/delete");
        $response->assertStatus(404);
        $response = $this->actingAs(User::factory()->create(['role_id' => 3]))->delete("/api/dishes/{$randDish->id}/delete");
        $response->assertStatus(403);
    }
}
