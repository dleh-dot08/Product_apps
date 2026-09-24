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
        // Cari modul HPP, mungkin bernama 'HPP' atau 'Laporan HPP' atau mirip itu.
        // Kita gunakan yang mirip dengan "HPP"
        $module = Module::where('name', 'like', '%HPP%')->first();
        
        if ($module) {
            $perms = is_array($module->available_permissions) ? $module->available_permissions : json_decode($module->available_permissions, true) ?? [];
            if (!in_array('Validasi HPP', $perms)) {
                $perms[] = 'Validasi HPP';
                $module->available_permissions = $perms;
                $module->save();
            }

            // Tambahkan ke role Administrator (atau Super Admin jika perlu tapi Super Admin bypass default)
            $adminRole = Role::where('name', 'Administrator')->first();
            if ($adminRole) {
                $pivot = DB::table('role_module')
                    ->where('role_id', $adminRole->id)
                    ->where('module_id', $module->id)
                    ->first();
                
                if ($pivot) {
                    $granted = json_decode($pivot->granted_permissions, true) ?? [];
                    if (!in_array('Validasi HPP', $granted)) {
                        $granted[] = 'Validasi HPP';
                        DB::table('role_module')
                            ->where('role_id', $adminRole->id)
                            ->where('module_id', $module->id)
                            ->update(['granted_permissions' => json_encode($granted)]);
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
        $module = Module::where('name', 'like', '%HPP%')->first();
        if ($module) {
            $perms = is_array($module->available_permissions) ? $module->available_permissions : json_decode($module->available_permissions, true) ?? [];
            if (($key = array_search('Validasi HPP', $perms)) !== false) {
                unset($perms[$key]);
                $module->available_permissions = array_values($perms);
                $module->save();
            }
            
            // Remove from admin
            $adminRole = Role::where('name', 'Administrator')->first();
            if ($adminRole) {
                $pivot = DB::table('role_module')
                    ->where('role_id', $adminRole->id)
                    ->where('module_id', $module->id)
                    ->first();
                
                if ($pivot) {
                    $granted = json_decode($pivot->granted_permissions, true) ?? [];
                    if (($key = array_search('Validasi HPP', $granted)) !== false) {
                        unset($granted[$key]);
                        DB::table('role_module')
                            ->where('role_id', $adminRole->id)
                            ->where('module_id', $module->id)
                            ->update(['granted_permissions' => json_encode(array_values($granted))]);
                    }
                }
            }
        }
    }
};
