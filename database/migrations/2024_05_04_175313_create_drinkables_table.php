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
            $table->id('id_drink');
            $table->string('name_drink', 255);
            $table->text('compound_drink');
            $table->integer('price_drink');
            $table->integer('discounted_price_drink');
            $table->string('img_drink', 255);
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
