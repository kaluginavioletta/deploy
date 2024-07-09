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
        Schema::create('drinkables', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('compound');
            $table->integer('price');
            $table->integer('percent_discount');
            $table->integer('discounted_price');
            $table->string('img', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drinkables');
    }
};
