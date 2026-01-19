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
        Schema::create('dental_charts', function (Blueprint $table) {
            $table->id();

            // Each chart belongs to a patient
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');

            // Tooth info
            $table->string('tooth_number');       // e.g., 11, 12, 21
            $table->string('tooth_condition');    // e.g., Healthy, Cavity
            $table->text('remarks')->nullable();  // Notes for that tooth

            $table->dateTime('last_updated')->default(now()); // Track last update

            $table->timestamps();

            // Composite unique index to prevent duplicates for same tooth per patient
            $table->unique(['patient_id', 'tooth_number'], 'patient_tooth_unique');

            // Indexes for faster lookups
            $table->index('patient_id');
            $table->index('tooth_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dental_charts');
    }
};
