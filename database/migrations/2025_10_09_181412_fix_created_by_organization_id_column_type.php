<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Drop the column and recreate it with correct type
            $table->dropColumn('created_by_organization_id');
        });

        Schema::table('vendors', function (Blueprint $table) {
            // Recreate with proper UUID type
            $table->char('created_by_organization_id', 36)
                ->nullable()
                ->after('vendor_type');

            $table->index('created_by_organization_id');

            // Add foreign key
            $table->foreign('created_by_organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign(['created_by_organization_id']);
            $table->dropColumn('created_by_organization_id');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->uuid('created_by_organization_id')
                ->nullable()
                ->after('vendor_type');
        });
    }
};
