<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Update the enum to include 'for_lease' status
        DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('vacant', 'occupied', 'maintenance', 'for_lease') NOT NULL DEFAULT 'vacant'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE units MODIFY COLUMN status ENUM('vacant', 'occupied', 'maintenance') NOT NULL DEFAULT 'vacant'");
    }
};
