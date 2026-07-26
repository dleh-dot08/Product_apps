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
        Schema::create('packing_job_calc_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->string('section')->nullable();
            $table->string('part_name')->nullable();
            $table->string('material_code')->nullable();
            $table->string('material_satuan_harga')->nullable();
            $table->string('direction')->nullable();
            $table->string('tipe_penutup')->nullable();
            $table->decimal('calculated_thickness', 10, 2)->nullable();
            $table->decimal('calculated_width', 10, 2)->nullable();
            $table->decimal('calculated_length', 10, 2)->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('side_count')->default(0);
            $table->integer('total_quantity')->default(0);
            $table->decimal('total_length', 10, 2)->nullable();
            $table->decimal('price_per_unit', 15, 2)->default(0);
            $table->decimal('subtotal_price', 15, 2)->default(0);
            $table->string('nail_points')->nullable();
            $table->uuid('job_calc_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_job_calc_details');
    }
};
