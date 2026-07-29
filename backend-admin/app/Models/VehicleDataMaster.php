<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDataMaster extends Model
{
    use HasFactory;

    protected $table = 'vehicles_data_master';

    protected $fillable = [
        'plate_number',
        'name',
        'fuel_price_per_liter',
        'km_per_liter',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'fuel_price_per_liter' => 'decimal:2',
        'km_per_liter' => 'decimal:2',
    ];
}
