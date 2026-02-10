<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patient_families', function (Blueprint $table) {
            $table->id();
            $table->string('family_code', 20)->unique();
            $table->string('family_name', 100);
            $table->foreignId('head_patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->timestamps();

            $table->index('family_code');
            $table->index('head_patient_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_families');
    }
};
