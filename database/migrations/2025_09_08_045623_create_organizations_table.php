
// database/migrations/create_organizations_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('subscription_tier')->default('starter');
            $table->string('subscription_status')->default('trial');
            $table->string('stripe_customer_id')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
