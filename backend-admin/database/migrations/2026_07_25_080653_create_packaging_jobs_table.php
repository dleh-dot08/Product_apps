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
        Schema::create('packaging_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('no_so');
            $table->string('customer')->nullable();
            $table->date('date_delivery')->nullable();
            $table->text('address')->nullable();
            $table->json('daftar_iso_item_json')->nullable();
            $table->string('status')->default('draft'); // draft, calculated, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packaging_jobs');
    }
};
