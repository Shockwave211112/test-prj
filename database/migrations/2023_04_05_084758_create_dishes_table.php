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
        Schema::create('dishes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('img')->nullable();
            $table->text('composition');
            $table->unsignedBigInteger('calories');
            $table->unsignedBigInteger('price')->default(0);

            $table->unsignedBigInteger('category_id');
            $table->index('category_id', 'dish_category_idx');
            $table->foreign('category_id', 'dish_category_fk')->references('id')->on('categories')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dishes');
    }
};
