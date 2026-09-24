<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('validasi_mp_deliverypickup', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Rate MP Driver');
            $table->decimal('rate_per_hour', 15, 2)->default(0);
            $table->decimal('rate_per_minute', 15, 2)->default(0);
            $table->decimal('rate_per_second', 15, 2)->default(0);
            $table->string('status_validasi')->default('Valid Sesuai');
            $table->timestamps();
        });

        // Seed 1 row global untuk rate manpower
        \Illuminate\Support\Facades\DB::table('validasi_mp_deliverypickup')->insert([
            'name' => 'Rate MP Driver',
            'rate_per_hour' => 34088,
            'rate_per_minute' => 568,
            'rate_per_second' => 9,
            'status_validasi' => 'Valid Sesuai',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validasi_mp_deliverypickup');
    }
};
