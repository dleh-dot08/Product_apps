<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('driver_id');
            $table->uuid('vehicle_id')->nullable();
            $table->date('work_date')->default(now()->toDateString());
            $table->timestamp('check_in_at')->useCurrent();
            $table->timestamp('check_out_at')->nullable();
            $table->integer('start_odometer');
            $table->integer('end_odometer')->nullable();
            $table->string('start_evidence_photo')->nullable();
            $table->string('end_evidence_photo')->nullable();
            $table->decimal('fuel_price_per_liter', 14, 2)->default(0);
            $table->decimal('km_per_liter', 10, 2)->default(0);
            $table->decimal('manpower_rate_per_hour', 14, 2)->default(0);
            $table->integer('manpower_count')->default(1);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            
            // Unique index for MySQL: while this allows multiple NULLs in check_out_at, 
            // the true constraint will be handled via application logic when creating a shift.
            $table->unique(['driver_id', 'check_out_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
