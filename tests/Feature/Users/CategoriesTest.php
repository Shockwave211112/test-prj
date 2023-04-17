<?php

namespace Tests\Feature\Users;

use App\Models\Category;
use App\Models\User;
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
        $category = Category::factory()->make();
        $fakeImg = UploadedFile::fake()->image('image_fake.jpeg');
        dd($fakeImg);
        dd(Storage::fake('img'));
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->post('/api/categories', [
            'name' => $category->name,
            'img' => $fakeImg
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => $category->name,
            'img' => $category->img
        ]);
        Storage::disk('img')->assertExists('category', $fakeImg->hashName());
        $response->assertStatus(200);
    }
    public function testShow(): void
    {
        $randCategory = Category::find(random_int(1, Category::count()));
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/categories/{$randCategory->id}");
        $response->assertStatus(200);
    }
//    public function testEdit(): void
//    {
//        $randCategory = Category::find(random_int(1, Category::count()));
//        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/categories/{$randCategory->id}/edit");
//        $response->assertStatus(200);
//    }
//    public function testUpdate(): void
//    {
//        $randCategory = Category::find(random_int(1, Category::count()));
//        $category = Category::factory()->make();
//        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->put("/api/categories/{$randCategory->id}/update", [$category]);
//        $this->assertDatabaseHas('categories', [
//            'name' => $category->name,
//            'email' => $category->email
//        ]);
//        $response->assertStatus(200);
//    }
//    public function testDelete(): void
//    {
//        $randCategory = Category::find(random_int(1, Category::count()));
//        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->delete("/api/users/{$randCategory->id}/delete");
//        $this->assertDatabaseMissing('users', [
//            'name' => $randCategory->name,
//            'email' => $randCategory->email
//        ]);
//        $response->assertStatus(200);
//    }
}
