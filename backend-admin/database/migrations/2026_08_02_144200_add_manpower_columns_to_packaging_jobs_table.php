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
            $table->decimal('manpower_potong', 10, 2)->nullable()->after('status');
            $table->decimal('manpower_serut', 10, 2)->nullable()->after('manpower_potong');
            $table->decimal('manpower_perakitan', 10, 2)->nullable()->after('manpower_serut');
            $table->decimal('manpower_prepare', 10, 2)->nullable()->after('manpower_perakitan');
            $table->decimal('total_waktu_manpower', 10, 2)->nullable()->after('manpower_prepare');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packaging_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'manpower_potong',
                'manpower_serut',
                'manpower_perakitan',
                'manpower_prepare',
                'total_waktu_manpower'
            ]);
        });
    }
};
