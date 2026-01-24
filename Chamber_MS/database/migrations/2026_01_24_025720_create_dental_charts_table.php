<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dental_charts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->date('chart_date');
            $table->string('tooth_number', 5); // e.g., "11", "12", "21", "22", etc.
            $table->string('surface', 20)->nullable(); // e.g., "occlusal", "buccal", "lingual"
            $table->string('condition', 100); // e.g., "caries", "filling", "crown", "missing"
            $table->string('procedure_done', 100)->nullable();
            $table->date('next_checkup')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('updated_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index('patient_id');
            $table->index('chart_date');
            $table->index('tooth_number');
            $table->index(['patient_id', 'tooth_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('dental_charts');
    }
};
