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
        Schema::table('packaging_jobs', function (Blueprint $table) {
            $table->string('additional_mat')->nullable()->after('tipe_penutup');
            $table->string('carton_material')->nullable()->after('additional_mat');
            $table->string('carton_type_sablon')->nullable()->after('carton_material');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packaging_jobs', function (Blueprint $table) {
            $table->dropColumn(['additional_mat', 'carton_material', 'carton_type_sablon']);
        });
    }
};
