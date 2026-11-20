<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NatsOutboxEvent extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'published_at' => 'datetime',
    ];
}
