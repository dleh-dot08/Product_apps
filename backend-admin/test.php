<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$job = \App\Models\PackagingJob::latest()->first();
if ($job) {
    echo json_encode($job->toArray(), JSON_PRETTY_PRINT);
} else {
    echo "No job found";
}
