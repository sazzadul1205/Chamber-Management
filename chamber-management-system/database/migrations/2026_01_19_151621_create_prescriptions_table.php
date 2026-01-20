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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id(); // id int [pk, increment]
            $table->foreignId('treatment_id')->constrained(); // treatment_id int
            $table->foreignId('created_by')->constrained('users'); // created_by int
            $table->timestamps(); // created_at datetime

            // Indexes
            $table->index('treatment_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
