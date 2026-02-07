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

        // Insert sample families
        DB::table('patient_families')->insert([
            [
                'family_code' => 'FAM001',
                'family_name' => 'Karim Family',
                'head_patient_id' => 1, // Abdul Karim
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'family_code' => 'FAM002',
                'family_name' => 'Rahman Family',
                'head_patient_id' => 2, // Fatema Begum
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('patient_families');
    }
};
