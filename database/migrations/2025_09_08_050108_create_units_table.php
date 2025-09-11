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
        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('property_id');
            $table->string('unit_number');
            $table->integer('bedrooms')->nullable();
            $table->decimal('bathrooms', 3, 1)->nullable();
            $table->integer('square_feet')->nullable();
            $table->decimal('rent_amount', 10, 2);
            $table->enum('status', ['vacant', 'occupied', 'maintenance'])->default('vacant');
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->unique(['property_id', 'unit_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
