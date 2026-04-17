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
        Schema::create('booking_buses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id');
            $table->uuid('bus_id');
            $table->uuid('driver_id');
            $table->integer('seat_count');
            $table->text('assignment_notes')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_buses');
    }
};
