<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'pending' to status enum constraints
        DB::statement("ALTER TABLE pickup_tasks DROP CONSTRAINT IF EXISTS pickup_tasks_status_check");
        DB::statement("ALTER TABLE delivery_assignments DROP CONSTRAINT IF EXISTS delivery_assignments_status_check");

        DB::statement("ALTER TABLE pickup_tasks ADD CONSTRAINT pickup_tasks_status_check CHECK (status::text = ANY (ARRAY['draft'::character varying, 'pending'::character varying, 'assigned'::character varying, 'on_route'::character varying, 'arrived'::character varying, 'delivered'::character varying, 'failed'::character varying, 'cancelled'::character varying]::text[]))");
        DB::statement("ALTER TABLE delivery_assignments ADD CONSTRAINT delivery_assignments_status_check CHECK (status::text = ANY (ARRAY['draft'::character varying, 'pending'::character varying, 'assigned'::character varying, 'on_route'::character varying, 'arrived'::character varying, 'delivered'::character varying, 'failed'::character varying, 'cancelled'::character varying]::text[]))");

        // Add is_out_of_city flag
        Schema::table('pickup_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('pickup_tasks', 'is_out_of_city')) {
                $table->boolean('is_out_of_city')->default(false);
            }
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_assignments', 'is_out_of_city')) {
                $table->boolean('is_out_of_city')->default(false);
            }
        });
    }

    public function down(): void
    {
        // Revert enum constraints
        DB::statement("ALTER TABLE pickup_tasks DROP CONSTRAINT IF EXISTS pickup_tasks_status_check");
        DB::statement("ALTER TABLE delivery_assignments DROP CONSTRAINT IF EXISTS delivery_assignments_status_check");

        DB::statement("ALTER TABLE pickup_tasks ADD CONSTRAINT pickup_tasks_status_check CHECK (status::text = ANY (ARRAY['draft'::character varying, 'assigned'::character varying, 'on_route'::character varying, 'arrived'::character varying, 'delivered'::character varying, 'failed'::character varying, 'cancelled'::character varying]::text[]))");
        DB::statement("ALTER TABLE delivery_assignments ADD CONSTRAINT delivery_assignments_status_check CHECK (status::text = ANY (ARRAY['draft'::character varying, 'assigned'::character varying, 'on_route'::character varying, 'arrived'::character varying, 'delivered'::character varying, 'failed'::character varying, 'cancelled'::character varying]::text[]))");

        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->dropColumn('is_out_of_city');
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dropColumn('is_out_of_city');
        });
    }
};
