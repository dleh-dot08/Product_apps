<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Division;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles
        $roles = [
            'Super Admin',
            'Manager',
            'Supervisor (SPV)',
            'Admin Staff',
            'Operator Staff'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Seed Divisions
        $divisions = [
            'Logistik',
            'Driver (Pengiriman)',
            'Accounting',
            'Sales',
            'Warehouse',
            'HR & Admin'
        ];

        foreach ($divisions as $divisionName) {
            Division::firstOrCreate(['name' => $divisionName]);
        }

        // 3. Update existing users (if any) or create a super admin
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $hrDivision = Division::where('name', 'HR & Admin')->first();

        // Check if admin user exists, if not create one
        if (!User::where('email', 'admin@aqpa.co.id')->exists()) {
            User::create([
                'username' => 'superadmin',
                'full_name' => 'Super Administrator',
                'email' => 'admin@aqpa.co.id',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'role_id' => $superAdminRole->id ?? null,
                'division_id' => $hrDivision->id ?? null,
            ]);
        }
    }
}
