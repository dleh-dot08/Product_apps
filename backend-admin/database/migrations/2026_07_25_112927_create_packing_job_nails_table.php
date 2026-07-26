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
        Schema::create('packing_job_nails', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->string('category')->nullable();
            $table->string('bagian')->nullable();
            $table->string('kode_material')->nullable();
            $table->integer('titik_paku')->default(0);
            $table->integer('jumlah_paku_per_titik')->default(0);
            $table->integer('total_paku')->default(0);
            $table->decimal('estimasi_berat', 10, 4)->nullable();
            $table->decimal('harga_per_kg', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->uuid('job_calc_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_job_nails');
    }
};
