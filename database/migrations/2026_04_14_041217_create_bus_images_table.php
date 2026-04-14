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
        Schema::create('bus_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bus_id')->constrained('buses')->onDelete('cascade');
            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // (Opsional) Mengubah kolom image di tabel buses menjadi nullable karena sekarang gambar ada di tabel bus_images
        Schema::table('buses', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_images');

        Schema::table('buses', function (Blueprint $table) {
            $table->string('image')->nullable(false)->change();
        });
    }
};
