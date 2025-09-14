<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('unit_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rent_amount', 10, 2);
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->enum('status', ['active', 'expiring_soon', 'expired', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['unit_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('leases');
    }
};
