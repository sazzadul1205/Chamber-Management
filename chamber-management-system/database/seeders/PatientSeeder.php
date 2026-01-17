<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\PatientFamily;
use App\Models\PatientFamilyMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->info("No users found. Please seed users first.");
            return;
        }

        $createdBy = $users->first()->id;

        // Disable foreign key checks and truncate relevant tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PatientFamilyMember::truncate();
        PatientFamily::truncate();
        Patient::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $patients = [];

        // Create 20 sample patients
        for ($i = 1; $i <= 20; $i++) {
            $referralType = $faker->randomElement(['patient', 'doctor', 'magazine', 'other']);
            $referredByPatientId = null;
            $referredByText = null;

            if ($referralType === 'patient' && $i > 5) {
                $referredByPatientId = $faker->numberBetween(1, $i - 1);
            } elseif ($referralType !== 'patient') {
                $referredByText = ucfirst($referralType) . ' ' . $faker->name;
            }

            $patients[$i] = Patient::create([
                'full_name' => $faker->name,
                'phone' => $faker->unique()->numerify('01#########'),
                'email' => $faker->unique()->safeEmail,
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'date_of_birth' => $faker->dateTimeBetween('-70 years', '-1 year')->format('Y-m-d'),
                'address' => $faker->address,
                'referral_type' => $referralType,
                'referred_by_patient_id' => $referredByPatientId,
                'referred_by_text' => $referredByText,
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => now(),
            ]);
        }

        // --- Create Families Dynamically ---
        $familyData = [
            ['head' => 1, 'name' => 'Smith Family', 'members' => [2, 3]],
            ['head' => 6, 'name' => 'Johnson Family', 'members' => [7, 8]],
        ];

        foreach ($familyData as $f) {
            // Create family
            $family = PatientFamily::create([
                'head_patient_id' => $f['head'],
                'family_name' => $f['name'],
            ]);

            // Add members safely (skip head if included accidentally)
            foreach ($f['members'] as $memberId) {
                if ($memberId !== $f['head']) {
                    PatientFamilyMember::create([
                        'family_id' => $family->id,
                        'patient_id' => $memberId,
                    ]);
                }
            }
        }

        $this->command->info("✅ Patients and families seeded successfully!");
    }
}
