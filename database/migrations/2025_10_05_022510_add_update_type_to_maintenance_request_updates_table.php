<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('maintenance_request_updates', function (Blueprint $table) {
            $table->string('update_type')->nullable()->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('maintenance_request_updates', function (Blueprint $table) {
            $table->dropColumn('update_type');
        });
    }
};
