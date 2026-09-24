<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HppRitase extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(HppRitaseItem::class, 'hpp_ritase_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
