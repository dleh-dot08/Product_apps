<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akurasi_document_caches', function (Blueprint $table) {
            $table->id();
            $table->string('doc_type', 10)->default('SO'); // SO atau PO
            $table->string('doc_number', 100)->index();
            $table->string('customer_or_vendor')->nullable();
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['doc_type', 'doc_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akurasi_document_caches');
    }
};
