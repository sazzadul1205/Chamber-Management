<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('medicine_code', 20)->unique();
            $table->string('brand_name', 100);
            $table->string('generic_name', 100);
            $table->string('strength', 50)->nullable();
            $table->enum('dosage_form', [
                'tablet',
                'capsule',
                'syrup',
                'suspension',
                'injection',
                'gel',
                'paste',
                'ointment',
                'mouthwash',
                'spray',
                'drops',
                'powder',
                'cream',
                'solution',
                'other'
            ]);
            $table->string('unit', 20)->default('pcs');
            $table->string('manufacturer', 100)->nullable();
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->timestamps();
        });

        // Insert common dental medicines
        DB::table('medicines')->insert([
            // Analgesics (Pain Killers)
            [
                'medicine_code' => 'PARA-500',
                'brand_name' => 'Napa',
                'generic_name' => 'Paracetamol',
                'strength' => '500mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Beximco Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'IBU-400',
                'brand_name' => 'Ibugesic',
                'generic_name' => 'Ibuprofen',
                'strength' => '400mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Square Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'DCL-50',
                'brand_name' => 'Diclofenac Sodium',
                'generic_name' => 'Diclofenac',
                'strength' => '50mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Incepta Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'ACE-100',
                'brand_name' => 'Ace',
                'generic_name' => 'Aceclofenac',
                'strength' => '100mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Square Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'TRAM-50',
                'brand_name' => 'Tramal',
                'generic_name' => 'Tramadol',
                'strength' => '50mg',
                'dosage_form' => 'capsule',
                'unit' => 'strip',
                'manufacturer' => 'Incepta Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Antibiotics
            [
                'medicine_code' => 'AMX-500',
                'brand_name' => 'Amoclav',
                'generic_name' => 'Amoxicillin + Clavulanic Acid',
                'strength' => '500mg+125mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Square Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'AMP-500',
                'brand_name' => 'Amoxicillin',
                'generic_name' => 'Amoxicillin',
                'strength' => '500mg',
                'dosage_form' => 'capsule',
                'unit' => 'strip',
                'manufacturer' => 'Beximco Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'MET-400',
                'brand_name' => 'Flagyl',
                'generic_name' => 'Metronidazole',
                'strength' => '400mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Sanofi Aventis',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'CLR-250',
                'brand_name' => 'Claricin',
                'generic_name' => 'Clarithromycin',
                'strength' => '250mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Incepta Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'DOX-100',
                'brand_name' => 'Doxy',
                'generic_name' => 'Doxycycline',
                'strength' => '100mg',
                'dosage_form' => 'capsule',
                'unit' => 'strip',
                'manufacturer' => 'Square Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'CEF-500',
                'brand_name' => 'Cef-3',
                'generic_name' => 'Cefixime',
                'strength' => '500mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Square Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Local Anesthetics
            [
                'medicine_code' => 'LIDO-2',
                'brand_name' => 'Xylocaine',
                'generic_name' => 'Lidocaine',
                'strength' => '2%',
                'dosage_form' => 'injection',
                'unit' => 'ampoule',
                'manufacturer' => 'AstraZeneca',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'ART-4',
                'brand_name' => 'Septanest',
                'generic_name' => 'Articaine',
                'strength' => '4%',
                'dosage_form' => 'injection',
                'unit' => 'cartridge',
                'manufacturer' => 'Septodont',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Mouthwashes and Oral Rinses
            [
                'medicine_code' => 'CHX-MW',
                'brand_name' => 'Hexidine',
                'generic_name' => 'Chlorhexidine Gluconate',
                'strength' => '0.2%',
                'dosage_form' => 'mouthwash',
                'unit' => 'bottle',
                'manufacturer' => 'ACI Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'BET-MW',
                'brand_name' => 'Betadine Mouthwash',
                'generic_name' => 'Povidone Iodine',
                'strength' => '1%',
                'dosage_form' => 'mouthwash',
                'unit' => 'bottle',
                'manufacturer' => 'Beximco Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'FLU-MW',
                'brand_name' => 'Colgate Fluorigard',
                'generic_name' => 'Sodium Fluoride',
                'strength' => '0.05%',
                'dosage_form' => 'mouthwash',
                'unit' => 'bottle',
                'manufacturer' => 'Colgate-Palmolive',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Topical Applications
            [
                'medicine_code' => 'LIDO-GEL',
                'brand_name' => 'Xylocaine Gel',
                'generic_name' => 'Lidocaine',
                'strength' => '2%',
                'dosage_form' => 'gel',
                'unit' => 'tube',
                'manufacturer' => 'AstraZeneca',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'KENA-GEL',
                'brand_name' => 'Kenalog in Orabase',
                'generic_name' => 'Triamcinolone Acetonide',
                'strength' => '0.1%',
                'dosage_form' => 'gel',
                'unit' => 'tube',
                'manufacturer' => 'Squibb',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'APH-GEL',
                'brand_name' => 'Aphthasol',
                'generic_name' => 'Amlexanox',
                'strength' => '5%',
                'dosage_form' => 'paste',
                'unit' => 'tube',
                'manufacturer' => 'Uluru',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Muscle Relaxants
            [
                'medicine_code' => 'CHLO-5',
                'brand_name' => 'Myoril',
                'generic_name' => 'Chlorzoxazone',
                'strength' => '500mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Sanofi Aventis',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'TIZ-2',
                'brand_name' => 'Tizan',
                'generic_name' => 'Tizanidine',
                'strength' => '2mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Square Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Antifungal
            [
                'medicine_code' => 'FLU-150',
                'brand_name' => 'Flucan',
                'generic_name' => 'Fluconazole',
                'strength' => '150mg',
                'dosage_form' => 'capsule',
                'unit' => 'capsule',
                'manufacturer' => 'Beximco Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'NYST-SUS',
                'brand_name' => 'Nystatin',
                'generic_name' => 'Nystatin',
                'strength' => '100,000 IU/ml',
                'dosage_form' => 'suspension',
                'unit' => 'bottle',
                'manufacturer' => 'Square Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Corticosteroids
            [
                'medicine_code' => 'DEX-0.5',
                'brand_name' => 'Decason',
                'generic_name' => 'Dexamethasone',
                'strength' => '0.5mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Square Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'PRED-5',
                'brand_name' => 'Prednisolone',
                'generic_name' => 'Prednisolone',
                'strength' => '5mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Beximco Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Antacids and GI Medicines
            [
                'medicine_code' => 'PAN-40',
                'brand_name' => 'Pantop',
                'generic_name' => 'Pantoprazole',
                'strength' => '40mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Square Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'OME-20',
                'brand_name' => 'Omez',
                'generic_name' => 'Omeprazole',
                'strength' => '20mg',
                'dosage_form' => 'capsule',
                'unit' => 'strip',
                'manufacturer' => 'Dr. Reddys',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'RAN-150',
                'brand_name' => 'Ranitidine',
                'generic_name' => 'Ranitidine',
                'strength' => '150mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Beximco Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Emergency Medicines
            [
                'medicine_code' => 'ADR-1',
                'brand_name' => 'Adrenaline',
                'generic_name' => 'Epinephrine',
                'strength' => '1mg/ml',
                'dosage_form' => 'injection',
                'unit' => 'ampoule',
                'manufacturer' => 'Sanofi Aventis',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'CHLOR-10',
                'brand_name' => 'Chlorpheniramine',
                'generic_name' => 'Chlorpheniramine Maleate',
                'strength' => '10mg',
                'dosage_form' => 'injection',
                'unit' => 'ampoule',
                'manufacturer' => 'Square Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'DEX-INJ',
                'brand_name' => 'Decason Injection',
                'generic_name' => 'Dexamethasone',
                'strength' => '4mg/ml',
                'dosage_form' => 'injection',
                'unit' => 'ampoule',
                'manufacturer' => 'Square Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Dental Specific
            [
                'medicine_code' => 'TRAN-HEM',
                'brand_name' => 'Tranexamic Acid',
                'generic_name' => 'Tranexamic Acid',
                'strength' => '500mg',
                'dosage_form' => 'tablet',
                'unit' => 'strip',
                'manufacturer' => 'Beximco Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'HYD-PER',
                'brand_name' => 'Hydrogen Peroxide',
                'generic_name' => 'Hydrogen Peroxide',
                'strength' => '3%',
                'dosage_form' => 'solution',
                'unit' => 'bottle',
                'manufacturer' => 'Local',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medicine_code' => 'SAL-SOL',
                'brand_name' => 'Saline Solution',
                'generic_name' => 'Sodium Chloride',
                'strength' => '0.9%',
                'dosage_form' => 'solution',
                'unit' => 'bottle',
                'manufacturer' => 'Beximco Pharmaceuticals',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
