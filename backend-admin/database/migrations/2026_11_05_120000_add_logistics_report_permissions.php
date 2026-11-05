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
        // 1. Ensure Module 'Laporan & Rekapitulasi' has new permissions
        $module = Module::where('name', 'like', '%Laporan%')->first();
        if ($module) {
            $perms = is_array($module->available_permissions) 
                ? $module->available_permissions 
                : (json_decode($module->available_permissions, true) ?? []);
            
            $newPerms = ['View Laporan Logistik', 'Export Laporan Logistik'];
            $dirty = false;
            foreach ($newPerms as $np) {
                if (!in_array($np, $perms)) {
                    $perms[] = $np;
                    $dirty = true;
                }
            }
            if ($dirty) {
                $module->available_permissions = array_values(array_unique($perms));
                $module->save();
            }

            // 2. Grant new permissions automatically to administrative roles
            $rolePermissions = [
                'Manager' => ['View Laporan Logistik', 'Export Laporan Logistik'],
                'Supervisor (SPV)' => ['View Laporan Logistik', 'Export Laporan Logistik'],
                'Admin Staff' => ['View Laporan Logistik', 'Export Laporan Logistik'],
            ];

            foreach ($rolePermissions as $roleName => $addPerms) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $pivot = DB::table('module_role')
                        ->where('role_id', $role->id)
                        ->where('module_id', $module->id)
                        ->first();

                    if ($pivot) {
                        $granted = json_decode($pivot->granted_permissions, true) ?? [];
                        $merged = array_values(array_unique(array_merge($granted, $addPerms)));
                        DB::table('module_role')
                            ->where('id', $pivot->id)
                            ->update([
                                'granted_permissions' => json_encode($merged),
                                'updated_at' => now(),
                            ]);
                    } else {
                        DB::table('module_role')->insert([
                            'role_id' => $role->id,
                            'module_id' => $module->id,
                            'granted_permissions' => json_encode($addPerms),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $module = Module::where('name', 'like', '%Laporan%')->first();
        if ($module) {
            $perms = is_array($module->available_permissions) 
                ? $module->available_permissions 
                : (json_decode($module->available_permissions, true) ?? []);

            $perms = array_values(array_diff($perms, ['View Laporan Logistik', 'Export Laporan Logistik']));
            $module->available_permissions = $perms;
            $module->save();
        }
    }
};

