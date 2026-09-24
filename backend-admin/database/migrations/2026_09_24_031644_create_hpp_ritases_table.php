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
        Schema::create('hpp_ritases', function (Blueprint $table) {
            $table->id();
            $table->uuid('shift_id');
            $table->decimal('fuel_cost', 15, 2)->default(0);
            $table->decimal('manpower_cost', 15, 2)->default(0);
            $table->decimal('toll_cost', 15, 2)->default(0);
            $table->decimal('parking_cost', 15, 2)->default(0);
            $table->decimal('other_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->decimal('base_value', 15, 2)->default(0);
            $table->boolean('is_prorata')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hpp_ritases');
    }
};
