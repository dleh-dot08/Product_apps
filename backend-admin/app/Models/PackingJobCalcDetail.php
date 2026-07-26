<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingJobCalcDetail extends Model
{
    use HasFactory;

    protected $table = 'packing_job_calc_details';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function material()
    {
        return $this->belongsTo(PackingMaterialPrice::class, 'material_code', 'code');
    }
}
