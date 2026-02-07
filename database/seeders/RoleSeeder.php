<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Doctor', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Receptionist', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Accountant', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inventory Manager', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nurse/Assistant', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Patient', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('roles')->insert($roles);
    }
}
