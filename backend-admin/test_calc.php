<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new \App\Services\PackagingCalculatorService();
$params = [
    'length' => 1000,
    'width' => 1000,
    'height' => 1000,
    'distance_between_pillars' => 300,
    'gap_atas' => 100,
    'gap_bawah' => 100,
    'atas_penyangga_include' => 1,
    'bawah_penyangga_include' => 1,
    'atas_penutup_tipe' => 'Papan Setengah',
    'bawah_penutup_tipe' => 'Papan Setengah'
];
$details = $service->buildDetailsArray($params, 'Horizontal', []);
echo json_encode(array_map(function($d) { return $d['section'] . ' - ' . $d['part_name']; }, $details), JSON_PRETTY_PRINT);
