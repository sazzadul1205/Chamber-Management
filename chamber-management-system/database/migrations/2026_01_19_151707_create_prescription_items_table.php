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
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id(); // id int [pk, increment]
            $table->foreignId('prescription_id')->constrained(); // prescription_id int
            $table->foreignId('medicine_id')->constrained(); // medicine_id int
            $table->string('dosage'); // dosage varchar
            $table->string('duration'); // duration varchar
            $table->timestamps();

            // Indexes
            $table->index('prescription_id');
            $table->index('medicine_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
