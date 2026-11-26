<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expeditions', function (Blueprint $table) {
            $table->text('tracking_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('expeditions', function (Blueprint $table) {
            $table->string('tracking_number', 100)->nullable()->change();
        });
    }
};
