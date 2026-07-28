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
        Schema::table('packaging_job_details', function (Blueprint $table) {
            // Kolom Area Bawah
            $table->string('bawah_penyanggah_status')->nullable();
            $table->string('bawah_penyanggah_arah')->nullable();
            $table->string('bawah_penyanggah_material')->nullable();
            $table->string('bawah_penutup_status')->nullable();
            $table->string('bawah_penutup_arah')->nullable();
            $table->string('bawah_penutup_material')->nullable();
            $table->string('bawah_kaki_balok_status')->nullable();
            $table->string('bawah_kaki_balok_arah')->nullable();
            $table->string('bawah_kaki_balok_material')->nullable();

            // Kolom Area Atas
            $table->string('atas_penyanggah_status')->nullable();
            $table->string('atas_penyanggah_arah')->nullable();
            $table->string('atas_penyanggah_material')->nullable();
            $table->string('atas_penutup_status')->nullable();
            $table->string('atas_penutup_arah')->nullable();
            $table->string('atas_penutup_material')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packaging_job_details', function (Blueprint $table) {
            $table->dropColumn([
                'bawah_penyanggah_status',
                'bawah_penyanggah_arah',
                'bawah_penyanggah_material',
                'bawah_penutup_status',
                'bawah_penutup_arah',
                'bawah_penutup_material',
                'bawah_kaki_balok_status',
                'bawah_kaki_balok_arah',
                'bawah_kaki_balok_material',
                'atas_penyanggah_status',
                'atas_penyanggah_arah',
                'atas_penyanggah_material',
                'atas_penutup_status',
                'atas_penutup_arah',
                'atas_penutup_material',
            ]);
        });
    }
};
