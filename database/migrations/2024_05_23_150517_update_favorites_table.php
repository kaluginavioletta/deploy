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
        Schema::table('favorites', function (Blueprint $table) {
            // Удаление столбцов
            $table->dropColumn('id_sushi');
            $table->dropColumn('id_drink');
            $table->dropColumn('id_dessert');

            // Добавление столбцов
            $table->foreignId('id_product')->constrained('products', 'id_product')->nullable();
            $table->string('type_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            // Откат изменений
            $table->unsignedBigInteger('id_sushi');
            $table->unsignedBigInteger('id_drink');
            $table->unsignedBigInteger('id_dessert');

            $table->dropForeign(['id_product']);
            $table->dropColumn('id_product');
            $table->dropColumn('type_product');
        });
    }
};
