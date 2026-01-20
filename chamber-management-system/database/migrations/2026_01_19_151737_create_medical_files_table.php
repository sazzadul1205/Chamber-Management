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
        Schema::create('medical_files', function (Blueprint $table) {
            $table->id(); // id int [pk, increment]
            $table->foreignId('treatment_id')->constrained(); // treatment_id int
            $table->enum('file_type', ['xray', 'report', 'image']); // file_type enum
            $table->string('file_path'); // file_path varchar
            $table->dateTime('uploaded_at'); // uploaded_at datetime
            $table->foreignId('uploaded_by')->constrained('users'); // uploaded_by int
            $table->timestamps();

            // Indexes
            $table->index('treatment_id');
            $table->index('file_type');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_files');
    }
};
