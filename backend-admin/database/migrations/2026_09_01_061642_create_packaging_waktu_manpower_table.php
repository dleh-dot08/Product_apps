<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packaging_waktu_manpower', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan');
            $table->integer('prepare_menit')->nullable();
            $table->integer('pekerjaan_menit')->nullable();
            $table->string('ket')->nullable();
            $table->timestamps();
        });

        // Insert default data
        $data = [
            ['kegiatan' => 'POTONG BALOK', 'prepare_menit' => 3, 'pekerjaan_menit' => 1, 'ket' => 'per-pcs', 'created_at' => now(), 'updated_at' => now()],
            ['kegiatan' => 'POTONG PAPAN', 'prepare_menit' => 3, 'pekerjaan_menit' => 1, 'ket' => 'per-pcs', 'created_at' => now(), 'updated_at' => now()],
            ['kegiatan' => 'POTONG TRIPLEK', 'prepare_menit' => 5, 'pekerjaan_menit' => 5, 'ket' => 'per-pcs', 'created_at' => now(), 'updated_at' => now()],
            ['kegiatan' => 'SERUT PAPAN', 'prepare_menit' => 3, 'pekerjaan_menit' => 2, 'ket' => 'per-pcs', 'created_at' => now(), 'updated_at' => now()],
            ['kegiatan' => 'SERUT BALOK', 'prepare_menit' => 3, 'pekerjaan_menit' => 2, 'ket' => 'per-pcs', 'created_at' => now(), 'updated_at' => now()],
            ['kegiatan' => 'PERAKITAN', 'prepare_menit' => null, 'pekerjaan_menit' => 105, 'ket' => '1m^3', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('packaging_waktu_manpower')->insert($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging_waktu_manpower');
    }
};
