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
        Schema::create('properties', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->string('name');
            $table->text('address');
            $table->enum('type', ['single_family', 'multi_family', 'apartment', 'commercial']);
            $table->integer('total_units')->default(1);
            $table->json('amenities')->nullable();
            $table->json('documents')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->index(['organization_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
