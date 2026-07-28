<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP TABLE IF EXISTS public.packaging_fastener_validations');
    }

    public function down(): void
    {
        // Cannot restore
    }
};
