<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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

        // Insert sample patients
        DB::table('patients')->insert([
            [
                'patient_code' => 'PAT001',
                'full_name' => 'Abdul Karim',
                'gender' => 'male',
                'date_of_birth' => '1985-06-15',
                'phone' => '+8801711111111',
                'email' => 'abdul@example.com',
                'address' => '123 Main Street, Dhaka',
                'emergency_contact' => '+8801722222222',
                'status' => 'active',
                'medical_history' => 'Diabetes Type 2, Hypertension',
                'allergies' => 'Penicillin',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'patient_code' => 'PAT002',
                'full_name' => 'Fatema Begum',
                'gender' => 'female',
                'date_of_birth' => '1990-08-20',
                'phone' => '+8801733333333',
                'email' => 'fatema@example.com',
                'address' => '456 Park Avenue, Chittagong',
                'emergency_contact' => '+8801744444444',
                'status' => 'active',
                'medical_history' => null,
                'allergies' => 'None',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
};
