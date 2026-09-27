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
        Schema::create('task_manifests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('manifest_number')->unique();
            $table->uuid('driver_id');
            $table->uuid('co_driver_id')->nullable();
            $table->uuid('vehicle_id');
            $table->uuid('assigned_by');
            $table->date('dispatch_date');
            $table->string('status')->default('assigned'); // assigned, on_route, completed
            $table->timestamps();

            $table->foreign('driver_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('co_driver_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('restrict');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_manifests');
    }
};
