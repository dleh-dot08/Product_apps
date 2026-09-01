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
        Schema::table('packing_job_calc_details', function (Blueprint $table) {
            $table->decimal('quantity', 10, 4)->default(0)->change();
            $table->decimal('total_quantity', 10, 4)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_job_calc_details', function (Blueprint $table) {
            $table->integer('quantity')->default(0)->change();
            $table->integer('total_quantity')->default(0)->change();
        });
    }
};
