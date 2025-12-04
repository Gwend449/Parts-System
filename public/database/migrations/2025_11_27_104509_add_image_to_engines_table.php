<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('engines', function (Blueprint $table) {
            Schema::table('engines', function (Blueprint $table) {
                $table->string('image')->nullable()->after('oem');
            });
        });
    }

    public function down(): void
    {
        Schema::table('engines', function (Blueprint $table) {
            Schema::table('engines', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        });
    }
};
