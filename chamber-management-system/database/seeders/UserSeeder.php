<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $password = Hash::make('123456789');
        $users = [];

        // ======================
        // ADMIN (ONLY ONE)
        // ======================
        $users[] = [
            'role_id'    => 1,
            'name'       => 'Admin',
            'full_name'  => 'Admin',
            'phone'      => '01700000001',
            'email'      => 'admin1205@example.com',
            'password'   => $password,
            'status'     => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // ======================
        // OTHER ROLES (3 USERS EACH)
        // ======================
        $roles = [
            2 => 'Manager',
            3 => 'Doctor',
            4 => 'Receptionist',
            5 => 'Accountant',
            6 => 'InventoryManager',
            7 => 'LabTechnician',
            8 => 'Patient',
            9 => 'Nurse',
        ];

        $phoneCounter = 2;

        foreach ($roles as $roleId => $roleKey) {
            for ($i = 1; $i <= 3; $i++) {
                $users[] = [
                    'role_id'    => $roleId,
                    'name'       => $roleKey . $i,
                    'full_name'  => str_replace('', ' ', $roleKey) . ' User ' . $i,
                    'phone'      => '017000000' . str_pad($phoneCounter, 2, '0', STR_PAD_LEFT),
                    'email'      => strtolower($roleKey) . $i . '@example.com',
                    'password'   => $password,
                    'status'     => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $phoneCounter++;
            }
        }

        DB::table('users')->insert($users);
    }
}
