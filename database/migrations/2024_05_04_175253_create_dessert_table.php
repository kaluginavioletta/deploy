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
        Schema::create('dessert', function (Blueprint $table) {
            $table->id('id_dessert');
            $table->string('name_dessert', 255);
            $table->text('compound_dessert');
            $table->integer('price_dessert');
            $table->integer('discounted_price_dessert');
            $table->string('img_dessert', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dessert');
    }
};
