<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lease_tenant', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('lease_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['lease_id', 'tenant_id']);
            $table->index('tenant_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lease_tenant');
    }
};
