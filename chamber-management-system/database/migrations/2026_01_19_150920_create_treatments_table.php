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
        Schema::create('treatments', function (Blueprint $table) {
            $table->id(); // id int [pk, increment]

            // Foreign keys
            $table->foreignId('patient_id')->constrained(); // patient_id int
            $table->foreignId('doctor_id')->constrained(); // doctor_id int
            $table->foreignId('appointment_id')->nullable()->constrained(); // appointment_id int

            // Treatment details
            $table->text('diagnosis')->nullable(); // diagnosis text
            $table->enum('status', ['ongoing', 'completed', 'cancelled'])->default('ongoing'); // status enum

            // Audit fields
            $table->foreignId('created_by')->constrained('users'); // created_by int
            $table->foreignId('updated_by')->constrained('users'); // updated_by int

            // Timestamps
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at datetime

            // Indexes
            $table->index('status');
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('appointment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};
