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
            $table->string('do_easy')->nullable();
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->string('do_easy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->dropColumn('do_easy');
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dropColumn('do_easy');
        });
    }
};
