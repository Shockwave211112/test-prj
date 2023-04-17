<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\DishOrder;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testIndex(): void
    {
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get('/api/orders');
        $response->assertStatus(200);
        $response = $this->actingAs(User::factory()->create(['role_id' => 3]))->get('/api/orders');
        $response->assertStatus(403);
    }
    public function testCreation(): void
    {
        $number = random_int(1, 9999);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->post('/api/orders', [
            "number" => $number,
            "user_id" => 1
        ]);
        $this->assertDatabaseHas('orders', [
            "number" => $number
        ]);

        $response->assertStatus(200);
    }
    public function testShow(): void
    {
        $randOrder = Order::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/orders/{$randOrder->id}");
        $response->assertStatus(200);
    }
    public function testEdit(): void
    {
        $randOrder = Order::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->get("/api/orders/{$randOrder->id}/edit");
        $response->assertStatus(200);
    }
    public function testAddDish(): void
    {
        $randOrder = Order::all()->random();
        $dish = Dish::all()->random();
        $count = random_int(1, 10);
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->put("/api/orders/{$randOrder->id}/edit/updateDish", [
            'dish' => $dish->id,
            'count' => $count
        ]);
        $this->assertDatabaseHas('dish_orders', [
            'dish_id' => $dish->id,
            'count' => $count,
            'order_id' => $randOrder->id
        ]);

        $response->assertStatus(200);
    }
    public function testDelDish(): void
    {
        $randOrder = Order::all()->random();
        $dish = DishOrder::where('order_id', '=', $randOrder->id)->first();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->put("/api/orders/{$randOrder->id}/edit/{$dish->dish_id}/del");
        $this->assertDatabaseMissing('dish_orders', [
            'dish_id' => $dish->dish_id,
            'order_id' => $randOrder->id
        ]);
        $response->assertStatus(200);
    }
    public function testUpdate(): void
    {
        $randOrder = Order::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->put("/api/orders/{$randOrder->id}/update", [
            'is_closed' => true
            ]);
        $this->assertDatabaseHas('orders', [
            'id' => $randOrder->id,
            'is_closed' => true
        ]);
        $response->assertStatus(200);
    }
    public function testDelete(): void
    {
        $randOrder = Order::all()->random();
        $response = $this->actingAs(User::factory()->create(['role_id' => 1]))->delete("/api/orders/{$randOrder->id}/delete");
        $this->assertDatabaseMissing('orders', [
            'id' => $randOrder->id
        ]);
        $response->assertStatus(200);
    }
}
