<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\SalesOrderAkurasi;
use App\Models\PembelianOrderAkurasi;
use Illuminate\Support\Facades\Log;

class SyncAkurasiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'akurasi:sync {--only=all : Sinkronisasi spesifik (so / po / all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data SO dan PO dari API Akurasi ke Database Lokal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Memulai sinkronisasi data Akurasi API...");
        
        $apiKey = 'Ym95Y29tcG9zaXRpb25leHBsYW5hdGlvbnRob3VnaHRwZWFjZWdpcmxjb2FjaHNlbnM=';
        $dateFrom = now()->subMonths(12)->format('Y-m-d');
        $dateTo = now()->format('Y-m-d');
        $limit = 1000;
        
        $val = function($v) {
            return ($v === '' || $v === null) ? null : $v;
        };

        // --- 1. SINKRONISASI SO ---
        $only = $this->option('only');
        
        if ($only === 'all' || $only === 'so') {
            $this->info("==> Sinkronisasi Sales Orders");
            $offset = 0;
        $soCount = 0;
        $soSkipped = 0;

        while (true) {
            $url = "https://akurasi-api.aqpa-indonesia.com/api/integration/penjualan-so?offset={$offset}&limit={$limit}&date_from={$dateFrom}&date_to={$dateTo}";
            $this->line("Fetching SO offset {$offset}...");
            
            $response = Http::withHeaders(['X-API-Key' => $apiKey])->timeout(60)->get($url);
            if (!$response->successful()) {
                $this->error("Gagal mengambil data SO. HTTP " . $response->status());
                break;
            }

            $data = $response->json();
            if (empty($data['data'])) {
                $this->info("SO habis ditarik.");
                break;
            }

            foreach ($data['data'] as $item) {
                // Generate a simple hash of the array to detect changes
                $hash = md5(json_encode($item));
                
                // We use no_so and no_barang to identify unique row
                $noSo = $val($item['no_so'] ?? '-');
                $noBarang = $val($item['no_barang'] ?? '-');

                $existing = SalesOrderAkurasi::where('no_so', $noSo)
                                             ->where('no_barang', $noBarang)
                                             ->first();

                if ($existing && $existing->sync_hash === $hash) {
                    $soSkipped++;
                    continue; // Skip, tidak ada perubahan
                }

                SalesOrderAkurasi::updateOrCreate(
                    ['no_so' => $noSo, 'no_barang' => $noBarang],
                    [
                        'tgl_so' => $val($item['tgl_so'] ?? null),
                        'tgl_estimasi' => $val($item['tgl_estimasi'] ?? null),
                        'tgl_pengiriman' => $val($item['tgl_pengiriman'] ?? null),
                        'no_pelanggan' => $val($item['no_pelanggan'] ?? null),
                        'nama_pelanggan' => $val($item['nama_pelanggan'] ?? null),
                        'no_po_customer' => $val($item['no_po_customer'] ?? null),
                        'nama_salesman' => $val($item['nama_salesman'] ?? null),
                        'shipto' => $val($item['shipto'] ?? null),
                        'deskripsi_so' => $val($item['deskripsi_so'] ?? null),
                        'status' => $val($item['status'] ?? null),
                        'is_held' => $item['is_held'] ?? false,
                        'hold_note' => $val($item['hold_note'] ?? null),
                        
                        'deskripsi_barang' => $val($item['deskripsi_barang'] ?? null),
                        'category_produk' => $val($item['category_produk'] ?? null),
                        'qty' => $val($item['qty'] ?? 0),
                        'qty_shipped' => $val($item['qty_shipped'] ?? 0),
                        'sisa_kirim' => $val($item['sisa_kirim'] ?? 0),
                        'stok_tersedia' => $val($item['stok_tersedia'] ?? 0),
                        'uom' => $val($item['uom'] ?? null),
                        
                        'unit_price' => $val($item['unit_price'] ?? 0),
                        'discount_amount' => $val($item['discount_amount'] ?? 0),
                        'ppn_rate' => $val($item['ppn_rate'] ?? 0),
                        'ppn_amount' => $val($item['ppn_amount'] ?? 0),
                        'subtotal' => $val($item['subtotal'] ?? 0),
                        'amount' => $val($item['amount'] ?? 0),
                        'no_pengiriman' => $val($item['no_pengiriman'] ?? null),
                        
                        'sync_hash' => $hash
                    ]
                );
                $soCount++;
            }

            if (count($data['data']) < $limit) {
                break; // Last page
            }
            $offset += $limit;
        }
        $this->info("Sinkronisasi SO selesai. Tersimpan/Diperbarui: {$soCount}, Skipped: {$soSkipped}");
        }

        // --- 2. SINKRONISASI PO ---
        if ($only === 'all' || $only === 'po') {
            $this->info("==> Sinkronisasi Purchase Orders");
            $offset = 0;
        $poCount = 0;
        $poSkipped = 0;

        while (true) {
            // Tetap menggunakan endpoint integration yg support X-API-Key dan format sama
            $url = "https://akurasi-api.aqpa-indonesia.com/api/integration/pembelian-po?offset={$offset}&limit={$limit}&date_from={$dateFrom}&date_to={$dateTo}";
            $this->line("Fetching PO offset {$offset}...");
            
            $response = Http::withHeaders(['X-API-Key' => $apiKey])->timeout(60)->get($url);
            if (!$response->successful()) {
                $this->error("Gagal mengambil data PO. HTTP " . $response->status());
                break;
            }

            $data = $response->json();
            if (empty($data['data'])) {
                $this->info("PO habis ditarik.");
                break;
            }

            foreach ($data['data'] as $item) {
                $hash = md5(json_encode($item));
                
                $noPo = $val($item['no_po'] ?? $item['no_pembelian'] ?? '-');
                $noBarang = $val($item['no_barang'] ?? '-');

                $existing = PembelianOrderAkurasi::where('no_pembelian', $noPo)
                                                 ->where('no_barang', $noBarang)
                                                 ->first();

                if ($existing && $existing->sync_hash === $hash) {
                    $poSkipped++;
                    continue;
                }

                PembelianOrderAkurasi::updateOrCreate(
                    ['no_pembelian' => $noPo, 'no_barang' => $noBarang],
                    [
                        'tgl_pembelian' => $val($item['tgl_po'] ?? $item['tanggal'] ?? null),
                        'tgl_ekspetasi' => $val($item['tgl_estimasi'] ?? $item['tgl_terima'] ?? null),
                        'top' => $val($item['top'] ?? null),
                        'sisa_hari' => $val($item['sisa_hari'] ?? null),
                        
                        'no_permintaan' => $val($item['no_permintaan'] ?? null),
                        'tgl_permintaan' => $val($item['tgl_permintaan'] ?? null),
                        'tgl_target' => $val($item['tgl_target'] ?? null),
                        'so_no' => $val($item['so_no'] ?? null),
                        
                        'no_penerimaan' => $val($item['no_penerimaan'] ?? null),
                        'tgl_penerimaan' => $val($item['tgl_penerimaan'] ?? null),
                        'ekspetasi_vs_pb' => $val($item['ekspetasi_vs_pb'] ?? null),
                        
                        'no_pemasok' => $val($item['no_pemasok'] ?? null),
                        'nama_pemasok' => $val($item['nama_pemasok'] ?? $item['pemasok'] ?? null),
                        'purchaser' => $val($item['purchaser'] ?? null),
                        
                        'deskripsi_barang' => $val($item['deskripsi_barang'] ?? null),
                        'qty' => $val($item['qty'] ?? $item['qty_order'] ?? 0),
                        'uom' => $val($item['uom'] ?? $item['satuan'] ?? null),
                        
                        'harga_satuan' => $val($item['unit_price'] ?? $item['harga'] ?? 0),
                        'diskon' => $val($item['diskon'] ?? 0),
                        'ppn' => $val($item['ppn'] ?? null),
                        'nominal_ppn' => $val($item['nominal_ppn'] ?? 0),
                        'pph' => $val($item['pph'] ?? 0),
                        'add_cost' => $val($item['add_cost'] ?? 0),
                        'dpp' => $val($item['dpp'] ?? 0),
                        'nilai_po' => $val($item['nilai_po'] ?? 0),
                        'uang_muka' => $val($item['uang_muka'] ?? 0),
                        'sisa_po' => $val($item['sisa_po'] ?? 0),
                        
                        'status_bayar' => $val($item['status_bayar'] ?? null),
                        'no_faktur_pengajuan' => $val($item['no_faktur_pengajuan'] ?? null),
                        'pengajuan_bayar' => $val($item['pengajuan_bayar'] ?? 0),
                        'dibayar_fat' => $val($item['dibayar_fat'] ?? 0),
                        'sisa_hutang_fat' => $val($item['sisa_hutang_fat'] ?? 0),
                        'status_fat' => $val($item['status_fat'] ?? null),
                        
                        'amount' => $val($item['subtotal'] ?? $item['amount'] ?? 0),
                        'sync_hash' => $hash
                    ]
                );
                $poCount++;
            }

            if (count($data['data']) < $limit) {
                break;
            }
            $offset += $limit;
        }
        $this->info("Sinkronisasi PO selesai. Tersimpan/Diperbarui: {$poCount}, Skipped: {$poSkipped}");
        }
        
        $this->info("SELURUH SINKRONISASI BERHASIL!");
    }
}
