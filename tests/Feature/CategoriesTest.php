<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testIndex(): void
    {
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get('/api/categories');
        $response->assertStatus(200);
    }
    public function testCreation(): void
    {
        Storage::fake('local');
        $category = Category::factory()->make();
        $fakeImg = File::create('test-image.jpeg', 100);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->post('/api/categories', [
            'name' => $category->name,
            'img' => $fakeImg
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => $category->name
        ]);
        $category = Category::all()->last();
        Storage::disk('local')->assertExists($category->img);
        $response->assertStatus(200);
    }
    public function testShow(): void
    {
        $randCategory = Category::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/categories/{$randCategory->id}");
        $response->assertStatus(200);
    }
    public function testEdit(): void
    {
        $randCategory = Category::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/categories/{$randCategory->id}/edit");
        $response->assertStatus(200);
    }
    public function testUpdate(): void
    {
        Storage::fake('local');
        $category = Category::factory()->make();
        $fakeImg = File::create('test-image.jpeg', 100);
        $randCategory = Category::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->put("/api/categories/{$randCategory->id}/update", [
            "name" => $category->name,
            "img" => $fakeImg
            ]);
        $this->assertDatabaseHas('categories', [
            'name' => $category->name
        ]);
        $category = Category::where('name', '=', $category->name)->first();
        Storage::disk('local')->assertExists($category->img);
        $response->assertStatus(200);
    }
    public function testDelete(): void
    {
        $randCategory = Category::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->delete("/api/categories/{$randCategory->id}/delete");
        $this->assertDatabaseMissing('categories', [
            'name' => $randCategory->name
        ]);
        $response->assertStatus(200);
    }
}
