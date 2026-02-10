<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_code', 20)->unique();
            $table->string('full_name', 100);
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone', 20)->unique();
            $table->string('email', 100)->unique()->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact', 20)->nullable();
            $table->foreignId('referred_by')->nullable()->constrained('patients')->nullOnDelete();
            $table->enum('status', ['active', 'inactive', 'deceased'])->default('active');
            $table->text('medical_history')->nullable();
            $table->text('allergies')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();

            $table->index('patient_code');
            $table->index('phone');
            $table->index('status');
            $table->index('created_by');
        });

    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
};
