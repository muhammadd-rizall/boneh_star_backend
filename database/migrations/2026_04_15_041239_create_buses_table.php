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
        Schema::create('buses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bus_category_id');
            $table->string('plat_number')->unique();
            $table->string('name');
            $table->integer('seat_count');
            $table->integer('production_year');
            $table->string('brand')->nullable();
            $table->enum('status', ['available', 'booked', 'maintenance', 'inactive']);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('bus_category_id')
                ->references('id')
                ->on('bus_categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
