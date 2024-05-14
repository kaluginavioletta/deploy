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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email', 'email_verified_at']); // Удаляем атрибуты email и email_verified_at
            $table->string('surname');
            $table->string('patronymic');
            $table->string('tel', 15)->unique();
            $table->string('login')->unique();
            $table->foreignId('id_role')->constrained('roles', 'id_role')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_order')->nullable()->constrained('orders', 'id_order')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique()->nullable();;
            $table->timestamp('email_verified_at')->nullable();

            $table->dropColumn(['surname', 'patronymic', 'tel', 'login', 'id_role', 'id_order']);
            $table->dropForeign(['id_role']);
            $table->dropForeign(['id_order']);
        });
    }
};
