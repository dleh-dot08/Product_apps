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
        'tracking_history',
        'last_tracking_status',
        'last_tracked_at',
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
        'last_tracked_at' => 'datetime',
        'tracking_history' => 'array',
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

    /**
     * Resolve direct public tracking URL for this expedition.
     */
    public function getTrackingUrlAttribute(): ?string
    {
        if (empty($this->tracking_number)) {
            return null;
        }

        $resi = trim($this->tracking_number);

        // 1. Dari master vendor jika ada pola URL
        if ($this->vendor && !empty($this->vendor->website_tracking_url)) {
            $template = $this->vendor->website_tracking_url;
            if (str_contains($template, '{RESI}')) {
                return str_replace('{RESI}', urlencode($resi), $template);
            }
            if (str_contains($template, '{resi}')) {
                return str_replace('{resi}', urlencode($resi), $template);
            }
            if (str_contains($template, '?') || str_contains($template, '=')) {
                return rtrim($template, '/') . urlencode($resi);
            }
            return rtrim($template, '/') . '/' . urlencode($resi);
        }

        // 2. Default fallback berdasarkan vendor_name
        $vName = strtolower($this->vendor_name ?? '');
        if (str_contains($vName, 'lion')) {
            return "https://lionparcel.com/track/stt?q=" . urlencode($resi);
        }
        if (str_contains($vName, 'j&t') || str_contains($vName, 'jnt')) {
            return "https://www.jet.co.id/track?awb=" . urlencode($resi);
        }
        if (str_contains($vName, 'sicepat')) {
            return "https://www.sicepat.com/checkAwb/" . urlencode($resi);
        }
        if (str_contains($vName, 'jne')) {
            return "https://www.jne.co.id/id/tracking/trace?resi=" . urlencode($resi);
        }
        if (str_contains($vName, 'ginta') || preg_match('/^JKT[0-9]+/i', $resi)) {
            return "https://trace.gintacargo.com/?awb=" . urlencode($resi);
        }
        if (str_contains($vName, 'indah')) {
            return "https://indahonline.com/layanan/cek_resi?resi=" . urlencode($resi);
        }

        return "https://trace.gintacargo.com/?awb=" . urlencode($resi);
    }
}
