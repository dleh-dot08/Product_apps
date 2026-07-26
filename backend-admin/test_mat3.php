<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$d = \App\Models\PackagingJobDetail::where('packaging_job_id', 11)->first();
$raw = $d->getRawOriginal('konfigurasi_atas');
echo "RAW: " . $raw . "\n";
echo "TYPE RAW: " . gettype($raw) . "\n";
$k = $d->konfigurasi_atas;
echo "TYPE CASTED: " . gettype($k) . "\n";
if (is_string($k)) {
    echo "DECODING AGAIN...\n";
    $k = json_decode($k, true);
}
echo "MATERIAL: " . ($k['penyanggah']['material'] ?? 'NULL') . "\n";
