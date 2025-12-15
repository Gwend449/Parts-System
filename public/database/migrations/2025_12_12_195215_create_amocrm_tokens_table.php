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
        Schema::create('amocrm_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->nullable(); // subdomain e.g. example.amocrm.ru
            $table->string('access_token', 1000)->nullable();
            $table->string('refresh_token', 1000)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('raw')->nullable(); // полный ответ токена для отладки
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amocrm_tokens');
    }
};
