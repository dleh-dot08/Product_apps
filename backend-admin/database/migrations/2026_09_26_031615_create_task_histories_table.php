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
        Schema::create('task_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->string('task_type', 50); // pickup / delivery
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('co_driver_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('status', 50);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable(); // user or admin id
            $table->timestamps();

            $table->index(['task_id', 'task_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_histories');
    }
};
