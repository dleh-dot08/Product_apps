<?php

use Illuminate\Database\Migrations\Migration;
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
        // 1. Ensure Module 'Laporan & Rekapitulasi' exists with correct permissions
        $module = Module::where('name', 'like', '%Laporan%')->first();
        if (!$module) {
            $module = Module::create([
                'name' => 'Laporan & Rekapitulasi',
                'icon' => 'fa-file-invoice',
                'color' => 'text-warning',
                'available_permissions' => ['View Laporan', 'Export Laporan']
            ]);
        } else {
            $perms = is_array($module->available_permissions) 
                ? $module->available_permissions 
                : (json_decode($module->available_permissions, true) ?? []);
            
            $dirty = false;
            if (!in_array('View Laporan', $perms)) {
                $perms[] = 'View Laporan';
                $dirty = true;
            }
            if (!in_array('Export Laporan', $perms)) {
                $perms[] = 'Export Laporan';
                $dirty = true;
            }
            if ($dirty) {
                $module->available_permissions = array_values(array_unique($perms));
                $module->save();
            }
        }

        // 2. Set default permissions for administrative roles so existing users retain access
        // Manager: full reports access (View + Export)
        // Supervisor (SPV): full reports access (View + Export)
        // Admin Staff: View Laporan
        $rolePermissions = [
            'Manager' => ['View Laporan', 'Export Laporan'],
            'Supervisor (SPV)' => ['View Laporan', 'Export Laporan'],
            'Admin Staff' => ['View Laporan'],
        ];

        foreach ($rolePermissions as $roleName => $defaultPerms) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $pivot = DB::table('module_role')
                    ->where('role_id', $role->id)
                    ->where('module_id', $module->id)
                    ->first();

                if ($pivot) {
                    $existing = json_decode($pivot->granted_permissions, true) ?? [];
                    $merged = array_values(array_unique(array_merge($existing, $defaultPerms)));
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
                        'granted_permissions' => json_encode($defaultPerms),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep module and permissions intact or remove assigned permissions if needed
    }
};
