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
        Schema::create('reset_pins', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('email');
            $table->unsignedBigInteger('pin_code')->unique();
            $table->dateTime('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reset_pins');
    }
};
