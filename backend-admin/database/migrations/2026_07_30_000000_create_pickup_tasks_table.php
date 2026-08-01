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
        Schema::create('pickup_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_number')->nullable();
            $table->enum('transaction_source', ['purchase', 'manual'])->default('manual');
            $table->uuid('driver_id');
            $table->uuid('vehicle_id');
            $table->uuid('shift_id')->nullable();
            $table->uuid('assigned_by')->nullable();
            $table->string('pickup_name');
            $table->string('pickup_location');
            $table->string('destination')->nullable();
            $table->string('item_number')->nullable();
            $table->text('item_description');
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->decimal('line_total', 15, 2)->nullable();
            $table->enum('status', ['assigned', 'on_route', 'arrived', 'delivered', 'failed', 'cancelled'])->default('assigned');
            $table->text('failure_reason')->nullable();
            $table->string('proof_photo')->nullable();
            $table->integer('completed_odometer')->nullable();
            
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Asumsi tabel users, vehicles, shifts ada dan menggunakan UUID
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_tasks');
    }
};
