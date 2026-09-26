<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidasiMpDeliveryPickup extends Model
{
    protected $table = 'validasi_mp_deliverypickup';

    protected $fillable = [
        'name',
        'rate_per_hour',
        'rate_per_minute',
        'rate_per_second',
        'status_validasi',
    ];
}
