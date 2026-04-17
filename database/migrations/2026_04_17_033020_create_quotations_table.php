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
        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id');
            $table->uuid('user_id'); // Admin who created the quotation
            $table->decimal('proposed_price', 15, 2);
            $table->decimal('dp_amount', 15, 2);
            $table->text('note_to_customer');
            $table->enum('status', ['draft', 'sent', 'accepted', 'revision', 'expired', 'rejected'])->default('draft');
            $table->timestamp('valid_until');
            $table->text('revision_note')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
