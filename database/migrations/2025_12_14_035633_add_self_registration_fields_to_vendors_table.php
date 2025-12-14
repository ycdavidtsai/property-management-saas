<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Phase 3: Vendor Self-Registration
     * Only adds columns that don't already exist in the vendors table.
     */
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Contact name (person's name, separate from business name)
            if (!Schema::hasColumn('vendors', 'contact_name')) {
                $table->string('contact_name')->nullable()->after('name');
            }

            // Approval tracking (for admin approval workflow)
            if (!Schema::hasColumn('vendors', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('rejected_at');
            }

            if (!Schema::hasColumn('vendors', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('vendors', 'contact_name')) {
                $columnsToDrop[] = 'contact_name';
            }
            if (Schema::hasColumn('vendors', 'approved_by')) {
                $columnsToDrop[] = 'approved_by';
            }
            if (Schema::hasColumn('vendors', 'approved_at')) {
                $columnsToDrop[] = 'approved_at';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
