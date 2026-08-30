<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Trip extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'distance_km' => 'decimal:2',
        'toll_cost' => 'decimal:2',
        'parking_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items()
    {
        return $this->hasMany(TripItem::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'trip_id');
    }

    public function getFuelCostAttribute()
    {
        if (!$this->vehicle || $this->vehicle->km_per_liter <= 0) {
            return 0;
        }
        return ($this->distance_km / $this->vehicle->km_per_liter) * $this->vehicle->fuel_price_per_liter;
    }

    public function getManpowerCostAttribute()
    {
        if ($this->driver && $this->driver->driverProfile) {
            return $this->driver->driverProfile->rate_per_trip;
        }
        return 0;
    }

    public function getTotalCostAttribute()
    {
        return $this->fuel_cost + $this->manpower_cost + $this->toll_cost + $this->parking_cost + $this->other_cost;
    }
}
