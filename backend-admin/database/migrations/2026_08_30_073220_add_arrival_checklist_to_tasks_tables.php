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
            $table->json('arrival_checklist')->nullable()->after('departure_checklist');
            $table->text('arrival_notes')->nullable()->after('arrival_checklist');
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->json('arrival_checklist')->nullable()->after('departure_checklist');
            $table->text('arrival_notes')->nullable()->after('arrival_checklist');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->dropColumn(['arrival_checklist', 'arrival_notes']);
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dropColumn(['arrival_checklist', 'arrival_notes']);
        });
    }
};
