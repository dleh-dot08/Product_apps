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
        Schema::table('task_manifests', function (Blueprint $table) {
            $table->boolean('is_out_of_city')->default(false);
            $table->dateTime('estimated_arrival')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_manifests', function (Blueprint $table) {
            $table->dropColumn(['is_out_of_city', 'estimated_arrival']);
        });
    }
};
