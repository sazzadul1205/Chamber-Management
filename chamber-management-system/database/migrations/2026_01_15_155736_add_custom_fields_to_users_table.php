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
        Schema::table('users', function (Blueprint $table) {
            // Add role_id with constraint
            $table->foreignId('role_id')->after('id')->constrained()->default(8);

            // Add phone with unique constraint
            $table->string('phone')->after('email')->unique()->nullable();

            // Add full_name (can keep name as well for Breeze compatibility)
            $table->string('full_name')->after('name')->nullable();

            // Add status
            $table->enum('status', ['active', 'inactive'])->after('password')->default('active');

            // Add soft deletes
            $table->softDeletes();

            // Make email nullable if needed (as per your DBML)
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'phone', 'full_name', 'status']);
            $table->dropSoftDeletes();
        });
    }
};
