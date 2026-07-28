<?php
$apiKey = 'Ym95Y29tcG9zaXRpb25leHBsYW5hdGlvbnRob3VnaHRwZWFjZWdpcmxjb2FjaHNlbnM=';
$url = "https://akurasi-api.aqpa-indonesia.com/api/integration/penjualan-so?offset=0&limit=5&q=AI-PP-261840";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: ' . $apiKey
]);
$res = curl_exec($ch);
echo $res;
