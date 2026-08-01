<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagingJobItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function packagingJob()
    {
        return $this->belongsTo(PackagingJob::class, 'packaging_job_id');
    }
}
