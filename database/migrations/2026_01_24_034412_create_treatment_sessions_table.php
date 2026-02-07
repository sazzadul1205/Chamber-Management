<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('treatment_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_id')->constrained('treatments')->onDelete('cascade');
            $table->integer('session_number');
            $table->string('session_title', 100);
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->date('scheduled_date');
            $table->date('actual_date')->nullable();
            $table->foreignId('chair_id')->nullable()->constrained('dental_chairs')->nullOnDelete();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'postponed'])->default('scheduled');
            $table->text('procedure_details')->nullable();
            $table->text('materials_used')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->text('assistant_notes')->nullable();
            $table->integer('duration_planned')->comment('minutes');
            $table->integer('duration_actual')->nullable()->comment('minutes');
            $table->decimal('cost_for_session', 10, 2)->nullable();
            $table->date('next_session_date')->nullable();
            $table->text('next_session_notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index('treatment_id');
            $table->index('session_number');
            $table->index('scheduled_date');
            $table->index('status');
            $table->index('chair_id');

            $table->unique(['treatment_id', 'session_number']);
        });

        // Insert sample session for the first treatment
        DB::table('treatment_sessions')->insert([
            [
                'treatment_id' => 1,
                'session_number' => 1,
                'session_title' => 'Initial Consultation & Examination',
                'appointment_id' => 1,
                'scheduled_date' => now()->format('Y-m-d'),
                'actual_date' => now()->format('Y-m-d'),
                'chair_id' => 1,
                'status' => 'completed',
                'procedure_details' => '1. Oral examination\n2. X-ray of tooth 16\n3. Diagnosis of caries\n4. Treatment planning',
                'materials_used' => 'X-ray film, mouth mirror, explorer',
                'doctor_notes' => 'Patient has deep caries in tooth 16. Recommended composite filling.',
                'assistant_notes' => 'Patient anxious but cooperative. Explained procedure in detail.',
                'duration_planned' => 45,
                'duration_actual' => 40,
                'cost_for_session' => 500.00,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('treatment_sessions');
    }
};
