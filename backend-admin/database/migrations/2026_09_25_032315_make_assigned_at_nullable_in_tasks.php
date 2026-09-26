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
            $table->timestamp('assigned_at')->nullable()->default(null)->change();
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->timestamp('assigned_at')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->timestamp('assigned_at')->useCurrent()->nullable(false)->change();
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->timestamp('assigned_at')->useCurrent()->nullable(false)->change();
        });
    }
};
