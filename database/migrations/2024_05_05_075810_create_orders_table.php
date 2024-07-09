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
            $table->foreignId('id_address')->constrained('addresses', 'id_address')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_status')->constrained('statuses', 'id_status')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('total_price'); // 8 цифр всего, 2 после запятой
            $table->morphs('product');
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
