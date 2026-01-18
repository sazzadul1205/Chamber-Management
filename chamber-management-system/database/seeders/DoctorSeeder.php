<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        // Make sure 'doctors' folder exists in storage/app/public
        if (!Storage::disk('public')->exists('doctors')) {
            Storage::disk('public')->makeDirectory('doctors');
        }

        // Doctor data array
        $doctors = [
            [
                'email' => 'doctor1@dentist.com',
                'name' => 'Dr. John Smith',
                'phone' => '9876543210',
                'specialization' => 'Orthodontist',
                'designation' => 'Consultant',
                'experience_years' => 10,
                'qualification' => 'BDS, MDS',
                'bio' => 'Experienced Orthodontist specializing in braces and aligners.',
                'consultation_fee' => 500.00,
                'commission_percent' => 30.00,
                'photo' => null, // Can be updated later
            ],
            [
                'email' => 'doctor2@dentist.com',
                'name' => 'Dr. Sarah Johnson',
                'phone' => '9876543211',
                'specialization' => 'Periodontist',
                'designation' => 'Senior Doctor',
                'experience_years' => 8,
                'qualification' => 'BDS, MDS',
                'bio' => 'Specializes in gum treatments and oral surgeries.',
                'consultation_fee' => 450.00,
                'commission_percent' => 25.00,
                'photo' => null,
            ],
        ];

        foreach ($doctors as $data) {
            // Create user
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'role_id' => 3,
                    'name' => $data['name'],
                    'full_name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('password123'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            // Create doctor profile
            Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization' => $data['specialization'],
                    'designation' => $data['designation'],
                    'experience_years' => $data['experience_years'],
                    'qualification' => $data['qualification'],
                    'bio' => $data['bio'],
                    'consultation_fee' => $data['consultation_fee'],
                    'commission_percent' => $data['commission_percent'],
                    'photo' => $data['photo'], // null or placeholder
                ]
            );
        }
    }
}
