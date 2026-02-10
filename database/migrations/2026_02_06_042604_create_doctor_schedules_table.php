<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->enum('day_of_week', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->integer('max_appointments')->default(20);
            $table->integer('slot_duration')->default(30); // in minutes
            $table->timestamps();

            $table->unique(['doctor_id', 'day_of_week']);
            $table->index(['doctor_id', 'is_active']);
        });

        Schema::create('doctor_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->date('leave_date');
            $table->enum('type', ['full_day', 'half_day', 'emergency']);
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['doctor_id', 'leave_date']);
            $table->index(['doctor_id', 'status']);
            $table->index('leave_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctor_leaves');
        Schema::dropIfExists('doctor_schedules');
    }
};
