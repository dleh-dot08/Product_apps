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
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dropUnique(['sales_order_id', 'driver_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->unique(['sales_order_id', 'driver_id']);
        });
    }
};
