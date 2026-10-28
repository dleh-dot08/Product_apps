<?php

namespace App\Services;

use App\Models\Expedition;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpeditionTrackingService
{
    /**
     * Sinkronisasi data tracking untuk satu pengiriman.
     * Mengembalikan array hasil sinkronisasi.
     */
    public function syncTracking(Expedition $ekspedisi): array
    {
        $resi = trim($ekspedisi->tracking_number);
        if (!$resi) {
            return [
                'success' => false,
                'synced_count' => 0,
                'is_delivered' => false,
                'message' => 'Nomor resi kosong.',
            ];
        }

        $vendorName = strtoupper($ekspedisi->vendor_name ?: '');
        $history = $ekspedisi->tracking_history ?: [];
        $syncedCount = 0;
        $lastStatus = $ekspedisi->last_tracking_status;
        $isDelivered = ($ekspedisi->status === 'delivered');
        $receiverName = $ekspedisi->receiver_pic_name;
        $actualArrivalDate = $ekspedisi->actual_arrival_date;

        // 1. Lion Parcel (atau nomor resi format LP)
        if (str_contains($vendorName, 'LION') || preg_match('/^[0-9]{2}LP/i', $resi)) {
            try {
                $response = Http::timeout(12)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Referer' => "https://lionparcel.com/track/stt?q={$resi}",
                    ])
                    ->get("https://lionparcel.com/api/tracking?q={$resi}");

                if ($response->successful()) {
                    $json = $response->json();
                    $sttData = $json['data'][0] ?? null;

                    if ($sttData && !empty($sttData['histories'])) {
                        $rawHistories = $sttData['histories'];
                        // Urutan kronologis lama ke baru, kita balik agar terbaru di atas
                        $reversed = array_reverse($rawHistories);
                        $newHistory = [];

                        foreach ($reversed as $h) {
                            $time = isset($h['status_date'])
                                ? Carbon::parse($h['status_date'])->format('Y-m-d H:i:s')
                                : now()->format('Y-m-d H:i:s');

                            $status = $h['status_label'] ?? ($h['status'] ?? 'CHECKPOINT');
                            $location = $h['location'] ?? '-';
                            $desc = $h['description'] ?? '';

                            $newHistory[] = [
                                'time' => $time,
                                'status' => $status,
                                'location' => $location,
                                'note' => $desc,
                                'logged_by' => 'Auto-Sync Lion Parcel',
                                'logged_at' => now()->format('Y-m-d H:i:s'),
                            ];

                            // Cek indikator apakah paket sudah sampai / diterima
                            $checkStr = strtoupper("{$status} {$desc}");
                            if (
                                str_contains($checkStr, 'DITERIMA') ||
                                str_contains($checkStr, 'DELIVERED') ||
                                str_contains($checkStr, 'POD') ||
                                str_contains($checkStr, 'SUDAH TIBA') ||
                                str_contains($checkStr, 'PAKET TIBA DI ALAMAT') ||
                                str_contains($checkStr, 'SUCCESS')
                            ) {
                                $isDelivered = true;
                                if (!$actualArrivalDate) {
                                    $actualArrivalDate = Carbon::parse($time);
                                }
                            }
                        }

                        $history = $newHistory;
                        $syncedCount = count($newHistory);

                        if (!empty($newHistory[0]['status'])) {
                            $lastStatus = $newHistory[0]['status'] . ' (' . ($newHistory[0]['location'] ?? '') . ')';
                        }

                        if (!empty($sttData['recipient_name'])) {
                            $receiverName = $sttData['recipient_name'];
                        }

                        // Cek current_status dari payload Lion Parcel
                        if (!empty($sttData['current_status'])) {
                            $curr = strtoupper($sttData['current_status']);
                            if ($curr === 'DELIVERED' || $curr === 'POD' || $curr === 'SUCCESS') {
                                $isDelivered = true;
                                if (!$actualArrivalDate) {
                                    $actualArrivalDate = now();
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("ExpeditionTrackingService Lion Parcel error for {$resi}: " . $e->getMessage());
            }
        }

        // 2. Ginta Cargo (trace.gintacargo.com)
        if (str_contains($vendorName, 'GINTA') || preg_match('/^JKT[0-9]+/i', $resi)) {
            try {
                $response = Http::timeout(12)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    ])
                    ->get("https://trace.gintacargo.com/?awb={$resi}");

                if ($response->successful()) {
                    $html = $response->body();

                    // Parse Informasi Penerima Barang
                    if (preg_match('/Diterima Oleh<\/td>\s*<td[^>]*>:<\/td>\s*<td[^>]*>([^<]+)<\/td>/i', $html, $recMatch)) {
                        $parsedReceiver = trim($recMatch[1]);
                        if ($parsedReceiver && $parsedReceiver !== '-') {
                            $receiverName = $parsedReceiver;
                            $isDelivered = true;
                            if (!$actualArrivalDate) {
                                $actualArrivalDate = now();
                            }
                        }
                    }

                    // Parse fieldset Tracking: <tr> <td>Tanggal</td> <td>Status</td> </tr>
                    if (preg_match('/<legend>Tracking<\/legend>[\s\S]*?<tbody>([\s\S]*?)<\/tbody>/i', $html, $trackMatch)) {
                        $tbody = $trackMatch[1];
                        preg_match_all('/<tr>\s*<td[^>]*>([\s\S]*?)<\/td>\s*<td[^>]*>([\s\S]*?)<\/td>\s*<\/tr>/i', $tbody, $rows, PREG_SET_ORDER);

                        if (!empty($rows)) {
                            $newHistory = [];
                            foreach ($rows as $r) {
                                $rawDate = trim(strip_tags($r[1]));
                                $statusDesc = trim(strip_tags($r[2]));

                                try {
                                    $time = Carbon::parse($rawDate)->format('Y-m-d H:i:s');
                                } catch (\Exception $e) {
                                    $time = now()->format('Y-m-d H:i:s');
                                }

                                $newHistory[] = [
                                    'time' => $time,
                                    'status' => $statusDesc,
                                    'location' => 'Ginta Hub',
                                    'note' => $statusDesc,
                                    'logged_by' => 'Auto-Sync Ginta Cargo',
                                    'logged_at' => now()->format('Y-m-d H:i:s'),
                                ];

                                $checkStr = strtoupper($statusDesc);
                                if (
                                    str_contains($checkStr, 'DITERIMA') ||
                                    str_contains($checkStr, 'DELIVERED') ||
                                    str_contains($checkStr, 'ACCEPTED') ||
                                    str_contains($checkStr, 'POD')
                                ) {
                                    $isDelivered = true;
                                    if (!$actualArrivalDate) {
                                        $actualArrivalDate = Carbon::parse($time);
                                    }
                                }
                            }

                            if (!empty($newHistory)) {
                                $history = $newHistory;
                                $syncedCount = count($newHistory);
                                $lastStatus = $newHistory[0]['status'];
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("ExpeditionTrackingService Ginta Cargo error for {$resi}: " . $e->getMessage());
            }
        }

        // 3. Express Jaya Group (https://www.expressjayagroup.com/)
        $isExpressJaya = str_contains($vendorName, 'EXPRESS JAYA') 
            || ($ekspedisi->vendor && str_contains($ekspedisi->vendor->website_tracking_url ?? '', 'expressjayagroup.com'));

        if ($isExpressJaya) {
            try {
                $response = Http::asForm()
                    ->timeout(12)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Referer' => 'https://www.expressjayagroup.com/',
                    ])
                    ->post('https://www.expressjayagroup.com/index.php', [
                        'keyword' => $resi,
                        'cari' => 'Search',
                    ]);

                if ($response->successful()) {
                    $html = $response->body();

                    // Cek apakah tabel No. Resi ditemukan dan terisi
                    if (preg_match('/<b>No\.\s*Resi\s*:\s*<\/b>\s*(?:<p>)?\s*([^<]+)/i', $html, $resiMatch)) {
                        $foundResi = trim($resiMatch[1]);

                        if (!empty($foundResi)) {
                            // 1. Parse Nama Penerima (baik di tabel ringkasan atas maupun di footer tabel status)
                            $parsedReceiver = null;
                            if (preg_match('/<b>Nama\s*Penerima\s*:\s*<\/b>\s*(?:<p>)?\s*([^<]+)/i', $html, $m)) {
                                $val = trim(strip_tags($m[1]));
                                if (!empty($val) && $val !== '-') {
                                    $parsedReceiver = $val;
                                    $receiverName = $val;
                                }
                            }

                            if (!$parsedReceiver && preg_match('/<b>Penerima\s*:\s*<\/b>\s*<dd>([^<]*)<\/dd>/i', $html, $m)) {
                                $val = trim(strip_tags($m[1]));
                                if (!empty($val) && $val !== '-') {
                                    $parsedReceiver = $val;
                                    $receiverName = $val;
                                }
                            }

                            // 2. Parse Status Akhir (toleran terhadap tag <p> yang tidak ditutup di Express Jaya)
                            $endStatus = '';
                            if (preg_match('/<b>Status\s*:\s*<\/b>\s*(?:<p>)?\s*([^<]+)/i', $html, $m)) {
                                $endStatus = trim(strip_tags($m[1]));
                            }

                            // 3. Parse Perjalanan Barang
                            $newHistory = [];
                            if (preg_match('/Perjalanan Barang<\/center><\/th>[\s\S]*?<\/tr>([\s\S]*?)<\/table>/i', $html, $m)) {
                                $rowsHtml = $m[1];
                                preg_match_all('/<tr>([\s\S]*?)<\/tr>/i', $rowsHtml, $trMatches);
                                foreach ($trMatches[1] as $tr) {
                                    if (preg_match('/<td[^>]*colspan=["\']?2["\']?[^>]*>([\s\S]*?)<\/td>/i', $tr, $tdMatch)) {
                                        $rawText = trim(strip_tags($tdMatch[1]));
                                        $cleanText = preg_replace('/\s+/', ' ', $rawText);
                                        if (!empty($cleanText)) {
                                            $checkpointTime = now()->format('Y-m-d H:i:s');
                                            if (preg_match('/(\d{1,2}\s+[A-Za-z]+\s+\d{4})/i', $cleanText, $dateM)) {
                                                try {
                                                    $checkpointTime = Carbon::parse($dateM[1])->format('Y-m-d H:i:s');
                                                } catch (\Exception $ex) {}
                                            }

                                            $newHistory[] = [
                                                'time' => $checkpointTime,
                                                'status' => $cleanText,
                                                'location' => 'Express Jaya Hub',
                                                'note' => $cleanText,
                                                'logged_by' => 'Auto-Sync Express Jaya',
                                                'logged_at' => now()->format('Y-m-d H:i:s'),
                                            ];
                                        }
                                    }
                                }
                            }

                            // Tambahkan status akhir sebagai checkpoint paling baru jika ada
                            if (!empty($endStatus)) {
                                $statusNote = "Status: {$endStatus}" . ($receiverName ? " (Penerima: {$receiverName})" : "");
                                $newHistory[] = [
                                    'time' => now()->format('Y-m-d H:i:s'),
                                    'status' => $endStatus,
                                    'location' => 'Alamat Penerima / Hub Tujuan',
                                    'note' => $statusNote,
                                    'logged_by' => 'Auto-Sync Express Jaya',
                                    'logged_at' => now()->format('Y-m-d H:i:s'),
                                ];
                            }

                            // 4. Deteksi apakah paket SUDAH SAMPAI / DITERIMA
                            $allCheckStr = strtoupper("{$endStatus} {$receiverName} " . implode(' ', array_column($newHistory, 'status')));
                            if (
                                str_contains($allCheckStr, 'DITERIMA') ||
                                str_contains($allCheckStr, 'DELIVERED') ||
                                str_contains($allCheckStr, 'SAMPAI') ||
                                str_contains($allCheckStr, 'TIBA') ||
                                str_contains($allCheckStr, 'SELESAI') ||
                                str_contains($allCheckStr, 'POD') ||
                                str_contains($allCheckStr, 'SUCCESS') ||
                                str_contains($allCheckStr, 'SUDAH') ||
                                (!empty($receiverName) && $receiverName !== '-')
                            ) {
                                $isDelivered = true;
                                if (!$actualArrivalDate) {
                                    $actualArrivalDate = now();
                                }
                            }

                            if (!empty($newHistory)) {
                                $history = array_reverse($newHistory);
                                $syncedCount = count($newHistory);
                                $lastStatus = $endStatus ?: $history[0]['status'];
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("ExpeditionTrackingService Express Jaya error for {$resi}: " . $e->getMessage());
            }
        }

        // 4. Vendor Dinamis Lainnya (Berdasarkan website_tracking_url dari master vendor)
        if ($syncedCount === 0 && $ekspedisi->vendor && !empty($ekspedisi->vendor->website_tracking_url)) {
            $template = $ekspedisi->vendor->website_tracking_url;
            $targetUrl = str_contains($template, '{RESI}') 
                ? str_replace('{RESI}', urlencode($resi), $template)
                : (str_contains($template, '{resi}') ? str_replace('{resi}', urlencode($resi), $template) : rtrim($template, '/') . '/' . urlencode($resi));

            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    ])
                    ->get($targetUrl);

                if ($response->successful()) {
                    $body = $response->body();
                    $checkStr = strtoupper(strip_tags($body));

                    // Deteksi kata kunci selesai secara dinamis dari halaman
                    if (
                        str_contains($checkStr, 'DELIVERED') || 
                        str_contains($checkStr, 'DITERIMA') || 
                        str_contains($checkStr, 'SUDAH TIBA') ||
                        str_contains($checkStr, 'SUCCESS')
                    ) {
                        $isDelivered = true;
                        if (!$actualArrivalDate) {
                            $actualArrivalDate = now();
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::info("Generic dynamic vendor tracking error for {$resi}: " . $e->getMessage());
            }
        }

        // Simpan langsung ke database
        $updateData = [
            'tracking_history' => $history,
            'last_tracking_status' => $lastStatus,
            'last_tracked_at' => now(),
        ];

        if ($receiverName) {
            $updateData['receiver_pic_name'] = $receiverName;
        }

        // OTOMATIS UBAH STATUS KE DELIVERED JIKA VENDOR MENYATAKAN DITERIMA
        if ($isDelivered) {
            $updateData['status'] = 'delivered';
            if (!$ekspedisi->actual_arrival_date || $actualArrivalDate) {
                $updateData['actual_arrival_date'] = $actualArrivalDate ?: now();
            }
        }

        $ekspedisi->update($updateData);

        return [
            'success' => true,
            'synced_count' => $syncedCount,
            'is_delivered' => ($ekspedisi->fresh()->status === 'delivered'),
            'status' => $ekspedisi->fresh()->status,
            'last_status' => $lastStatus,
            'message' => $syncedCount > 0
                ? "Berhasil sinkron {$syncedCount} riwayat perjalanan otomatis dari {$ekspedisi->vendor_name}!"
                : "Tidak ditemukan update status baru dari server vendor.",
            'data' => $ekspedisi->fresh(),
        ];
    }

    /**
     * Sinkronisasi massal seluruh ekspedisi yang masih aktif (belum delivered / cancelled).
     */
    public function syncAllActiveExpeditions(): array
    {
        $activeExpeditions = Expedition::whereNotNull('tracking_number')
            ->where('tracking_number', '!=', '')
            ->whereNotIn('status', ['delivered', 'cancelled', 'returned'])
            ->get();

        $total = $activeExpeditions->count();
        $deliveredCount = 0;
        $updatedCount = 0;

        foreach ($activeExpeditions as $exp) {
            $prevStatus = $exp->status;
            $result = $this->syncTracking($exp);

            if (!empty($result['synced_count'])) {
                $updatedCount++;
            }

            if ($prevStatus !== 'delivered' && ($result['is_delivered'] ?? false)) {
                $deliveredCount++;
            }
        }

        return [
            'total_checked' => $total,
            'updated_count' => $updatedCount,
            'newly_delivered' => $deliveredCount,
        ];
    }
}
