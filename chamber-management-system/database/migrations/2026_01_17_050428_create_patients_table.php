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
        Schema::create('patients', function (Blueprint $table) {
            $table->id(); // id int [pk, increment]

            // Patient Information
            $table->string('patient_code')->unique(); // patient_code varchar [unique]
            $table->string('full_name'); // full_name varchar
            $table->string('phone'); // phone varchar
            $table->string('email')->nullable(); // email varchar
            $table->enum('gender', ['male', 'female', 'other']); // gender enum
            $table->date('date_of_birth'); // date_of_birth date
            $table->text('address')->nullable(); // address text

            // Referral Info
            $table->enum('referral_type', ['patient', 'doctor', 'magazine', 'other'])->nullable();
            $table->unsignedBigInteger('referred_by_patient_id')->nullable(); // if type is patient
            $table->string('referred_by_text')->nullable(); // if type is doctor/magazine/other

            // Audit fields
            $table->foreignId('created_by')->constrained('users'); // created_by int
            $table->foreignId('updated_by')->constrained('users'); // updated_by int

            // Timestamps
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at datetime
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
