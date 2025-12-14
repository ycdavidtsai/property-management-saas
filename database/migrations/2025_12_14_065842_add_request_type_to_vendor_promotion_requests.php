<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds request_type to track both:
     * - 'promotion' = existing private vendor requesting global status
     * - 'registration' = self-registered vendor waiting for initial approval
     */
    public function up(): void
    {
        Schema::table('vendor_promotion_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_promotion_requests', 'request_type')) {
                $table->string('request_type')->default('promotion')->after('vendor_id')
                      ->comment('promotion or registration');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_promotion_requests', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_promotion_requests', 'request_type')) {
                $table->dropColumn('request_type');
            }
        });
    }
};
