<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PackagingJob;
use App\Models\PackagingJobItem;
use Illuminate\Support\Facades\DB;

class PackagingController extends Controller
{
    public function index()
    {
        // Mengambil data dari tabel packaging_jobs beserta details-nya terbaru di atas
        $packagingJobs = PackagingJob::with('details')->orderBy('created_at', 'desc')->get();
        $materials = DB::table('packing_material_prices')->orderBy('id')->get();
        $nails = DB::table('nail_size_rules')->orderBy('id')->get();

        $monthsId = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        $trendData = [];
        $maxCount = 0;
        
        for ($i = -2; $i <= 1; $i++) {
            $date = now()->addMonths($i);
            $count = PackagingJob::whereYear('created_at', $date->year)
                                 ->whereMonth('created_at', $date->month)
                                 ->count();
            if ($count > $maxCount) $maxCount = $count;
            
            $trendData[] = [
                'label' => $monthsId[$date->month],
                'count' => $count,
                'percentage' => 0,
                'is_current' => $date->format('Y-m') === now()->format('Y-m')
            ];
        }
        
        foreach ($trendData as &$data) {
            $data['percentage'] = $maxCount > 0 ? ($data['count'] / $maxCount) * 100 : 0;
        }

        // Tipe Packaging Distribution
        $typeDistribution = PackagingJob::select('type_packaging', DB::raw('count(*) as count'))
            ->groupBy('type_packaging')
            ->pluck('count', 'type_packaging')
            ->toArray();

        $totalTypes = array_sum($typeDistribution);
        
        $typeColors = [
            'Box' => '#0ea5e9',
            'Palet' => '#10b981',
            'Peti' => '#f59e0b',
            'Kerangka' => '#6366f1'
        ];
        
        $typeLabels = [
            'Box' => 'Box Kayu',
            'Palet' => 'Palet Kayu',
            'Peti' => 'Peti Kayu',
            'Kerangka' => 'Kerangka Kayu'
        ];

        $compositionData = [];
        $currentOffset = 0;
        foreach (['Box', 'Palet', 'Peti', 'Kerangka'] as $type) {
            $count = $typeDistribution[$type] ?? 0;
            $percentage = $totalTypes > 0 ? round(($count / $totalTypes) * 100) : 0;
            
            $compositionData[] = [
                'type' => $type,
                'label' => $typeLabels[$type],
                'count' => $count,
                'percentage' => $percentage,
                'color' => $typeColors[$type],
                'dasharray' => "{$percentage} " . (100 - $percentage),
                'dashoffset' => $currentOffset === 0 ? "0" : "-" . $currentOffset
            ];
            
            $currentOffset += $percentage;
        }

        // KPI: Permintaan Baru (This week vs Last week)
        $startOfCurrentWeek = now()->startOfWeek();
        $endOfCurrentWeek = now()->endOfWeek();
        $startOfLastWeek = now()->subWeek()->startOfWeek();
        $endOfLastWeek = now()->subWeek()->endOfWeek();

        $currentWeekCount = PackagingJob::whereBetween('created_at', [$startOfCurrentWeek, $endOfCurrentWeek])->count();
        $previousWeekCount = PackagingJob::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();
        
        $weekGrowth = 0;
        if ($previousWeekCount > 0) {
            $weekGrowth = (($currentWeekCount - $previousWeekCount) / $previousWeekCount) * 100;
        } elseif ($currentWeekCount > 0) {
            $weekGrowth = 100;
        }
        
        $kpiPermintaanBaru = [
            'count' => $currentWeekCount,
            'growth' => round($weekGrowth, 1),
            'is_positive' => $weekGrowth >= 0
        ];

        // KPI: Dalam Proses (assigned, process)
        $currentWeekDalamProses = PackagingJob::whereBetween('created_at', [$startOfCurrentWeek, $endOfCurrentWeek])->whereIn('status', ['assigned', 'process'])->count();
        $previousWeekDalamProses = PackagingJob::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->whereIn('status', ['assigned', 'process'])->count();
        $weekGrowthDalamProses = 0;
        if ($previousWeekDalamProses > 0) {
            $weekGrowthDalamProses = (($currentWeekDalamProses - $previousWeekDalamProses) / $previousWeekDalamProses) * 100;
        } elseif ($currentWeekDalamProses > 0) {
            $weekGrowthDalamProses = 100;
        }
        $kpiDalamProses = [
            'count' => $currentWeekDalamProses,
            'growth' => round($weekGrowthDalamProses, 1),
            'is_positive' => $weekGrowthDalamProses >= 0
        ];

        // KPI: Siap Kirim (done)
        $currentWeekSiapKirim = PackagingJob::whereBetween('created_at', [$startOfCurrentWeek, $endOfCurrentWeek])->where('status', 'done')->count();
        $previousWeekSiapKirim = PackagingJob::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->where('status', 'done')->count();
        $weekGrowthSiapKirim = 0;
        if ($previousWeekSiapKirim > 0) {
            $weekGrowthSiapKirim = (($currentWeekSiapKirim - $previousWeekSiapKirim) / $previousWeekSiapKirim) * 100;
        } elseif ($currentWeekSiapKirim > 0) {
            $weekGrowthSiapKirim = 100;
        }
        $kpiSiapKirim = [
            'count' => $currentWeekSiapKirim,
            'growth' => round($weekGrowthSiapKirim, 1),
            'is_positive' => $weekGrowthSiapKirim >= 0
        ];

        // Ringkasan Permintaan
        $currentMonthCount = PackagingJob::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $lastMonthCount = PackagingJob::whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year)->count();
        $monthlyGrowth = 0;
        if ($lastMonthCount > 0) {
            $monthlyGrowth = (($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100;
        } elseif ($currentMonthCount > 0) {
            $monthlyGrowth = 100;
        }
        
        $customerAktif = \App\Models\PackagingJobItem::distinct('customer')->whereNotNull('customer')->count();
        
        $tujuanTerbanyakItem = \App\Models\PackagingJobItem::select('address', DB::raw('count(*) as total'))
            ->whereNotNull('address')
            ->where('address', '!=', '')
            ->groupBy('address')
            ->orderByDesc('total')
            ->first();
        $tujuanTerbanyak = $tujuanTerbanyakItem ? $tujuanTerbanyakItem->address : '-';

        $tipeDominanItem = PackagingJob::select('type_packaging', DB::raw('count(*) as total'))
            ->whereNotNull('type_packaging')
            ->groupBy('type_packaging')
            ->orderByDesc('total')
            ->first();
        $tipeDominan = $tipeDominanItem ? $tipeDominanItem->type_packaging : '-';

        $urgentCount = PackagingJob::whereNotNull('deadline')
            ->where('deadline', '<=', now()->addDays(3))
            ->whereIn('status', ['draft', 'assigned', 'process'])
            ->count();
            
        $jobsWithDeadline = PackagingJob::whereNotNull('deadline')->select('deadline', 'created_at')->get();
        $totalDays = 0;
        $countJobs = $jobsWithDeadline->count();
        if ($countJobs > 0) {
            foreach ($jobsWithDeadline as $jobData) {
                $deadlineDate = \Carbon\Carbon::parse($jobData->deadline);
                $createdDate = \Carbon\Carbon::parse($jobData->created_at);
                // Menghitung selisih hari absolut
                $totalDays += $createdDate->diffInDays($deadlineDate);
            }
            $avgLeadTime = $totalDays / $countJobs;
        } else {
            $avgLeadTime = 0;
        }
            
        $summaryData = [
            'total_month' => $currentMonthCount,
            'monthly_growth' => round($monthlyGrowth, 1),
            'customer_aktif' => $customerAktif,
            'tujuan_terbanyak' => $tujuanTerbanyak,
            'tipe_dominan' => $tipeDominan,
            'urgent_count' => $urgentCount,
            'urgent_percentage' => $totalTypes > 0 ? round(($urgentCount / $totalTypes) * 100, 1) : 0,
            'avg_lead_time' => $avgLeadTime ? round($avgLeadTime, 1) : 0
        ];

        return view('packaging.index', compact(
            'packagingJobs', 'materials', 'nails', 'trendData', 
            'compositionData', 'totalTypes', 'kpiPermintaanBaru', 
            'kpiDalamProses', 'kpiSiapKirim', 'summaryData'
        ));
    }

    public function destroy(PackagingJob $packagingJob)
    {
        $packagingJob->delete();
        return redirect()->route('packaging.index')->with('success', 'Data packaging berhasil dihapus.');
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('PackagingController@store Payload: ', $request->all());

        $request->validate([
            'no_so' => 'required|string',
            'customer' => 'nullable|string',
            'date_delivery' => 'nullable|date',
            'address' => 'nullable|string',
            'packType' => 'nullable|string',
            'type_packaging' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.no_so' => 'nullable|string',
            'items.*.customer' => 'nullable|string',
            'items.*.no_product' => 'required|string',
            'items.*.desc_product' => 'nullable|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.qty_kirim' => 'nullable|integer|min:1',
            'items.*.qty_barang_dikirim' => 'nullable|integer|min:1',
            'items.*.jarak_penyanggah_atas' => 'nullable|numeric|min:0',
            'items.*.jarak_penyanggah_bawah' => 'nullable|numeric|min:0',
            'items.*.gap_atas' => 'nullable|numeric|min:0',
            'items.*.gap_bawah' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Gunakan konfigurasi dari item pertama untuk membuat 1 Box Packaging
            $items = $request->input('items', []);
            $firstItem = reset($items);
            
            $datePrefix = date('Ymd');
            $latestJob = PackagingJob::where('packaging_number', 'like', "PKG-{$datePrefix}-%")
                ->orderBy('packaging_number', 'desc')
                ->first();
                
            if ($latestJob) {
                $lastSequence = (int) substr($latestJob->packaging_number, -4);
                $newSequence = $lastSequence + 1;
            } else {
                $newSequence = 1;
            }
            $packagingNumber = 'PKG-' . $datePrefix . '-' . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
            
            $job = PackagingJob::create([
                'type_packaging' => $request->input('type_packaging', $request->packType ?? 'Box'),
                // Menyimpan nilai "Bahan Penutup" (contoh: Papan / Triplek) dari Step 2 
                // Nilai ini penting karena menentukan interaksi logika dropdown Konfigurasi di Step 3
                // (seperti Box, Palet, Peti, Kerangka memiliki perlakuan berbeda terhadap Papan/Triplek).
                'tipe_penutup' => $request->input('tipe_penutup') ?? $firstItem['tipe_penutup'] ?? null,
                'additional_mat' => $request->input('additional_mat') ?? $firstItem['additional_mat'] ?? null,
                'carton_material' => stripos($request->input('additional_mat') ?? $firstItem['additional_mat'] ?? '', 'Carton') !== false ? ($request->input('carton_material') ?? $firstItem['carton_material'] ?? null) : null,
                'carton_type_sablon' => stripos($request->input('additional_mat') ?? $firstItem['additional_mat'] ?? '', 'Carton') !== false ? ($request->input('carton_type_sablon') ?? $firstItem['carton_type_sablon'] ?? null) : null,
                'terpal_material' => stripos($request->input('additional_mat') ?? $firstItem['additional_mat'] ?? '', 'Terpal') !== false ? ($request->input('terpal_material') ?? $firstItem['terpal_material'] ?? null) : null,
                'inner_carton_boxes' => json_encode($request->input('inner_carton_boxes') ?? $firstItem['inner_carton_boxes'] ?? []),
                'packaging_number' => $packagingNumber,
                'packer_id' => !empty($firstItem['packer']) ? $firstItem['packer'] : null,
                'qty_packaging' => $firstItem['qty_pack'] ?? 1,
                'deadline' => $request->date_delivery,
                'completion_date' => $request->completion_date,
                
                'panjang' => $firstItem['length'] ?? 0,
                'lebar' => $firstItem['width'] ?? 0,
                'tinggi' => $firstItem['height'] ?? 0,
                'gap_atas' => $firstItem['gap_atas'] ?? 0,
                'gap_bawah' => $firstItem['gap_bawah'] ?? 0,
                'jarak_penyanggah_atas' => $firstItem['jarak_penyanggah_atas'] ?? $firstItem['jarak_atas'] ?? $firstItem['jarak'] ?? 0,
                'jarak_penyanggah_bawah' => $firstItem['jarak_penyanggah_bawah'] ?? $firstItem['jarak_bawah'] ?? $firstItem['jarak'] ?? 0,
                
                'bawah_kakibalok_status' => $firstItem['kb_status'] ?? 'Include',
                'bawah_kakibalok_material' => $firstItem['kb_material'] ?? 'A001',
                'bawah_kakibalok_arahpemasangan' => $firstItem['kb_arah'] ?? 'Horizontal',
                'bawah_penyanggah_status' => $firstItem['pb_status'] ?? 'Include',
                'bawah_penyanggah_material' => $firstItem['pb_material'] ?? 'A001',
                'bawah_penyanggah_arahpemasangan' => $firstItem['pb_arah'] ?? 'Horizontal',
                'bawah_penutup_status' => $firstItem['ptb_status'] ?? 'Tanpa Penutup',
                'bawah_penutup_material' => $firstItem['ptb_material'] ?? 'A001',
                'bawah_penutup_arahpemasangan' => $firstItem['ptb_arah'] ?? 'Horizontal',
                
                'atas_penyanggah_status' => $firstItem['pa_status'] ?? 'Include',
                'atas_penyanggah_material' => $firstItem['pa_material'] ?? 'A001',
                'atas_penyanggah_arahpemasangan' => $firstItem['pa_arah'] ?? 'Horizontal',
                'atas_penutup_status' => $firstItem['pta_status'] ?? 'Tanpa Penutup',
                'atas_penutup_material' => $firstItem['pta_material'] ?? 'A001',
                'atas_penutup_arahpemasangan' => $firstItem['pta_arah'] ?? 'Horizontal',
                
                'status' => 'draft'
            ]);

            $job->update([
                'packaging_number' => 'PKG-' . date('Ymd') . '-' . str_pad($job->id, 4, '0', STR_PAD_LEFT)
            ]);

            // Masukkan semua produk Step 1 ke dalam packaging yang sama.
            // SO dan customer diambil per item agar tidak tertukar ketika
            // pengguna menambahkan produk dari SO/customer berbeda.
            foreach ($request->input('items', []) as $item) {
                $job->items()->create([
                    'no_so' => trim((string) (
                        $item['no_so'] ??
                        $request->no_so ??
                        ''
                    )),
                    'customer' => trim((string) (
                        $item['customer'] ??
                        $request->customer ??
                        ''
                    )),
                    'date_delivery' =>
                        $item['date_delivery'] ??
                        $request->date_delivery,
                    'address' =>
                        $item['address'] ??
                        $request->address,
                    'no_product' => trim((string) (
                        $item['no_product'] ??
                        $item['no_barang'] ??
                        ''
                    )),
                    'desc_product' => trim((string) (
                        $item['desc_product'] ??
                        $item['nama_barang'] ??
                        ''
                    )),
                    'qty' => max(1, (int) (
                        $item['qty'] ??
                        $item['qty_kirim'] ??
                        $item['qty_barang_dikirim'] ??
                        1
                    )),
                ]);
            }

            // Hitung harga box sekali saja berdasarkan dimensi/konfigurasi box
            $extraParams = [
                'atas_penyangga_include' => ($firstItem['pa_status'] ?? 'Include') === 'Include' ? '1' : '0',
                'bawah_penyangga_include' => ($firstItem['pb_status'] ?? 'Include') === 'Include' ? '1' : '0',
                'bawah_penutup_tipe' => $firstItem['ptb_status'] ?? 'Tanpa Penutup',
                'atas_penutup_tipe' => $firstItem['pta_status'] ?? 'Tanpa Penutup',
            ];

            $calculator = new \App\Services\PackagingCalculatorService();
            $calculator->calculateForJobDetail($job, $extraParams);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Kalkulasi packaging berhasil disimpan.',
                'redirect' => route('packaging.calculations.show', $job->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error in PackagingController@store: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(PackagingJob $packagingJob)
    {
        $packagingJob->load('details');
        return view('packaging.edit', compact('packagingJob'));
    }

    public function update(Request $request, PackagingJob $packagingJob)
    {
        $request->validate([
            'no_so' => 'required|string',
            'customer' => 'nullable|string',
            'date_delivery' => 'nullable|date',
            'address' => 'nullable|string',
            'packType' => 'nullable|string',
            'type_packaging' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.no_so' => 'nullable|string',
            'items.*.customer' => 'nullable|string',
            'items.*.no_product' => 'required|string',
            'items.*.desc_product' => 'nullable|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.qty_kirim' => 'nullable|integer|min:1',
            'items.*.qty_barang_dikirim' => 'nullable|integer|min:1',
            'items.*.jarak_penyanggah_atas' => 'nullable|numeric|min:0',
            'items.*.jarak_penyanggah_bawah' => 'nullable|numeric|min:0',
            'items.*.gap_atas' => 'nullable|numeric|min:0',
            'items.*.gap_bawah' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $items = $request->input('items', []);
            $firstItem = reset($items);
            
            // Update 1 Box Packaging dengan konfigurasi dari form
            $packagingJob->update([
                'type_packaging' => $request->input('type_packaging', $request->packType ?? 'Box'),
                'tipe_penutup' => $request->input('tipe_penutup') ?? $firstItem['tipe_penutup'] ?? null,
                'additional_mat' => $request->input('additional_mat') ?? $firstItem['additional_mat'] ?? null,
                'carton_material' => stripos($request->input('additional_mat') ?? $firstItem['additional_mat'] ?? '', 'Carton') !== false ? ($request->input('carton_material') ?? $firstItem['carton_material'] ?? null) : null,
                'carton_type_sablon' => stripos($request->input('additional_mat') ?? $firstItem['additional_mat'] ?? '', 'Carton') !== false ? ($request->input('carton_type_sablon') ?? $firstItem['carton_type_sablon'] ?? null) : null,
                'terpal_material' => stripos($request->input('additional_mat') ?? $firstItem['additional_mat'] ?? '', 'Terpal') !== false ? ($request->input('terpal_material') ?? $firstItem['terpal_material'] ?? null) : null,
                'inner_carton_boxes' => json_encode($request->input('inner_carton_boxes') ?? $firstItem['inner_carton_boxes'] ?? []),
                'packer_id' => !empty($firstItem['packer']) ? $firstItem['packer'] : null,
                'qty_packaging' => $firstItem['qty_pack'] ?? 1,
                'deadline' => $request->date_delivery,
                
                'panjang' => $firstItem['length'] ?? 0,
                'lebar' => $firstItem['width'] ?? 0,
                'tinggi' => $firstItem['height'] ?? 0,
                'gap_atas' => $firstItem['gap_atas'] ?? 0,
                'gap_bawah' => $firstItem['gap_bawah'] ?? 0,
                'jarak_penyanggah_atas' => $firstItem['jarak_penyanggah_atas'] ?? $firstItem['jarak_atas'] ?? $firstItem['jarak'] ?? $packagingJob->jarak_penyanggah_atas ?? 0,
                'jarak_penyanggah_bawah' => $firstItem['jarak_penyanggah_bawah'] ?? $firstItem['jarak_bawah'] ?? $firstItem['jarak'] ?? $packagingJob->jarak_penyanggah_bawah ?? 0,
                
                'bawah_kakibalok_status' => $firstItem['kb_status'] ?? 'Include',
                'bawah_kakibalok_material' => $firstItem['kb_material'] ?? 'A001',
                'bawah_kakibalok_arahpemasangan' => $firstItem['kb_arah'] ?? 'Horizontal',
                'bawah_penyanggah_status' => $firstItem['pb_status'] ?? 'Include',
                'bawah_penyanggah_material' => $firstItem['pb_material'] ?? 'A001',
                'bawah_penyanggah_arahpemasangan' => $firstItem['pb_arah'] ?? 'Horizontal',
                'bawah_penutup_status' => $firstItem['ptb_status'] ?? 'Tanpa Penutup',
                'bawah_penutup_material' => $firstItem['ptb_material'] ?? 'A001',
                'bawah_penutup_arahpemasangan' => $firstItem['ptb_arah'] ?? 'Horizontal',
                
                'atas_penyanggah_status' => $firstItem['pa_status'] ?? 'Include',
                'atas_penyanggah_material' => $firstItem['pa_material'] ?? 'A001',
                'atas_penyanggah_arahpemasangan' => $firstItem['pa_arah'] ?? 'Horizontal',
                'atas_penutup_status' => $firstItem['pta_status'] ?? 'Tanpa Penutup',
                'atas_penutup_material' => $firstItem['pta_material'] ?? 'A001',
                'atas_penutup_arahpemasangan' => $firstItem['pta_arah'] ?? 'Horizontal',
            ]);

            // Bersihkan item lama lalu simpan isi terbaru dari Step 1.
            $packagingJob->items()->delete();

            foreach ($request->input('items', []) as $item) {
                $packagingJob->items()->create([
                    'no_so' => trim((string) (
                        $item['no_so'] ??
                        $request->no_so ??
                        ''
                    )),
                    'customer' => trim((string) (
                        $item['customer'] ??
                        $request->customer ??
                        ''
                    )),
                    'date_delivery' =>
                        $item['date_delivery'] ??
                        $request->date_delivery,
                    'address' =>
                        $item['address'] ??
                        $request->address,
                    'no_product' => trim((string) (
                        $item['no_product'] ??
                        $item['no_barang'] ??
                        ''
                    )),
                    'desc_product' => trim((string) (
                        $item['desc_product'] ??
                        $item['nama_barang'] ??
                        ''
                    )),
                    'qty' => max(1, (int) (
                        $item['qty'] ??
                        $item['qty_kirim'] ??
                        $item['qty_barang_dikirim'] ??
                        1
                    )),
                ]);
            }

            // Bersihkan kalkulasi lama
            \App\Models\PackingJobCalcDetail::where('job_id', $packagingJob->id)->delete();
            \Illuminate\Support\Facades\DB::table('packing_job_calc_manpowers')->where('job_id', $packagingJob->id)->delete();
            \Illuminate\Support\Facades\DB::table('packing_job_nails')->where('job_id', $packagingJob->id)->delete();

            // Hitung harga box sekali saja berdasarkan konfigurasi yang diperbarui
            $extraParams = [
                'atas_penyangga_include' => ($firstItem['pa_status'] ?? 'Include') === 'Include' ? '1' : '0',
                'bawah_penyangga_include' => ($firstItem['pb_status'] ?? 'Include') === 'Include' ? '1' : '0',
                'bawah_penutup_tipe' => $firstItem['ptb_status'] ?? 'Tanpa Penutup',
                'atas_penutup_tipe' => $firstItem['pta_status'] ?? 'Tanpa Penutup',
            ];

            $calculator = new \App\Services\PackagingCalculatorService();
            $calculator->calculateForJobDetail($packagingJob, $extraParams);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Kalkulasi packaging berhasil diperbarui.',
                'redirect' => route('packaging.calculations.show', $packagingJob->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error in PackagingController@update: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, PackagingJob $packagingJob)
    {
        $request->validate([
            'status' => 'required|in:draft,assigned,process,done'
        ]);

        $packagingJob->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status packaging berhasil diperbarui.');
    }
}
