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
        Schema::table('task_attachments', function (Blueprint $table) {
            $table->uuid('shift_id')->nullable()->after('id');
            $table->uuid('task_id')->nullable()->change();
            $table->string('task_type')->nullable()->change();
            
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_attachments', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
            
            // Reverting back might fail if there are nulls, but for strictness:
            $table->uuid('task_id')->nullable(false)->change();
            $table->string('task_type')->nullable(false)->change();
        });
    }
};
