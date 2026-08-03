<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Vehicle extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
        'fuel_price_per_liter' => 'decimal:2',
        'km_per_liter' => 'decimal:2',
    ];
}
