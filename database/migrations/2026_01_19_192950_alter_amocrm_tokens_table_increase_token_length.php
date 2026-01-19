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
        Schema::table('amocrm_tokens', function (Blueprint $table) {
            // Изменяем тип колонок на TEXT для хранения длинных токенов
            $table->text('access_token')->nullable()->change();
            $table->text('refresh_token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amocrm_tokens', function (Blueprint $table) {
            // Возвращаем обратно к VARCHAR(1000)
            $table->string('access_token', 1000)->nullable()->change();
            $table->string('refresh_token', 1000)->nullable()->change();
        });
    }
};
