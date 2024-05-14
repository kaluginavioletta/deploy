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
        Schema::table('sushi', function (Blueprint $table) {
                $table->integer('grams')->after('discounted_price_sushi')->default(0); // Добавляем столбец grams после discounted_price_sushi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sushi', function (Blueprint $table) {
            $table->dropColumn('grams');
        });
    }
};
