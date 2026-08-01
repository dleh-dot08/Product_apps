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
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->string('so_number')->nullable()->after('vehicle_id');
            $table->string('customer_name')->nullable()->after('so_number');
            $table->text('address')->nullable()->after('customer_name');
            $table->string('item_name')->nullable()->after('address');
            $table->text('remarks')->nullable()->after('quantity');
            
            $table->dropColumn(['pickup_name', 'pickup_location', 'destination', 'item_number', 'item_description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->dropColumn(['so_number', 'customer_name', 'address', 'item_name', 'remarks']);
            
            $table->string('pickup_name')->default('');
            $table->string('pickup_location')->default('');
            $table->string('destination')->nullable();
            $table->string('item_number')->nullable();
            $table->text('item_description')->default('');
        });
    }
};
