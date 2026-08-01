<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil MasterDataSeeder untuk membuat roles dan divisions
        $this->call([MasterDataSeeder::class]);

        $superAdminRole = \App\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        $driverRole = \App\Models\Role::firstOrCreate(['name' => 'Driver']);
        $packerRole = \App\Models\Role::firstOrCreate(['name' => 'Packer']);
        
        $hrDivision = \App\Models\Division::firstOrCreate(['name' => 'HR & Admin']);
        $driverDivision = \App\Models\Division::firstOrCreate(['name' => 'Driver (Pengiriman)']);
        $warehouseDivision = \App\Models\Division::firstOrCreate(['name' => 'Warehouse']);

        // Buat akun Super Admin default
        User::updateOrCreate(['email' => 'admin@mail.com'], [
            'username' => 'admin',
            'full_name' => 'Administrator',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'super_admin',
            'role_id' => $superAdminRole->id,
            'division_id' => $hrDivision->id,
            'active' => true,
        ]);

        // Buat akun Driver 1, 2, 3
        for ($i = 1; $i <= 3; $i++) {
            User::updateOrCreate(['email' => "driver{$i}@mail.com"], [
                'username' => "driver{$i}",
                'full_name' => "Driver {$i}",
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'driver',
                'role_id' => $driverRole->id,
                'division_id' => $driverDivision->id,
                'active' => true,
            ]);
        }

        // Buat akun Packer 1, 2, 3
        for ($i = 1; $i <= 3; $i++) {
            User::updateOrCreate(['email' => "packer{$i}@mail.com"], [
                'username' => "packer{$i}",
                'full_name' => "Packer {$i}",
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'packer',
                'role_id' => $packerRole->id,
                'division_id' => $warehouseDivision->id,
                'active' => true,
            ]);
        }
    }
}
