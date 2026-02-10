<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->foreignId('medicine_id')->constrained('medicines')->onDelete('restrict');
            $table->string('dosage', 50);
            $table->string('frequency', 50);
            $table->string('duration', 50);
            $table->enum('route', ['oral', 'topical', 'injection', 'inhalation'])->default('oral');
            $table->string('instructions', 255)->nullable();
            $table->integer('quantity')->default(1);
            $table->enum('status', ['pending', 'dispensed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index('prescription_id');
            $table->index('medicine_id');
            $table->index('status');
        });

    }

    public function down()
    {
        Schema::dropIfExists('prescription_items');
    }
};
