<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');

            // FIXED: Changed from foreignUuid to foreignId for user references
            $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');


            // Notification details
            $table->string('type'); // email, sms, in_app
            $table->string('channel'); // maintenance, payment, lease, general, broadcast
            $table->string('subject')->nullable();
            $table->text('content');

            // Delivery tracking
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'read'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();

            // Related records (polymorphic)
            $table->string('notifiable_type')->nullable();
            $table->uuid('notifiable_id')->nullable();

            // Provider tracking
            $table->string('provider_id')->nullable(); // Twilio SID or email message ID
            $table->json('provider_response')->nullable();
            $table->text('error_message')->nullable();

            // Metadata
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['organization_id', 'type', 'status']);
            $table->index(['to_user_id', 'status']);
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
