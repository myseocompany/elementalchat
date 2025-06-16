<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitor_stores', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('competitor_stores', function (Blueprint $table) {
            $table->dropColumn(['address', 'latitude', 'longitude']);
        });
    }
};
