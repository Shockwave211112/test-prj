<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dish_orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('dish_id');
            $table->unsignedBigInteger('order_id');

            $table->index('dish_id', 'dish_order_dish_idx');
            $table->index('order_id', 'dish_order_order_idx');

            $table->foreign('dish_id', 'dish_order_dish_fk')->on('dishes')->references('id');
            $table->foreign('order_id', 'dish_order_order_fk')->on('orders')->references('id')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dish_orders');
    }
};
