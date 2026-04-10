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
            $table->string('booking_code')->unique();
            $table->uuid('customer_id');
            $table->string('trip_type');
            $table->string('destination');
            $table->string('pickup_location');
            $table->decimal('total_price', 15, 2);
            $table->enum('payment_status', ['unpaid', 'dp_paid', 'paid']);
            $table->enum('status', ['request', 'waiting', 'approved', 'completed', 'cancelled']);
            $table->uuid('approved_by')->nullable();
            $table->datetime('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
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
