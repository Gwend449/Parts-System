<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('engines', function (Blueprint $table) {
            $table->id();

            // Уникальный идентификатор из колонки A
            $table->string('slug')->index();

            // Название объявления (колонка L)
            $table->string('title')->nullable();

            // Цена (колонка J)
            $table->decimal('price', 10, 2)->nullable();

            // Производитель / марка (колонка U)
            $table->string('brand')->nullable();

            // Модель + кузов + генерация (колонка Y — CSV/строка)
            $table->text('fit_for')->nullable();

            // Описание без HTML (колонка I)
            $table->text('description')->nullable();

            // OEM номер (колонка V)
            $table->string('oem')->nullable();

            $table->unique(['brand', 'oem']);

            // Дата добавления из Excel не нужна — используем timestamps
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engines');
    }
};
