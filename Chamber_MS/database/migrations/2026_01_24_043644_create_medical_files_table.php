<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medical_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_code', 20)->unique();
            $table->foreignId('treatment_id')->nullable()->constrained('treatments');
            $table->foreignId('patient_id')->constrained('patients');
            $table->enum('file_type', ['xray', 'photo', 'document', 'prescription', 'report', 'other']);
            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->integer('file_size')->comment('in bytes');
            $table->text('description')->nullable();
            $table->datetime('uploaded_at');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['patient_id', 'treatment_id']);
            $table->index('file_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_files');
    }
};
