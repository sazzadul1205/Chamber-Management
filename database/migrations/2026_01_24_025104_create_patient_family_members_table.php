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

        // Insert sample family members
        DB::table('patient_family_members')->insert([
            // Karim Family
            [
                'family_id' => 1,
                'patient_id' => 1, // Abdul Karim
                'relationship' => 'self',
                'is_head' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Rahman Family
            [
                'family_id' => 2,
                'patient_id' => 2, // Fatema Begum
                'relationship' => 'self',
                'is_head' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('patient_family_members');
    }
};
