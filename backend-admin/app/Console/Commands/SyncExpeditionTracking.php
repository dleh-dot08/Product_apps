<?php

namespace App\Console\Commands;

use App\Services\ExpeditionTrackingService;
use Illuminate\Console\Command;

class SyncExpeditionTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ekspedisi:sync-tracking {--id= : ID Ekspedisi tertentu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis sinkron riwayat pelacakan ekspedisi vendor & ubah status delivered jika paket telah sampai';

    /**
     * Execute the console command.
     */
    public function handle(ExpeditionTrackingService $trackingService): int
    {
        $id = $this->option('id');

        if ($id) {
            $exp = \App\Models\Expedition::find($id);
            if (!$exp) {
                $this->error("Ekspedisi dengan ID {$id} tidak ditemukan.");
                return Command::FAILURE;
            }

            $this->info("Menyinkronkan ekspedisi {$exp->expedition_number} (Resi: {$exp->tracking_number})...");
            $res = $trackingService->syncTracking($exp);
            $this->line("Status: " . ($res['is_delivered'] ? 'DELIVERED (Tiba)' : $exp->fresh()->status));
            $this->info($res['message']);
            return Command::SUCCESS;
        }

        $this->info("Memulai sinkronisasi otomatis seluruh ekspedisi aktif...");
        $startTime = microtime(true);

        $summary = $trackingService->syncAllActiveExpeditions();
        $duration = round(microtime(true) - $startTime, 2);

        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Ekspedisi Diperiksa', $summary['total_checked']],
                ['Ekspedisi Terupdate', $summary['updated_count']],
                ['Ekspedisi Baru Tiba (Delivered)', $summary['newly_delivered']],
                ['Waktu Eksekusi', "{$duration} detik"],
            ]
        );

        $this->info("Sinkronisasi ekspedisi selesai dengan sukses.");
        return Command::SUCCESS;
    }
}
