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
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            }
            // Hapus 'storage/uploads/' jika ada agar sama dengan folder minio
            $cleanPath = preg_replace('/^storage\/uploads\//', '', $value);
            return \Illuminate\Support\Facades\Storage::url($cleanPath);
        }
        return null;
    }
}
