<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingJobCalcManpower extends Model
{
    use HasFactory;

    protected $table = 'packing_job_calc_manpowers';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}
