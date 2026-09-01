<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('packing_material_prices', function (Blueprint $table) {
            $table->renameColumn('wood_type', 'material_type');
            $table->decimal('height', 10, 2)->nullable()->after('thickness');
        });

        // 2. Insert data Carton & Terpal
        DB::unprepared("
            INSERT INTO public.packing_material_prices 
                (id, code, component, material_type, unit, unit_price, active, height, width, length, created_at, updated_at)
            VALUES 
                (gen_random_uuid(), 'CARTON-COKLAT-POLOS', 'Carton Box Cokelat, Size : 300mm x 300mm x 115mm', 'Carton', 'Pcs', 4777, TRUE, 115, 300, 300, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-ISI-2-POLOS', 'Carton Box Cokelat, Size : 350mm x 170mm x 130mm', 'Carton', 'Pcs', 2600, TRUE, 130, 170, 350, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-ISI-2-SABLON', 'Carton Box Cokelat, Size : 350mm x 170mm x 130mm (Include Sablon AQPA)', 'Carton', 'Pcs', 3364, TRUE, 130, 170, 350, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-ISI-4-POLOS', 'Carton Box Cokelat, Size : 350mm x 350mm x 130mm', 'Carton', 'Pcs', 5526, TRUE, 130, 350, 350, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-ISI-4-SABLON', 'Carton Box  Size :  Gland Packing (Isi 4 Sablon) Size 350mm x 350mm x 130mm', 'Carton', 'Pcs', 5919, TRUE, 130, 350, 350, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-ISI-5-POLOS', 'Carton Box Cokelat Size, P : 580mm x L : 310mm x T : 310mm', 'Carton', 'Pcs', 8393, TRUE, 310, 310, 580, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-ISI-5-SABLON', 'Carton Box Cokelat Size, P : 580mm x L : 310mm x T : 310mm (Include Sablon AQPA)', 'Carton', 'Pcs', 9536, TRUE, 310, 310, 580, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-ISI-8-POLOS', 'Carton Box Cokelat, Size : 350mm x 350mm x 230mm', 'Carton', 'Pcs', 5373, TRUE, 230, 350, 350, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-ISI-8-SABLON', 'Carton Box Cokelat, Size : 350mm x 350mm x 230mm (Include Sablon AQPA)', 'Carton', 'Pcs', 6956, TRUE, 230, 350, 350, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-PUTIH-SABLON', 'Carton Box Putih, Size : 300mm x 300mm x 115mm (Include Sablon GTE)', 'Carton', 'Pcs', 6168, TRUE, 115, 300, 300, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-TJ-POLOS', 'Carton Box Cokelat, Size : 700mm x 560mm x 300mm', 'Carton', 'Pcs', 23046, TRUE, 300, 560, 700, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-TJ-SABLON', 'Carton Box Cokelat, Size : 700mm x 560mm x 300mm (Include Sablon AQPA)', 'Carton', 'Pcs', 25507, TRUE, 300, 560, 700, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-TJ-SABLON-700-560-500', 'Carton Box Cokelat, Size : 700mm x 560mm x 500mm (Include Sablon AQPA)', 'Carton', 'Pcs', 30511, TRUE, 500, 560, 700, NOW(), NOW()),
                (gen_random_uuid(), 'CARTON-LEMBARAN', 'Carton Box Cokelat Lembaran, Size : 1000mm x 1000mm', 'Carton', 'Pcs', 10200, TRUE, NULL, 1000, 1000, NOW(), NOW()),
                (gen_random_uuid(), 'TERPAL-001', 'TERPAL A2, SIZE : 2000MM x 100000MM', 'Terpal', 'Lbr', 600000, TRUE, NULL, 2000, 100000, NOW(), NOW())
            ON CONFLICT (code) DO UPDATE SET 
                component = EXCLUDED.component,
                material_type = EXCLUDED.material_type,
                unit = EXCLUDED.unit,
                unit_price = EXCLUDED.unit_price,
                height = EXCLUDED.height,
                width = EXCLUDED.width,
                length = EXCLUDED.length,
                updated_at = NOW();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $codes = [
            'CARTON-COKLAT-POLOS', 'CARTON-ISI-2-POLOS', 'CARTON-ISI-2-SABLON',
            'CARTON-ISI-4-POLOS', 'CARTON-ISI-4-SABLON', 'CARTON-ISI-5-POLOS',
            'CARTON-ISI-5-SABLON', 'CARTON-ISI-8-POLOS', 'CARTON-ISI-8-SABLON',
            'CARTON-PUTIH-SABLON', 'CARTON-TJ-POLOS', 'CARTON-TJ-SABLON',
            'CARTON-TJ-SABLON-700-560-500', 'CARTON-LEMBARAN', 'TERPAL-001'
        ];
        
        DB::table('packing_material_prices')->whereIn('code', $codes)->delete();

        Schema::table('packing_material_prices', function (Blueprint $table) {
            $table->dropColumn('height');
            $table->renameColumn('material_type', 'wood_type');
        });
    }
};
