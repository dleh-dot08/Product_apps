<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackagingFastenerValidationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('packaging_fastener_validations')->insert([
            [
                'is_active' => true,
                'type_from' => 'Balok Kayu',
                'thk_from_min_mm' => 10,
                'thk_from_max_mm' => 50,
                'type_to' => 'Balok Kayu',
                'thk_to_min_mm' => 10,
                'thk_to_max_mm' => 50,
                'nail_code' => 'NAIL-1',
                'nail_length_mm' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'is_active' => true,
                'type_from' => 'Plywood',
                'thk_from_min_mm' => 0,
                'thk_from_max_mm' => 20,
                'type_to' => 'Balok Kayu',
                'thk_to_min_mm' => 10,
                'thk_to_max_mm' => 100,
                'nail_code' => 'NAIL-2',
                'nail_length_mm' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'is_active' => true,
                'type_from' => 'Papan Kayu',
                'thk_from_min_mm' => 10,
                'thk_from_max_mm' => 30,
                'type_to' => 'Balok Kayu',
                'thk_to_min_mm' => 20,
                'thk_to_max_mm' => 80,
                'nail_code' => 'NAIL-3',
                'nail_length_mm' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
