<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Dashboard & Analytics', 
                'icon' => 'fa-chart-pie', 
                'color' => 'text-primary',
                'available_permissions' => ['View Dashboard']
            ],
            [
                'name' => 'Data Akurasi', 
                'icon' => 'fa-bullseye', 
                'color' => 'text-success',
                'available_permissions' => ['View Data SO', 'View Data PO']
            ],
            [
                'name' => 'Manajemen Pengguna', 
                'icon' => 'fa-users', 
                'color' => 'text-info',
                'available_permissions' => ['View Daftar Pengguna', 'Create Pengguna', 'Edit Pengguna', 'Delete Pengguna', 'Mengatur Hak Akses']
            ],
            [
                'name' => 'Armada & Kendaraan', 
                'icon' => 'fa-truck', 
                'color' => 'text-danger',
                'available_permissions' => ['View Daftar Kendaraan', 'Create Kendaraan', 'Edit Kendaraan', 'Delete Kendaraan']
            ],
            [
                'name' => 'Packing', 
                'icon' => 'fa-box', 
                'color' => 'text-warning',
                'available_permissions' => ['View Packing', 'Create Packing', 'Edit Packing', 'Delete Packing']
            ],
            [
                'name' => 'Tugas Delivery & Pickup', 
                'icon' => 'fa-truck-fast', 
                'color' => 'text-primary',
                'available_permissions' => ['View Tugas', 'Create Tugas', 'Edit Tugas', 'Delete Tugas']
            ],
            [
                'name' => 'Find Driver', 
                'icon' => 'fa-map-location-dot', 
                'color' => 'text-info',
                'available_permissions' => ['View Find Driver']
            ],
            [
                'name' => 'Daftar Tugas', 
                'icon' => 'fa-list-check', 
                'color' => 'text-secondary',
                'available_permissions' => ['View Daftar Tugas']
            ],
            [
                'name' => 'HPP Ritase', 
                'icon' => 'fa-money-bill-wave', 
                'color' => 'text-success',
                'available_permissions' => ['View HPP Ritase', 'Export Data']
            ],
            [
                'name' => 'Pengeluaran', 
                'icon' => 'fa-wallet', 
                'color' => 'text-danger',
                'available_permissions' => ['View Pengeluaran', 'Create Pengeluaran', 'Edit Pengeluaran', 'Delete Pengeluaran']
            ],
        ];

        // Hapus semua data modul lama yang tidak ada di list di atas (opsional, untuk memastikan sinkron)
        $moduleNames = array_column($modules, 'name');
        \App\Models\Module::whereNotIn('name', $moduleNames)->delete();

        foreach ($modules as $module) {
            \App\Models\Module::updateOrCreate(
                ['name' => $module['name']], 
                $module
            );
        }
    }
}
