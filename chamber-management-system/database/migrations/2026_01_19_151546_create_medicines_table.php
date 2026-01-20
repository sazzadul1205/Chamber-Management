<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id(); // id int [pk, increment]
            $table->string('name'); // name varchar
            $table->string('unit'); // unit varchar
            $table->timestamps();

            // Index
            $table->index('name');
        });

        // Insert common medicines
        $this->seedCommonMedicines();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }

    /**
     * Seed common medicines.
     */
    private function seedCommonMedicines(): void
    {
        $medicines = [
            ['name' => 'Amoxicillin', 'unit' => 'capsule'],
            ['name' => 'Ibuprofen', 'unit' => 'tablet'],
            ['name' => 'Paracetamol', 'unit' => 'tablet'],
            ['name' => 'Chlorhexidine Mouthwash', 'unit' => 'bottle'],
            ['name' => 'Lidocaine 2%', 'unit' => 'vial'],
            ['name' => 'Diazepam', 'unit' => 'tablet'],
            ['name' => 'Clindamycin', 'unit' => 'capsule'],
            ['name' => 'Metronidazole', 'unit' => 'tablet'],
            ['name' => 'Fluoride Gel', 'unit' => 'tube'],
            ['name' => 'Antibiotic Paste', 'unit' => 'syringe'],
        ];

        foreach ($medicines as $medicine) {
            DB::table('medicines')->insert($medicine);
        }
    }
};
