<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PickupTask extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
        'dispatch_date' => 'datetime',
        'estimated_arrival' => 'datetime',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'departure_checklist' => 'array',
        'arrival_checklist' => 'array',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function attachments()
    {
        return $this->morphMany(TaskAttachment::class, 'task');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function items()
    {
        return $this->morphMany(TaskItem::class, 'itemable');
    }
}
