<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SalesOrder extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'so_date' => 'date',
        'estimated_delivery_date' => 'date',
        'ordered_quantity' => 'decimal:3',
        'shipped_quantity' => 'decimal:3',
        'remaining_quantity' => 'decimal:3',
        'source_data' => 'array',
        'source_updated_at' => 'datetime',
    ];

    public function assignments()
    {
        return $this->hasMany(DeliveryAssignment::class, 'sales_order_id');
    }

    public function items()
    {
        return $this->morphMany(TaskItem::class, 'itemable');
    }
}
