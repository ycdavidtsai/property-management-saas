<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add missing columns for vendor acceptance/rejection flow
 *
 * This migration adds all the new fields needed for the vendor
 * acceptance/rejection workflow to the maintenance_requests table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            // Check if columns don't exist before adding them

            // Vendor acceptance tracking
            if (!Schema::hasColumn('maintenance_requests', 'accepted_at')) {
                $table->dateTime('accepted_at')->nullable()->after('assigned_at');
            }

            // Vendor rejection tracking
            if (!Schema::hasColumn('maintenance_requests', 'rejection_reason')) {
                $table->string('rejection_reason')->nullable()->after('tenant_feedback');
            }

            if (!Schema::hasColumn('maintenance_requests', 'rejection_notes')) {
                $table->text('rejection_notes')->nullable()->after('rejection_reason');
            }

            if (!Schema::hasColumn('maintenance_requests', 'rejected_at')) {
                $table->dateTime('rejected_at')->nullable()->after('rejection_notes');
            }

            if (!Schema::hasColumn('maintenance_requests', 'rejected_by')) {
                $table->foreignId('rejected_by')
                    ->nullable()
                    ->after('rejected_at')
                    ->constrained('users')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            // Drop columns in reverse order
            if (Schema::hasColumn('maintenance_requests', 'rejected_by')) {
                $table->dropForeign(['rejected_by']);
                $table->dropColumn('rejected_by');
            }

            if (Schema::hasColumn('maintenance_requests', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }

            if (Schema::hasColumn('maintenance_requests', 'rejection_notes')) {
                $table->dropColumn('rejection_notes');
            }

            if (Schema::hasColumn('maintenance_requests', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }

            if (Schema::hasColumn('maintenance_requests', 'accepted_at')) {
                $table->dropColumn('accepted_at');
            }
        });
    }
};
