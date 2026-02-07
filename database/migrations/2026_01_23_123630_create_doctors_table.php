<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('doctor_code', 20)->unique();
            $table->string('specialization', 100)->nullable();
            $table->text('qualification')->nullable();
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->decimal('commission_percent', 5, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('doctor_code');
        });

        // Create a new user for this doctor
        $userId = DB::table('users')->insertGetId([
            'role_id' => 3, // Doctor role
            'full_name' => 'Dr. Mohammad Rahman',
            'phone' => '+880100000000' . rand(10, 99), // Randomized phone
            'email' => 'doctor' . rand(10, 99) . '@example.com', // Randomized email
            'password' => Hash::make('Doctor1205'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert doctor record linked to the new user
        DB::table('doctors')->insert([
            [
                'user_id' => $userId,
                'doctor_code' => 'DOC001',
                'specialization' => 'Orthodontics',
                'qualification' => 'BDS, MDS (Orthodontics)',
                'consultation_fee' => 500.00,
                'commission_percent' => 15.00,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('doctors');
    }
};
