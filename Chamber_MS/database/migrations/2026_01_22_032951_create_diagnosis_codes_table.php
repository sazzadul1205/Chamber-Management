<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosis_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('description', 255);
            $table->string('category', 50);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Insert common dental diagnosis codes (ICD-10 based)
        DB::table('diagnosis_codes')->insert([
            // K00-K14 Diseases of oral cavity, salivary glands and jaws
            [
                'code' => 'K02.0',
                'description' => 'Caries limited to enamel',
                'category' => 'dental_caries',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K02.1',
                'description' => 'Caries of dentine',
                'category' => 'dental_caries',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K02.2',
                'description' => 'Caries of cementum',
                'category' => 'dental_caries',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K02.3',
                'description' => 'Arrested dental caries',
                'category' => 'dental_caries',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K02.9',
                'description' => 'Dental caries, unspecified',
                'category' => 'dental_caries',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // K04 Diseases of pulp and periapical tissues
            [
                'code' => 'K04.0',
                'description' => 'Pulpitis',
                'category' => 'pulp_diseases',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K04.1',
                'description' => 'Necrosis of pulp',
                'category' => 'pulp_diseases',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K04.2',
                'description' => 'Pulp degeneration',
                'category' => 'pulp_diseases',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K04.3',
                'description' => 'Abnormal hard tissue formation in pulp',
                'category' => 'pulp_diseases',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K04.4',
                'description' => 'Acute apical periodontitis of pulpal origin',
                'category' => 'periodontal_diseases',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K04.5',
                'description' => 'Chronic apical periodontitis',
                'category' => 'periodontal_diseases',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K04.7',
                'description' => 'Periapical abscess without sinus',
                'category' => 'abscess',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K04.8',
                'description' => 'Radicular cyst',
                'category' => 'cysts',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // K05 Gingivitis and periodontal diseases
            [
                'code' => 'K05.0',
                'description' => 'Acute gingivitis',
                'category' => 'gingivitis',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K05.1',
                'description' => 'Chronic gingivitis',
                'category' => 'gingivitis',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K05.2',
                'description' => 'Acute periodontitis',
                'category' => 'periodontitis',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K05.3',
                'description' => 'Chronic periodontitis',
                'category' => 'periodontitis',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K05.4',
                'description' => 'Periodontosis',
                'category' => 'periodontitis',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K05.5',
                'description' => 'Other periodontal diseases',
                'category' => 'periodontitis',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // K06 Other disorders of gingiva and edentulous alveolar ridge
            [
                'code' => 'K06.0',
                'description' => 'Gingival recession',
                'category' => 'gingival_disorders',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K06.1',
                'description' => 'Gingival enlargement',
                'category' => 'gingival_disorders',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K06.2',
                'description' => 'Gingival and edentulous alveolar ridge lesions associated with trauma',
                'category' => 'gingival_disorders',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // K07 Dentofacial anomalies [including malocclusion]
            [
                'code' => 'K07.0',
                'description' => 'Major anomalies of jaw size',
                'category' => 'dentofacial_anomalies',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K07.1',
                'description' => 'Anomalies of jaw-cranial base relationship',
                'category' => 'dentofacial_anomalies',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K07.2',
                'description' => 'Anomalies of dental arch relationship',
                'category' => 'dentofacial_anomalies',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K07.3',
                'description' => 'Anomalies of tooth position',
                'category' => 'dentofacial_anomalies',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K07.4',
                'description' => 'Malocclusion, unspecified',
                'category' => 'malocclusion',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K07.5',
                'description' => 'Dentofacial functional abnormalities',
                'category' => 'dentofacial_anomalies',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // K08 Other disorders of teeth and supporting structures
            [
                'code' => 'K08.0',
                'description' => 'Exfoliation of teeth due to systemic causes',
                'category' => 'tooth_disorders',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K08.1',
                'description' => 'Loss of teeth due to accident, extraction or local periodontal disease',
                'category' => 'tooth_disorders',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K08.2',
                'description' => 'Atrophy of edentulous alveolar ridge',
                'category' => 'alveolar_disorders',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K08.3',
                'description' => 'Retained dental root',
                'category' => 'tooth_disorders',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // K09 Cysts of oral region, not elsewhere classified
            [
                'code' => 'K09.0',
                'description' => 'Developmental odontogenic cysts',
                'category' => 'cysts',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K09.1',
                'description' => 'Developmental (nonodontogenic) cysts of oral region',
                'category' => 'cysts',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // K10 Other diseases of jaws
            [
                'code' => 'K10.0',
                'description' => 'Developmental disorders of jaws',
                'category' => 'jaw_disorders',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K10.1',
                'description' => 'Giant cell granuloma, central',
                'category' => 'jaw_disorders',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // K11 Diseases of salivary glands
            [
                'code' => 'K11.0',
                'description' => 'Atrophy of salivary gland',
                'category' => 'salivary_gland',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K11.1',
                'description' => 'Hypertrophy of salivary gland',
                'category' => 'salivary_gland',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K11.2',
                'description' => 'Sialoadenitis',
                'category' => 'salivary_gland',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // K12 Stomatitis and related lesions
            [
                'code' => 'K12.0',
                'description' => 'Recurrent oral aphthae',
                'category' => 'stomatitis',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K12.1',
                'description' => 'Other forms of stomatitis',
                'category' => 'stomatitis',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // K13 Other diseases of lip and oral mucosa
            [
                'code' => 'K13.0',
                'description' => 'Diseases of lips',
                'category' => 'oral_mucosa',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K13.1',
                'description' => 'Cheek and lip biting',
                'category' => 'oral_mucosa',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'K13.2',
                'description' => 'Leukoplakia and other disturbances of oral epithelium',
                'category' => 'oral_mucosa',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Common dental conditions (non-ICD)
            [
                'code' => 'DC001',
                'description' => 'Impacted tooth',
                'category' => 'common_conditions',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'DC002',
                'description' => 'Tooth sensitivity',
                'category' => 'common_conditions',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'DC003',
                'description' => 'Bruxism (teeth grinding)',
                'category' => 'common_conditions',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'DC004',
                'description' => 'Temporomandibular Joint Disorder (TMJ)',
                'category' => 'common_conditions',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'DC005',
                'description' => 'Halitosis (bad breath)',
                'category' => 'common_conditions',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'DC006',
                'description' => 'Dental trauma/fracture',
                'category' => 'common_conditions',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'DC007',
                'description' => 'Discoloration of teeth',
                'category' => 'common_conditions',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'DC008',
                'description' => 'Post-extraction complication',
                'category' => 'common_conditions',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'DC009',
                'description' => 'Dental abscess',
                'category' => 'common_conditions',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'DC010',
                'description' => 'Dry socket (alveolar osteitis)',
                'category' => 'common_conditions',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosis_codes');
    }
};
