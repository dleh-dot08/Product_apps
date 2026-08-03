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
        // Drop lama
        Schema::dropIfExists('packaging_job_details');
        Schema::dropIfExists('packaging_jobs');

        // Create packaging_jobs
        Schema::create('packaging_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('type_packaging')->nullable(); // Box, Palet, Peti, Kerangka
            $table->string('packaging_number')->unique(); // PKG-YYMMDD-XXX
            $table->string('packer_id')->nullable();
            $table->integer('qty_packaging')->default(1);
            
            // Timeline
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->date('completion_date')->nullable();
            
            // Dimensi
            $table->float('panjang')->nullable();
            $table->float('lebar')->nullable();
            $table->float('tinggi')->nullable();
            $table->float('gap_atas')->nullable();
            $table->float('gap_bawah')->nullable();
            $table->float('jarak_penyanggah_atas')->nullable();
            $table->float('jarak_penyanggah_bawah')->nullable();
            
            // Material Bawah
            $table->string('bawah_kakibalok_status')->nullable();
            $table->string('bawah_kakibalok_material')->nullable();
            $table->string('bawah_kakibalok_arahpemasangan')->nullable();
            $table->string('bawah_penyanggah_status')->nullable();
            $table->string('bawah_penyanggah_material')->nullable();
            $table->string('bawah_penyanggah_arahpemasangan')->nullable();
            $table->string('bawah_penutup_status')->nullable();
            $table->string('bawah_penutup_material')->nullable();
            $table->string('bawah_penutup_arahpemasangan')->nullable();
            
            // Material Atas
            $table->string('atas_penyanggah_status')->nullable();
            $table->string('atas_penyanggah_material')->nullable();
            $table->string('atas_penyanggah_arahpemasangan')->nullable();
            $table->string('atas_penutup_status')->nullable();
            $table->string('atas_penutup_material')->nullable();
            $table->string('atas_penutup_arahpemasangan')->nullable();
            
            // Harga
            $table->decimal('subtotal_harga_material', 15, 2)->default(0);
            $table->decimal('subtotal_harga_paku', 15, 2)->default(0);
            $table->decimal('subtotal_man_power', 15, 2)->default(0);
            $table->decimal('harga_total', 15, 2)->default(0);
            
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        // Create packaging_job_items
        Schema::create('packaging_job_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('packaging_job_id')->constrained('packaging_jobs')->onDelete('cascade');
            $table->string('no_so')->nullable();
            $table->string('customer')->nullable();
            $table->string('no_product')->nullable();
            $table->text('desc_product')->nullable();
            $table->integer('qty')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging_job_items');
        Schema::dropIfExists('packaging_jobs');
    }
};
