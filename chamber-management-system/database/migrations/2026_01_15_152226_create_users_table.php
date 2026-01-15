<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->foreignId('role_id')->constrained()->default(8);
            $table->string('full_name')->nullable();
            $table->string('phone')->unique();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
        });

        // Update existing name field to match our structure
        Schema::table('users', function (Blueprint $table) {
            // Copy data from name to full_name if needed
            DB::statement("UPDATE users SET full_name = name");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
