<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_code', 20)->unique();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('restrict');
            $table->foreignId('chair_id')->nullable()->constrained('dental_chairs')->nullOnDelete();
            $table->enum('appointment_type', ['consultation', 'treatment', 'followup', 'emergency', 'checkup'])->default('consultation');
            $table->enum('schedule_type', ['fixed', 'walkin'])->default('fixed');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->integer('expected_duration')->default(30); // in minutes
            $table->integer('queue_no')->nullable();
            $table->enum('status', ['scheduled', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->enum('priority', ['normal', 'urgent', 'high'])->default('normal');
            $table->text('chief_complaint')->nullable();
            $table->text('notes')->nullable();
            $table->text('reason_cancellation')->nullable();
            $table->timestamp('checked_in_time')->nullable();
            $table->timestamp('started_time')->nullable();
            $table->timestamp('completed_time')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();

            $table->index('appointment_code');
            $table->index('appointment_date');
            $table->index(['appointment_date', 'doctor_id']);
            $table->index(['appointment_date', 'chair_id']);
            $table->index('status');
            $table->index('patient_id');
            $table->index('doctor_id');
        });

        // Insert sample appointments
        DB::table('appointments')->insert([
            [
                'appointment_code' => 'APT001',
                'patient_id' => 1, // Abdul Karim
                'doctor_id' => 1, // Dr. Mohammad Rahman
                'chair_id' => 1,
                'appointment_type' => 'consultation',
                'schedule_type' => 'fixed',
                'appointment_date' => now()->addDays(1)->format('Y-m-d'),
                'appointment_time' => '09:00:00',
                'expected_duration' => 30,
                'queue_no' => 1,
                'status' => 'scheduled',
                'priority' => 'normal',
                'chief_complaint' => 'Tooth pain in upper left molar',
                'notes' => 'Patient reported sensitivity to cold drinks',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'appointment_code' => 'APT002',
                'patient_id' => 2, // Fatema Begum
                'doctor_id' => 1,
                'chair_id' => 2,
                'appointment_type' => 'checkup',
                'schedule_type' => 'fixed',
                'appointment_date' => now()->addDays(2)->format('Y-m-d'),
                'appointment_time' => '10:30:00',
                'expected_duration' => 45,
                'queue_no' => 1,
                'status' => 'scheduled',
                'priority' => 'normal',
                'chief_complaint' => 'Regular dental checkup',
                'notes' => 'Patient due for routine cleaning',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
