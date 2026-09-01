<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('nail_size_rules')->truncate();

        $data = [
            // Papan ke Balok
            ['from' => 'papan', 'to' => 'balok', 'thk_from' => 18, 'thk_to' => 40, 'size_nails' => 4],
            ['from' => 'papan', 'to' => 'balok', 'thk_from' => 18, 'thk_to' => 60, 'size_nails' => 5],
            ['from' => 'papan', 'to' => 'balok', 'thk_from' => 18, 'thk_to' => 100, 'size_nails' => 5],
            ['from' => 'papan', 'to' => 'balok', 'thk_from' => 20, 'thk_to' => 40, 'size_nails' => 4],
            ['from' => 'papan', 'to' => 'balok', 'thk_from' => 20, 'thk_to' => 60, 'size_nails' => 5],
            ['from' => 'papan', 'to' => 'balok', 'thk_from' => 20, 'thk_to' => 100, 'size_nails' => 5],
            
            // Balok ke Balok Bawah
            ['from' => 'balok', 'to' => 'balok_bawah', 'thk_from' => 40, 'thk_to' => 40, 'size_nails' => 7],
            ['from' => 'balok', 'to' => 'balok_bawah', 'thk_from' => 40, 'thk_to' => 60, 'size_nails' => 0], // Tidak Pakai
            ['from' => 'balok', 'to' => 'balok_bawah', 'thk_from' => 40, 'thk_to' => 100, 'size_nails' => 7],
            ['from' => 'balok', 'to' => 'balok_bawah', 'thk_from' => 60, 'thk_to' => 40, 'size_nails' => 7],
            ['from' => 'balok', 'to' => 'balok_bawah', 'thk_from' => 60, 'thk_to' => 60, 'size_nails' => 10],
            ['from' => 'balok', 'to' => 'balok_bawah', 'thk_from' => 60, 'thk_to' => 100, 'size_nails' => 10],
        ];

        // Triplek ke Balok
        $tripleks = [3, 4, 6, 8, 9, 12, 15, 18];
        $baloks = [40, 60, 100];
        foreach ($tripleks as $t) {
            foreach ($baloks as $b) {
                $data[] = [
                    'from' => 'triplek',
                    'to' => 'balok',
                    'thk_from' => $t,
                    'thk_to' => $b,
                    'size_nails' => 4
                ];
            }
        }

        $now = now();
        foreach ($data as &$row) {
            $row['id'] = Str::uuid()->toString();
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        DB::table('nail_size_rules')->insert($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only data update, nothing to drop
    }
};
