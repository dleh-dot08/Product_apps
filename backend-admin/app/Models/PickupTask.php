<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PickupTask extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($task) {
            \App\Models\TaskHistory::create([
                'task_id' => $task->id,
                'task_type' => 'pickup',
                'driver_id' => $task->driver_id,
                'co_driver_id' => $task->co_driver_id,
                'vehicle_id' => $task->vehicle_id,
                'status' => $task->status ?? 'draft',
                'notes' => 'Tugas dibuat',
                'recorded_by' => auth()->id() ?? null,
            ]);
        });

        static::updated(function ($task) {
            if ($task->isDirty('status') || $task->isDirty('driver_id') || $task->isDirty('vehicle_id') || $task->isDirty('co_driver_id')) {
                $notes = [];
                if ($task->isDirty('status')) {
                    $notes[] = "Status berubah dari {$task->getOriginal('status')} menjadi {$task->status}";
                }
                if ($task->isDirty('driver_id') || $task->isDirty('vehicle_id') || $task->isDirty('co_driver_id')) {
                    $notes[] = "Informasi penugasan driver/kendaraan diperbarui";
                }
                
                \App\Models\TaskHistory::create([
                    'task_id' => $task->id,
                    'task_type' => 'pickup',
                    'driver_id' => $task->driver_id,
                    'co_driver_id' => $task->co_driver_id,
                    'vehicle_id' => $task->vehicle_id,
                    'status' => $task->status,
                    'notes' => implode(', ', $notes),
                    'recorded_by' => auth()->id() ?? null,
                ]);
            }
        });
    }

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

    public function manifest()
    {
        return $this->belongsTo(TaskManifest::class, 'manifest_id');
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

    public function history()
    {
        return $this->hasMany(TaskHistory::class, 'task_id');
    }
}
