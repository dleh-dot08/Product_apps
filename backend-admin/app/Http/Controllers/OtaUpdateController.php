<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use ZipArchive;
use Illuminate\Support\Str;

class OtaUpdateController extends Controller
{
    /**
     * GET /updates
     *
     * Mengirim manifest Expo Updates Protocol.
     */
    public function manifest(Request $request)
    {
        $platform = $request->header('expo-platform')
            ?? $request->query('platform');

        $runtimeVersion = $request->header('expo-runtime-version')
            ?? $request->query('runtime-version');

        $protocolVersion = (int) (
            $request->header('expo-protocol-version') ?? 1
        );

        if (! in_array($platform, ['android', 'ios'], true)) {
            return response()->json([
                'error' => 'Unsupported platform. Expected android or ios.',
            ], 400);
        }

        if (! is_string($runtimeVersion) || $runtimeVersion === '') {
            return response()->json([
                'error' => 'No runtimeVersion provided.',
            ], 400);
        }

        if (! preg_match('/^[A-Za-z0-9._-]+$/', $runtimeVersion)) {
            return response()->json([
                'error' => 'Invalid runtimeVersion.',
            ], 400);
        }

        $updateDirectory = $this->getLatestUpdateDirectory($runtimeVersion);

        if (! $updateDirectory) {
            return response()->json([
                'error' => 'Unsupported runtime version',
            ], 404);
        }

        $metadataPath = $updateDirectory . DIRECTORY_SEPARATOR . 'metadata.json';
        $expoConfigPath = $updateDirectory . DIRECTORY_SEPARATOR . 'expoConfig.json';

        if (! File::exists($metadataPath)) {
            return response()->json([
                'error' => 'metadata.json not found.',
            ], 404);
        }

        if (! File::exists($expoConfigPath)) {
            return response()->json([
                'error' => 'expoConfig.json not found.',
            ], 404);
        }

        $metadata = json_decode(
            File::get($metadataPath),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $expoConfig = json_decode(
            File::get($expoConfigPath),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $platformMetadata = $metadata['fileMetadata'][$platform] ?? null;

        if (! $platformMetadata) {
            return response()->json([
                'error' => "No update available for platform {$platform}.",
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Update ID
        |--------------------------------------------------------------------------
        */

        $metadataHash = hash_file('sha256', $metadataPath);
        $updateId = $this->hashToUuid($metadataHash);

        /*
        |--------------------------------------------------------------------------
        | Jika HP sudah memakai update terbaru
        |--------------------------------------------------------------------------
        */

        $currentUpdateId = $request->header('expo-current-update-id');

        if (
            $protocolVersion === 1 &&
            is_string($currentUpdateId) &&
            $currentUpdateId === $updateId
        ) {
            return $this->noUpdateResponse();
        }

        /*
        |--------------------------------------------------------------------------
        | Assets
        |--------------------------------------------------------------------------
        */

        $assets = [];

        foreach (($platformMetadata['assets'] ?? []) as $asset) {
            $assets[] = $this->makeAssetMetadata(
                updateDirectory: $updateDirectory,
                relativePath: $asset['path'],
                runtimeVersion: $runtimeVersion,
                platform: $platform,
                extension: $asset['ext'] ?? null,
                isLaunchAsset: false,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Launch Bundle
        |--------------------------------------------------------------------------
        */

        $launchAsset = $this->makeAssetMetadata(
            updateDirectory: $updateDirectory,
            relativePath: $platformMetadata['bundle'],
            runtimeVersion: $runtimeVersion,
            platform: $platform,
            extension: null,
            isLaunchAsset: true,
        );

        /*
        |--------------------------------------------------------------------------
        | Manifest
        |--------------------------------------------------------------------------
        */

        $manifest = [
            'id' => $updateId,

            'createdAt' => gmdate(
                'c',
                File::lastModified($metadataPath)
            ),

            'runtimeVersion' => $runtimeVersion,

            'assets' => $assets,

            'launchAsset' => $launchAsset,

            'metadata' => (object) [],

            'extra' => [
                'expoClient' => $expoConfig,
            ],
        ];

        return $this->multipartResponse([
            [
                'name' => 'manifest',
                'data' => $manifest,
            ],
            [
                'name' => 'extensions',
                'data' => [
                    'assetRequestHeaders' => (object) [],
                ],
            ],
        ], $protocolVersion);
    }

    /**
     * GET /updates/assets
     *
     * Mengirim JS bundle / image / font / asset OTA.
     */
    public function asset(Request $request)
    {
        $runtimeVersion = $request->query('runtimeVersion');
        $platform = $request->query('platform');
        $asset = $request->query('asset');

        if (
            ! is_string($runtimeVersion) ||
            ! preg_match('/^[A-Za-z0-9._-]+$/', $runtimeVersion)
        ) {
            return response()->json([
                'error' => 'Invalid runtimeVersion.',
            ], 400);
        }

        if (! in_array($platform, ['android', 'ios'], true)) {
            return response()->json([
                'error' => 'Invalid platform.',
            ], 400);
        }

        if (! is_string($asset) || $asset === '') {
            return response()->json([
                'error' => 'No asset provided.',
            ], 400);
        }

        $updateDirectory = $this->getLatestUpdateDirectory($runtimeVersion);

        if (! $updateDirectory) {
            return response()->json([
                'error' => 'Unsupported runtime version.',
            ], 404);
        }

        $metadataPath = $updateDirectory . DIRECTORY_SEPARATOR . 'metadata.json';

        if (! File::exists($metadataPath)) {
            return response()->json([
                'error' => 'metadata.json not found.',
            ], 404);
        }

        $metadata = json_decode(
            File::get($metadataPath),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $platformMetadata = $metadata['fileMetadata'][$platform] ?? null;

        if (! $platformMetadata) {
            return response()->json([
                'error' => 'Platform metadata not found.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Hanya file yang tercatat di metadata.json boleh diakses
        |--------------------------------------------------------------------------
        */

        $allowedAssets = [];

        if (! empty($platformMetadata['bundle'])) {
            $allowedAssets[] = $this->normalizePath(
                $platformMetadata['bundle']
            );
        }

        foreach (($platformMetadata['assets'] ?? []) as $metadataAsset) {
            if (! empty($metadataAsset['path'])) {
                $allowedAssets[] = $this->normalizePath(
                    $metadataAsset['path']
                );
            }
        }

        $asset = $this->normalizePath($asset);

        if (! in_array($asset, $allowedAssets, true)) {
            return response()->json([
                'error' => 'Asset is not part of this update.',
            ], 403);
        }

        $fullPath = $updateDirectory
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $asset);

        $realUpdateDirectory = realpath($updateDirectory);
        $realAssetPath = realpath($fullPath);

        /*
        |--------------------------------------------------------------------------
        | Proteksi path traversal
        |--------------------------------------------------------------------------
        */

        if (
            ! $realUpdateDirectory ||
            ! $realAssetPath ||
            ! str_starts_with($realAssetPath, $realUpdateDirectory)
        ) {
            return response()->json([
                'error' => 'Invalid asset path.',
            ], 403);
        }

        if (! File::exists($realAssetPath)) {
            return response()->json([
                'error' => 'Asset not found.',
            ], 404);
        }

        $isLaunchAsset =
            $asset === $this->normalizePath($platformMetadata['bundle']);

        $contentType = $isLaunchAsset
            ? 'application/javascript'
            : $this->detectContentType($realAssetPath);

        return response()->file($realAssetPath, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
    
    /**
     * POST /api/internal/ota/publish
     *
     * Menerima ZIP OTA dari GitHub Actions.
     */
    public function publish(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        $expectedToken = config('services.ota.publish_token');
        $providedToken = $request->header('X-OTA-Publish-Token');

        if (
            ! is_string($expectedToken) ||
            $expectedToken === '' ||
            ! is_string($providedToken) ||
            ! hash_equals($expectedToken, $providedToken)
        ) {
            return response()->json([
                'error' => 'Unauthorized.',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Runtime Version
        |--------------------------------------------------------------------------
        */

        $runtimeVersion = $request->input('runtimeVersion');

        if (
            ! is_string($runtimeVersion) ||
            ! preg_match('/^[A-Za-z0-9._-]+$/', $runtimeVersion)
        ) {
            return response()->json([
                'error' => 'Invalid runtimeVersion.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | ZIP
        |--------------------------------------------------------------------------
        */

        if (! $request->hasFile('bundle')) {
            return response()->json([
                'error' => 'OTA ZIP bundle is required.',
            ], 422);
        }

        $uploadedFile = $request->file('bundle');

        if (! $uploadedFile->isValid()) {
            return response()->json([
                'error' => 'Uploaded ZIP is invalid.',
            ], 422);
        }

        $tempId = Str::uuid()->toString();

        $tempDirectory = storage_path(
            'app/private/ota-upload/' . $tempId
        );

        $extractDirectory = $tempDirectory . DIRECTORY_SEPARATOR . 'extracted';

        File::ensureDirectoryExists($extractDirectory);

        $zipPath = $tempDirectory . DIRECTORY_SEPARATOR . 'update.zip';

        $uploadedFile->move(
            $tempDirectory,
            'update.zip'
        );

        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            File::deleteDirectory($tempDirectory);

            return response()->json([
                'error' => 'Unable to open OTA ZIP.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | ZIP Slip / Path Traversal Protection
        |--------------------------------------------------------------------------
        */

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);

            if (! is_string($entry)) {
                continue;
            }

            $normalized = str_replace('\\', '/', $entry);

            if (
                str_starts_with($normalized, '/') ||
                preg_match('/^[A-Za-z]:\//', $normalized) ||
                str_contains($normalized, '../')
            ) {
                $zip->close();
                File::deleteDirectory($tempDirectory);

                return response()->json([
                    'error' => 'Unsafe path detected inside ZIP.',
                ], 422);
            }
        }

        if (! $zip->extractTo($extractDirectory)) {
            $zip->close();
            File::deleteDirectory($tempDirectory);

            return response()->json([
                'error' => 'Unable to extract OTA ZIP.',
            ], 500);
        }

        $zip->close();

        /*
        |--------------------------------------------------------------------------
        | Validate Expo export
        |--------------------------------------------------------------------------
        */

        $metadataPath = $extractDirectory
            . DIRECTORY_SEPARATOR
            . 'metadata.json';

        $expoConfigPath = $extractDirectory
            . DIRECTORY_SEPARATOR
            . 'expoConfig.json';

        if (
            ! File::exists($metadataPath) ||
            ! File::exists($expoConfigPath)
        ) {
            File::deleteDirectory($tempDirectory);

            return response()->json([
                'error' => 'metadata.json or expoConfig.json is missing.',
            ], 422);
        }

        try {
            $metadata = json_decode(
                File::get($metadataPath),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            json_decode(
                File::get($expoConfigPath),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            File::deleteDirectory($tempDirectory);

            return response()->json([
                'error' => 'Invalid OTA JSON metadata.',
            ], 422);
        }

        if (
            empty($metadata['fileMetadata']) ||
            ! is_array($metadata['fileMetadata'])
        ) {
            File::deleteDirectory($tempDirectory);

            return response()->json([
                'error' => 'Invalid Expo OTA metadata structure.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Publish
        |--------------------------------------------------------------------------
        */

        $timestamp = time();

        $runtimeDirectory = storage_path(
            'app/private/ota/' . $runtimeVersion
        );

        File::ensureDirectoryExists($runtimeDirectory);

        $destination = $runtimeDirectory
            . DIRECTORY_SEPARATOR
            . $timestamp;

        while (File::exists($destination)) {
            $timestamp++;

            $destination = $runtimeDirectory
                . DIRECTORY_SEPARATOR
                . $timestamp;
        }

        if (! File::moveDirectory($extractDirectory, $destination)) {
            File::deleteDirectory($tempDirectory);

            return response()->json([
                'error' => 'Unable to publish OTA bundle.',
            ], 500);
        }

        File::deleteDirectory($tempDirectory);

        return response()->json([
            'message' => 'OTA published successfully.',
            'runtimeVersion' => $runtimeVersion,
            'timestamp' => $timestamp,
        ], 201);
    }

    /**
     * Cari update terbaru berdasarkan timestamp folder.
     *
     * storage/app/private/ota/{runtimeVersion}/{timestamp}
     */
    private function getLatestUpdateDirectory(string $runtimeVersion): ?string
    {
        $runtimeDirectory = storage_path(
            'app/private/ota/' . $runtimeVersion
        );

        if (! File::isDirectory($runtimeDirectory)) {
            return null;
        }

        $directories = collect(File::directories($runtimeDirectory))
            ->filter(function ($directory) {
                return ctype_digit(basename($directory));
            })
            ->sortByDesc(function ($directory) {
                return (int) basename($directory);
            })
            ->values();

        if ($directories->isEmpty()) {
            return null;
        }

        return $directories->first();
    }

    /**
     * Buat metadata asset sesuai Expo Updates Protocol.
     */
    private function makeAssetMetadata(
        string $updateDirectory,
        string $relativePath,
        string $runtimeVersion,
        string $platform,
        ?string $extension,
        bool $isLaunchAsset,
    ): array {
        $relativePath = $this->normalizePath($relativePath);

        $fullPath = $updateDirectory
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        if (! File::exists($fullPath)) {
            throw new \RuntimeException(
                "OTA asset not found: {$relativePath}"
            );
        }

        $hashRaw = hash_file('sha256', $fullPath, true);

        $hash = rtrim(
            strtr(base64_encode($hashRaw), '+/', '-_'),
            '='
        );

        $key = md5_file($fullPath);

        $fileExtension = $isLaunchAsset
            ? '.bundle'
            : '.' . ltrim(
                $extension ?: pathinfo($relativePath, PATHINFO_EXTENSION),
                '.'
            );

        $contentType = $isLaunchAsset
            ? 'application/javascript'
            : $this->detectContentType($fullPath);

        $url = url('/updates/assets')
            . '?'
            . http_build_query([
                'asset' => $relativePath,
                'runtimeVersion' => $runtimeVersion,
                'platform' => $platform,
            ]);

        return [
            'hash' => $hash,
            'key' => $key,
            'fileExtension' => $fileExtension,
            'contentType' => $contentType,
            'url' => $url,
        ];
    }

    /**
     * Response Expo multipart/mixed.
     */
    private function multipartResponse(
        array $parts,
        int $protocolVersion = 1
    ) {
        $boundary = 'expo-' . bin2hex(random_bytes(16));

        $body = '';

        foreach ($parts as $part) {
            $body .= "--{$boundary}\r\n";

            $body .= 'Content-Disposition: form-data; name="'
                . $part['name']
                . "\"\r\n";

            $body .= "Content-Type: application/json; charset=utf-8\r\n\r\n";

            $body .= json_encode(
                $part['data'],
                JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE |
                JSON_THROW_ON_ERROR
            );

            $body .= "\r\n";
        }

        $body .= "--{$boundary}--\r\n";

        return response($body, 200, [
            'expo-protocol-version' => (string) $protocolVersion,
            'expo-sfv-version' => '0',
            'Cache-Control' => 'private, max-age=0',
            'Content-Type' => "multipart/mixed; boundary={$boundary}",
        ]);
    }

    /**
     * Saat tidak ada update baru.
     */
    private function noUpdateResponse()
    {
        return $this->multipartResponse([
            [
                'name' => 'directive',
                'data' => [
                    'type' => 'noUpdateAvailable',
                ],
            ],
        ], 1);
    }

    /**
     * SHA256 → UUID format.
     */
    private function hashToUuid(string $hash): string
    {
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            substr($hash, 12, 4),
            substr($hash, 16, 4),
            substr($hash, 20, 12),
        );
    }

    private function normalizePath(string $path): string
    {
        return ltrim(
            str_replace('\\', '/', $path),
            '/'
        );
    }

    /**
     * Content-Type asset umum Expo.
     */
    private function detectContentType(string $path): string
    {
        $extension = strtolower(
            pathinfo($path, PATHINFO_EXTENSION)
        );

        return match ($extension) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',

            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',

            'json' => 'application/json',

            'mp4' => 'video/mp4',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',

            default => File::mimeType($path)
                ?: 'application/octet-stream',
        };
    }
}