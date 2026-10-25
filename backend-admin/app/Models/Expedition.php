<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expedition extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'expeditions';

    protected $fillable = [
        'expedition_number',
        'tracking_number',
        'vendor_id',
        'vendor_name',
        'service_type',
        'task_type',
        'reference_number',
        'do_easy',
        'customer_vendor_name',
        'origin_name',
        'origin_address',
        'origin_pic_name',
        'origin_pic_phone',
        'destination_name',
        'destination_address',
        'receiver_pic_name',
        'receiver_pic_phone',
        'item_description',
        'total_colli',
        'weight_kg',
        'cost_amount',
        'payment_method',
        'shipping_date',
        'estimated_arrival_date',
        'actual_arrival_date',
        'status',
        'proof_photo',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'total_colli' => 'integer',
        'weight_kg' => 'decimal:2',
        'cost_amount' => 'decimal:2',
        'shipping_date' => 'date',
        'estimated_arrival_date' => 'date',
        'actual_arrival_date' => 'datetime',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     * Prevents UTC timezone shift on date fields.
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function vendor()
    {
        return $this->belongsTo(ExpeditionVendor::class, 'vendor_id');
    }

    public function items()
    {
        return $this->hasMany(ExpeditionItem::class, 'expedition_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
