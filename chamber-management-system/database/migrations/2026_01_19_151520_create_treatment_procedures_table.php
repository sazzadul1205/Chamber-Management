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
        Schema::create('treatment_procedures', function (Blueprint $table) {
            $table->id(); // id int [pk, increment]
            $table->foreignId('treatment_id')->constrained(); // treatment_id int
            $table->string('procedure_name'); // procedure_name varchar
            $table->string('tooth_no')->nullable(); // tooth_no varchar
            $table->decimal('price', 10, 2)->default(0); // price decimal
            $table->enum('status', ['planned', 'done'])->default('planned'); // status enum
            $table->timestamps();

            // Indexes
            $table->index('treatment_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_procedures');
    }
};
