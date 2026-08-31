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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('external_key')->unique();
            $table->string('so_number');
            $table->date('so_date')->nullable();
            $table->date('estimated_delivery_date')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_po_number')->nullable();
            $table->string('salesperson_name')->nullable();
            $table->string('item_number')->nullable();
            $table->string('item_description')->nullable();
            $table->decimal('ordered_quantity', 14, 3)->nullable();
            $table->decimal('shipped_quantity', 14, 3)->nullable();
            $table->decimal('remaining_quantity', 14, 3)->nullable();
            $table->string('unit')->nullable();
            $table->string('status')->nullable();
            $table->json('source_data');
            $table->timestamp('source_updated_at')->useCurrent();
            $table->timestamps();

            $table->index('so_number');
            $table->index('estimated_delivery_date');
        });

        Schema::create('delivery_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sales_order_id');
            $table->uuid('driver_id');
            $table->uuid('vehicle_id')->nullable();
            $table->uuid('shift_id')->nullable();
            $table->uuid('assigned_by')->nullable();
            $table->string('status')->default('assigned'); // assigned, on_route, arrived, delivered, failed, cancelled
            $table->text('failure_reason')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('proof_photo')->nullable();
            $table->integer('completed_odometer')->nullable();

            $table->foreign('sales_order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');

            $table->unique(['sales_order_id', 'driver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_assignments');
        Schema::dropIfExists('sales_orders');
    }
};
