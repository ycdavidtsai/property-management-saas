<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization_vendor', function (Blueprint $table) {
            $table->id();
            $table->uuid('organization_id');
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['organization_id', 'vendor_id']);

            // Indexes
            $table->index('organization_id');
            $table->index('vendor_id');

            // Foreign keys
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization_vendor');
    }
};
