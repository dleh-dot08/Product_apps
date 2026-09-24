<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HppRitaseItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function hppRitase()
    {
        return $this->belongsTo(HppRitase::class, 'hpp_ritase_id');
    }
}
