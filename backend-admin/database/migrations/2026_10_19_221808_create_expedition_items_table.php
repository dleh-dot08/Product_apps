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
        if (!Schema::hasTable('expedition_items')) {
            Schema::create('expedition_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('expedition_id')->constrained('expeditions')->onDelete('cascade');
                $table->string('ref_no', 100)->nullable();
                $table->string('item_number', 100)->nullable();
                $table->text('item_description');
                $table->integer('quantity')->default(1);
                $table->string('unit', 50)->nullable();
                $table->decimal('unit_price', 14, 2)->default(0);
                $table->decimal('total_price', 14, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expedition_items');
    }
};
