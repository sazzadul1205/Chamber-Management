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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Professional info
            $table->string('designation')->nullable();
            $table->string('specialization')->nullable();
            $table->string('qualification')->nullable();
            $table->integer('experience_years')->nullable();

            // Profile & bio
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();

            // Fees & commission
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->decimal('commission_percent', 5, 2)->default(0);

            // Frontend control
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('slug')->unique()->nullable();
            $table->integer('display_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
