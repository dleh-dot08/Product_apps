<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkurasiDocumentCache extends Model
{
    protected $table = 'akurasi_document_caches';

    protected $fillable = [
        'doc_type',
        'doc_number',
        'customer_or_vendor',
        'total_amount',
        'last_synced_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'last_synced_at' => 'datetime',
    ];
}
