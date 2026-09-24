<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function task()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFilePathAttribute($value)
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
            
            return 'https://bucket.gte.co.id/' . ltrim($cleanPath, '/');
        }
        return null;
    }
}
