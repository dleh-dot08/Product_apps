<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Expense extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'occurred_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function getReceiptUrlAttribute($value)
    {
        if ($value) {
            // Jika sudah berbentuk url minio yang baru, biarkan
            if (str_contains($value, 'bucket.gte.co.id/')) {
                return $value;
            }
            
            // Bersihkan jika DB menyimpan awalan storage/uploads/ atau storage/
            $cleanPath = preg_replace('/^storage\/(uploads\/)?/', '', $value);
            
            // Bersihkan jika DB menyimpan full url server lama
            $cleanPath = str_replace('https://driverapp.aqpa-indonesia.com/storage/', '', $cleanPath);
            
            // Hapus domain bucket lama jika ada, lalu susun ulang dengan struktur baru
            $cleanPath = str_replace(['https://bucket.gte.co.id/', 'http://bucket.gte.co.id/'], '', $cleanPath);
            
            return 'https://bucket.gte.co.id/driver-apps/' . ltrim($cleanPath, '/');
        }
        return null;
    }
}
