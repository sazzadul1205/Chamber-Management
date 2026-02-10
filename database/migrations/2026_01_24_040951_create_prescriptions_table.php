<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_code', 20)->unique();
            $table->foreignId('treatment_id')->constrained('treatments')->onDelete('cascade');
            $table->date('prescription_date');
            $table->integer('validity_days')->default(7);
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'expired', 'cancelled', 'filled'])->default('active');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index('prescription_code');
            $table->index('treatment_id');
            $table->index('prescription_date');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('prescriptions');
    }
};
