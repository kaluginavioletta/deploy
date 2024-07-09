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
        Schema::table('dessert', function (Blueprint $table) {
            $table->foreignId('id_view_dessert')->constrained('view_dessert', 'id_view_dessert')->after('name_dessert')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dessert', function (Blueprint $table) {
            $table->dropForeign(['id_view_dessert']);
            $table->dropColumn('id_view_dessert');
        });
    }
};
