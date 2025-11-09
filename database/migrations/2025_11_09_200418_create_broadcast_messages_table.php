<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');

            // FIXED: Changed from foreignUuid to foreignId for user reference
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');


            // Message details
            $table->string('title');
            $table->text('message');
            $table->json('channels'); // ['email', 'sms']

            // Targeting
            $table->enum('recipient_type', ['all_tenants', 'property', 'unit', 'specific_users']);
            $table->json('recipient_filters')->nullable(); // property_ids, unit_ids, user_ids
            $table->integer('recipient_count')->default(0);

            // Scheduling
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Statistics
            $table->integer('emails_sent')->default(0);
            $table->integer('emails_delivered')->default(0);
            $table->integer('emails_failed')->default(0);
            $table->integer('sms_sent')->default(0);
            $table->integer('sms_delivered')->default(0);
            $table->integer('sms_failed')->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['organization_id', 'status']);
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_messages');
    }
};
