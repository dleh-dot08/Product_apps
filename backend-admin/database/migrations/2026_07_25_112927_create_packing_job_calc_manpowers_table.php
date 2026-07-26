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
        Schema::create('packing_job_calc_manpowers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->string('bagian')->nullable();
            $table->decimal('panjang', 10, 2)->nullable();
            $table->decimal('lebar', 10, 2)->nullable();
            $table->integer('sisi')->default(0);
            $table->decimal('luas', 10, 4)->nullable();
            $table->decimal('total_luas', 10, 4)->nullable();
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->uuid('job_calc_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_job_calc_manpowers');
    }
};
