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
        Schema::create('patient_families', function (Blueprint $table) {
            $table->id(); // id int [pk, increment]
            $table->foreignId('head_patient_id')->constrained('patients'); // head_patient_id int
            $table->string('family_name'); // family_name varchar
            $table->timestamps();

            // Index for better performance
            $table->index('head_patient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_families');
    }
};
