<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Dish;
use App\Models\DishOrder;
use App\Models\Order;
use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'Super Admin'
        ]);
        Role::create([
            'name' => 'Admin'
        ]);
        Role::create([
            'name' => 'Waiter'
        ]);
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.ru',
            'password' => bcrypt('admin'),
            'pin_code' => 1111,
            'role_id' => 1
        ]);
        User::factory(10)->create();
        Category::factory(5)->create();
        Dish::factory(10)->create();
        Order::factory(3)->create();
        DishOrder::factory(10)->create();
    }
}
