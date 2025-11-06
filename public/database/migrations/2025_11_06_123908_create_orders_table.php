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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('model_id')->constrained('models')->cascadeOnDelete();
            $table->foreignId('engine_id')->nullable()->constrained('engines')->nullOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->text('comment')->nullable();
            $table->enum('status', ['new', 'sent', 'processed'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
