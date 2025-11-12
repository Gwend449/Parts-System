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
            $table->foreignId('model_id')->constrained()->cascadeOnDelete();
            $table->string('code')->index();
            $table->string('volume')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('engine_type')->nullable();
            $table->string('drive')->nullable();
            $table->string('transmission')->nullable();
            $table->string('oem')->nullable();
            $table->text('note')->nullable();
            $table->decimal('price', 10, 2)->nullable();
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
