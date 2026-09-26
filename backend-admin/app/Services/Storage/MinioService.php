<?php

namespace App\Services\Storage;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

class MinioService
{
    protected $client;
    protected $bucket;

    public function __construct()
    {
        // Hardcoded configuration as requested by user
        $this->bucket = 'driver-apps';
        
        $this->client = new S3Client([
            'version'                   => 'latest',
            'region'                    => 'us-east-1',
            'endpoint'                  => 'http://bucket.gte.co.id',
            'use_path_style_endpoint'   => true, // MinIO requires this
            'credentials' => [
                'key'    => 'gtefileadmin',
                'secret' => 'gte1122@2026',
            ],
            // Since SSL is false, we can disable verification if needed
            'http' => [
                'verify' => false
            ]
        ]);
    }

    /**
     * Generate a Pre-signed URL for uploading a file directly from Mobile PWA to MinIO.
     *
     * @param string $path Target path inside the bucket (e.g., task-driver/DOC-123/receipt_12345.jpg)
     * @param int $expirationMinutes How long the URL is valid
     * @param string $contentType The mime type expected to be uploaded
     * @return string|null
     */
    public function generatePresignedUploadUrl($path, $expirationMinutes = 15, $contentType = 'application/octet-stream')
    {
        try {
            $cmd = $this->client->getCommand('PutObject', [
                'Bucket' => $this->bucket,
                'Key'    => $path,
                'ContentType' => $contentType,
            ]);

            $request = $this->client->createPresignedRequest($cmd, "+{$expirationMinutes} minutes");

            return (string) $request->getUri();
        } catch (AwsException $e) {
            Log::error("MinioService presigned URL generation failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate a Pre-signed URL for viewing/downloading a file.
     *
     * @param string $path
     * @param int $expirationMinutes
     * @return string|null
     */
    public function getFileUrl($path, $expirationMinutes = 60)
    {
        try {
            // Jika path adalah full URL dari MinIO, ambil key-nya saja
            $prefix = rtrim($this->client->getEndpoint(), '/') . '/' . $this->bucket . '/';
            if (strpos($path, $prefix) === 0) {
                $path = substr($path, strlen($prefix));
            } elseif (strpos($path, 'http') === 0) {
                // Alternatif fallback jika URL formatnya sedikit berbeda
                $urlParts = parse_url($path);
                if (isset($urlParts['path'])) {
                    $pathParts = explode('/' . $this->bucket . '/', $urlParts['path']);
                    if (count($pathParts) > 1) {
                        $path = $pathParts[1];
                    }
                }
            }

            $cmd = $this->client->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key'    => ltrim($path, '/')
            ]);

            $request = $this->client->createPresignedRequest($cmd, "+{$expirationMinutes} minutes");

            return (string) $request->getUri();
        } catch (AwsException $e) {
            Log::error("MinioService get file URL failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a file from MinIO
     *
     * @param string $path
     * @return bool
     */
    public function deleteFile($path)
    {
        try {
            $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $path
            ]);
            return true;
        } catch (AwsException $e) {
            Log::error("MinioService file deletion failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the raw S3 Client if needed for other operations
     */
    public function getClient()
    {
        return $this->client;
    }
}
