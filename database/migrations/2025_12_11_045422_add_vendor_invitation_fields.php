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
        Schema::table('vendors', function (Blueprint $table) {
            // Registration/Setup status
            $table->string('setup_status')->default('active')->after('is_active');
            // Values: pending_setup (invited, not registered), pending_approval (self-registered), active, rejected

            // Invitation fields (for landlord-initiated flow)
            $table->string('invitation_token', 64)->nullable()->after('setup_status');
            $table->timestamp('invitation_sent_at')->nullable()->after('invitation_token');
            $table->timestamp('invitation_expires_at')->nullable()->after('invitation_sent_at');
            $table->unsignedTinyInteger('invitation_resend_count')->default(0)->after('invitation_expires_at');
            $table->timestamp('last_invitation_sent_at')->nullable()->after('invitation_resend_count');

            // Phone verification (OTP)
            $table->string('phone_verification_code', 6)->nullable()->after('last_invitation_sent_at');
            $table->timestamp('phone_verification_expires_at')->nullable()->after('phone_verification_code');
            $table->timestamp('phone_verified_at')->nullable()->after('phone_verification_expires_at');

            // For rejected self-registrations
            $table->text('rejection_reason')->nullable()->after('phone_verified_at');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejection_reason');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');

            // Index for token lookups
            $table->index('invitation_token');
            $table->index('setup_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndex(['invitation_token']);
            $table->dropIndex(['setup_status']);

            $table->dropColumn([
                'setup_status',
                'invitation_token',
                'invitation_sent_at',
                'invitation_expires_at',
                'invitation_resend_count',
                'last_invitation_sent_at',
                'phone_verification_code',
                'phone_verification_expires_at',
                'phone_verified_at',
                'rejection_reason',
                'rejected_by',
                'rejected_at',
            ]);
        });
    }
};
