<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('property_id');
            $table->uuid('unit_id')->nullable();
            $table->unsignedBigInteger('tenant_id'); // User who submitted (BIGINT from users table)
            $table->unsignedBigInteger('assigned_vendor_id')->nullable(); // If vendors table uses BIGINT
            $table->unsignedBigInteger('assigned_by')->nullable(); // User who assigned (BIGINT from users table)
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['emergency', 'high', 'normal', 'low'])->default('normal');
            $table->enum('status', ['submitted', 'assigned', 'in_progress', 'completed', 'closed'])->default('submitted');
            $table->string('category')->nullable(); // plumbing, electrical, appliance, etc.
            $table->json('photos')->nullable(); // array of photo paths
            $table->timestamp('preferred_date')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->text('completion_notes')->nullable();
            $table->integer('tenant_rating')->nullable(); // 1-5 rating
            $table->text('tenant_feedback')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_vendor_id')->references('id')->on('vendors')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');

            // Custom shorter index names to avoid MySQL's 64-character limit
            $table->index(['organization_id', 'status'], 'mr_org_status_idx');
            $table->index(['property_id', 'status'], 'mr_property_status_idx');
            $table->index(['tenant_id', 'status'], 'mr_tenant_status_idx');
            $table->index(['assigned_vendor_id', 'status'], 'mr_vendor_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
