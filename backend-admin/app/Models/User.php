<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'role',
        'division_id',
        'role_id',
        'manpower_rate_per_hour',
        'manpower_count',
        'active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'active' => 'boolean',
            'manpower_rate_per_hour' => 'decimal:2',
        ];
    }

    public function hasRole($roles)
    {
        $userRole = strtolower($this->role);

        if (is_array($roles)) {
            $roles = array_map('strtolower', $roles);
            return in_array($userRole, $roles);
        }

        return $userRole === strtolower($roles);
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function roleRelation()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission($permissionName)
    {
        // 1. Cek role relation
        $role = $this->roleRelation;
        if (!$role) {
            return false;
        }

        // 2. Super Admin bypass (Semua role ini punya akses penuh seperti admin)
        if (in_array(strtoupper($role->name), ['SUPER ADMIN', 'MANAGER', 'SUPERVISOR (SPV)', 'ADMIN STAFF', 'OPERATOR STAFF'])) {
            return true;
        }

        // 3. Cek di setiap modul
        foreach ($role->modules as $module) {
            $granted = json_decode($module->pivot->granted_permissions, true) ?? [];
            if (in_array($permissionName, $granted)) {
                return true;
            }
        }

        return false;
    }

    public function driverProfile()
    {
        return $this->hasOne(DriverProfile::class, 'user_id');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }
}
