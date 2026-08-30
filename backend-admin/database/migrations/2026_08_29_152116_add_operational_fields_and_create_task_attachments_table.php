<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add scalar fields to pickup_tasks
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->integer('start_odometer')->nullable();
            $table->string('start_fuel')->nullable();
            $table->text('departure_notes')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_role')->nullable();
            $table->string('item_condition')->nullable();
        });

        // Add scalar fields to delivery_assignments
        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->integer('start_odometer')->nullable();
            $table->string('start_fuel')->nullable();
            $table->text('departure_notes')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_role')->nullable();
            $table->string('item_condition')->nullable();
        });

        // Create task_attachments table
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            $table->uuid('task_id');
            $table->string('task_type'); // 'App\Models\PickupTask' or 'App\Models\DeliveryAssignment'
            $table->string('category');
            $table->string('file_path');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['task_type', 'task_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_attachments');

        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->dropColumn([
                'start_odometer',
                'start_fuel',
                'departure_notes',
                'receiver_name',
                'receiver_role',
                'item_condition'
            ]);
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dropColumn([
                'start_odometer',
                'start_fuel',
                'departure_notes',
                'receiver_name',
                'receiver_role',
                'item_condition'
            ]);
        });
    }
};
