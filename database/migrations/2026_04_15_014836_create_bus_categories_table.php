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
        Schema::create('bus_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('min_capacity');
            $table->integer('max_capacity');
            $table->integer('price_inside_min')->nullable();
            $table->integer('price_inside_max')->nullable();
            $table->integer('price_outside')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_categories');
    }
};
