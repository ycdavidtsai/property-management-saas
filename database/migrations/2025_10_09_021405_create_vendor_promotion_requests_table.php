<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_promotion_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');

            // Request details (initiated by vendor user)
            $table->foreignId('requested_by_user_id')->constrained('users')->onDelete('cascade');
            $table->text('request_message')->nullable();
            $table->timestamp('requested_at');

            // Review details
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            // Fee details (for future monetization)
            $table->decimal('fee_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'waived'])
                ->default('waived'); // Free for now
            $table->timestamp('payment_completed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('vendor_id');
            $table->index('status');
            $table->index('requested_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_promotion_requests');
    }
};
