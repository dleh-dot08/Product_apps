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
            $table->string('source')->default('attendance')->after('vehicle_id'); // 'attendance' or 'task'
            $table->string('task_reference')->nullable()->after('source'); // To store PKP-1234 or DEL-1234
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn(['source', 'task_reference']);
        });
    }
};
