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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id(); // id int [pk, increment]

            // Foreign keys
            $table->foreignId('patient_id')->constrained(); // patient_id int
            $table->foreignId('doctor_id')->constrained(); // doctor_id int
            $table->foreignId('chair_id')->constrained('dental_chairs'); // chair_id int

            // Appointment details
            $table->enum('appointment_type', ['slot', 'fifo']); // appointment_type enum
            $table->date('appointment_date'); // appointment_date date
            $table->time('appointment_time')->nullable(); // appointment_time time null
            $table->integer('queue_no')->nullable(); // queue_no int null

            // Status
            $table->enum('status', [
                'scheduled',
                'checked_in',
                'in_progress',
                'completed',
                'cancelled',
                'no_show'
            ])->default('scheduled'); // status enum

            // Notes
            $table->text('notes')->nullable(); // notes text

            // Audit fields
            $table->foreignId('created_by')->constrained('users'); // created_by int
            $table->foreignId('updated_by')->constrained('users'); // updated_by int

            // Timestamps
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at datetime

            // Indexes for better performance
            $table->index('appointment_date');
            $table->index('status');
            $table->index(['appointment_date', 'doctor_id']);
            $table->index(['appointment_date', 'chair_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
