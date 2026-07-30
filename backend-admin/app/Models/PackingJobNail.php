<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingJobNail extends Model
{
    use HasFactory;

    protected $table = 'packing_job_nails';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}
