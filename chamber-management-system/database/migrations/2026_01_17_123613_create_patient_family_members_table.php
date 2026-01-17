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
        Schema::create('patient_family_members', function (Blueprint $table) {
            $table->id(); // id int [pk, increment]
            $table->foreignId('family_id')->constrained('patient_families'); // family_id int
            $table->foreignId('patient_id')->constrained('patients'); // patient_id int
            $table->timestamps();

            // Unique constraint: A patient can only belong to one family
            $table->unique('patient_id');

            // Indexes for better performance
            $table->index('family_id');
            $table->index('patient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_family_members');
    }
};
