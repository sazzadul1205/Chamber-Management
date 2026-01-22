<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 30)->unique();
            $table->string('name', 100);
            $table->enum('category', [
                'consumable',
                'instrument',
                'equipment',
                'medicine_related',
                'dental_material',
                'protective_gear',
                'laboratory',
                'office_supplies',
                'other'
            ]);
            $table->string('subcategory', 50)->nullable();
            $table->string('unit', 20)->default('pcs');
            $table->text('description')->nullable();
            $table->string('manufacturer', 100)->nullable();
            $table->string('supplier', 100)->nullable();
            $table->integer('reorder_level')->default(10);
            $table->integer('optimum_level')->nullable();
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->timestamps();
        });

        // Insert common dental inventory items
        DB::table('inventory_items')->insert([
            // Consumables
            [
                'item_code' => 'GLOV-LATEX',
                'name' => 'Latex Examination Gloves',
                'category' => 'consumable',
                'subcategory' => 'gloves',
                'unit' => 'pair',
                'description' => 'Powder-free latex gloves, size medium',
                'manufacturer' => 'Ansell',
                'supplier' => 'BD Medical Supplies',
                'reorder_level' => 100,
                'optimum_level' => 500,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_code' => 'GLOV-NIT',
                'name' => 'Nitrile Examination Gloves',
                'category' => 'consumable',
                'subcategory' => 'gloves',
                'unit' => 'pair',
                'description' => 'Powder-free nitrile gloves, various sizes',
                'manufacturer' => 'Hartalega',
                'supplier' => 'BD Medical Supplies',
                'reorder_level' => 80,
                'optimum_level' => 400,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_code' => 'MASK-SURG',
                'name' => 'Surgical Face Mask',
                'category' => 'consumable',
                'subcategory' => 'masks',
                'unit' => 'pcs',
                'description' => '3-ply surgical mask with ear loops',
                'manufacturer' => '3M',
                'supplier' => 'BD Medical Supplies',
                'reorder_level' => 200,
                'optimum_level' => 1000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_code' => 'SYR-3ML',
                'name' => 'Disposable Syringe 3ml',
                'category' => 'consumable',
                'subcategory' => 'syringes',
                'unit' => 'pcs',
                'description' => '3ml syringe with needle',
                'manufacturer' => 'BD',
                'supplier' => 'BD Pharma',
                'reorder_level' => 50,
                'optimum_level' => 250,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_code' => 'NED-27G',
                'name' => 'Dental Needle 27G',
                'category' => 'consumable',
                'subcategory' => 'needles',
                'unit' => 'pcs',
                'description' => '27G dental injection needle',
                'manufacturer' => 'Terumo',
                'supplier' => 'BD Pharma',
                'reorder_level' => 100,
                'optimum_level' => 500,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Dental Materials
            [
                'item_code' => 'AMALGAM',
                'name' => 'Dental Amalgam',
                'category' => 'dental_material',
                'subcategory' => 'filling_materials',
                'unit' => 'capsule',
                'description' => 'Silver amalgam for fillings',
                'manufacturer' => 'Dentsply',
                'supplier' => 'Dental Depot BD',
                'reorder_level' => 20,
                'optimum_level' => 100,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_code' => 'COMP-A2',
                'name' => 'Composite Resin A2',
                'category' => 'dental_material',
                'subcategory' => 'filling_materials',
                'unit' => 'syringe',
                'description' => 'Tooth-colored composite resin, shade A2',
                'manufacturer' => '3M ESPE',
                'supplier' => 'Dental Depot BD',
                'reorder_level' => 15,
                'optimum_level' => 75,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_code' => 'GIC-LUT',
                'name' => 'Glass Ionomer Cement',
                'category' => 'dental_material',
                'subcategory' => 'cements',
                'unit' => 'kit',
                'description' => 'Glass ionomer luting cement',
                'manufacturer' => 'GC Corporation',
                'supplier' => 'Dental Depot BD',
                'reorder_level' => 10,
                'optimum_level' => 50,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_code' => 'IMPR-ALG',
                'name' => 'Alginate Impression Material',
                'category' => 'dental_material',
                'subcategory' => 'impression_materials',
                'unit' => 'can',
                'description' => 'Fast-set alginate impression material',
                'manufacturer' => 'Kerr',
                'supplier' => 'Dental Depot BD',
                'reorder_level' => 5,
                'optimum_level' => 25,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Instruments
            [
                'item_code' => 'EXCA-EX1',
                'name' => 'Excavator Set',
                'category' => 'instrument',
                'subcategory' => 'hand_instruments',
                'unit' => 'set',
                'description' => 'Set of dental excavators',
                'manufacturer' => 'Hu-Friedy',
                'supplier' => 'Dental Instruments BD',
                'reorder_level' => 2,
                'optimum_level' => 10,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_code' => 'MIR-HAND',
                'name' => 'Dental Mirror',
                'category' => 'instrument',
                'subcategory' => 'examination',
                'unit' => 'pcs',
                'description' => 'Front surface dental mirror',
                'manufacturer' => 'ASA Dental',
                'supplier' => 'Dental Instruments BD',
                'reorder_level' => 5,
                'optimum_level' => 25,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_code' => 'PROBE-WIL',
                'name' => 'Williams Periodontal Probe',
                'category' => 'instrument',
                'subcategory' => 'periodontal',
                'unit' => 'pcs',
                'description' => 'Periodontal probe with Williams markings',
                'manufacturer' => 'Hu-Friedy',
                'supplier' => 'Dental Instruments BD',
                'reorder_level' => 5,
                'optimum_level' => 20,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Protective Gear
            [
                'item_code' => 'GOWN-DIS',
                'name' => 'Disposable Gown',
                'category' => 'protective_gear',
                'subcategory' => 'gowns',
                'unit' => 'pcs',
                'description' => 'Non-woven disposable isolation gown',
                'manufacturer' => 'Cardinal Health',
                'supplier' => 'BD Medical Supplies',
                'reorder_level' => 50,
                'optimum_level' => 200,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_code' => 'FACE-SHIELD',
                'name' => 'Face Shield',
                'category' => 'protective_gear',
                'subcategory' => 'face_protection',
                'unit' => 'pcs',
                'description' => 'Reusable face shield with headband',
                'manufacturer' => '3M',
                'supplier' => 'BD Medical Supplies',
                'reorder_level' => 10,
                'optimum_level' => 50,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Medicine Related
            [
                'item_code' => 'LIDO-2',
                'name' => 'Lidocaine 2% with Epinephrine',
                'category' => 'medicine_related',
                'subcategory' => 'local_anesthetics',
                'unit' => 'cartridge',
                'description' => 'Local anesthetic cartridge 1.8ml',
                'manufacturer' => 'Septodont',
                'supplier' => 'BD Pharma',
                'reorder_level' => 50,
                'optimum_level' => 250,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_code' => 'GAUZE-2x2',
                'name' => 'Sterile Gauze 2x2 inches',
                'category' => 'medicine_related',
                'subcategory' => 'dressings',
                'unit' => 'pack',
                'description' => 'Sterile gauze pads, 100 per pack',
                'manufacturer' => 'Johnson & Johnson',
                'supplier' => 'BD Medical Supplies',
                'reorder_level' => 10,
                'optimum_level' => 50,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Office Supplies
            [
                'item_code' => 'CHART-PAT',
                'name' => 'Patient Chart Folder',
                'category' => 'office_supplies',
                'subcategory' => 'stationery',
                'unit' => 'pcs',
                'description' => 'Patient record file folder',
                'manufacturer' => 'Local',
                'supplier' => 'Stationery Mart',
                'reorder_level' => 20,
                'optimum_level' => 100,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
