<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpeditionItem extends Model
{
    use HasFactory;

    protected $table = 'expedition_items';

    protected $fillable = [
        'expedition_id',
        'ref_no',
        'item_number',
        'item_description',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function expedition()
    {
        return $this->belongsTo(Expedition::class, 'expedition_id');
    }
}
