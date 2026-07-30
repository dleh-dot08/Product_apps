<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagingJobDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'konfigurasi_atas' => 'array',
        'konfigurasi_bawah' => 'array',
    ];

    public function job()
    {
        return $this->belongsTo(PackagingJob::class, 'packaging_job_id');
    }
    
    public function packer()
    {
        return $this->belongsTo(User::class, 'packer_id');
    }

    public function details()
    {
        return $this->hasMany(\App\Models\PackingJobCalcDetail::class, 'job_id');
    }

    public function consumables()
    {
        return $this->hasMany(\App\Models\PackingJobNail::class, 'job_id');
    }

    public function manpower()
    {
        return $this->hasMany(\App\Models\PackingJobCalcManpower::class, 'job_id');
    }
}
