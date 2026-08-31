<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update thickness menjadi 10 untuk semua jenis material Carton
        DB::table('packing_material_prices')
            ->where('material_type', 'Carton')
            ->update([
                'thickness' => 10,
                'updated_at' => now()
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke null jika di rollback
        DB::table('packing_material_prices')
            ->where('material_type', 'Carton')
            ->update([
                'thickness' => null,
                'updated_at' => now()
            ]);
    }
};
