<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->dateTime('dispatch_date')->nullable();
            $table->dateTime('estimated_arrival')->nullable();
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dateTime('dispatch_date')->nullable();
            $table->dateTime('estimated_arrival')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->dropColumn(['dispatch_date', 'estimated_arrival']);
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dropColumn(['dispatch_date', 'estimated_arrival']);
        });
    }
};
