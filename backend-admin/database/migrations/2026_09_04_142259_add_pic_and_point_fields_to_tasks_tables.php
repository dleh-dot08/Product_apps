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
            $table->string('priority')->nullable()->after('status');
            $table->string('pickup_pic_name')->nullable()->after('pickup_name');
            $table->string('pickup_point')->nullable()->after('pickup_location');
            $table->string('destination_pic_name')->nullable()->after('destination_name');
            $table->string('destination_point')->nullable()->after('destination');
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->string('priority')->nullable()->after('status');
            $table->string('delivery_sender_pic')->nullable()->after('pickup_name');
            $table->string('delivery_origin_point')->nullable()->after('pickup_location');
            $table->string('delivery_receiver_pic')->nullable();
            $table->string('delivery_target_point')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pickup_tasks', function (Blueprint $table) {
            $table->dropColumn(['priority', 'pickup_pic_name', 'pickup_point', 'destination_pic_name', 'destination_point']);
        });

        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dropColumn(['priority', 'delivery_sender_pic', 'delivery_origin_point', 'delivery_receiver_pic', 'delivery_target_point']);
        });
    }
};
