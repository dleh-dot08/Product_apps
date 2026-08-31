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
        // Memisahkan string Size : ... dari Component dan memindahkannya ke kolom size
        $updates = [
            'CARTON-COKLAT-POLOS' => ['component' => 'Carton Box Cokelat', 'size' => '300mm x 300mm x 115mm'],
            'CARTON-ISI-2-POLOS' => ['component' => 'Carton Box Cokelat', 'size' => '350mm x 170mm x 130mm'],
            'CARTON-ISI-2-SABLON' => ['component' => 'Carton Box Cokelat', 'size' => '350mm x 170mm x 130mm (Include Sablon AQPA)'],
            'CARTON-ISI-4-POLOS' => ['component' => 'Carton Box Cokelat', 'size' => '350mm x 350mm x 130mm'],
            'CARTON-ISI-4-SABLON' => ['component' => 'Carton Box', 'size' => 'Gland Packing (Isi 4 Sablon) Size 350mm x 350mm x 130mm'],
            'CARTON-ISI-5-POLOS' => ['component' => 'Carton Box Cokelat', 'size' => 'P : 580mm x L : 310mm x T : 310mm'],
            'CARTON-ISI-5-SABLON' => ['component' => 'Carton Box Cokelat', 'size' => 'P : 580mm x L : 310mm x T : 310mm (Include Sablon AQPA)'],
            'CARTON-ISI-8-POLOS' => ['component' => 'Carton Box Cokelat', 'size' => '350mm x 350mm x 230mm'],
            'CARTON-ISI-8-SABLON' => ['component' => 'Carton Box Cokelat', 'size' => '350mm x 350mm x 230mm (Include Sablon AQPA)'],
            'CARTON-PUTIH-SABLON' => ['component' => 'Carton Box Putih', 'size' => '300mm x 300mm x 115mm (Include Sablon GTE)'],
            'CARTON-TJ-POLOS' => ['component' => 'Carton Box Cokelat', 'size' => '700mm x 560mm x 300mm'],
            'CARTON-TJ-SABLON' => ['component' => 'Carton Box Cokelat', 'size' => '700mm x 560mm x 300mm (Include Sablon AQPA)'],
            'CARTON-TJ-SABLON-700-560-500' => ['component' => 'Carton Box Cokelat', 'size' => '700mm x 560mm x 500mm (Include Sablon AQPA)'],
            'CARTON-LEMBARAN' => ['component' => 'Carton Box Cokelat Lembaran', 'size' => '1000mm x 1000mm'],
            'TERPAL-001' => ['component' => 'TERPAL A2', 'size' => '2000MM x 100000MM']
        ];

        foreach ($updates as $code => $data) {
            DB::table('packing_material_prices')
                ->where('code', $code)
                ->update([
                    'component' => $data['component'],
                    'size' => $data['size'],
                    'updated_at' => now()
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengembalikan kolom component ke teks gabungan asli
        $reverts = [
            'CARTON-COKLAT-POLOS' => 'Carton Box Cokelat, Size : 300mm x 300mm x 115mm',
            'CARTON-ISI-2-POLOS' => 'Carton Box Cokelat, Size : 350mm x 170mm x 130mm',
            'CARTON-ISI-2-SABLON' => 'Carton Box Cokelat, Size : 350mm x 170mm x 130mm (Include Sablon AQPA)',
            'CARTON-ISI-4-POLOS' => 'Carton Box Cokelat, Size : 350mm x 350mm x 130mm',
            'CARTON-ISI-4-SABLON' => 'Carton Box  Size :  Gland Packing (Isi 4 Sablon) Size 350mm x 350mm x 130mm',
            'CARTON-ISI-5-POLOS' => 'Carton Box Cokelat Size, P : 580mm x L : 310mm x T : 310mm',
            'CARTON-ISI-5-SABLON' => 'Carton Box Cokelat Size, P : 580mm x L : 310mm x T : 310mm (Include Sablon AQPA)',
            'CARTON-ISI-8-POLOS' => 'Carton Box Cokelat, Size : 350mm x 350mm x 230mm',
            'CARTON-ISI-8-SABLON' => 'Carton Box Cokelat, Size : 350mm x 350mm x 230mm (Include Sablon AQPA)',
            'CARTON-PUTIH-SABLON' => 'Carton Box Putih, Size : 300mm x 300mm x 115mm (Include Sablon GTE)',
            'CARTON-TJ-POLOS' => 'Carton Box Cokelat, Size : 700mm x 560mm x 300mm',
            'CARTON-TJ-SABLON' => 'Carton Box Cokelat, Size : 700mm x 560mm x 300mm (Include Sablon AQPA)',
            'CARTON-TJ-SABLON-700-560-500' => 'Carton Box Cokelat, Size : 700mm x 560mm x 500mm (Include Sablon AQPA)',
            'CARTON-LEMBARAN' => 'Carton Box Cokelat Lembaran, Size : 1000mm x 1000mm',
            'TERPAL-001' => 'TERPAL A2, SIZE : 2000MM x 100000MM'
        ];

        foreach ($reverts as $code => $component) {
            DB::table('packing_material_prices')
                ->where('code', $code)
                ->update([
                    'component' => $component,
                    'size' => null, // Kosongkan lagi sizenya
                    'updated_at' => now()
                ]);
        }
    }
};
