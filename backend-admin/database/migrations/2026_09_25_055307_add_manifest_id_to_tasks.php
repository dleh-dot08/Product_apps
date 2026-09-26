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
            $table->uuid('manifest_id')->nullable()->after('id');
            $table->foreign('manifest_id')->references('id')->on('task_manifests')->onDelete('set null');
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->uuid('manifest_id')->nullable()->after('id');
            $table->foreign('manifest_id')->references('id')->on('task_manifests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->dropForeign(['manifest_id']);
            $table->dropColumn('manifest_id');
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dropForeign(['manifest_id']);
            $table->dropColumn('manifest_id');
        });
    }
};
