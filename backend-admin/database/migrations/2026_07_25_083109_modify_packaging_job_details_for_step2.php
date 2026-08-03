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
            $table->foreignUuid('packer_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Ubah tipe data menggunakan string sementara jika DB engine tidak mendukung direct json ubah. 
            // Karena ini SQLite/MySQL, biasanya drop dan recreate bisa jika masih development.
            // Namun yang paling aman, drop col lalu tambah col.
            $table->dropColumn('konfigurasi_atas');
            $table->dropColumn('konfigurasi_bawah');
        });

        Schema::table('packaging_job_details', function (Blueprint $table) {
            $table->json('konfigurasi_atas')->nullable();
            $table->json('konfigurasi_bawah')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packaging_job_details', function (Blueprint $table) {
            $table->dropForeign(['packer_id']);
            $table->dropColumn('packer_id');
            
            $table->dropColumn('konfigurasi_atas');
            $table->dropColumn('konfigurasi_bawah');
        });

        Schema::table('packaging_job_details', function (Blueprint $table) {
            $table->string('konfigurasi_atas')->nullable();
            $table->string('konfigurasi_bawah')->nullable();
        });
    }
};
