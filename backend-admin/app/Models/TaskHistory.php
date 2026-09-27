<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskHistory extends Model
{
    protected $fillable = [
        'task_id',
        'task_type',
        'driver_id',
        'co_driver_id',
        'vehicle_id',
        'status',
        'notes',
        'recorded_by',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function coDriver()
    {
        return $this->belongsTo(User::class, 'co_driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
