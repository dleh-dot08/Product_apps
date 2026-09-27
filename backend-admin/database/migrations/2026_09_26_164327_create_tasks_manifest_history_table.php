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
        Schema::create('tasks_manifest_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('manifest_id');
            $table->uuid('task_id');
            $table->string('task_type', 50); // pickup / delivery
            $table->string('status', 50)->default('draft');
            $table->timestamps();

            $table->index(['manifest_id', 'task_id', 'task_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks_manifest_history');
    }
};
