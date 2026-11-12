<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_assignments', 'created_at')) {
                $table->timestamps();
            }
        });

        DB::statement("
            UPDATE delivery_assignments
            SET
                created_at = COALESCE(
                    assigned_at,
                    dispatch_date,
                    (SELECT sales_orders.created_at FROM sales_orders WHERE sales_orders.id = delivery_assignments.sales_order_id),
                    CURRENT_TIMESTAMP
                ),
                updated_at = COALESCE(
                    completed_at,
                    started_at,
                    assigned_at,
                    dispatch_date,
                    (SELECT sales_orders.updated_at FROM sales_orders WHERE sales_orders.id = delivery_assignments.sales_order_id),
                    CURRENT_TIMESTAMP
                )
            WHERE created_at IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('delivery_assignments', function (Blueprint $table) {
            $columns = array_values(array_filter(['created_at', 'updated_at'], function ($col) {
                return Schema::hasColumn('delivery_assignments', $col);
            }));

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

