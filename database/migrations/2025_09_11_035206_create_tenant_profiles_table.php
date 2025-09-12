<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tenant_profiles', function (Blueprint $table) {
            $table->id(); // Use regular auto-incrementing ID
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Use foreignId instead of foreignUuid
            $table->date('date_of_birth')->nullable();
            $table->string('ssn_last_four', 4)->nullable();
            $table->enum('employment_status', ['employed', 'self_employed', 'unemployed', 'retired', 'student'])->nullable();
            $table->decimal('monthly_income', 10, 2)->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->date('move_in_date')->nullable();
            $table->enum('background_check_status', ['pending', 'approved', 'rejected', 'not_required'])->nullable();
            $table->date('background_check_date')->nullable();
            $table->integer('credit_score')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenant_profiles');
    }
};
