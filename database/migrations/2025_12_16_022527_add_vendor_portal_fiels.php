<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Phase 4: Enhanced Vendor Portal
     * - Vendor availability schedule, service areas, portfolio
     * - Appointment scheduling fields on maintenance requests
     * - Vendor invoices table
     */
    public function up(): void
    {
        // =============================================
        // VENDORS TABLE - Add availability, service areas, portfolio
        // =============================================
        Schema::table('vendors', function (Blueprint $table) {
            $table->json('availability_schedule')->nullable()->after('notes');
            $table->json('service_areas')->nullable()->after('availability_schedule');
            $table->json('portfolio_photos')->nullable()->after('service_areas');
        });

        // =============================================
        // MAINTENANCE REQUESTS - Add scheduling fields
        // =============================================
        Schema::table('maintenance_requests', function (Blueprint $table) {
            // Scheduled appointment time (in addition to existing preferred_date)
            $table->date('scheduled_date')->nullable()->after('preferred_date');
            $table->time('scheduled_start_time')->nullable()->after('scheduled_date');
            $table->time('scheduled_end_time')->nullable()->after('scheduled_start_time');

            // Scheduling workflow status
            $table->string('scheduling_status', 50)->nullable()->after('scheduled_end_time');
            // Values: pending_tenant_proposal, pending_vendor_confirmation, confirmed, rescheduled

            // Tenant's proposed time slots (JSON array of options)
            $table->json('proposed_times')->nullable()->after('scheduling_status');

            // When appointment was confirmed by vendor
            $table->timestamp('appointment_confirmed_at')->nullable()->after('proposed_times');

            // "On My Way" notification tracking
            $table->boolean('tenant_notified_on_way')->default(false)->after('appointment_confirmed_at');
            $table->timestamp('on_way_notified_at')->nullable()->after('tenant_notified_on_way');
        });

        // =============================================
        // VENDOR INVOICES - New table
        // =============================================
        Schema::create('vendor_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relationships
            $table->unsignedBigInteger('vendor_id');
            $table->uuid('maintenance_request_id');
            $table->uuid('organization_id');

            // Invoice details
            $table->string('invoice_number', 50)->unique();
            $table->decimal('amount', 10, 2);
            $table->string('status', 20)->default('pending');
            // Status values: pending, paid, overdue

            // Flexible details field for labor, materials, terms
            $table->text('details')->nullable();

            // Internal vendor notes (not shown to landlord)
            $table->text('notes')->nullable();

            // Dates
            $table->date('issued_date');
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Payment info
            $table->string('payment_method', 50)->nullable();
            // Values: check, transfer, cash, zelle, venmo, other

            $table->timestamps();

            // Foreign keys
            $table->foreign('vendor_id')
                  ->references('id')
                  ->on('vendors')
                  ->onDelete('cascade');

            $table->foreign('maintenance_request_id')
                  ->references('id')
                  ->on('maintenance_requests')
                  ->onDelete('cascade');

            $table->foreign('organization_id')
                  ->references('id')
                  ->on('organizations')
                  ->onDelete('cascade');

            // Indexes for common queries
            $table->index(['vendor_id', 'status']);
            $table->index(['organization_id', 'status']);
            $table->index(['vendor_id', 'issued_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop vendor_invoices table
        Schema::dropIfExists('vendor_invoices');

        // Remove scheduling fields from maintenance_requests
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropColumn([
                'scheduled_date',
                'scheduled_start_time',
                'scheduled_end_time',
                'scheduling_status',
                'proposed_times',
                'appointment_confirmed_at',
                'tenant_notified_on_way',
                'on_way_notified_at',
            ]);
        });

        // Remove new fields from vendors
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'availability_schedule',
                'service_areas',
                'portfolio_photos',
            ]);
        });
    }
};
