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
        Schema::create('pembelian_order_akurasi', function (Blueprint $table) {
            $table->id();
            
            // Header Info
            $table->string('no_pembelian')->index();
            $table->date('tgl_pembelian')->nullable();
            $table->date('tgl_ekspetasi')->nullable();
            $table->string('top')->nullable();
            $table->integer('sisa_hari')->nullable();
            
            // Request Info
            $table->string('no_permintaan')->nullable();
            $table->date('tgl_permintaan')->nullable();
            $table->date('tgl_target')->nullable();
            $table->string('so_no')->nullable();
            
            // Receiving
            $table->string('no_penerimaan')->nullable();
            $table->date('tgl_penerimaan')->nullable();
            $table->string('ekspetasi_vs_pb')->nullable();
            
            // Supplier & Purchaser
            $table->string('no_pemasok')->nullable();
            $table->string('nama_pemasok')->nullable()->index();
            $table->string('purchaser')->nullable();
            
            // Item Info
            $table->string('no_barang')->nullable()->index();
            $table->text('deskripsi_barang')->nullable();
            $table->decimal('qty', 15, 2)->default(0);
            $table->string('uom')->nullable();
            
            // Pricing Info
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->string('ppn')->nullable();
            $table->decimal('nominal_ppn', 15, 2)->default(0);
            $table->decimal('pph', 15, 2)->default(0);
            $table->decimal('add_cost', 15, 2)->default(0);
            $table->decimal('dpp', 15, 2)->default(0);
            $table->decimal('nilai_po', 15, 2)->default(0);
            $table->decimal('uang_muka', 15, 2)->default(0);
            $table->decimal('sisa_po', 15, 2)->default(0);
            
            // Payment / FAT Status
            $table->string('status_bayar')->nullable();
            $table->string('no_faktur_pengajuan')->nullable();
            $table->decimal('pengajuan_bayar', 15, 2)->default(0);
            $table->decimal('dibayar_fat', 15, 2)->default(0);
            $table->decimal('sisa_hutang_fat', 15, 2)->default(0);
            $table->string('status_fat')->nullable();
            
            $table->decimal('amount', 15, 2)->default(0);

            // Hash for checking differences
            $table->string('sync_hash')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_order_akurasi');
    }
};
