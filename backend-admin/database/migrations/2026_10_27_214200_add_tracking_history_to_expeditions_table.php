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
        if (Schema::hasTable('expeditions')) {
            Schema::table('expeditions', function (Blueprint $table) {
                if (!Schema::hasColumn('expeditions', 'tracking_history')) {
                    $table->json('tracking_history')->nullable()->after('proof_photo');
                }
                if (!Schema::hasColumn('expeditions', 'last_tracking_status')) {
                    $table->string('last_tracking_status', 100)->nullable()->after('tracking_history');
                }
                if (!Schema::hasColumn('expeditions', 'last_tracked_at')) {
                    $table->timestamp('last_tracked_at')->nullable()->after('last_tracking_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('expeditions')) {
            Schema::table('expeditions', function (Blueprint $table) {
                if (Schema::hasColumn('expeditions', 'tracking_history')) {
                    $table->dropColumn('tracking_history');
                }
                if (Schema::hasColumn('expeditions', 'last_tracking_status')) {
                    $table->dropColumn('last_tracking_status');
                }
                if (Schema::hasColumn('expeditions', 'last_tracked_at')) {
                    $table->dropColumn('last_tracked_at');
                }
            });
        }
    }
};
