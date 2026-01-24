<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_id')->nullable()->constrained('treatments');
            $table->foreignId('prescription_id')->nullable()->constrained('prescriptions');
            $table->foreignId('item_id')->constrained('inventory_items');
            $table->integer('used_quantity')->default(1);
            $table->enum('usage_type', ['treatment', 'prescription', 'wastage', 'other']);
            $table->foreignId('used_by')->constrained('users');
            $table->foreignId('used_for_patient_id')->nullable()->constrained('patients');
            $table->date('usage_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['treatment_id', 'usage_date']);
            $table->index(['prescription_id', 'usage_date']);
            $table->index(['item_id', 'usage_date']);
            $table->index('used_for_patient_id');
            $table->index('usage_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_usage');
    }
};
