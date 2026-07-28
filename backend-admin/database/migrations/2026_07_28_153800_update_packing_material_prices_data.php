<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah unique constraint pada kolom code jika belum ada
        DB::statement('ALTER TABLE public.packing_material_prices ADD CONSTRAINT packing_material_prices_code_unique UNIQUE (code)');

        $keepCodes = [
            'KAYU-BALOK-MHNI-100-060-3000',
            'KAYU-BALOK-MHNI-100-060-4000',
            'KAYU-BALOK-MHNI-060-040-3000',
            'KAYU-BALOK-SPTH-060-040-4000',
            'KAYU-BALOK-MSUP-060-040-4000',
            'KAYU-BALOK-KCPI-060-040-3000',
            'KAYU-BALOK-PUTH-060-040-4000',
            'KAYU-PAPAN-MHNI-020-150-3000',
            'KAYU-PAPAN-JTBL-018-150-0110',
            'KAYU-PAPAN-JINJ-020-150-3000',
            'KAYU-TRIPL-TUNS-003-122-0244',
            'KAYU-TRIPL-MRNT-003-122-0244',
            'KAYU-TRIPL-TUNS-004-122-0244',
            'KAYU-TRIPL-MRNT-004-122-0244',
            'KAYU-TRIPL-TUNS-006-122-0244',
            'KAYU-TRIPL-MRNT-006-122-0244',
            'KAYU-TRIPL-TUNS-008-122-0244',
            'KAYU-TRIPL-MRNT-008-122-0244',
            'KAYU-TRIPL-TUNS-009-122-0244',
            'KAYU-TRIPL-MRNT-009-122-0244',
            'KAYU-TRIPL-TUNS-012-122-0244',
            'KAYU-TRIPL-MRNT-012-122-0244',
            'KAYU-TRIPL-TUNS-015-122-0244',
            'KAYU-TRIPL-MRNT-015-122-0244',
            'KAYU-TRIPL-TUNS-018-122-0244',
            'KAYU-TRIPL-MRNT-018-122-0244',
        ];

        // 1. Hapus data yang tidak ada di daftar baru
        DB::table('packing_material_prices')->whereNotIn('code', $keepCodes)->delete();

        // 2. Upsert data baru
        DB::unprepared("
            INSERT INTO public.packing_material_prices 
                (id, component, size, wood_type, unit, unit_price, active, thickness, width, length, code, created_at, updated_at)
            VALUES 
                (gen_random_uuid(), 'Balok', '100 mm x 60 mm x 3000 mm', 'Mahoni', 'Batang', 54000, TRUE, 100, 60, 3000, 'KAYU-BALOK-MHNI-100-060-3000', NOW(), NOW()),
                (gen_random_uuid(), 'Balok', '100 mm x 60 mm x 4000 mm', 'Mahoni', 'Batang', 72000, TRUE, 100, 60, 4000, 'KAYU-BALOK-MHNI-100-060-4000', NOW(), NOW()),
                (gen_random_uuid(), 'Balok', '60 mm x 40 mm x 3000 mm', 'Mahoni', 'Batang', 26000, TRUE, 60, 40, 3000, 'KAYU-BALOK-MHNI-060-040-3000', NOW(), NOW()),
                (gen_random_uuid(), 'Balok', '60 mm x 40 mm x 4000 mm', 'Super Putih', 'Batang', 36000, TRUE, 60, 40, 4000, 'KAYU-BALOK-SPTH-060-040-4000', NOW(), NOW()),
                (gen_random_uuid(), 'Balok', '60 mm x 40 mm x 4000 mm', 'Merah Super', 'Batang', 42000, TRUE, 60, 40, 4000, 'KAYU-BALOK-MSUP-060-040-4000', NOW(), NOW()),
                (gen_random_uuid(), 'Balok', '60 mm x 40 mm x 3000 mm', 'Kecapi', 'Batang', 24000, TRUE, 60, 40, 3000, 'KAYU-BALOK-KCPI-060-040-3000', NOW(), NOW()),
                (gen_random_uuid(), 'Balok', '60 mm x 40 mm x 4000 mm', 'Putih', 'Batang', 33000, TRUE, 60, 40, 4000, 'KAYU-BALOK-PUTH-060-040-4000', NOW(), NOW()),
                (gen_random_uuid(), 'Papan Kayu', '20 mm x 150 mm x 3000 mm', 'Mahoni', 'Lembar', 17000, TRUE, 20, 150, 3000, 'KAYU-PAPAN-MHNI-020-150-3000', NOW(), NOW()),
                (gen_random_uuid(), 'Papan Kayu', '18 mm x 90 mm x 1100 mm', 'Jati Belanda', 'Lembar', 12000, TRUE, 18, 90, 1100, 'KAYU-PAPAN-JTBL-018-150-0110', NOW(), NOW()),
                (gen_random_uuid(), 'Papan Kayu', '20 mm x 150 mm x 3000 mm', 'Jinjing', 'Lembar', 8000, TRUE, 20, 150, 3000, 'KAYU-PAPAN-JINJ-020-150-3000', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 3 mm x 1220 mm x 2440 mm', 'Tunas', 'Lembar', 66000, TRUE, 3, 1220, 2440, 'KAYU-TRIPL-TUNS-003-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 3 mm x 1220 mm x 2440 mm', 'Meranti', 'Lembar', 78000, TRUE, 3, 1220, 2440, 'KAYU-TRIPL-MRNT-003-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 4 mm x 1220 mm x 2440 mm', 'Tunas', 'Lembar', 78000, TRUE, 4, 1220, 2440, 'KAYU-TRIPL-TUNS-004-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 4 mm x 1220 mm x 2440 mm', 'Meranti', 'Lembar', 96000, TRUE, 4, 1220, 2440, 'KAYU-TRIPL-MRNT-004-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 6 mm x 1220 mm x 2440 mm', 'Tunas', 'Lembar', 96000, TRUE, 6, 1220, 2440, 'KAYU-TRIPL-TUNS-006-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 6 mm x 1220 mm x 2440 mm', 'Meranti', 'Lembar', 108000, TRUE, 6, 1220, 2440, 'KAYU-TRIPL-MRNT-006-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 8 mm x 1220 mm x 2440 mm', 'Tunas', 'Lembar', 126000, TRUE, 8, 1220, 2440, 'KAYU-TRIPL-TUNS-008-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 8 mm x 1220 mm x 2440 mm', 'Meranti', 'Lembar', 144000, TRUE, 8, 1220, 2440, 'KAYU-TRIPL-MRNT-008-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 9 mm x 1220 mm x 2440 mm', 'Tunas', 'Lembar', 144000, TRUE, 9, 1220, 2440, 'KAYU-TRIPL-TUNS-009-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 9 mm x 1220 mm x 2440 mm', 'Meranti', 'Lembar', 150000, TRUE, 9, 1220, 2440, 'KAYU-TRIPL-MRNT-009-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 12 mm x 1220 mm x 2440 mm', 'Tunas', 'Lembar', 168000, TRUE, 12, 1220, 2440, 'KAYU-TRIPL-TUNS-012-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 12 mm x 1220 mm x 2440 mm', 'Meranti', 'Lembar', 192000, TRUE, 12, 1220, 2440, 'KAYU-TRIPL-MRNT-012-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 15 mm x 1220 mm x 2440 mm', 'Tunas', 'Lembar', 210000, TRUE, 15, 1220, 2440, 'KAYU-TRIPL-TUNS-015-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 15 mm x 1220 mm x 2440 mm', 'Meranti', 'Lembar', 222000, TRUE, 15, 1220, 2440, 'KAYU-TRIPL-MRNT-015-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 18 mm x 1220 mm x 2440 mm', 'Tunas', 'Lembar', 240000, TRUE, 18, 1220, 2440, 'KAYU-TRIPL-TUNS-018-122-0244', NOW(), NOW()),
                (gen_random_uuid(), 'Triplek', 'Tebal 18 mm x 1220 mm x 2440 mm', 'Meranti', 'Lembar', 252000, TRUE, 18, 1220, 2440, 'KAYU-TRIPL-MRNT-018-122-0244', NOW(), NOW())
            ON CONFLICT (code) 
            DO UPDATE SET 
                component = EXCLUDED.component,
                size = EXCLUDED.size,
                wood_type = EXCLUDED.wood_type,
                unit = EXCLUDED.unit,
                unit_price = EXCLUDED.unit_price,
                active = EXCLUDED.active,
                thickness = EXCLUDED.thickness,
                width = EXCLUDED.width,
                length = EXCLUDED.length,
                updated_at = NOW();
        ");
    }

    public function down(): void
    {
        // Tidak bisa di-rollback karena data lama sudah dihapus
    }
};
