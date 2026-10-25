<?php

namespace App\Http\Controllers;

use App\Exports\DriverReportExport;
use App\Exports\ExpenseExport;
use App\Exports\HppRitaseExport;
use App\Exports\ListTugasExport;
use App\Exports\SalesOrderExport;
use App\Models\DeliveryAssignment;
use App\Models\Expense;
use App\Models\PickupTask;
use App\Models\SalesOrder;
use App\Models\Shift;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\HppCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected $hppService;

    public function __construct(HppCalculationService $hppService)
    {
        $this->hppService = $hppService;
    }

    /**
     * Parse date range from request or defaults
     */
    protected function getDateRange(Request $request): array
    {
        $preset = $request->input('preset', 'this_month');
        $today = Carbon::today();

        switch ($preset) {
            case 'today':
                $startDate = $today->copy()->startOfDay();
                $endDate = $today->copy()->endOfDay();
                break;
            case 'this_week':
                $startDate = $today->copy()->startOfWeek();
                $endDate = $today->copy()->endOfWeek();
                break;
            case 'last_month':
                $startDate = $today->copy()->subMonth()->startOfMonth();
                $endDate = $today->copy()->subMonth()->endOfMonth();
                break;
            case 'all':
                $startDate = Carbon::parse('2020-01-01')->startOfDay();
                $endDate = $today->copy()->endOfDay();
                break;
            case 'custom':
                $startDate = $request->filled('start_date') 
                    ? Carbon::parse($request->input('start_date'))->startOfDay() 
                    : $today->copy()->startOfMonth();
                $endDate = $request->filled('end_date') 
                    ? Carbon::parse($request->input('end_date'))->endOfDay() 
                    : $today->copy()->endOfDay();
                break;
            case 'this_month':
            default:
                $preset = 'this_month';
                $startDate = $today->copy()->startOfMonth();
                $endDate = $today->copy()->endOfDay();
                break;
        }

        return [$startDate, $endDate, $preset];
    }

    /**
     * Laporan Utama: Laporan Driver & Utilisasi Kendaraan
     */
    public function driverReport(Request $request)
    {
        if (auth()->check() && !auth()->user()->hasPermission('View Laporan')) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat Laporan.');
        }

        [$startDate, $endDate, $preset] = $this->getDateRange($request);
        
        $driverIds = $request->input('driver_id', []);
        if (!is_array($driverIds)) {
            $driverIds = $driverIds ? [$driverIds] : [];
        }
        $driverIds = array_values(array_filter($driverIds));

        $vehicleIds = $request->input('vehicle_id', []);
        if (!is_array($vehicleIds)) {
            $vehicleIds = $vehicleIds ? [$vehicleIds] : [];
        }
        $vehicleIds = array_values(array_filter($vehicleIds));

        $status = $request->input('status');

        // 1. QUERY SHIFTS / PERJALANAN
        $query = Shift::with([
            'driver',
            'vehicle',
            'attachments',
            'expenses',
            'pickupTasks.attachments',
            'deliveryAssignments.salesOrder',
            'deliveryAssignments.attachments'
        ])
            ->whereBetween('work_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if (!empty($driverIds)) {
            $query->whereIn('driver_id', $driverIds);
        }
        if (!empty($vehicleIds)) {
            $query->whereIn('vehicle_id', $vehicleIds);
        }
        if ($status === 'completed') {
            $query->whereNotNull('check_out_at');
        } elseif ($status === 'active') {
            $query->whereNull('check_out_at');
        }

        $shifts = $query->orderBy('work_date', 'desc')->orderBy('check_in_at', 'desc')->get();

        // 2. MASTER DATA
        $allVehicles = Vehicle::where('active', true)->orderBy('name')->get();
        $drivers = User::where('role', 'driver')
            ->orWhereHas('roleRelation', function ($q) {
                $q->whereRaw('LOWER(name) = ?', ['driver']);
            })
            ->orderBy('name')
            ->get();

        $targetVehicles = !empty($vehicleIds)
            ? $allVehicles->whereIn('id', $vehicleIds)
            : $allVehicles;

        $targetDrivers = !empty($driverIds)
            ? $drivers->whereIn('id', $driverIds)
            : $drivers;

        // 3. KALKULASI STATISTIK, TABEL DATA & METRIK GRAFIK
        $tableData = [];
        $totalKmAll = 0;
        $totalDurasiMenitAll = 0;
        $totalTasksAll = 0;

        $kmPerVehicleMap = [];
        $opsPerVehicleMap = [];
        $kmPerDriverMap = [];
        $tasksPerDriverMap = [];
        $usedVehicleIds = [];

        // Inisialisasi Kendaraan (sesuai filter)
        foreach ($targetVehicles as $v) {
            $vLabel = $v->plate_number ? $v->plate_number : $v->name;
            $kmPerVehicleMap[$vLabel] = 0;
            $opsPerVehicleMap[$vLabel] = [
                'operasional' => 0,
                'standby' => 0,
            ];
        }

        // Inisialisasi Driver (sesuai filter)
        foreach ($targetDrivers as $d) {
            $dName = $d->full_name ?? $d->name ?? ('Driver #' . substr($d->id, 0, 4));
            $kmPerDriverMap[$dName] = 0;
            $tasksPerDriverMap[$dName] = 0;
        }

        foreach ($shifts as $shift) {
            $odoStart = (int) ($shift->start_odometer ?? 0);
            $odoEnd = (int) ($shift->end_odometer ?? 0);
            $tripKm = ($odoEnd && $odoStart) ? max(0, $odoEnd - $odoStart) : 0;
            $totalKmAll += $tripKm;

            $driverName = $shift->driver->full_name ?? $shift->driver->name ?? 'Belum Ditugaskan';
            $vehicleLabel = $shift->vehicle ? $shift->vehicle->plate_number : 'Lainnya';

            // Durasi
            $durasiMenit = 0;
            $durasiFormatted = '-';
            if ($shift->check_in_at && $shift->check_out_at) {
                $durasiMenit = $shift->check_in_at->diffInMinutes($shift->check_out_at);
                $totalDurasiMenitAll += $durasiMenit;
                $diff = $shift->check_in_at->diff($shift->check_out_at);
                $durasiFormatted = sprintf('%d Jam %d Menit', ($diff->days * 24) + $diff->h, $diff->i);
            } elseif ($shift->check_in_at) {
                $durasiFormatted = 'Sedang Berjalan';
            }

            // Tugas
            $taskList = [];
            foreach ($shift->pickupTasks as $p) {
                $taskList[] = [
                    'type' => 'Pickup',
                    'ref' => $p->reference_number,
                    'status' => $p->status,
                ];
            }
            foreach ($shift->deliveryAssignments as $d) {
                $taskList[] = [
                    'type' => 'Delivery',
                    'ref' => $d->salesOrder->so_number ?? '-',
                    'status' => $d->status,
                ];
            }
            $taskCount = count($taskList);
            $totalTasksAll += $taskCount;

            // Tracking used vehicle
            if ($shift->vehicle_id) {
                $usedVehicleIds[$shift->vehicle_id] = true;
            }

            // Akumulasi metric per kendaraan (hanya jika sesuai filter)
            if (isset($kmPerVehicleMap[$vehicleLabel])) {
                $kmPerVehicleMap[$vehicleLabel] += $tripKm;
                $opsPerVehicleMap[$vehicleLabel]['operasional'] += 1;
            } elseif (empty($vehicleIds)) {
                $kmPerVehicleMap[$vehicleLabel] = $tripKm;
                $opsPerVehicleMap[$vehicleLabel] = ['operasional' => 1, 'standby' => 0];
            }

            // Akumulasi metric per driver (hanya jika sesuai filter)
            if (isset($kmPerDriverMap[$driverName])) {
                $kmPerDriverMap[$driverName] += $tripKm;
                $tasksPerDriverMap[$driverName] += $taskCount;
            } elseif (empty($driverIds)) {
                $kmPerDriverMap[$driverName] = $tripKm;
                $tasksPerDriverMap[$driverName] = $taskCount;
            }

            // Kumpulkan file upload (Shift vehicle check-in/out, Tugas attachments, Bon pengeluaran)
            $uploadedFiles = [];

            // 1. Lampiran Foto Shift (Check-in/Check-out armada & odometer)
            foreach ($shift->attachments as $att) {
                if ($att->file_path) {
                    $uploadedFiles[] = [
                        'source' => 'Shift Kendaraan',
                        'category' => $att->category ?? $att->document_type ?? 'Kondisi Armada',
                        'title' => $att->category ?? 'Kondisi Kendaraan',
                        'file_name' => $att->file_name ?? basename($att->file_path),
                        'url' => $att->file_path,
                        'notes' => $att->notes ?? null,
                        'created_at' => $att->created_at ? $att->created_at->format('d M Y, H:i') : '-',
                    ];
                }
            }

            // 2. Lampiran Foto Tugas Pickup
            foreach ($shift->pickupTasks as $p) {
                foreach ($p->attachments as $pAtt) {
                    if ($pAtt->file_path) {
                        $uploadedFiles[] = [
                            'source' => 'Pickup Task (' . ($p->reference_number ?? '-') . ')',
                            'category' => $pAtt->category ?? $pAtt->document_type ?? 'Bukti Pickup',
                            'title' => 'Pickup: ' . ($p->reference_number ?? '-'),
                            'file_name' => $pAtt->file_name ?? basename($pAtt->file_path),
                            'url' => $pAtt->file_path,
                            'notes' => $pAtt->notes ?? null,
                            'created_at' => $pAtt->created_at ? $pAtt->created_at->format('d M Y, H:i') : '-',
                        ];
                    }
                }
            }

            // 3. Lampiran Foto Tugas Delivery
            foreach ($shift->deliveryAssignments as $d) {
                $soNumber = $d->salesOrder->so_number ?? '-';
                foreach ($d->attachments as $dAtt) {
                    if ($dAtt->file_path) {
                        $uploadedFiles[] = [
                            'source' => 'Delivery SO (' . $soNumber . ')',
                            'category' => $dAtt->category ?? $dAtt->document_type ?? 'Bukti Delivery / BAST',
                            'title' => 'Delivery SO: ' . $soNumber,
                            'file_name' => $dAtt->file_name ?? basename($dAtt->file_path),
                            'url' => $dAtt->file_path,
                            'notes' => $dAtt->notes ?? null,
                            'created_at' => $dAtt->created_at ? $dAtt->created_at->format('d M Y, H:i') : '-',
                        ];
                    }
                }
            }

            // 4. Lampiran Struk Bon Pengeluaran (BBM, Tol, Parkir, dll)
            foreach ($shift->expenses as $exp) {
                if ($exp->receipt_url) {
                    $uploadedFiles[] = [
                        'source' => 'Pengeluaran (' . ucfirst($exp->category ?? 'Bon') . ')',
                        'category' => 'Struk Bon ' . ucfirst($exp->category ?? 'Operasional'),
                        'title' => 'Bon Rp ' . number_format($exp->amount ?? 0, 0, ',', '.') . ' - ' . ($exp->description ?? ucfirst($exp->category ?? '')),
                        'file_name' => basename($exp->receipt_url),
                        'url' => $exp->receipt_url,
                        'notes' => $exp->description ?? ('Nominal: Rp ' . number_format($exp->amount ?? 0, 0, ',', '.')),
                        'created_at' => $exp->occurred_at ? $exp->occurred_at->format('d M Y, H:i') : ($exp->created_at ? $exp->created_at->format('d M Y, H:i') : '-'),
                    ];
                }
            }

            $tableData[] = [
                'id' => $shift->id,
                'driver_name' => $driverName,
                'vehicle_name' => $shift->vehicle->name ?? '-',
                'vehicle_plate' => $shift->vehicle->plate_number ?? '-',
                'odo_start' => $odoStart,
                'odo_end' => $odoEnd,
                'total_odo' => $tripKm,
                'start_time' => $shift->check_in_at ? $shift->check_in_at->format('H:i') : '-',
                'start_date' => $shift->check_in_at ? $shift->check_in_at->format('d/m/Y') : '-',
                'end_time' => $shift->check_out_at ? $shift->check_out_at->format('H:i') : '-',
                'end_date' => $shift->check_out_at ? $shift->check_out_at->format('d/m/Y') : '-',
                'durasi' => $durasiFormatted,
                'tasks' => $taskList,
                'task_count' => $taskCount,
                'status' => $shift->check_out_at ? 'Selesai' : 'Aktif',
                'files' => $uploadedFiles,
                'files_count' => count($uploadedFiles),
            ];
        }

        // Hitung standby per kendaraan
        $totalDaysInPeriod = max(1, $startDate->diffInDays($endDate) + 1);
        foreach ($opsPerVehicleMap as $vKey => &$ops) {
            $ops['standby'] = max(0, $totalDaysInPeriod - $ops['operasional']);
        }
        unset($ops);

        // GRAFIK 1: Persentase Pemakaian Kendaraan (Standby vs Operasional)
        $totalVehiclesCount = $targetVehicles->count();
        $targetVehicleIds = $targetVehicles->pluck('id')->all();
        $operasionalVehiclesCount = count(array_intersect(array_keys($usedVehicleIds), $targetVehicleIds));
        $standbyVehiclesCount = max(0, $totalVehiclesCount - $operasionalVehiclesCount);
        $pctOperasional = $totalVehiclesCount > 0 ? round(($operasionalVehiclesCount / $totalVehiclesCount) * 100, 1) : 0;
        $pctStandby = $totalVehiclesCount > 0 ? round(($standbyVehiclesCount / $totalVehiclesCount) * 100, 1) : 0;

        $chartVehicleStatus = [
            'labels' => ['Operasional (' . $operasionalVehiclesCount . ' Armada)', 'Standby (' . $standbyVehiclesCount . ' Armada)'],
            'data' => [$operasionalVehiclesCount, $standbyVehiclesCount],
            'percentages' => [$pctOperasional, $pctStandby],
        ];

        // Filter kendaraan untuk chart pemakaian
        $filteredVehiclesForChart = [];
        foreach ($opsPerVehicleMap as $plate => $val) {
            if (!empty($vehicleIds) || $val['operasional'] > 0 || count($filteredVehiclesForChart) < 8) {
                $filteredVehiclesForChart[$plate] = $val;
            }
        }

        // GRAFIK 2: Pemakaian per Kendaraan (Standby & Operasional)
        $chartUsagePerVehicle = [
            'labels' => array_keys($filteredVehiclesForChart),
            'operasional' => array_column($filteredVehiclesForChart, 'operasional'),
            'standby' => array_column($filteredVehiclesForChart, 'standby'),
        ];

        // GRAFIK 3: Jarak Tempuh per Kendaraan (KM)
        // Sort descending
        arsort($kmPerVehicleMap);
        if (empty($vehicleIds)) {
            $kmWithVal = array_filter($kmPerVehicleMap, fn($v) => $v > 0);
            $topKmPerVehicle = !empty($kmWithVal) ? array_slice($kmWithVal, 0, 12, true) : array_slice($kmPerVehicleMap, 0, 8, true);
        } else {
            $topKmPerVehicle = $kmPerVehicleMap;
        }
        $chartKmPerVehicle = [
            'labels' => array_keys($topKmPerVehicle),
            'data' => array_values($topKmPerVehicle),
        ];

        // GRAFIK 4: Jarak Tempuh per Driver (KM)
        arsort($kmPerDriverMap);
        if (empty($driverIds)) {
            $driverKmWithVal = array_filter($kmPerDriverMap, fn($v) => $v > 0);
            $finalKmDriver = !empty($driverKmWithVal) ? array_slice($driverKmWithVal, 0, 12, true) : array_slice($kmPerDriverMap, 0, 8, true);
        } else {
            $finalKmDriver = $kmPerDriverMap;
        }
        $chartKmPerDriver = [
            'labels' => array_keys($finalKmDriver),
            'data' => array_values($finalKmDriver),
        ];

        // GRAFIK 5: Tugas yang Didapat per Driver
        arsort($tasksPerDriverMap);
        if (empty($driverIds)) {
            $driverTasksWithVal = array_filter($tasksPerDriverMap, fn($v) => $v > 0);
            $finalTasksDriver = !empty($driverTasksWithVal) ? array_slice($driverTasksWithVal, 0, 12, true) : array_slice($tasksPerDriverMap, 0, 8, true);
        } else {
            $finalTasksDriver = $tasksPerDriverMap;
        }
        $chartTasksPerDriver = [
            'labels' => array_keys($finalTasksDriver),
            'data' => array_values($finalTasksDriver),
        ];

        $driverId = count($driverIds) === 1 ? $driverIds[0] : null;
        $vehicleId = count($vehicleIds) === 1 ? $vehicleIds[0] : null;
        $hasDriverFilter = !empty($driverIds);
        $hasVehicleFilter = !empty($vehicleIds);

        return view('reports.driver', compact(
            'startDate',
            'endDate',
            'preset',
            'driverId',
            'vehicleId',
            'driverIds',
            'vehicleIds',
            'status',
            'drivers',
            'allVehicles',
            'tableData',
            'shifts',
            'totalKmAll',
            'totalTasksAll',
            'totalDurasiMenitAll',
            'totalVehiclesCount',
            'operasionalVehiclesCount',
            'standbyVehiclesCount',
            'pctOperasional',
            'pctStandby',
            'chartVehicleStatus',
            'chartUsagePerVehicle',
            'chartKmPerVehicle',
            'chartKmPerDriver',
            'chartTasksPerDriver',
            'hasDriverFilter',
            'hasVehicleFilter'
        ));
    }

    /**
     * Export Excel Laporan Driver (Dengan Grafik & Data)
     */
    public function exportDriverReport(Request $request)
    {
        if (auth()->check() && !auth()->user()->hasPermission('Export Laporan')) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengekspor Laporan.');
        }

        [$startDate, $endDate, $preset] = $this->getDateRange($request);
        
        $driverIds = $request->input('driver_id', []);
        if (!is_array($driverIds)) {
            $driverIds = $driverIds ? [$driverIds] : [];
        }
        $driverIds = array_values(array_filter($driverIds));

        $vehicleIds = $request->input('vehicle_id', []);
        if (!is_array($vehicleIds)) {
            $vehicleIds = $vehicleIds ? [$vehicleIds] : [];
        }
        $vehicleIds = array_values(array_filter($vehicleIds));

        $status = $request->input('status');

        $query = Shift::with(['driver', 'vehicle', 'pickupTasks', 'deliveryAssignments.salesOrder'])
            ->whereBetween('work_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if (!empty($driverIds)) {
            $query->whereIn('driver_id', $driverIds);
        }
        if (!empty($vehicleIds)) {
            $query->whereIn('vehicle_id', $vehicleIds);
        }
        if ($status === 'completed') {
            $query->whereNotNull('check_out_at');
        } elseif ($status === 'active') {
            $query->whereNull('check_out_at');
        }

        $shifts = $query->orderBy('work_date', 'desc')->orderBy('check_in_at', 'desc')->get();

        // Summary metrics
        $allVehicles = Vehicle::where('active', true)->get();
        $targetVehicles = !empty($vehicleIds) ? $allVehicles->whereIn('id', $vehicleIds) : $allVehicles;
        $totalVehicles = $targetVehicles->count();
        $targetVehicleIds = $targetVehicles->pluck('id')->all();
        $usedVehicles = $shifts->pluck('vehicle_id')->filter()->unique()->intersect($targetVehicleIds)->count();
        $standbyVehicles = max(0, $totalVehicles - $usedVehicles);
        $totalKm = $shifts->sum(function($s) {
            return ($s->end_odometer && $s->start_odometer) ? max(0, $s->end_odometer - $s->start_odometer) : 0;
        });
        $totalTasks = $shifts->sum(function($s) {
            return $s->pickupTasks->count() + $s->deliveryAssignments->count();
        });

        $summary = [
            'period' => $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y'),
            'total_shifts' => $shifts->count(),
            'total_km' => $totalKm,
            'total_vehicles' => $totalVehicles,
            'operasional_vehicles' => $usedVehicles,
            'standby_vehicles' => $standbyVehicles,
            'pct_operasional' => $totalVehicles > 0 ? round(($usedVehicles / $totalVehicles) * 100, 1) : 0,
            'pct_standby' => $totalVehicles > 0 ? round(($standbyVehicles / $totalVehicles) * 100, 1) : 0,
            'total_tasks' => $totalTasks,
        ];

        // Process base64 chart images if posted
        $chartImages = [];
        $chartKeys = ['chart_vehicle_pct', 'chart_usage', 'chart_km_vehicle', 'chart_km_driver', 'chart_tasks_driver'];

        foreach ($chartKeys as $key) {
            $b64 = $request->input($key);
            if ($b64 && str_contains($b64, 'base64,')) {
                $parts = explode('base64,', $b64);
                $imgData = base64_decode($parts[1]);
                if ($imgData !== false) {
                    $tmpPath = tempnam(sys_get_temp_dir(), 'exp_chart_') . '.png';
                    file_put_contents($tmpPath, $imgData);
                    $chartImages[$key] = $tmpPath;
                }
            }
        }

        $filename = 'Laporan_Driver_Grafik_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.xlsx';
        return Excel::download(new DriverReportExport($shifts, $summary, $chartImages), $filename);
    }

    /**
     * Index redirecting to driverReport
     */
    public function index(Request $request)
    {
        return $this->driverReport($request);
    }
}
