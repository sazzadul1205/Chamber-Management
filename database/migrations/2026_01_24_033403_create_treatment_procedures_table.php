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
    }

    public function down()
    {
        Schema::dropIfExists('treatment_procedures');
    }
};
