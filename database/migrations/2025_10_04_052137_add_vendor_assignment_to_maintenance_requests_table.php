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
         Schema::table('maintenance_requests', function (Blueprint $table) {
            // Only add if they don't exist
            if (!Schema::hasColumn('maintenance_requests', 'assigned_vendor_id')) {
                $table->foreignId('assigned_vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            }
            if (!Schema::hasColumn('maintenance_requests', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable();
            }
            if (!Schema::hasColumn('maintenance_requests', 'assigned_by')) {
                $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('maintenance_requests', 'assignment_notes')) {
                $table->text('assignment_notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            //
        });
    }
};
