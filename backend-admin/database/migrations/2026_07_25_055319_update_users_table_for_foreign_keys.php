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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['division', 'position', 'role']);
            $table->foreignId('division_id')->nullable()->after('password')->constrained('divisions')->nullOnDelete();
            $table->foreignId('role_id')->nullable()->after('division_id')->constrained('roles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropForeign(['role_id']);
            $table->dropColumn(['division_id', 'role_id']);
            
            $table->string('division')->nullable()->after('password');
            $table->string('position')->nullable()->after('division');
            $table->string('role')->default('staff')->after('position');
        });
    }
};
