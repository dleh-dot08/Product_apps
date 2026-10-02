<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TaskManifest extends Model
{
    use SoftDeletes;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'manifest_number',
        'driver_id',
        'co_driver_id',
        'vehicle_id',
        'assigned_by',
        'dispatch_date',
        'status',
        'is_out_of_city',
        'estimated_arrival',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

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

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function pickupTasks()
    {
        return $this->hasMany(PickupTask::class, 'manifest_id');
    }

    public function deliveryAssignments()
    {
        return $this->hasMany(DeliveryAssignment::class, 'manifest_id');
    }

    public function historicalPickupTasks()
    {
        return $this->belongsToMany(PickupTask::class, 'tasks_manifest_history', 'manifest_id', 'task_id')
                    ->wherePivot('task_type', 'pickup')
                    ->withPivot('status', 'created_at', 'updated_at')
                    ->orderBy('tasks_manifest_history.created_at', 'asc');
    }

    public function historicalDeliveryAssignments()
    {
        return $this->belongsToMany(DeliveryAssignment::class, 'tasks_manifest_history', 'manifest_id', 'task_id')
                    ->wherePivot('task_type', 'delivery')
                    ->withPivot('status', 'created_at', 'updated_at')
                    ->orderBy('tasks_manifest_history.created_at', 'asc');
    }
}
