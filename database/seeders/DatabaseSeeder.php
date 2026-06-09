<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Default super-admin account.
        // Change email and password immediately after first login.
        User::create([
            'name'     => 'MedRecords Admin',
            'email'    => 'admin@medrecords.ng',
            'phone'    => '+2348000000001',
            'role'     => 'super_admin',
            'password' => Hash::make('Admin@1234'),
        ]);
    }
}
