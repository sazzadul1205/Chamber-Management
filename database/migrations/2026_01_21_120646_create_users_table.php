<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 100);
            $table->string('phone', 20)->unique();
            $table->string('email', 100)->unique()->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');

            // New fields
            $table->timestamp('last_login_at')->nullable()->comment('Last login timestamp');
            $table->string('last_login_device_id', 100)->nullable()->comment('Device ID from last login session');
            $table->string('current_session_id', 100)->nullable()->comment('Current active session ID');
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable()->comment('User blood group');

            // Add role_id with default 1
            $table->foreignId('role_id')
                ->nullable(false)
                ->default(1)
                ->constrained('roles')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('role_id');
            $table->index('status');
            $table->index('last_login_at');
            $table->index('blood_group');
        });

        // Insert default super admin user
        DB::table('users')->insert([
            [
                'role_id' => 1, // Assuming Super Admin role has ID 1
                'full_name' => 'Super Admin',
                'phone' => '+8801000000001',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('Admin1205'),
                'status' => 'active',
                'blood_group' => 'O+',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => 2, // Admin
                'full_name' => 'Admin User',
                'phone' => '+8801000000002',
                'email' => 'admin2@gmail.com',
                'password' => Hash::make('Admin1205'),
                'status' => 'active',
                'blood_group' => 'A+',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => 3, // Doctor
                'full_name' => 'Doctor User',
                'phone' => '+8801000000003',
                'email' => 'doctor@gmail.com',
                'password' => Hash::make('Admin1205'),
                'status' => 'active',
                'blood_group' => 'B+',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => 4, // Receptionist
                'full_name' => 'Receptionist User',
                'phone' => '+8801000000004',
                'email' => 'reception@gmail.com',
                'password' => Hash::make('Admin1205'),
                'status' => 'active',
                'blood_group' => 'AB+',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => 5, // Accountant
                'full_name' => 'Accountant User',
                'phone' => '+8801000000005',
                'email' => 'accountant@gmail.com',
                'password' => Hash::make('Admin1205'),
                'status' => 'active',
                'blood_group' => 'O-',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
