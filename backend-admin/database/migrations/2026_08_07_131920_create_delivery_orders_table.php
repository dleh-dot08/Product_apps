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
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('do_number')->unique();
            $table->date('do_date');
            $table->string('customer_name');
            $table->string('customer_po_number')->nullable();
            $table->text('destination');
            $table->string('status')->default('pending'); // pending, assigned, on_route, arrived, delivered
            $table->uuid('trip_id')->nullable();
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
