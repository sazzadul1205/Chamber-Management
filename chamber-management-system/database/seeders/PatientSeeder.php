<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

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
        $patients = [];

        // Create 20 sample patients
        for ($i = 1; $i <= 20; $i++) {
            // Random referral type
            $referralType = $faker->randomElement(['patient', 'doctor', 'magazine', 'other']);

            // Determine referred_by_patient_id and referred_by_text
            $referredByPatientId = null;
            $referredByText = null;

            if ($referralType === 'patient' && $i > 5) {
                // Only refer to already created patients
                $referredByPatientId = $faker->numberBetween(1, $i - 1);
            } elseif ($referralType !== 'patient') {
                $referredByText = ucfirst($referralType) . ' ' . $faker->name;
            }

            $patients[$i] = Patient::create([
                // 'patient_code' => 'PT' . now()->format('Ym') . str_pad($i, 4, '0', STR_PAD_LEFT), // REMOVE
                'full_name' => $faker->name,
                'phone' => $faker->unique()->numerify('01#########'), // realistic phone
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
    }
}
