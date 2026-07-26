<?php
$params = ['atas_penutup_tipe' => 'Papan Setengah'];
$customDetails = [];
$override = collect($customDetails)->where('section', 'Penutup')->where('part_name', 'Atas')->first();
$isIncluded = $override['include'] ?? ($params['atas_penutup_tipe'] == 'Tanpa Penutup' ? 0 : 1);
echo "isIncluded: "; var_dump($isIncluded);
if ($isIncluded == 0 || $isIncluded === '0') echo "SKIPPING!\n"; else echo "NOT SKIPPING!\n";
