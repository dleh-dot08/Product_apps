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
        Schema::table('hpp_ritases', function (Blueprint $table) {
            $table->decimal('depreciation_cost', 15, 2)->default(0)->after('other_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hpp_ritases', function (Blueprint $table) {
            $table->dropColumn(['depreciation_cost']);
        });
    }
};
