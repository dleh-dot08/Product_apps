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
        Schema::create('sales_order_akurasi', function (Blueprint $table) {
            $table->id();
            
            // Header Info
            $table->string('no_so')->index();
            $table->date('tgl_so')->nullable();
            $table->date('tgl_estimasi')->nullable();
            $table->date('tgl_pengiriman')->nullable();
            $table->string('no_pelanggan')->nullable();
            $table->string('nama_pelanggan')->nullable()->index();
            $table->string('no_po_customer')->nullable();
            $table->string('nama_salesman')->nullable();
            $table->text('shipto')->nullable();
            $table->text('deskripsi_so')->nullable();
            $table->string('status')->nullable();
            
            // Hold Info
            $table->boolean('is_held')->default(false);
            $table->text('hold_note')->nullable();
            
            // Item Info
            $table->string('no_barang')->nullable()->index();
            $table->text('deskripsi_barang')->nullable();
            $table->string('category_produk')->nullable();
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('qty_shipped', 15, 2)->default(0);
            $table->decimal('sisa_kirim', 15, 2)->default(0);
            $table->decimal('stok_tersedia', 15, 2)->default(0);
            $table->string('uom')->nullable();
            
            // Pricing Info
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('ppn_rate', 5, 2)->default(0);
            $table->decimal('ppn_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            
            // Pengiriman
            $table->string('no_pengiriman')->nullable();
            
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
        Schema::dropIfExists('sales_order_akurasi');
    }
};
