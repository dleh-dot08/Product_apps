<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Module;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Table: expedition_vendors
        if (!Schema::hasTable('expedition_vendors')) {
            Schema::create('expedition_vendors', function (Blueprint $table) {
                $table->id();
                $table->string('vendor_code', 50)->unique();
                $table->string('vendor_name', 150);
                $table->string('vendor_type', 50)->default('cargo'); // cargo, courier, trucking_charter, forwarder
                $table->string('contact_person', 150)->nullable();
                $table->string('phone_number', 50)->nullable();
                $table->string('telephone', 50)->nullable();
                $table->string('email', 100)->nullable();
                $table->text('office_address')->nullable();
                $table->json('supported_services')->nullable(); // Cargo Darat, Udara, Laut, FTL, dll
                $table->string('payment_terms', 50)->default('tempo_30'); // cash_cod, tempo_14, tempo_30, monthly_invoice
                $table->text('website_tracking_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2. Table: expeditions
        if (!Schema::hasTable('expeditions')) {
            Schema::create('expeditions', function (Blueprint $table) {
                $table->id();
                $table->string('expedition_number', 100)->unique(); // EXP-20261019-001
                $table->string('tracking_number', 100)->nullable(); // No Resi / AWB
                $table->foreignId('vendor_id')->nullable()->constrained('expedition_vendors')->nullOnDelete();
                $table->string('vendor_name', 150);
                $table->string('service_type', 100)->nullable();
                
                $table->string('task_type', 20)->default('delivery'); // delivery / pickup
                $table->string('reference_number', 100)->nullable(); // SO / PO
                $table->string('do_easy', 100)->nullable();
                $table->string('customer_vendor_name', 255)->nullable();
                
                $table->string('origin_name', 255)->nullable();
                $table->text('origin_address')->nullable();
                $table->string('origin_pic_name', 150)->nullable();
                $table->string('origin_pic_phone', 50)->nullable();

                $table->string('destination_name', 255)->nullable();
                $table->text('destination_address')->nullable();
                $table->string('receiver_pic_name', 150)->nullable();
                $table->string('receiver_pic_phone', 50)->nullable();

                $table->text('item_description')->nullable();
                $table->integer('total_colli')->default(1);
                $table->decimal('weight_kg', 10, 2)->nullable();
                $table->decimal('cost_amount', 14, 2)->default(0);
                $table->string('payment_method', 50)->default('non_cod');

                $table->date('shipping_date')->nullable();
                $table->date('estimated_arrival_date')->nullable();
                $table->timestamp('actual_arrival_date')->nullable();

                $table->string('status', 30)->default('on_delivery'); // draft, on_delivery, delivered, returned, cancelled
                $table->text('proof_photo')->nullable();
                $table->text('notes')->nullable();

                $table->uuid('created_by')->nullable();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Module & Permissions setup for Admin Access
        $module = Module::where('name', 'like', '%Ekspedisi%')->first();
        if (!$module) {
            $module = Module::create([
                'name' => 'Ekspedisi & Vendor',
                'icon' => 'fa-plane-departure',
                'color' => 'text-primary',
                'available_permissions' => [
                    'View Ekspedisi',
                    'Create Ekspedisi',
                    'Edit Ekspedisi',
                    'Delete Ekspedisi',
                    'View Vendor Ekspedisi',
                    'Manage Vendor Ekspedisi'
                ]
            ]);
        }

        // Grant permissions to administrative roles
        $roles = Role::whereIn('name', ['Super Admin', 'SUPER ADMIN', 'Manager', 'Supervisor (SPV)', 'Admin Staff'])->get();
        $allPerms = [
            'View Ekspedisi',
            'Create Ekspedisi',
            'Edit Ekspedisi',
            'Delete Ekspedisi',
            'View Vendor Ekspedisi',
            'Manage Vendor Ekspedisi'
        ];

        foreach ($roles as $role) {
            $pivot = DB::table('module_role')
                ->where('role_id', $role->id)
                ->where('module_id', $module->id)
                ->first();

            if ($pivot) {
                $existing = json_decode($pivot->granted_permissions, true) ?: [];
                $merged = array_values(array_unique(array_merge($existing, $allPerms)));
                DB::table('module_role')
                    ->where('role_id', $role->id)
                    ->where('module_id', $module->id)
                    ->update([
                        'granted_permissions' => json_encode($merged),
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('module_role')->insert([
                    'role_id' => $role->id,
                    'module_id' => $module->id,
                    'granted_permissions' => json_encode($allPerms),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expeditions');
        Schema::dropIfExists('expedition_vendors');
    }
};
