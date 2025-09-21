<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id(); // Use auto-incrementing BIGINT instead of UUID
            $table->uuid('organization_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('business_type')->nullable(); // plumbing, electrical, general, etc.
            $table->text('description')->nullable();
            $table->json('specialties')->nullable(); // array of specialty areas
            $table->boolean('is_active')->default(true);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->index(['organization_id', 'is_active'], 'vendors_org_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
