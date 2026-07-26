<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagingJob extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'daftar_iso_item_json' => 'array',
        'date_delivery' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(PackagingJobDetail::class, 'packaging_job_id');
    }
}
