<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingMaterialPrice extends Model
{
    public function getWoodTypeAttribute()
    {
        return $this->attributes['material_type'] ?? null;
    }

    public function setWoodTypeAttribute($value)
    {
        $this->attributes['material_type'] = $value;
    }
}
