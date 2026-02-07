<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('treatment_procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_id')->constrained('treatments')->onDelete('cascade');
            $table->string('procedure_code', 20);
            $table->string('procedure_name', 100);
            $table->string('tooth_number', 5)->nullable();
            $table->string('surface', 20)->nullable();
            $table->decimal('cost', 10, 2);
            $table->integer('duration')->comment('in minutes');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('treatment_id');
            $table->index('procedure_code');
            $table->index('status');
            $table->index('tooth_number');
        });

        // Insert sample procedure for the first treatment
        DB::table('treatment_procedures')->insert([
            [
                'treatment_id' => 1,
                'procedure_code' => 'FILL-001',
                'procedure_name' => 'Composite Filling',
                'tooth_number' => '16',
                'surface' => 'occlusal',
                'cost' => 1500.00,
                'duration' => 45,
                'status' => 'completed',
                'notes' => 'Patient reported sensitivity to cold, used desensitizer before filling',
                'completed_at' => now(),
                'completed_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('treatment_procedures');
    }
};
