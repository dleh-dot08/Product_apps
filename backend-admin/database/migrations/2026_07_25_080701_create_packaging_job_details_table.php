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
        Schema::create('packaging_job_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packaging_job_id')->constrained('packaging_jobs')->onDelete('cascade');
            $table->string('packaging_number')->nullable();
            $table->string('no_product')->nullable();
            $table->text('desc_product')->nullable();
            
            $table->integer('qty_barang_dikirim')->default(0);
            $table->integer('qty_packaging')->default(1);
            $table->integer('qty_product_per_packaging')->default(0);
            
            $table->float('panjang')->nullable();
            $table->float('lebar')->nullable();
            $table->float('tinggi')->nullable();
            $table->float('gap_atas')->nullable();
            $table->float('gap_bawah')->nullable();
            $table->float('jarak_penyanggah')->nullable();
            
            $table->string('konfigurasi_atas')->nullable();
            $table->string('konfigurasi_bawah')->nullable();
            
            // Kolom Harga Tambahan
            $table->decimal('subtotal_harga_material', 15, 2)->default(0);
            $table->decimal('subtotal_harga_paku', 15, 2)->default(0);
            $table->decimal('subtotal_man_power', 15, 2)->default(0);
            $table->decimal('harga_total', 15, 2)->default(0);
            
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging_job_details');
    }
};
