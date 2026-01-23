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

            $table->index('role_id');
            $table->index('status');
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
                'created_at' => now(),
                'updated_at' => now(),
            ]
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
