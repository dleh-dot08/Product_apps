<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    protected $fillable = [
        'user_id',
        'task_id',
        'latitude',
        'longitude',
        'heading',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
