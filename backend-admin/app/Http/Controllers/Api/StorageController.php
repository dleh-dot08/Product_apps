<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Storage\MinioService;
use App\Models\PickupTask;
use App\Models\TaskAttachment;

class StorageController extends Controller
{
    protected $minio;

    public function __construct(MinioService $minio)
    {
        $this->minio = $minio;
    }

    /**
     * Dapatkan Pre-signed URL untuk mengupload file secara aman dari PWA ke MinIO
     */
    public function getPresignedUploadUrl(Request $request)
    {
        $request->validate([
            'file_name' => 'required|string',
            'content_type' => 'required|string',
            'delivery_number' => 'required|string',
        ]);

        $fileName = $request->file_name;
        $deliveryNumber = $request->delivery_number;
        $contentType = $request->content_type;
        
        // Clean filename just in case
        $fileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $fileName);

        // Path yang diminta user: task-driver/nomor_delivery/...
        $path = "task-driver/{$deliveryNumber}/" . time() . "_{$fileName}";

        $url = $this->minio->generatePresignedUploadUrl($path, 15, $contentType);

        if (!$url) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate pre-signed URL.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'url' => $url,
            'path' => $path
        ]);
    }

    /**
     * Konfirmasi bahwa file telah diupload oleh PWA, simpan ke database
     */
    public function confirmUpload(Request $request)
    {
        $request->validate([
            'task_id' => 'required|integer',
            'file_path' => 'required|string',
            'document_type' => 'required|string', // e.g. 'Keberangkatan', 'Pengeluaran', dll
        ]);

        // Cek apakah task valid
        $task = PickupTask::find($request->task_id);
        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Task tidak ditemukan'], 404);
        }

        // Cek apakah file sudah di-upload ke MinIO (optional, bisa lewat request HEAD atau anggap sukses)
        // Simpan ke database
        $attachment = $task->attachments()->create([
            'file_path' => $request->file_path,
            'file_name' => basename($request->file_path),
            'document_type' => $request->document_type,
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti berhasil disimpan.',
            'attachment' => $attachment
        ]);
    }

    /**
     * Mendapatkan URL gambar sementara untuk di-render di frontend
     */
    public function getFileUrl(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string'
        ]);

        $url = $this->minio->getFileUrl($request->file_path);

        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    }
}
