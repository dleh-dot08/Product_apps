<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $disk = \Illuminate\Support\Facades\Storage::disk('s3');
    // Using internal flysystem adapter to force throw
    $disk->getAdapter()->write('test.txt', 'hello', new \League\Flysystem\Config());
    echo "Success\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
