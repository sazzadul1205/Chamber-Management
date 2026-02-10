<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->string('treatment_code', 20)->unique();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('restrict');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->enum('treatment_type', ['single_visit', 'multi_visit'])->default('single_visit');
            $table->integer('estimated_sessions')->default(1);
            $table->integer('completed_sessions')->default(0);
            $table->foreignId('initial_appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->date('treatment_date');
            $table->date('start_date')->nullable();
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->text('diagnosis');
            $table->text('treatment_plan')->nullable();
            $table->decimal('total_estimated_cost', 10, 2)->nullable();
            $table->decimal('total_actual_cost', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled', 'on_hold'])->default('planned');
            $table->date('followup_date')->nullable();
            $table->text('followup_notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();

            $table->index('treatment_code');
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('status');
            $table->index('treatment_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('treatments');
    }
};
