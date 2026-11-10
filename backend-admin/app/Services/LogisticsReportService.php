<?php

namespace App\Services;

use App\Models\Expedition;
use App\Models\PackagingJob;
use App\Models\PackagingJobItem;
use App\Models\PickupTask;
use App\Models\DeliveryAssignment;
use App\Models\SalesOrder;
use App\Models\SalesOrderAkurasi;
use App\Models\PembelianOrderAkurasi;
use App\Models\Shift;
use App\Models\HppRitase;
use App\Models\HppRitaseItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LogisticsReportService
{
    protected HppCalculationService $hppService;
    protected AkurasiService $akurasiService;

    public function __construct(HppCalculationService $hppService, AkurasiService $akurasiService)
    {
        $this->hppService = $hppService;
        $this->akurasiService = $akurasiService;
    }

    /**
     * Parse date range filters
     */
    public function parseDateRange($request): array
    {
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)->startOfDay() 
            : Carbon::now()->startOfMonth()->startOfDay();

        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date)->endOfDay() 
            : Carbon::now()->endOfMonth()->endOfDay();

        return [$startDate, $endDate];
    }

    /**
     * Get Report Tab 1: Biaya Logistik (Konsolidasi per SO / PO)
     */
    public function getLogisticsReport(array $filters): array
    {
        [$startDate, $endDate] = [$filters['start_date'], $filters['end_date']];
        $search = $filters['search'] ?? null;
        $customer = $filters['customer'] ?? null;
        $taskType = $filters['task_type'] ?? null;
        $status = $filters['status'] ?? null;

        // 1. Ambil seluruh referensi SO / PO dari 3 modul dalam periode
        // Expeditions
        $expQuery = Expedition::with(['items', 'vendor'])
            ->whereBetween('shipping_date', [$startDate->toDateString(), $endDate->toDateString()]);
        $expeditions = $expQuery->get();

        // Packaging Jobs
        $packQuery = PackagingJob::with(['items', 'details.material'])
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->orWhereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()]);
            });
        $packJobs = $packQuery->get();

        // Internal Shifts & Tasks
        $shiftQuery = Shift::with(['vehicle', 'driver', 'pickupTasks.items', 'deliveryAssignments.salesOrder.items', 'expenses'])
            ->whereBetween('work_date', [$startDate->toDateString(), $endDate->toDateString()]);
        $shifts = $shiftQuery->get();

        // Hitung alokasi HPP per shift
        $internalTaskHpp = []; // key: ref_no => amount
        $shiftTasksMap = [];

        foreach ($shifts as $shift) {
            $calc = $this->hppService->getSavedOrCalculateProrata($shift);
            $allocations = $calc['allocations'] ?? [];
            
            // Map allocations ke nomor SO / PO
            if (!empty($allocations)) {
                foreach ($allocations as $alloc) {
                    $ref = trim($alloc['reference_number'] ?? '');
                    if ($ref && $ref !== '-') {
                        $internalTaskHpp[$ref] = ($internalTaskHpp[$ref] ?? 0) + ($alloc['hpp_per_baris'] ?? 0);
                    }
                }
            } else {
                // Fallback jika belum terbagi per alokasi: bagi rata ke task shift tsb
                $totalShiftCost = $calc['costs']['total'] ?? 0;
                $tasks = collect()->merge($shift->pickupTasks)->merge($shift->deliveryAssignments);
                $taskCount = $tasks->count();
                if ($taskCount > 0 && $totalShiftCost > 0) {
                    $costPerTask = $totalShiftCost / $taskCount;
                    foreach ($tasks as $t) {
                        $ref = trim($t->so_number ?? $t->po_number ?? ($t->salesOrder ? $t->salesOrder->no_so : '') ?? '');
                        if ($ref && $ref !== '-') {
                            $internalTaskHpp[$ref] = ($internalTaskHpp[$ref] ?? 0) + $costPerTask;
                        }
                    }
                }
            }
        }

        // 2. Kumpulkan SEMUA SO / PO unik
        $soPoData = [];

        // Input dari Ekspedisi
        foreach ($expeditions as $exp) {
            $ref = trim($exp->reference_number ?: '');
            if (!$ref) continue;

            if (!isset($soPoData[$ref])) {
                $soPoData[$ref] = [
                    'ref_no' => $ref,
                    'customer' => $exp->customer_vendor_name ?: '-',
                    'item_value' => 0,
                    'internal_cost' => 0,
                    'expedition_cost' => 0,
                    'packaging_cost' => 0,
                    'task_type' => $exp->task_type ?: 'EKSPEDISI',
                    'status' => $this->normalizeStatus($exp->status),
                    'date' => $exp->shipping_date ? $exp->shipping_date->format('Y-m-d') : null,
                    'details' => [
                        'expeditions' => [],
                        'internal_tasks' => [],
                        'packagings' => []
                    ]
                ];
            }

            // Hitung nilai barang dari item ekspedisi (qty x unit_price)
            $itemTotal = 0;
            if ($exp->items && $exp->items->count() > 0) {
                foreach ($exp->items as $it) {
                    $itemTotal += (float)($it->total_price > 0 ? $it->total_price : (($it->quantity ?? 1) * ($it->unit_price ?? 0)));
                }
            }
            $soPoData[$ref]['item_value'] = max($soPoData[$ref]['item_value'], $itemTotal);
            $soPoData[$ref]['expedition_cost'] += (float)$exp->cost_amount;
            $soPoData[$ref]['details']['expeditions'][] = $exp;
        }

        // Input dari Packaging
        foreach ($packJobs as $job) {
            foreach ($job->items as $item) {
                $ref = trim($item->no_so ?: '');
                if (!$ref) continue;

                if (!isset($soPoData[$ref])) {
                    $soPoData[$ref] = [
                        'ref_no' => $ref,
                        'customer' => $item->customer ?: ($job->customer_name ?: '-'),
                        'item_value' => 0,
                        'internal_cost' => 0,
                        'expedition_cost' => 0,
                        'packaging_cost' => 0,
                        'task_type' => 'DELIVERY',
                        'status' => 'Terkirim',
                        'date' => $job->start_date ? $job->start_date->format('Y-m-d') : null,
                        'details' => [
                            'expeditions' => [],
                            'internal_tasks' => [],
                            'packagings' => []
                        ]
                    ];
                }

                // Biaya Packaging dari job (harga_total atau sum subtotal_price / material + paku + manpower)
                $jobCost = (float)($job->harga_total ?? 0);
                if ($jobCost <= 0) {
                    $jobCost = (float)($job->subtotal_harga_material + $job->subtotal_harga_paku + $job->subtotal_man_power);
                }
                if ($jobCost <= 0 && $job->details) {
                    $jobCost = (float)$job->details->sum('subtotal_price');
                }

                $costPart = $jobCost > 0 ? $jobCost / max(1, $job->items->count()) : 0;
                $soPoData[$ref]['packaging_cost'] += $costPart;
                $soPoData[$ref]['details']['packagings'][] = [
                    'job_number' => $job->packaging_number ?? $job->job_number ?? '-',
                    'type' => $job->type_packaging,
                    'cost' => $costPart
                ];
            }
        }

        // Input dari Internal Tasks
        foreach ($shifts as $shift) {
            $tasks = collect()->merge($shift->pickupTasks)->merge($shift->deliveryAssignments);
            foreach ($tasks as $task) {
                $isDelivery = $task instanceof DeliveryAssignment;
                $ref = $isDelivery 
                    ? trim($task->salesOrder->so_number ?? '') 
                    : trim($task->reference_number ?? '');

                if (!$ref) continue;

                $cust = $isDelivery 
                    ? trim($task->salesOrder->customer_name ?? '') 
                    : trim($task->pickup_name ?? ($task->destination_name ?? ''));
                if (!$cust) $cust = '-';

                $taskTypeLabel = $isDelivery ? 'PO COSTUMER' : ($task->transaction_source === 'purchase' ? 'PO VENDOR' : 'DELIVERY');

                if (!isset($soPoData[$ref])) {
                    $soPoData[$ref] = [
                        'ref_no' => $ref,
                        'customer' => $cust,
                        'item_value' => 0,
                        'internal_cost' => 0,
                        'expedition_cost' => 0,
                        'packaging_cost' => 0,
                        'task_type' => $taskTypeLabel,
                        'status' => $this->normalizeStatus($task->status ?? 'completed'),
                        'date' => $shift->work_date ? $shift->work_date->format('Y-m-d') : null,
                        'details' => [
                            'expeditions' => [],
                            'internal_tasks' => [],
                            'packagings' => []
                        ]
                    ];
                }

                // Ambil nilai barang dari task items jika ada
                if ($isDelivery && $task->salesOrder && $task->salesOrder->total_amount > 0) {
                    $soPoData[$ref]['item_value'] = max($soPoData[$ref]['item_value'], (float)$task->salesOrder->total_amount);
                } elseif (isset($task->line_total) && $task->line_total > 0) {
                    $soPoData[$ref]['item_value'] = max($soPoData[$ref]['item_value'], (float)$task->line_total);
                }

                $soPoData[$ref]['details']['internal_tasks'][] = [
                    'driver' => $shift->driver ? $shift->driver->name : '-',
                    'vehicle' => $shift->vehicle ? $shift->vehicle->plate_number : '-',
                    'date' => $shift->work_date ? $shift->work_date->format('d/m/Y') : '-',
                    'task_type' => $taskTypeLabel,
                    'status' => $task->status ?? '-'
                ];
            }
        }

        // Terapkan internal_cost yang sudah dihitung per ref_no
        foreach ($internalTaskHpp as $ref => $cost) {
            if (isset($soPoData[$ref])) {
                $soPoData[$ref]['internal_cost'] += (float)$cost;
            } else {
                $soPoData[$ref] = [
                    'ref_no' => $ref,
                    'customer' => '-',
                    'item_value' => 0,
                    'internal_cost' => (float)$cost,
                    'expedition_cost' => 0,
                    'packaging_cost' => 0,
                    'task_type' => 'DELIVERY',
                    'status' => 'Terkirim',
                    'date' => null,
                    'details' => [
                        'expeditions' => [],
                        'internal_tasks' => [],
                        'packagings' => []
                    ]
                ];
            }
        }

        // Fallback untuk Nilai Barang jika 0 tapi terdapat relasi SalesOrder / Akurasi di database
        $allRefs = array_keys($soPoData);
        if (!empty($allRefs)) {
            // 1. Cek tabel sales_orders (kolom: so_number)
            try {
                $salesOrders = SalesOrder::whereIn('so_number', $allRefs)->get()->keyBy('so_number');
                foreach ($salesOrders as $soNum => $so) {
                    if (isset($soPoData[$soNum])) {
                        if ($soPoData[$soNum]['item_value'] <= 0) {
                            $soPoData[$soNum]['item_value'] = (float)$so->total_amount;
                        }
                        if ($soPoData[$soNum]['customer'] === '-' && $so->customer_name) {
                            $soPoData[$soNum]['customer'] = $so->customer_name;
                        }
                    }
                }
            } catch (\Throwable $e) {}

            // 2. Cek tabel sales_order_akurasi (kolom: no_so)
            try {
                $soAkurasi = DB::table('sales_order_akurasi')
                    ->where(function($q) use ($allRefs) {
                        foreach ($allRefs as $ref) {
                            $q->orWhere('no_so', 'ilike', trim($ref));
                        }
                    })
                    ->select('no_so', 'nama_pelanggan', DB::raw('SUM(CASE WHEN amount > 0 THEN amount WHEN subtotal > 0 THEN subtotal ELSE (qty * unit_price) END) as total_amount'))
                    ->groupBy('no_so', 'nama_pelanggan')
                    ->get();

                foreach ($soAkurasi as $soa) {
                    $k = strtoupper(trim($soa->no_so));
                    foreach ($soPoData as $refKey => &$valRef) {
                        if (strtoupper(trim($refKey)) === $k) {
                            if ($valRef['item_value'] <= 0 && $soa->total_amount > 0) {
                                $valRef['item_value'] = (float)$soa->total_amount;
                            }
                            if ($valRef['customer'] === '-' && $soa->nama_pelanggan) {
                                $valRef['customer'] = $soa->nama_pelanggan;
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {}

            // 3. Cek tabel pembelian_order_akurasi (kolom: no_pembelian)
            try {
                $poAkurasi = DB::table('pembelian_order_akurasi')
                    ->where(function($q) use ($allRefs) {
                        foreach ($allRefs as $ref) {
                            $q->orWhere('no_pembelian', 'ilike', trim($ref));
                        }
                    })
                    ->select('no_pembelian', 'nama_pemasok', DB::raw('SUM(CASE WHEN nilai_po > 0 THEN nilai_po ELSE (qty * harga_satuan) END) as total_po'))
                    ->groupBy('no_pembelian', 'nama_pemasok')
                    ->get();

                foreach ($poAkurasi as $poa) {
                    $k = strtoupper(trim($poa->no_pembelian));
                    foreach ($soPoData as $refKey => &$valRef) {
                        if (strtoupper(trim($refKey)) === $k) {
                            if ($valRef['item_value'] <= 0 && $poa->total_po > 0) {
                                $valRef['item_value'] = (float)$poa->total_po;
                            }
                            if ($valRef['customer'] === '-' && $poa->nama_pemasok) {
                                $valRef['customer'] = $poa->nama_pemasok;
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {}

            // 4. Fallback realtime via AkurasiService jika belum ada di database lokal
            foreach ($soPoData as $refKey => &$valRef) {
                if ($valRef['item_value'] <= 0) {
                    $cleanRef = strtoupper(trim($refKey));
                    try {
                        // Coba Sales Order terlebih dahulu (karena nomor AI-PP di sistem ini bisa merupakan Sales Order)
                        $soRes = null;
                        try {
                            $soRes = $this->akurasiService->detailSo($cleanRef);
                        } catch (\Throwable $e) {}

                        if (!empty($soRes)) {
                            $v = (float)($soRes['total_amount'] ?? 0);
                            if ($v <= 0 && !empty($soRes['items'])) {
                                foreach ($soRes['items'] as $it) {
                                    $v += (float)($it['amount'] ?? $it['subtotal'] ?? (($it['qty'] ?? 1) * ($it['unit_price'] ?? $it['harga_satuan'] ?? 0)));
                                }
                            }
                            if ($v > 0) $valRef['item_value'] = $v;
                            if ($valRef['customer'] === '-' && !empty($soRes['pelanggan'])) {
                                $valRef['customer'] = $soRes['pelanggan'];
                            }
                        }

                        // Jika SO tidak dapat atau nilainya 0, coba sebagai Purchase Order
                        if ($valRef['item_value'] <= 0) {
                            $poRes = null;
                            try {
                                $poRes = $this->akurasiService->detailPo($cleanRef);
                            } catch (\Throwable $e) {}

                            if (!empty($poRes)) {
                                $v = (float)($poRes['total_amount'] ?? 0);
                                if ($v <= 0 && !empty($poRes['items'])) {
                                    foreach ($poRes['items'] as $it) {
                                        $v += (float)($it['amount'] ?? $it['subtotal'] ?? (($it['qty'] ?? 1) * ($it['harga_satuan'] ?? $it['unit_price'] ?? 0)));
                                    }
                                }
                                if ($v > 0) $valRef['item_value'] = $v;
                                if ($valRef['customer'] === '-' && !empty($poRes['pemasok'])) {
                                    $valRef['customer'] = $poRes['pemasok'];
                                }
                            }
                        }
                    } catch (\Throwable $e) {}
                }
            }
        }

        // 3. Filter collection
        $rows = collect(array_values($soPoData))->map(function($row) {
            $totalCost = $row['internal_cost'] + $row['expedition_cost'] + $row['packaging_cost'];
            $pct = $row['item_value'] > 0 ? ($totalCost / $row['item_value']) * 100 : 0;
            $row['total_logistics_cost'] = $totalCost;
            $row['cost_percentage'] = round($pct, 2);
            return $row;
        });

        if ($search) {
            $s = strtolower($search);
            $rows = $rows->filter(function($r) use ($s) {
                return str_contains(strtolower($r['ref_no']), $s) 
                    || str_contains(strtolower($r['customer']), $s);
            });
        }

        if ($customer && $customer !== 'ALL') {
            $rows = $rows->filter(function($r) use ($customer) {
                return $r['customer'] === $customer;
            });
        }

        if ($taskType && $taskType !== 'ALL') {
            $rows = $rows->filter(function($r) use ($taskType) {
                return strtoupper($r['task_type']) === strtoupper($taskType);
            });
        }

        if ($status && $status !== 'ALL') {
            $rows = $rows->filter(function($r) use ($status) {
                return strtolower($r['status']) === strtolower($status);
            });
        }

        // Urutkan default berdasarkan ref_no / total cost
        $rows = $rows->sortByDesc('total_logistics_cost')->values();

        // Summary Cards
        $totalSoPo = $rows->count();
        $totalItemValue = $rows->sum('item_value');
        $totalInternalCost = $rows->sum('internal_cost');
        $totalExpeditionCost = $rows->sum('expedition_cost');
        $totalPackagingCost = $rows->sum('packaging_cost');
        $totalLogisticsCost = $totalInternalCost + $totalExpeditionCost + $totalPackagingCost;
        $overallPercentage = $totalItemValue > 0 ? ($totalLogisticsCost / $totalItemValue) * 100 : 0;

        // Komposisi Biaya (Donut)
        $composition = [
            'internal' => [
                'label' => 'Pengiriman Internal (HPP Ritase)',
                'amount' => $totalInternalCost,
                'pct' => $totalLogisticsCost > 0 ? round(($totalInternalCost / $totalLogisticsCost) * 100, 1) : 0,
                'color' => '#3b82f6'
            ],
            'expedition' => [
                'label' => 'Ekspedisi (Vendor 3PL)',
                'amount' => $totalExpeditionCost,
                'pct' => $totalLogisticsCost > 0 ? round(($totalExpeditionCost / $totalLogisticsCost) * 100, 1) : 0,
                'color' => '#f59e0b'
            ],
            'packaging' => [
                'label' => 'Packaging',
                'amount' => $totalPackagingCost,
                'pct' => $totalLogisticsCost > 0 ? round(($totalPackagingCost / $totalLogisticsCost) * 100, 1) : 0,
                'color' => '#10b981'
            ]
        ];

        // Tren Biaya Per Bulan (6 bulan terakhir)
        $trendMonths = [];
        $trendValues = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $monthLabel = $m->translatedFormat('M');
            $trendMonths[] = $monthLabel;
            
            // Hitung estimasi biaya logistik per bulan
            $mStart = $m->copy()->startOfMonth();
            $mEnd = $m->copy()->endOfMonth();
            $expMonth = Expedition::whereBetween('shipping_date', [$mStart->toDateString(), $mEnd->toDateString()])->sum('cost_amount');
            $packMonth = PackagingJob::whereBetween('created_at', [$mStart, $mEnd])->sum('harga_total');
            $shiftMonthIds = Shift::whereBetween('work_date', [$mStart->toDateString(), $mEnd->toDateString()])->pluck('id');
            $hppMonth = HppRitase::whereIn('shift_id', $shiftMonthIds)->sum('total_cost');

            $trendValues[] = (float)($expMonth + $packMonth + $hppMonth);
        }

        return [
            'rows' => $rows,
            'summary' => [
                'total_sopo' => $totalSoPo,
                'total_item_value' => $totalItemValue,
                'total_logistics_cost' => $totalLogisticsCost,
                'overall_percentage' => round($overallPercentage, 2),
            ],
            'composition' => $composition,
            'trend' => [
                'labels' => $trendMonths,
                'values' => $trendValues,
            ]
        ];
    }

    /**
     * Get Report Tab 2: Biaya Ekspedisi (Vendor 3PL)
     */
    public function getExpeditionReport(array $filters): array
    {
        [$startDate, $endDate] = [$filters['start_date'], $filters['end_date']];
        $search = $filters['search'] ?? null;
        $customer = $filters['customer'] ?? null;
        $taskType = $filters['task_type'] ?? null;
        $status = $filters['status'] ?? null;

        $query = Expedition::with(['vendor', 'items'])
            ->whereBetween('shipping_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('expedition_number', 'ilike', "%{$search}%")
                  ->orWhere('tracking_number', 'ilike', "%{$search}%")
                  ->orWhere('vendor_name', 'ilike', "%{$search}%")
                  ->orWhere('reference_number', 'ilike', "%{$search}%")
                  ->orWhere('customer_vendor_name', 'ilike', "%{$search}%")
                  ->orWhere('do_easy', 'ilike', "%{$search}%");
            });
        }

        if ($customer && $customer !== 'ALL') {
            $query->where('customer_vendor_name', $customer);
        }

        if ($taskType && $taskType !== 'ALL') {
            $query->where('task_type', strtolower($taskType));
        }

        if ($status && $status !== 'ALL') {
            $st = strtolower($status);
            $query->where(function($q) use ($st) {
                if ($st === 'terkirim') {
                    $q->whereIn('status', ['delivered', 'completed', 'terkirim', 'selesai']);
                } elseif ($st === 'dalam pengiriman') {
                    $q->whereIn('status', ['on_delivery', 'active', 'in_progress', 'berjalan', 'dalam pengiriman']);
                } else {
                    $q->where('status', 'ilike', $st);
                }
            });
        }

        $items = $query->orderBy('shipping_date', 'desc')->get();

        // Kumpulkan referensi untuk fallback jika total_price item ekspedisi belum tersimpan
        $refList = $items->pluck('reference_number')->filter()->unique()->values()->all();
        $refValues = [];

        if (!empty($refList)) {
            // Cek sales_order_akurasi (amount / subtotal / qty x unit_price)
            try {
                $soRows = DB::table('sales_order_akurasi')
                    ->where(function($q) use ($refList) {
                        foreach ($refList as $ref) {
                            $q->orWhere('no_so', 'ilike', trim($ref));
                        }
                    })
                    ->select('no_so', DB::raw('SUM(CASE WHEN amount > 0 THEN amount WHEN subtotal > 0 THEN subtotal ELSE (qty * unit_price) END) as total_val'))
                    ->groupBy('no_so')
                    ->get();
                foreach ($soRows as $r) {
                    $refValues[strtoupper(trim($r->no_so))] = max($refValues[strtoupper(trim($r->no_so))] ?? 0, (float)$r->total_val);
                }
            } catch (\Throwable $e) {}

            // Cek pembelian_order_akurasi (nilai_po / qty x harga_satuan)
            try {
                $poRows = DB::table('pembelian_order_akurasi')
                    ->where(function($q) use ($refList) {
                        foreach ($refList as $ref) {
                            $q->orWhere('no_pembelian', 'ilike', trim($ref));
                        }
                    })
                    ->select('no_pembelian', DB::raw('SUM(CASE WHEN nilai_po > 0 THEN nilai_po ELSE (qty * harga_satuan) END) as total_val'))
                    ->groupBy('no_pembelian')
                    ->get();
                foreach ($poRows as $r) {
                    $refValues[strtoupper(trim($r->no_pembelian))] = max($refValues[strtoupper(trim($r->no_pembelian))] ?? 0, (float)$r->total_val);
                }
            } catch (\Throwable $e) {}

            // Cek sales_orders
            try {
                $soOrders = SalesOrder::whereIn('so_number', $refList)->get();
                foreach ($soOrders as $so) {
                    $refValues[strtoupper(trim($so->so_number))] = max($refValues[strtoupper(trim($so->so_number))] ?? 0, (float)$so->total_amount);
                }
            } catch (\Throwable $e) {}

            // Jika masih belum ada di tabel lokal, panggil AkurasiService secara realtime
            foreach ($refList as $singleRef) {
                $cleanRef = strtoupper(trim($singleRef));
                if (empty($refValues[$cleanRef])) {
                    try {
                        // Coba Sales Order terlebih dahulu (karena nomor AI-PP di sistem ini bisa merupakan Sales Order)
                        $soDetail = null;
                        try {
                            $soDetail = $this->akurasiService->detailSo($cleanRef);
                        } catch (\Throwable $e) {}

                        if (!empty($soDetail)) {
                            $soVal = (float)($soDetail['total_amount'] ?? 0);
                            if ($soVal <= 0 && !empty($soDetail['items'])) {
                                foreach ($soDetail['items'] as $it) {
                                    $soVal += (float)($it['amount'] ?? $it['subtotal'] ?? (($it['qty'] ?? 1) * ($it['unit_price'] ?? $it['harga_satuan'] ?? 0)));
                                }
                            }
                            if ($soVal > 0) $refValues[$cleanRef] = $soVal;
                        }

                        // Jika SO tidak dapat atau nilainya 0, coba sebagai Purchase Order
                        if (empty($refValues[$cleanRef])) {
                            $poDetail = null;
                            try {
                                $poDetail = $this->akurasiService->detailPo($cleanRef);
                            } catch (\Throwable $e) {}

                            if (!empty($poDetail)) {
                                $poVal = (float)($poDetail['total_amount'] ?? 0);
                                if ($poVal <= 0 && !empty($poDetail['items'])) {
                                    foreach ($poDetail['items'] as $it) {
                                        $poVal += (float)($it['amount'] ?? $it['subtotal'] ?? (($it['qty'] ?? 1) * ($it['harga_satuan'] ?? $it['unit_price'] ?? 0)));
                                    }
                                }
                                if ($poVal > 0) $refValues[$cleanRef] = $poVal;
                            }
                        }
                    } catch (\Throwable $e) {}
                }
            }
        }

        $rows = $items->map(function($exp) use ($refValues) {
            $ref = trim($exp->reference_number ?: '');
            $upperRef = strtoupper($ref);
            $itemValue = 0;
            if ($exp->items && $exp->items->count() > 0) {
                foreach ($exp->items as $it) {
                    $itemValue += (float)($it->total_price > 0 ? $it->total_price : (($it->quantity ?? 1) * ($it->unit_price ?? 0)));
                }
            }
            if ($itemValue <= 0 && isset($refValues[$upperRef])) {
                $itemValue = (float)$refValues[$upperRef];
            }

            return [
                'id' => $exp->id,
                'ref_no' => $ref ?: '-',
                'customer' => $exp->customer_vendor_name ?: '-',
                'do_easy' => $exp->do_easy ?: '-',
                'vendor_name' => $exp->vendor_name ?: ($exp->vendor ? $exp->vendor->vendor_name : '-'),
                'tracking_number' => $exp->tracking_number ?: '-',
                'shipping_date' => $exp->shipping_date ? $exp->shipping_date->format('d/m/Y') : '-',
                'estimated_arrival_date' => $exp->estimated_arrival_date ? $exp->estimated_arrival_date->format('d/m/Y') : '-',
                'actual_arrival_date' => $exp->actual_arrival_date ? $exp->actual_arrival_date->format('d/m/Y') : '-',
                'item_value' => (float)$itemValue,
                'cost_amount' => (float)$exp->cost_amount,
                'status' => $this->normalizeStatus($exp->status),
                'status_raw' => $exp->status,
                'task_type' => $exp->task_type ?: '-',
            ];
        });

        // Summary
        $totalDeliveries = $rows->count();
        $totalVendors = $items->pluck('vendor_name')->unique()->filter()->count();
        $totalItemValue = $rows->sum('item_value');
        $totalExpeditionCost = $rows->sum('cost_amount');

        return [
            'rows' => $rows,
            'summary' => [
                'total_deliveries' => $totalDeliveries,
                'total_vendors' => $totalVendors,
                'total_item_value' => $totalItemValue,
                'total_expedition_cost' => $totalExpeditionCost,
            ]
        ];
    }

    /**
     * Get Report Tab 3: Biaya Pengiriman Internal (HPP Ritase & Driver)
     */
    public function getInternalShippingReport(array $filters): array
    {
        [$startDate, $endDate] = [$filters['start_date'], $filters['end_date']];
        $search = $filters['search'] ?? null;
        $customer = $filters['customer'] ?? null;
        $taskType = $filters['task_type'] ?? null;
        $status = $filters['status'] ?? null;

        $query = Shift::with(['vehicle', 'driver', 'pickupTasks.items', 'deliveryAssignments.salesOrder.items', 'expenses'])
            ->whereBetween('work_date', [$startDate->toDateString(), $endDate->toDateString()]);

        $shifts = $query->orderBy('work_date', 'desc')->get();

        $rows = [];
        $uniqueVehicles = [];
        $uniqueDrivers = [];

        foreach ($shifts as $shift) {
            $calc = $this->hppService->getSavedOrCalculateProrata($shift);
            $costs = $calc['costs'] ?? [
                'fuel' => 0, 'manpower' => 0, 'toll' => 0, 'parking' => 0, 'other' => 0, 'depreciation' => 0, 'total' => 0
            ];

            if ($shift->vehicle_id) $uniqueVehicles[$shift->vehicle_id] = true;
            if ($shift->driver_id) $uniqueDrivers[$shift->driver_id] = true;

            $distance = ($shift->end_odometer !== null && $shift->start_odometer !== null) 
                ? max(0, $shift->end_odometer - $shift->start_odometer) 
                : 0;

            $tasks = collect()->merge($shift->pickupTasks)->merge($shift->deliveryAssignments);

            // Jika ada tasks dalam shift, tampilkan baris per task
            if ($tasks->count() > 0) {
                $taskCount = $tasks->count();
                foreach ($tasks as $task) {
                    $isDelivery = $task instanceof DeliveryAssignment;
                    $ref = '';
                    $cust = '';

                    if ($isDelivery) {
                        $ref = trim($task->salesOrder->so_number ?? '');
                        $cust = trim($task->salesOrder->customer_name ?? '');
                    } else {
                        // PickupTask
                        $ref = trim($task->reference_number ?? '');
                        $cust = trim($task->pickup_name ?? ($task->destination_name ?? ''));
                    }

                    if (!$ref) $ref = '-';
                    if (!$cust) $cust = '-';

                    // Prorata biaya shift per task
                    $ratio = 1 / $taskCount;

                    // Nilai barang task dari line_total, items (qty x unit_price), atau SO/PO Akurasi
                    $taskValue = 0;
                    if ($isDelivery && $task->salesOrder) {
                        $taskValue = (float)$task->salesOrder->total_amount;
                        if ($taskValue <= 0 && $task->salesOrder->items) {
                            $taskValue = (float)$task->salesOrder->items->sum(function($it) {
                                return $it->line_total > 0 ? $it->line_total : (($it->quantity ?? 1) * ($it->unit_price ?? 0));
                            });
                        }
                    } else {
                        if (isset($task->line_total) && $task->line_total > 0) {
                            $taskValue = (float)$task->line_total;
                        } elseif (isset($task->items) && $task->items->count() > 0) {
                            $taskValue = (float)$task->items->sum(function($it) {
                                return $it->line_total > 0 ? $it->line_total : (($it->quantity ?? 1) * ($it->unit_price ?? 0));
                            });
                        }
                    }

                    // Fallback nilai barang jika masih 0, ambil dari SO/PO Akurasi
                    if ($taskValue <= 0 && $ref !== '-') {
                        $upperRef = strtoupper($ref);
                        try {
                            $val = DB::table('sales_order_akurasi')
                                ->where('no_so', 'ilike', $upperRef)
                                ->sum(DB::raw('CASE WHEN amount > 0 THEN amount WHEN subtotal > 0 THEN subtotal ELSE (qty * unit_price) END'));
                            if ($val > 0) $taskValue = (float)$val;
                        } catch (\Throwable $e) {}

                        if ($taskValue <= 0) {
                            try {
                                $valPo = DB::table('pembelian_order_akurasi')
                                    ->where('no_pembelian', 'ilike', $upperRef)
                                    ->sum(DB::raw('CASE WHEN nilai_po > 0 THEN nilai_po ELSE (qty * harga_satuan) END'));
                                if ($valPo > 0) $taskValue = (float)$valPo;
                            } catch (\Throwable $e) {}
                        }
                    }

                    $rows[] = [
                        'date' => $shift->work_date ? $shift->work_date->format('d/m/Y') : '-',
                        'raw_date' => $shift->work_date ? $shift->work_date->toDateString() : '',
                        'ref_no' => $ref,
                        'customer' => $cust,
                        'do_easy' => $task->do_easy ?? '-',
                        'item_value' => $taskValue,
                        'vehicle' => $shift->vehicle ? $shift->vehicle->plate_number : '-',
                        'driver' => $shift->driver ? $shift->driver->name : '-',
                        'task_type' => $isDelivery ? 'PO COSTUMER' : ($task->transaction_source === 'purchase' ? 'PO VENDOR' : 'PICKUP'),
                        'distance' => round($distance * $ratio, 1),
                        'fuel' => (float)$costs['fuel'] * $ratio,
                        'manpower' => (float)$costs['manpower'] * $ratio,
                        'toll' => (float)$costs['toll'] * $ratio,
                        'parking' => (float)$costs['parking'] * $ratio,
                        'other' => (float)$costs['other'] * $ratio,
                        'depreciation' => (float)$costs['depreciation'] * $ratio,
                        'total_cost' => (float)$costs['total'] * $ratio,
                        'status' => $this->normalizeStatus($task->status ?? ($shift->check_out_at ? 'completed' : 'active')),
                    ];
                }
            }
            // Catatan: Shift tanpa task / tugas driver (misal operasional internal / standby tanpa SO/PO) 
            // sengaja tidak dimasukkan ke Report Biaya Pengiriman agar hanya fokus pada tugas pengiriman riil driver.
        }

        $collection = collect($rows);

        if ($search) {
            $s = strtolower($search);
            $collection = $collection->filter(function($r) use ($s) {
                return str_contains(strtolower($r['ref_no']), $s)
                    || str_contains(strtolower($r['customer']), $s)
                    || str_contains(strtolower($r['do_easy']), $s)
                    || str_contains(strtolower($r['driver']), $s)
                    || str_contains(strtolower($r['vehicle']), $s);
            });
        }

        if ($customer && $customer !== 'ALL') {
            $collection = $collection->filter(function($r) use ($customer) {
                return $r['customer'] === $customer;
            });
        }

        if ($taskType && $taskType !== 'ALL') {
            $collection = $collection->filter(function($r) use ($taskType) {
                return strtoupper($r['task_type']) === strtoupper($taskType);
            });
        }

        if ($status && $status !== 'ALL') {
            $collection = $collection->filter(function($r) use ($status) {
                return strtolower($r['status']) === strtolower($status);
            });
        }

        $finalRows = $collection->values();

        return [
            'rows' => $finalRows,
            'summary' => [
                'total_trips' => $finalRows->count(),
                'total_vehicles' => count($uniqueVehicles),
                'total_drivers' => count($uniqueDrivers),
                'total_internal_cost' => $finalRows->sum('total_cost'),
                'breakdown' => [
                    'fuel' => $finalRows->sum('fuel'),
                    'manpower' => $finalRows->sum('manpower'),
                    'toll' => $finalRows->sum('toll'),
                    'parking' => $finalRows->sum('parking'),
                    'other' => $finalRows->sum('other'),
                    'depreciation' => $finalRows->sum('depreciation'),
                ]
            ]
        ];
    }

    /**
     * Normalisasi status ke label seragam
     */
    protected function normalizeStatus(?string $status): string
    {
        $s = strtolower(trim($status ?? ''));
        switch ($s) {
            case 'delivered':
            case 'completed':
            case 'selesai':
                return 'Terkirim';
            case 'on_delivery':
            case 'in_progress':
            case 'active':
            case 'berjalan':
                return 'Dalam Pengiriman';
            case 'pending':
            case 'waiting':
                return 'Pending';
            case 'draft':
                return 'Draft';
            case 'returned':
                return 'Retur';
            case 'cancelled':
                return 'Dibatalkan';
            default:
                return ucfirst($s ?: 'Draft');
        }
    }
}

