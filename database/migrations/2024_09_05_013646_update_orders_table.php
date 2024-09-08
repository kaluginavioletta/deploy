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
        Schema::table('orders', function (Blueprint $table) {
            // Проверяем, существует ли столбец id_cart перед добавлением внешнего ключа
            if (!Schema::hasColumn('orders', 'id_cart')) {
                $table->foreignId('id_cart')->nullable()->constrained('cart_orders', 'id_cart')->onUpdate('cascade')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Проверяем, существует ли внешний ключ перед его удалением
            if (Schema::hasColumn('orders', 'id_cart')) {
                $table->dropForeign(['id_cart']);
                $table->dropColumn('id_cart');
            }
        });
    }
};
