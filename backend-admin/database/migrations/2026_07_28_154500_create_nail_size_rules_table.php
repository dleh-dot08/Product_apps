<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel jika belum ada
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS public.nail_size_rules (
                id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                "from" VARCHAR(50) NOT NULL,
                "to" VARCHAR(50) NOT NULL,
                thk_from INT NOT NULL,
                thk_to INT NOT NULL,
                size_nails INT NOT NULL,
                created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
                updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
                CONSTRAINT unique_nail_rule UNIQUE ("from", "to", thk_from, thk_to)
            );
        ');

        // 2. Upsert data
        DB::unprepared("
            INSERT INTO public.nail_size_rules (\"from\", \"to\", thk_from, thk_to, size_nails, created_at, updated_at)
            VALUES 
                ('Balok', 'Balok', 40,  40, 7, NOW(), NOW()),
                ('Balok', 'Balok', 40,  60, 7, NOW(), NOW()),
                ('Balok', 'Balok', 60,  40, 7, NOW(), NOW()),
                ('Balok', 'Balok', 60,  60, 10, NOW(), NOW()),
                ('Balok', 'Balok', 100, 40, 7, NOW(), NOW()),
                ('Balok', 'Balok', 100, 60, 10, NOW(), NOW()),

                ('Triplek', 'Balok', 3,  40,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 3,  60,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 3,  100, 4, NOW(), NOW()),
                ('Triplek', 'Balok', 4,  40,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 4,  60,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 4,  100, 4, NOW(), NOW()),
                ('Triplek', 'Balok', 6,  40,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 6,  60,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 6,  100, 4, NOW(), NOW()),
                ('Triplek', 'Balok', 8,  40,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 8,  60,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 8,  100, 4, NOW(), NOW()),
                ('Triplek', 'Balok', 9,  40,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 9,  60,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 9,  100, 4, NOW(), NOW()),
                ('Triplek', 'Balok', 12, 40,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 12, 60,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 12, 100, 4, NOW(), NOW()),
                ('Triplek', 'Balok', 15, 40,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 15, 60,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 15, 100, 4, NOW(), NOW()),
                ('Triplek', 'Balok', 18, 40,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 18, 60,  4, NOW(), NOW()),
                ('Triplek', 'Balok', 18, 100, 4, NOW(), NOW())
            ON CONFLICT (\"from\", \"to\", thk_from, thk_to)
            DO UPDATE SET 
                size_nails = EXCLUDED.size_nails,
                updated_at = NOW();
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS public.nail_size_rules');
    }
};
