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
            $table->integer('percent_discount_sushi')->after('price_sushi')->default(0);
            $table->foreignId('id_view_sushi')->nullable()->constrained('view_sushi', 'id_view_sushi')->after('name_sushi');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sushi', function (Blueprint $table) {
            $table->dropColumn('percent_discount_sushi');
            $table->dropForeign(['id_view_sushi']);
            $table->dropColumn('id_view_sushi');
        });
    }
};
