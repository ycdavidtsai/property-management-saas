<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to use raw SQL to modify ENUM
        // This adds 'pending_acceptance' to the status enum

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            // MySQL approach - modify ENUM column
            DB::statement("ALTER TABLE maintenance_requests
                MODIFY COLUMN status ENUM(
                    'submitted',
                    'pending_acceptance',
                    'assigned',
                    'in_progress',
                    'completed',
                    'closed'
                ) NOT NULL DEFAULT 'submitted'");
        } else {
            // PostgreSQL approach - drop check constraint and recreate
            // First, drop existing constraint if it exists
            DB::statement("ALTER TABLE maintenance_requests DROP CONSTRAINT IF EXISTS maintenance_requests_status_check");

            // Add new constraint with updated values
            DB::statement("ALTER TABLE maintenance_requests
                ADD CONSTRAINT maintenance_requests_status_check
                CHECK (status IN (
                    'submitted',
                    'pending_acceptance',
                    'assigned',
                    'in_progress',
                    'completed',
                    'closed'
                ))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            // Revert to original ENUM (remove pending_acceptance)
            DB::statement("ALTER TABLE maintenance_requests
                MODIFY COLUMN status ENUM(
                    'submitted',
                    'assigned',
                    'in_progress',
                    'completed',
                    'closed'
                ) NOT NULL DEFAULT 'submitted'");
        } else {
            // PostgreSQL - drop and recreate constraint
            DB::statement("ALTER TABLE maintenance_requests DROP CONSTRAINT IF EXISTS maintenance_requests_status_check");

            DB::statement("ALTER TABLE maintenance_requests
                ADD CONSTRAINT maintenance_requests_status_check
                CHECK (status IN (
                    'submitted',
                    'assigned',
                    'in_progress',
                    'completed',
                    'closed'
                ))");
        }
    }
};
