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
        Schema::table('drinkables', function (Blueprint $table) {
            $table->foreignId('id_view_drink')->constrained('view_drinkables', 'id_view_drink')->after('name_drink')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drinkables', function (Blueprint $table) {
            $table->dropForeign(['id_view_drink']);
            $table->dropColumn('id_view_drink');
        });
    }
};
