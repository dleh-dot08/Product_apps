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
        Schema::create('hpp_ritase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hpp_ritase_id')->constrained('hpp_ritases')->onDelete('cascade');
            $table->uuid('task_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('item_description')->nullable();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->string('unit')->nullable();
            $table->decimal('line_total', 15, 2)->default(0);
            $table->decimal('hpp_per_baris', 15, 2)->default(0);
            $table->decimal('hpp_per_qty', 15, 2)->default(0);
            $table->decimal('percentage', 10, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hpp_ritase_items');
    }
};
