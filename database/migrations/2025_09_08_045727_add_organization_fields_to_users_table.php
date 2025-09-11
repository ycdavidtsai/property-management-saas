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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('organization_id')->nullable()->after('id');
            $table->string('phone')->nullable()->after('email');
            $table->enum('role', ['admin', 'manager', 'landlord', 'tenant', 'vendor'])->default('landlord');
            $table->json('permissions')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->string('profile_photo_path')->nullable();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
