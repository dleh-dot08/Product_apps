<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackingMaterialPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = base_path('../public.packing_material_prices.csv');

        if (!file_exists($csvPath)) {
            $this->command->warn("CSV file not found at {$csvPath}. Seeding skipped.");
            return;
        }

        $file = fopen($csvPath, 'r');
        $headers = fgetcsv($file); // Read headers

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($headers, $row);

            DB::table('packing_material_prices')->insert([
                'id'         => $data['id'] ?: Str::uuid(),
                'component'  => $data['component'],
                'size'       => $data['size'],
                'wood_type'  => $data['wood_type'],
                'unit'       => $data['unit'],
                'unit_price' => is_numeric($data['unit_price']) ? $data['unit_price'] : 0,
                'active'     => $data['active'] === 'true' || $data['active'] == 1,
                'thickness'  => is_numeric($data['thickness']) ? $data['thickness'] : null,
                'width'      => is_numeric($data['width']) ? $data['width'] : null,
                'length'     => is_numeric($data['length']) ? $data['length'] : null,
                'code'       => $data['code'],
                'created_at' => now(),
                'updated_at' => $data['updated_at'] ?? now(),
            ]);
        }

        fclose($file);
    }
}
