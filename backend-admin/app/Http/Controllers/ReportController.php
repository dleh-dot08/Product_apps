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
        [$startDate, $endDate, $preset] = $this->getDateRange($request);
        $driverId = $request->input('driver_id');
        $vehicleId = $request->input('vehicle_id');

        // 1. QUERY SHIFTS / PERJALANAN
        $query = Shift::with(['driver', 'vehicle', 'pickupTasks', 'deliveryAssignments.salesOrder'])
            ->whereBetween('work_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($driverId) {
            $query->where('driver_id', $driverId);
        }
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
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

        // Inisialisasi Kendaraan
        foreach ($allVehicles as $v) {
            $vLabel = $v->plate_number ? $v->plate_number : $v->name;
            $kmPerVehicleMap[$vLabel] = 0;
            $opsPerVehicleMap[$vLabel] = [
                'operasional' => 0,
                'standby' => 0,
            ];
        }

        // Inisialisasi Driver
        foreach ($drivers as $d) {
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

            // Akumulasi metric per kendaraan
            if (isset($kmPerVehicleMap[$vehicleLabel])) {
                $kmPerVehicleMap[$vehicleLabel] += $tripKm;
                $opsPerVehicleMap[$vehicleLabel]['operasional'] += 1;
            } else {
                $kmPerVehicleMap[$vehicleLabel] = $tripKm;
                $opsPerVehicleMap[$vehicleLabel] = ['operasional' => 1, 'standby' => 0];
            }

            // Akumulasi metric per driver
            if (isset($kmPerDriverMap[$driverName])) {
                $kmPerDriverMap[$driverName] += $tripKm;
                $tasksPerDriverMap[$driverName] += $taskCount;
            } else {
                $kmPerDriverMap[$driverName] = $tripKm;
                $tasksPerDriverMap[$driverName] = $taskCount;
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
            ];
        }

        // Hitung standby per kendaraan
        $totalDaysInPeriod = max(1, $startDate->diffInDays($endDate) + 1);
        foreach ($opsPerVehicleMap as $vKey => &$ops) {
            $ops['standby'] = max(0, $totalDaysInPeriod - $ops['operasional']);
        }
        unset($ops);

        // GRAFIK 1: Persentase Pemakaian Kendaraan (Standby vs Operasional)
        $totalVehiclesCount = $allVehicles->count();
        $operasionalVehiclesCount = count($usedVehicleIds);
        $standbyVehiclesCount = max(0, $totalVehiclesCount - $operasionalVehiclesCount);
        $pctOperasional = $totalVehiclesCount > 0 ? round(($operasionalVehiclesCount / $totalVehiclesCount) * 100, 1) : 0;
        $pctStandby = $totalVehiclesCount > 0 ? round(($standbyVehiclesCount / $totalVehiclesCount) * 100, 1) : 0;

        $chartVehicleStatus = [
            'labels' => ['Operasional (' . $operasionalVehiclesCount . ' Armada)', 'Standby (' . $standbyVehiclesCount . ' Armada)'],
            'data' => [$operasionalVehiclesCount, $standbyVehiclesCount],
            'percentages' => [$pctOperasional, $pctStandby],
        ];

        // Filter kendaraan untuk chart pemakaian (ambil kendaraan yang memiliki aktivitas atau batasi jika banyak)
        $filteredVehiclesForChart = [];
        foreach ($opsPerVehicleMap as $plate => $val) {
            if ($val['operasional'] > 0 || count($filteredVehiclesForChart) < 10) {
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
        $topKmPerVehicle = array_slice($kmPerVehicleMap, 0, 12, true);
        $chartKmPerVehicle = [
            'labels' => array_keys($topKmPerVehicle),
            'data' => array_values($topKmPerVehicle),
        ];

        // GRAFIK 4: Jarak Tempuh per Driver (KM)
        arsort($kmPerDriverMap);
        $chartKmPerDriver = [
            'labels' => array_keys($kmPerDriverMap),
            'data' => array_values($kmPerDriverMap),
        ];

        // GRAFIK 5: Tugas yang Didapat per Driver
        arsort($tasksPerDriverMap);
        $chartTasksPerDriver = [
            'labels' => array_keys($tasksPerDriverMap),
            'data' => array_values($tasksPerDriverMap),
        ];

        return view('reports.driver', compact(
            'startDate',
            'endDate',
            'preset',
            'driverId',
            'vehicleId',
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
            'chartTasksPerDriver'
        ));
    }

    /**
     * Export Excel Laporan Driver (Dengan Grafik & Data)
     */
    public function exportDriverReport(Request $request)
    {
        [$startDate, $endDate, $preset] = $this->getDateRange($request);
        $driverId = $request->input('driver_id');
        $vehicleId = $request->input('vehicle_id');

        $query = Shift::with(['driver', 'vehicle', 'pickupTasks', 'deliveryAssignments.salesOrder'])
            ->whereBetween('work_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($driverId) {
            $query->where('driver_id', $driverId);
        }
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        $shifts = $query->orderBy('work_date', 'desc')->orderBy('check_in_at', 'desc')->get();

        // Summary metrics
        $allVehicles = Vehicle::where('active', true)->get();
        $totalVehicles = $allVehicles->count();
        $usedVehicles = $shifts->pluck('vehicle_id')->filter()->unique()->count();
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
