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
        Schema::create('short_urls', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique(); // Short code (e.g., "aB3xK9")
            $table->text('url'); // Full destination URL
            $table->string('purpose')->nullable(); // vendor_invitation, maintenance_request, etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // Related record ID
            $table->string('reference_type')->nullable(); // Model class
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('click_count')->default(0);
            $table->timestamp('last_clicked_at')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_urls');
    }
};
