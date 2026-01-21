<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedure_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('procedure_code', 20)->unique();
            $table->string('procedure_name', 100);
            $table->string('category', 50);
            $table->integer('standard_duration')->comment('in minutes');
            $table->decimal('standard_cost', 10, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Insert common dental procedures
        DB::table('procedure_catalog')->insert([
            // Diagnostic Procedures
            [
                'procedure_code' => 'CONSULT',
                'procedure_name' => 'Consultation',
                'category' => 'diagnostic',
                'standard_duration' => 15,
                'standard_cost' => 500.00,
                'description' => 'Initial dental consultation',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'procedure_code' => 'XR-PANO',
                'procedure_name' => 'Panoramic X-Ray',
                'category' => 'diagnostic',
                'standard_duration' => 10,
                'standard_cost' => 1000.00,
                'description' => 'Full mouth panoramic X-ray',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'procedure_code' => 'XR-PA',
                'procedure_name' => 'Periapical X-Ray',
                'category' => 'diagnostic',
                'standard_duration' => 5,
                'standard_cost' => 300.00,
                'description' => 'Single tooth X-ray',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Preventive Procedures
            [
                'procedure_code' => 'SCALING',
                'procedure_name' => 'Scaling',
                'category' => 'preventive',
                'standard_duration' => 30,
                'standard_cost' => 800.00,
                'description' => 'Teeth cleaning and scaling',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'procedure_code' => 'POLISHING',
                'procedure_name' => 'Polishing',
                'category' => 'preventive',
                'standard_duration' => 15,
                'standard_cost' => 400.00,
                'description' => 'Teeth polishing after scaling',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'procedure_code' => 'FLUORIDE',
                'procedure_name' => 'Fluoride Treatment',
                'category' => 'preventive',
                'standard_duration' => 10,
                'standard_cost' => 600.00,
                'description' => 'Fluoride application',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Restorative Procedures
            [
                'procedure_code' => 'AMALGAM',
                'procedure_name' => 'Amalgam Filling',
                'category' => 'restorative',
                'standard_duration' => 30,
                'standard_cost' => 1500.00,
                'description' => 'Silver amalgam filling',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'procedure_code' => 'COMPOSITE',
                'procedure_name' => 'Composite Filling',
                'category' => 'restorative',
                'standard_duration' => 45,
                'standard_cost' => 2000.00,
                'description' => 'Tooth-colored composite filling',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'procedure_code' => 'GIC',
                'procedure_name' => 'GIC Filling',
                'category' => 'restorative',
                'standard_duration' => 30,
                'standard_cost' => 1200.00,
                'description' => 'Glass Ionomer Cement filling',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Endodontic Procedures
            [
                'procedure_code' => 'RCT',
                'procedure_name' => 'Root Canal Treatment',
                'category' => 'endodontic',
                'standard_duration' => 60,
                'standard_cost' => 5000.00,
                'description' => 'Single root canal treatment',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'procedure_code' => 'RCT-M',
                'procedure_name' => 'RCT Molar',
                'category' => 'endodontic',
                'standard_duration' => 90,
                'standard_cost' => 8000.00,
                'description' => 'Molar root canal treatment',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Prosthodontic Procedures
            [
                'procedure_code' => 'CROWN',
                'procedure_name' => 'Dental Crown',
                'category' => 'prosthodontic',
                'standard_duration' => 60,
                'standard_cost' => 10000.00,
                'description' => 'Porcelain fused to metal crown',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'procedure_code' => 'BRIDGE',
                'procedure_name' => 'Dental Bridge',
                'category' => 'prosthodontic',
                'standard_duration' => 120,
                'standard_cost' => 25000.00,
                'description' => 'Three unit bridge',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'procedure_code' => 'DENTURE',
                'procedure_name' => 'Complete Denture',
                'category' => 'prosthodontic',
                'standard_duration' => 180,
                'standard_cost' => 15000.00,
                'description' => 'Full denture upper/lower',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Periodontic Procedures
            [
                'procedure_code' => 'SRP',
                'procedure_name' => 'Scaling & Root Planing',
                'category' => 'periodontic',
                'standard_duration' => 60,
                'standard_cost' => 3000.00,
                'description' => 'Deep cleaning for gum disease',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Oral Surgery
            [
                'procedure_code' => 'EXTRACT',
                'procedure_name' => 'Simple Extraction',
                'category' => 'oral_surgery',
                'standard_duration' => 30,
                'standard_cost' => 1500.00,
                'description' => 'Simple tooth extraction',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'procedure_code' => 'EXTRACT-S',
                'procedure_name' => 'Surgical Extraction',
                'category' => 'oral_surgery',
                'standard_duration' => 60,
                'standard_cost' => 5000.00,
                'description' => 'Surgical tooth extraction',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'procedure_code' => 'IMP-1',
                'procedure_name' => 'Single Implant',
                'category' => 'oral_surgery',
                'standard_duration' => 120,
                'standard_cost' => 40000.00,
                'description' => 'Single dental implant placement',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Orthodontic Procedures
            [
                'procedure_code' => 'BRA-FIX',
                'procedure_name' => 'Fixed Braces',
                'category' => 'orthodontic',
                'standard_duration' => 120,
                'standard_cost' => 50000.00,
                'description' => 'Fixed orthodontic braces',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Pediatric Dentistry
            [
                'procedure_code' => 'PIT-SEAL',
                'procedure_name' => 'Pit & Fissure Sealant',
                'category' => 'pediatric',
                'standard_duration' => 20,
                'standard_cost' => 800.00,
                'description' => 'Sealant for children',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('procedure_catalog');
    }
};
