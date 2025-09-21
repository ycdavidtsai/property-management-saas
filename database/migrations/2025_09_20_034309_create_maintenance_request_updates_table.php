<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_request_updates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('maintenance_request_id');
            $table->unsignedBigInteger('user_id'); // BIGINT to match users table
            $table->text('message');
            $table->json('photos')->nullable(); // array of photo paths
            $table->enum('type', ['comment', 'status_change', 'assignment', 'completion'])->default('comment');
            $table->json('metadata')->nullable(); // for storing additional data like old/new status
            $table->boolean('is_internal')->default(false); // internal notes vs tenant-visible
            $table->timestamps();

            $table->foreign('maintenance_request_id')->references('id')->on('maintenance_requests')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Custom shorter index name to avoid MySQL's 64-character limit
            $table->index(['maintenance_request_id', 'created_at'], 'mr_updates_request_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_request_updates');
    }
};
