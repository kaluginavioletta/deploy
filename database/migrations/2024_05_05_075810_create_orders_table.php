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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order');
            $table->foreignId('id_drink')->constrained('drinkables', 'id_drink')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_sushi')->constrained('sushi', 'id_sushi')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_dessert')->constrained('dessert', 'id_dessert')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('count_order');
            $table->foreignId('id_address')->constrained('addresses', 'id_address')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_status')->constrained('statuses', 'id_status')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('price_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
