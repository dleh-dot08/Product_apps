<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpeditionVendor extends Model
{
    use HasFactory;

    protected $table = 'expedition_vendors';

    protected $fillable = [
        'vendor_code',
        'vendor_name',
        'vendor_type',
        'contact_person',
        'phone_number',
        'telephone',
        'email',
        'office_address',
        'supported_services',
        'payment_terms',
        'website_tracking_url',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'supported_services' => 'array',
        'is_active' => 'boolean',
    ];

    public function expeditions()
    {
        return $this->hasMany(Expedition::class, 'vendor_id');
    }
}
