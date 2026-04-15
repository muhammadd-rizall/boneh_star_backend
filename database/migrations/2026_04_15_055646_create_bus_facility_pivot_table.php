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
        Schema::create('bus_facility', function (Blueprint $table) {
            $table->uuid('bus_id');
            $table->uuid('bus_facility_id');

            // Menjadikan gabungan kedua ID sebagai primary key
            $table->primary(['bus_id', 'bus_facility_id']);

            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
            $table->foreign('bus_facility_id')->references('id')->on('bus_facilities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_facility');


    }
};
