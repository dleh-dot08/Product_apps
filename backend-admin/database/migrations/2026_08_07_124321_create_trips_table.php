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
        Schema::create('trips', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vehicle_id');
            $table->uuid('driver_id');
            $table->date('date');
            $table->decimal('distance_km', 10, 2)->default(0);
            $table->decimal('toll_cost', 14, 2)->default(0);
            $table->decimal('parking_cost', 14, 2)->default(0);
            $table->decimal('other_cost', 14, 2)->default(0);
            $table->enum('status', ['planned', 'on_trip', 'completed'])->default('planned');
            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
