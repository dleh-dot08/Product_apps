<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['name', 'available_permissions', 'icon', 'color'];

    protected $casts = [
        'available_permissions' => 'array',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class)
                    ->withPivot('granted_permissions')
                    ->withTimestamps();
    }
}
