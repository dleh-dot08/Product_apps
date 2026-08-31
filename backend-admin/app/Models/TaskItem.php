<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'itemable_id',
        'itemable_type',
        'item_number',
        'item_description',
        'quantity',
        'unit',
        'unit_price',
        'line_total'
    ];

    /**
     * Get the parent itemable model (PickupTask or SalesOrder).
     */
    public function itemable()
    {
        return $this->morphTo();
    }
}
