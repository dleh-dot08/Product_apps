<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HppRitase extends Model
{
    protected $guarded = [];

    protected $casts = [
        'fuel_cost' => 'decimal:2',
        'manpower_cost' => 'decimal:2',
        'toll_cost' => 'decimal:2',
        'parking_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'depreciation_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'base_value' => 'decimal:2',
        'is_prorata' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(HppRitaseItem::class, 'hpp_ritase_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
