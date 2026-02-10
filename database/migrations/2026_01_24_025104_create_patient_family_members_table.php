<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patient_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained('patient_families')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->string('relationship', 20);
            $table->boolean('is_head')->default(false);
            $table->timestamps();

            $table->unique(['family_id', 'patient_id']);
            $table->index('family_id');
            $table->index('patient_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_family_members');
    }
};
