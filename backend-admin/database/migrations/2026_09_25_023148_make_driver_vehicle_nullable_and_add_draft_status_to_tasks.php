<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->uuid('driver_id')->nullable()->change();
            $table->uuid('vehicle_id')->nullable()->change();
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->uuid('driver_id')->nullable()->change();
            $table->uuid('vehicle_id')->nullable()->change();
        });

        // Add 'draft' to status enums by replacing the check constraints in postgres
        DB::statement("ALTER TABLE pickup_tasks DROP CONSTRAINT IF EXISTS pickup_tasks_status_check");
        DB::statement("ALTER TABLE delivery_assignments DROP CONSTRAINT IF EXISTS delivery_assignments_status_check");

        DB::statement("ALTER TABLE pickup_tasks ADD CONSTRAINT pickup_tasks_status_check CHECK (status::text = ANY (ARRAY['draft'::character varying, 'assigned'::character varying, 'on_route'::character varying, 'arrived'::character varying, 'delivered'::character varying, 'failed'::character varying, 'cancelled'::character varying]::text[]))");
        DB::statement("ALTER TABLE delivery_assignments ADD CONSTRAINT delivery_assignments_status_check CHECK (status::text = ANY (ARRAY['draft'::character varying, 'assigned'::character varying, 'on_route'::character varying, 'arrived'::character varying, 'delivered'::character varying, 'failed'::character varying, 'cancelled'::character varying]::text[]))");
    }

    public function down(): void
    {
        // Revert to non-nullable if needed (warning: will fail if nulls exist)
        // Schema::table('pickup_tasks', function (Blueprint $table) {
        //     $table->uuid('driver_id')->nullable(false)->change();
        //     $table->uuid('vehicle_id')->nullable(false)->change();
        // });
    }
};
