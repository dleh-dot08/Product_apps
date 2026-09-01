<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $data = [
            ['code' => 'KAYU-BALOK-MHNI-100-060-3000', 'component' => 'Balok', 'material_type' => 'MAHONI', 'thickness' => 60, 'width' => 100, 'length' => 3000, 'unit' => 'Batang', 'unit_price' => 45000],
            ['code' => 'KAYU-BALOK-MHNI-060-040-3000', 'component' => 'Balok', 'material_type' => 'MAHONI', 'thickness' => 40, 'width' => 60, 'length' => 3000, 'unit' => 'Batang', 'unit_price' => 17500],
            ['code' => 'KAYU-PAPAN-MHNI-020-150-3000', 'component' => 'Papan Kayu', 'material_type' => 'MAHONI', 'thickness' => 20, 'width' => 150, 'length' => 3000, 'unit' => 'Lembar', 'unit_price' => 14000],
            ['code' => 'KAYU-PAPAN-JTBL-018-150-0110', 'component' => 'Papan Kayu', 'material_type' => 'Jati Belanda', 'thickness' => 18, 'width' => 90, 'length' => 1100, 'unit' => 'Lembar', 'unit_price' => 10000],
            ['code' => 'KAYU-PAPAN-JINJ-020-150-3000', 'component' => 'Papan Kayu', 'material_type' => 'JINJING', 'thickness' => 20, 'width' => 150, 'length' => 3000, 'unit' => 'Lembar', 'unit_price' => 6000],
            ['code' => 'KAYU-TRIPL-TUNS-003-122-0244', 'component' => 'Triplek', 'material_type' => 'TUNAS', 'thickness' => 3, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 55000],
            ['code' => 'KAYU-TRIPL-MRNT-003-122-0244', 'component' => 'Triplek', 'material_type' => 'MERANTI', 'thickness' => 3, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 65000],
            ['code' => 'KAYU-TRIPL-TUNS-004-122-0244', 'component' => 'Triplek', 'material_type' => 'TUNAS', 'thickness' => 4, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 65000],
            ['code' => 'KAYU-TRIPL-MRNT-004-122-0244', 'component' => 'Triplek', 'material_type' => 'MERANTI', 'thickness' => 4, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 80000],
            ['code' => 'KAYU-TRIPL-TUNS-006-122-0244', 'component' => 'Triplek', 'material_type' => 'TUNAS', 'thickness' => 6, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 80000],
            ['code' => 'KAYU-TRIPL-MRNT-006-122-0244', 'component' => 'Triplek', 'material_type' => 'MERANTI', 'thickness' => 6, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 90000],
            ['code' => 'KAYU-TRIPL-TUNS-008-122-0244', 'component' => 'Triplek', 'material_type' => 'TUNAS', 'thickness' => 8, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 105000],
            ['code' => 'KAYU-TRIPL-MRNT-008-122-0244', 'component' => 'Triplek', 'material_type' => 'MERANTI', 'thickness' => 8, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 120000],
            ['code' => 'KAYU-TRIPL-TUNS-009-122-0244', 'component' => 'Triplek', 'material_type' => 'TUNAS', 'thickness' => 9, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 120000],
            ['code' => 'KAYU-TRIPL-MRNT-009-122-0244', 'component' => 'Triplek', 'material_type' => 'MERANTI', 'thickness' => 9, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 125000],
            ['code' => 'KAYU-TRIPL-TUNS-012-122-0244', 'component' => 'Triplek', 'material_type' => 'TUNAS', 'thickness' => 12, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 140000],
            ['code' => 'KAYU-TRIPL-MRNT-012-122-0244', 'component' => 'Triplek', 'material_type' => 'MERANTI', 'thickness' => 12, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 160000],
            ['code' => 'KAYU-TRIPL-TUNS-015-122-0244', 'component' => 'Triplek', 'material_type' => 'TUNAS', 'thickness' => 15, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 175000],
            ['code' => 'KAYU-TRIPL-MRNT-015-122-0244', 'component' => 'Triplek', 'material_type' => 'MERANTI', 'thickness' => 15, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 185000],
            ['code' => 'KAYU-TRIPL-TUNS-018-122-0244', 'component' => 'Triplek', 'material_type' => 'TUNAS', 'thickness' => 18, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 200000],
            ['code' => 'KAYU-TRIPL-MRNT-018-122-0244', 'component' => 'Triplek', 'material_type' => 'MERANTI', 'thickness' => 18, 'width' => 1220, 'length' => 2440, 'unit' => 'Lembar', 'unit_price' => 210000],
        ];

        $codesToKeep = array_column($data, 'code');

        // Delete records for Balok, Papan Kayu, Triplek that are not in the new list
        DB::table('packing_material_prices')
            ->whereIn('component', ['Balok', 'Papan Kayu', 'Triplek'])
            ->whereNotIn('code', $codesToKeep)
            ->delete();

        // Update or Insert the new data
        foreach ($data as $item) {
            $existing = DB::table('packing_material_prices')->where('code', $item['code'])->first();

            if ($existing) {
                DB::table('packing_material_prices')
                    ->where('code', $item['code'])
                    ->update([
                        'component' => $item['component'],
                        'material_type' => $item['material_type'],
                        'thickness' => $item['thickness'],
                        'width' => $item['width'],
                        'length' => $item['length'],
                        'unit' => $item['unit'],
                        'unit_price' => $item['unit_price'],
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('packing_material_prices')->insert([
                    'id' => Str::uuid(),
                    'code' => $item['code'],
                    'component' => $item['component'],
                    'material_type' => $item['material_type'],
                    'thickness' => $item['thickness'],
                    'width' => $item['width'],
                    'length' => $item['length'],
                    'unit' => $item['unit'],
                    'unit_price' => $item['unit_price'],
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration logic provided as it requires restoring old state
    }
};
