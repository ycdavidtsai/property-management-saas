<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->enum('role', ['admin', 'manager', 'landlord', 'tenant', 'vendor'])
            //       ->default('tenant')->after('organization_id'); // duplicated in previous migration
            // $table->json('permissions')->nullable()->after('role');
            // $table->string('phone')->nullable()->after('email');
            //$table->string('profile_photo_path')->nullable()->after('phone');
            $table->timestamp('last_login_at')->nullable()->after('profile_photo_path');
            $table->json('emergency_contact')->nullable()->after('last_login_at');
            $table->text('notes')->nullable()->after('emergency_contact');
            $table->boolean('is_active')->default(true)->after('notes');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                // 'role',
                //'permissions',
                // 'phone',
                //'profile_photo_path',
                'last_login_at',
                'emergency_contact',
                'notes',
                'is_active'
            ]);
        });
    }
};
