<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitor_stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('franchise_id')->nullable()->constrained('franchises');
            $table->integer('opened_year')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitor_stores');
    }
};
