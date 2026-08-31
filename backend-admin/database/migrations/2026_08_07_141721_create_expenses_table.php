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
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('shift_id');
            $table->uuid('driver_id');
            $table->enum('category', ['fuel', 'toll', 'parking', 'meal', 'other']);
            $table->text('description')->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('receipt_url')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
