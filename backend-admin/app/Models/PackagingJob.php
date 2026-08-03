<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagingJob extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
        'completion_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PackagingJobItem::class, 'packaging_job_id');
    }

    public function details()
    {
        return $this->hasMany(PackingJobCalcDetail::class, 'job_id');
    }
}
