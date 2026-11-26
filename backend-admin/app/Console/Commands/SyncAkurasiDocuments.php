<?php

namespace App\Console\Commands;

use App\Models\AkurasiDocumentCache;
use App\Models\DeliveryAssignment;
use App\Models\Expedition;
use App\Models\PackagingJob;
use App\Models\PickupTask;
use App\Services\AkurasiService;
use Illuminate\Console\Command;

class SyncAkurasiDocuments extends Command
{
    protected $signature = 'akurasi:sync-documents {--limit=0 : Batasi jumlah dokumen yang disync}';
    protected $description = 'Sinkronisasi nilai barang & customer/vendor SO/PO dari API Akurasi ke tabel lokal akurasi_document_caches untuk Laporan Logistik & Distribusi';

    public function handle(AkurasiService $akurasiService): int
    {
        $refs = collect()
            ->merge(Expedition::whereNotNull('reference_number')->pluck('reference_number'))
            ->merge(PackagingJob::whereNotNull('reference_number')->pluck('reference_number'))
            ->merge(PickupTask::whereNotNull('reference_number')->pluck('reference_number'))
            ->merge(
                DeliveryAssignment::with('salesOrder:id,so_number')
                    ->get()
                    ->pluck('salesOrder.so_number')
            )
            ->map(fn($r) => strtoupper(trim((string) $r)))
            ->filter(fn($r) => $r !== '' && $r !== '-')
            ->unique()
            ->values();

        $limit = (int) $this->option('limit');
        if ($limit > 0) {
            $refs = $refs->take($limit);
        }

        $total = $refs->count();
        $this->info("Memulai sinkronisasi {$total} referensi SO/PO dari API Akurasi...");

        $synced = 0;
        $failed = 0;

        foreach ($refs as $ref) {
            $found = false;

            try {
                $so = $akurasiService->detailSo($ref);
                if (!empty($so)) {
                    $val = $this->extractAmount($so);
                    AkurasiDocumentCache::updateOrCreate(
                        ['doc_type' => 'SO', 'doc_number' => $ref],
                        [
                            'customer_or_vendor' => $so['pelanggan'] ?? null,
                            'total_amount' => $val,
                            'last_synced_at' => now(),
                        ]
                    );
                    $found = true;
                    $synced++;
                }
            } catch (\Throwable $e) {
                // Lanjut coba PO
            }

            if (!$found) {
                try {
                    $po = $akurasiService->detailPo($ref);
                    if (!empty($po)) {
                        $val = $this->extractAmount($po);
                        AkurasiDocumentCache::updateOrCreate(
                            ['doc_type' => 'PO', 'doc_number' => $ref],
                            [
                                'customer_or_vendor' => $po['pemasok'] ?? null,
                                'total_amount' => $val,
                                'last_synced_at' => now(),
                            ]
                        );
                        $found = true;
                        $synced++;
                    }
                } catch (\Throwable $e) {
                    $failed++;
                }
            }
        }

        $this->info("Selesai. Berhasil sync: {$synced}, Tidak ditemukan/gagal: {$failed}.");
        return self::SUCCESS;
    }

    private function extractAmount(array $data): float
    {
        $val = (float) ($data['total_amount'] ?? 0);
        if ($val <= 0 && !empty($data['items'])) {
            foreach ($data['items'] as $it) {
                $val += (float) ($it['amount'] ?? $it['subtotal'] ?? (($it['qty'] ?? 1) * ($it['unit_price'] ?? $it['harga_satuan'] ?? 0)));
            }
        }
        return $val;
    }
}
