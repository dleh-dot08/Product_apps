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
        Schema::create('packaging_fastener_validations', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->string('type_from')->nullable();
            $table->decimal('thk_from_min_mm', 8, 2)->nullable();
            $table->decimal('thk_from_max_mm', 8, 2)->nullable();
            $table->string('type_to')->nullable();
            $table->decimal('thk_to_min_mm', 8, 2)->nullable();
            $table->decimal('thk_to_max_mm', 8, 2)->nullable();
            $table->string('nail_code')->nullable();
            $table->decimal('nail_length_mm', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging_fastener_validations');
    }
};
