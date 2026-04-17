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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('activity_id');
            $table->uuid('route_type_id');
            $table->string('booking_code')->unique();
            $table->dateTime('departure_date');
            $table->dateTime('return_date')->nullable();
            $table->text('origin');
            $table->text('full_route')->nullable();
            $table->integer('passenger_count');
            $table->integer('bus_count_requested');
            $table->text('special_notes')->nullable();
            $table->enum('status', ['pending', 'quoted', 'confirmed', 'ongoing', 'completed', 'cancelled'])->default('pending');
            $table->enum('cancelled_by',['customer', 'admin'])->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activity_types')->onDelete('cascade');
            $table->foreign('route_type_id')->references('id')->on('route_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
