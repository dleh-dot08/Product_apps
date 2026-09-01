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
            $table->json('inner_carton_boxes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packaging_jobs', function (Blueprint $table) {
            $table->dropColumn('inner_carton_boxes');
        });
    }
};
