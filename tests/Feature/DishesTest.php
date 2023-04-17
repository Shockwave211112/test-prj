<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dish;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DishesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
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
        $this->assertDatabaseHas('categories', [
            'name' => $dish->name
        ]);
        $dish = Dish::all()->last();
        Storage::disk('local')->assertExists($dish->img);
        $response->assertStatus(200);
    }
    public function testShow(): void
    {
        $randDish = Dish::find(random_int(1, Dish::count()));
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/dish/{$randDish->id}");
        $response->assertStatus(200);
    }
    public function testEdit(): void
    {
        $randDish = Dish::find(random_int(1, Dish::count()));
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/dish/{$randDish->id}/edit");
        $response->assertStatus(200);
    }
    public function testUpdate(): void
    {
        Storage::fake('local');
        $dish = Dish::factory()->make();
        $fakeImg = File::create('test-image.jpeg', 100);
        $randDish = Dish::find(random_int(1, Dish::count()));
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->put("/api/categories/{$randDish->id}/update", [
            "name" => $dish->name,
            "img" => $fakeImg
            ]);
        $this->assertDatabaseHas('categories', [
            'name' => $dish->name
        ]);
        $dish = Dish::where('name', '=', $dish->name)->first();
        Storage::disk('local')->assertExists($dish->img);
        $response->assertStatus(200);
    }
    public function testDelete(): void
    {
        $randDish = Dish::find(random_int(1, Dish::count()));
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->delete("/api/users/{$randDish->id}/delete");
        $this->assertDatabaseMissing('users', [
            'name' => $randDish->name,
            'email' => $randDish->email
        ]);
        $response->assertStatus(200);
    }
}
