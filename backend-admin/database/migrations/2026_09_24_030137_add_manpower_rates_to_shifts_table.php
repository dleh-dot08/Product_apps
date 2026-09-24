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
        Schema::table('shifts', function (Blueprint $table) {
            $table->decimal('manpower_rate_per_minute', 15, 2)->nullable()->after('manpower_rate_per_hour');
            $table->decimal('manpower_rate_per_second', 15, 2)->nullable()->after('manpower_rate_per_minute');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('manpower_rate_per_minute');
            $table->dropColumn('manpower_rate_per_second');
        });
    }
};
