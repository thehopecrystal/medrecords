<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Default super-admin account — stored in the admins table.
        // Email is pre-verified so no verification step on first login.
        // Change email and password immediately after first login.
        $admin = Admin::create([
            'name'     => 'MedRecords Admin',
            'email'    => 'admin@medrecords.ng',
            'phone'    => '+2348000000001',
            'password' => Hash::make('Admin@1234'),
        ]);
        $admin->role = 'super_admin';
        $admin->save();
        $admin->markEmailAsVerified();
    }
}
