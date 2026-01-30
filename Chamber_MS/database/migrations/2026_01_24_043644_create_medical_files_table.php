<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('medical_files');

        Schema::create('medical_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_code', 20)->unique();
            $table->foreignId('treatment_id')->nullable()->constrained('treatments');
            $table->foreignId('patient_id')->constrained('patients');

            $table->enum('status', ['requested', 'pending', 'completed', 'cancelled'])
                ->default('requested');

            $table->dateTime('requested_date')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users');
            $table->text('requested_notes')->nullable();
            $table->date('expected_delivery_date')->nullable();

            $table->enum('file_type', [
                'xray',
                'lab_report',
                'ct_scan',
                'photo',
                'document',
                'prescription',
                'report',
                'other'
            ]);

            $table->string('file_name', 255)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->integer('file_size')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('uploaded_at')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->boolean('is_confidential')->default(false);
            $table->timestamps();

            $table->index(['patient_id', 'treatment_id']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_files');
    }
};
