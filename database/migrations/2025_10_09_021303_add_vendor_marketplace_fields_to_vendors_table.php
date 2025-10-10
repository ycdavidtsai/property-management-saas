<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Vendor visibility type
            $table->enum('vendor_type', ['global', 'private'])
                ->default('private')
                ->after('id');

            // Track which organization created this vendor
            // Using unsignedBigInteger to match most Laravel conventions
            $table->unsignedBigInteger('created_by_organization_id')
                ->nullable()
                ->after('vendor_type');

            // Promotion tracking
            $table->timestamp('promoted_at')->nullable()->after('is_active');
            $table->decimal('promotion_fee_paid', 10, 2)->nullable()->after('promoted_at');

            // Indexes
            $table->index('vendor_type');
            $table->index('created_by_organization_id');
        });

        // Add foreign key constraint ONLY if organizations.id is compatible
        // Check if organizations table exists and has the right structure
        if (Schema::hasTable('organizations') && Schema::hasColumn('organizations', 'id')) {
            try {
                Schema::table('vendors', function (Blueprint $table) {
                    $table->foreign('created_by_organization_id')
                        ->references('id')
                        ->on('organizations')
                        ->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Log warning but don't fail the migration
                Log::warning('Foreign key constraint not created: ' . $e->getMessage());
                echo "\nWarning: Foreign key constraint could not be created. The column was added successfully.\n";
            }
        }
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Drop foreign key first if it exists
            $foreignKeys = Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableForeignKeys('vendors');

            foreach ($foreignKeys as $foreignKey) {
                if (in_array('created_by_organization_id', $foreignKey->getLocalColumns())) {
                    $table->dropForeign($foreignKey->getName());
                }
            }

            // Drop columns
            $table->dropColumn([
                'vendor_type',
                'created_by_organization_id',
                'promoted_at',
                'promotion_fee_paid',
            ]);
        });
    }
};
