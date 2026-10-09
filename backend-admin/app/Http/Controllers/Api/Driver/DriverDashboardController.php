<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverDashboardController extends Controller
{
    /**
     * GET /api/driver/dashboard
     * Mengambil ringkasan dashboard driver (real-time)
     */
    public function dashboardSummary(Request $request)
    {
        $user = Auth::user();
        
        $today = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        $pickups = DB::table('pickup_tasks')
            ->whereNull('pickup_tasks.deleted_at')
            ->leftJoin('task_manifests', 'pickup_tasks.manifest_id', '=', 'task_manifests.id')
            ->leftJoin('vehicles', function ($join) {
                $join->on('vehicles.id', '=', DB::raw('COALESCE(task_manifests.vehicle_id, pickup_tasks.vehicle_id)'));
            })
            ->select(
                'pickup_tasks.id', 
                'pickup_tasks.reference_number', 
                'pickup_tasks.pickup_name', 
                'pickup_tasks.pickup_location', 
                'pickup_tasks.destination', 
                'pickup_tasks.assigned_at', 
                DB::raw("CASE 
                    WHEN (COALESCE(task_manifests.driver_id, pickup_tasks.driver_id) IS NULL OR (COALESCE(task_manifests.driver_id, pickup_tasks.driver_id) != '" . $user->id . "' AND COALESCE(task_manifests.co_driver_id, pickup_tasks.co_driver_id) != '" . $user->id . "') OR (pickup_tasks.dispatch_date < CURRENT_DATE AND pickup_tasks.status NOT IN ('completed', 'delivered') AND COALESCE(pickup_tasks.is_out_of_city, false) = false)) THEN 'Tidak Terkirim'
                    ELSE pickup_tasks.status 
                END as status"), 
                DB::raw("'pickup' as task_type"),
                'pickup_tasks.quantity',
                'pickup_tasks.unit',
                DB::raw("NULL as item_category"),
                'vehicles.plate_number as vehicle_plate_number',
                'vehicles.name as vehicle_name',
                'pickup_tasks.dispatch_date',
                'pickup_tasks.estimated_arrival',
                'pickup_tasks.proof_photo',
                'pickup_tasks.failure_reason',
                'pickup_tasks.completed_odometer',
                'pickup_tasks.start_odometer',
                'pickup_tasks.start_fuel',
                'pickup_tasks.departure_notes',
                'pickup_tasks.receiver_name',
                'pickup_tasks.receiver_role',
                'pickup_tasks.item_condition',
                'pickup_tasks.completed_at',
                'pickup_tasks.has_issue'
            )
            ->where(function ($q) use ($user) {
                $q->where('pickup_tasks.driver_id', $user->id)
                  ->orWhere('pickup_tasks.co_driver_id', $user->id)
                  ->orWhere('task_manifests.driver_id', $user->id)
                  ->orWhere('task_manifests.co_driver_id', $user->id);
            });

        $deliveries = DB::table('delivery_assignments')
            ->whereNull('delivery_assignments.deleted_at')
            ->join('sales_orders', 'delivery_assignments.sales_order_id', '=', 'sales_orders.id')
            ->leftJoin('task_manifests', 'delivery_assignments.manifest_id', '=', 'task_manifests.id')
            ->leftJoin('vehicles', function ($join) {
                $join->on('vehicles.id', '=', DB::raw('COALESCE(task_manifests.vehicle_id, delivery_assignments.vehicle_id)'));
            })
            ->select(
                'delivery_assignments.id', 
                'sales_orders.so_number as reference_number', 
                DB::raw("COALESCE(delivery_assignments.pickup_name, 'Gudang AQPA') as pickup_name"), 
                DB::raw("COALESCE(delivery_assignments.pickup_location, '-') as pickup_location"), 
                'sales_orders.customer_name as destination', 
                'delivery_assignments.assigned_at', 
                DB::raw("CASE 
                    WHEN (COALESCE(task_manifests.driver_id, delivery_assignments.driver_id) IS NULL OR (COALESCE(task_manifests.driver_id, delivery_assignments.driver_id) != '" . $user->id . "' AND COALESCE(task_manifests.co_driver_id, delivery_assignments.co_driver_id) != '" . $user->id . "') OR (delivery_assignments.dispatch_date < CURRENT_DATE AND delivery_assignments.status NOT IN ('completed', 'delivered') AND COALESCE(delivery_assignments.is_out_of_city, false) = false)) THEN 'Tidak Terkirim'
                    ELSE delivery_assignments.status 
                END as status"), 
                DB::raw("'delivery' as task_type"),
                'sales_orders.ordered_quantity as quantity',
                'sales_orders.unit',
                'sales_orders.item_description as item_category',
                'vehicles.plate_number as vehicle_plate_number',
                'vehicles.name as vehicle_name',
                'delivery_assignments.dispatch_date',
                'delivery_assignments.estimated_arrival',
                'delivery_assignments.proof_photo',
                'delivery_assignments.failure_reason',
                'delivery_assignments.completed_odometer',
                'delivery_assignments.start_odometer',
                'delivery_assignments.start_fuel',
                'delivery_assignments.departure_notes',
                'delivery_assignments.receiver_name',
                'delivery_assignments.receiver_role',
                'delivery_assignments.item_condition',
                'delivery_assignments.completed_at',
                'delivery_assignments.has_issue'
            )
            ->where(function ($q) use ($user) {
                $q->where('delivery_assignments.driver_id', $user->id)
                  ->orWhere('delivery_assignments.co_driver_id', $user->id)
                  ->orWhere('task_manifests.driver_id', $user->id)
                  ->orWhere('task_manifests.co_driver_id', $user->id);
            });

        $unionQuery = $pickups->unionAll($deliveries);
        
        // Query untuk HARI INI
        $todayQuery = DB::query()->fromSub($unionQuery, 'tasks')
            ->where('status', '!=', 'Tidak Terkirim')
            ->where(function ($query) use ($today, $endOfDay) {
                $query->where(function ($q) use ($today, $endOfDay) {
                    // Cek berdasarkan dispatch_date & estimated_arrival jika ada
                    $q->whereDate(DB::raw("COALESCE(dispatch_date, assigned_at)"), '<=', $endOfDay)
                      ->whereDate(DB::raw("COALESCE(estimated_arrival, dispatch_date, assigned_at)"), '>=', $today);
                })
                // Tugas yang statusnya masih berjalan (belum delivered) tetap diikutsertakan
                ->orWhereIn('status', ['assigned', 'on_route', 'arrived']);
            });

        // 1. Total Trip Hari Ini
        $totalTrips = (clone $todayQuery)->count();

        // 2. Selesai (Hari ini)
        $completedTrips = (clone $todayQuery)->where('status', 'delivered')->count();

        // 3. Berlangsung (Hari ini)
        $inProgressTrips = (clone $todayQuery)->whereIn('status', ['assigned', 'on_route', 'arrived'])->count();

        // 4. Jarak Tempuh Hari Ini (dari Shift)
        $distanceKm = 0;
        $todayShifts = DB::table('shifts')
            ->where('driver_id', $user->id)
            ->whereDate('work_date', $today)
            ->get();
            
        foreach ($todayShifts as $shift) {
            if ($shift->start_odometer) {
                $end = $shift->end_odometer;
                if (!$end) {
                    $maxOdoData = DB::query()->fromSub($unionQuery, 'tasks')
                        ->where(function($q) use ($today, $endOfDay) {
                             $q->whereDate(DB::raw("COALESCE(dispatch_date, assigned_at)"), '<=', $endOfDay)
                               ->whereDate(DB::raw("COALESCE(estimated_arrival, dispatch_date, assigned_at)"), '>=', $today);
                        })
                        ->selectRaw('MAX(completed_odometer) as max_c, MAX(start_odometer) as max_s')
                        ->first();
                    if ($maxOdoData) {
                        $end = max((float)$maxOdoData->max_c, (float)$maxOdoData->max_s);
                    }
                }
                if ($end > $shift->start_odometer) {
                    $distanceKm += ($end - $shift->start_odometer);
                }
            }
        }

        // 5. List Trip Hari ini
        $todayTasks = (clone $todayQuery)
            ->orderByRaw("CASE 
                WHEN status IN ('on_route', 'arrived') THEN 1 
                WHEN status = 'assigned' THEN 2 
                ELSE 3 END")
            ->orderBy('assigned_at', 'desc')
            ->limit(5)
            ->get();

        // 6. Active Task (Tanpa dibatasi hari ini)
        $activeTask = DB::query()->fromSub($unionQuery, 'tasks')
            ->whereIn('status', ['on_route', 'arrived'])
            ->orderBy('assigned_at', 'desc')
            ->first();

        // 7. KPI Performance (Berdasarkan Filter)
        $period = $request->input('period', '7_days');
        
        $kpiQueryAll = DB::query()->fromSub($unionQuery, 'tasks');
            
        if ($period !== 'all') {
            $startDate = null;
            if ($period === '7_days') {
                $startDate = now()->subDays(7)->startOfDay();
            } elseif ($period === '1_month') {
                $startDate = now()->subMonth()->startOfDay();
            } elseif ($period === '3_months') {
                $startDate = now()->subMonths(3)->startOfDay();
            } elseif ($period === '6_months') {
                $startDate = now()->subMonths(6)->startOfDay();
            } elseif ($period === '1_year') {
                $startDate = now()->subYear()->startOfDay();
            }
            
            if ($startDate) {
                $kpiQueryAll->where(DB::raw("COALESCE(dispatch_date, assigned_at)"), '>=', $startDate);
            }
        }
            
        $allPeriodTrips = $kpiQueryAll->get();
            
        $totalValid = 0;
        $deliveredCount = 0;
        $kendalaCount = 0;
        $lateCount = 0;
        $failedCount = 0;
        
        $totalDistanceAll = 0;
        $totalFuelAll = 0;
        
        foreach($allPeriodTrips as $trip) {
            if ($trip->status === 'pending') {
                $kendalaCount++;
                continue; // Kendala (arrived but issue) tidak masuk valid total
            }
            
            if ($trip->status === 'Tidak Terkirim' || $trip->status === 'failed') {
                if ($trip->has_issue) {
                    $kendalaCount++;
                    continue; // Kendala, kecualikan dari performa
                } else {
                    $failedCount++;
                    // Jangan continue, biarkan lanjut agar masuk ke $totalValid (menurunkan performa)
                }
            }
            
            $totalValid++;
            
            if ($trip->status === 'delivered') {
                $deliveredCount++;
                
                // Cek apakah terlambat
                if ($trip->estimated_arrival && $trip->completed_at) {
                    if (\Carbon\Carbon::parse($trip->completed_at)->gt(\Carbon\Carbon::parse($trip->estimated_arrival))) {
                        $lateCount++;
                    }
                }
                
                $totalFuelAll += (float)$trip->start_fuel;
            }
        }
        
        // Jarak tempuh untuk period ini (dari Shift)
        $totalDistanceAll = 0;
        $periodShiftsQuery = DB::table('shifts')->where('driver_id', $user->id);
        
        if (isset($startDate)) {
            $periodShiftsQuery->whereDate('work_date', '>=', $startDate);
        }
        
        $periodShifts = $periodShiftsQuery->get();
            
        foreach ($periodShifts as $shift) {
            if ($shift->start_odometer) {
                $end = $shift->end_odometer;
                if (!$end) {
                    $maxOdoData = DB::query()->fromSub($unionQuery, 'tasks')
                        ->where(function($q) use ($shift) {
                             $q->whereDate(DB::raw("COALESCE(dispatch_date, assigned_at)"), '<=', $shift->work_date)
                               ->whereDate(DB::raw("COALESCE(estimated_arrival, dispatch_date, assigned_at)"), '>=', $shift->work_date);
                        })
                        ->selectRaw('MAX(completed_odometer) as max_c, MAX(start_odometer) as max_s')
                        ->first();
                    if ($maxOdoData) {
                        $end = max((float)$maxOdoData->max_c, (float)$maxOdoData->max_s);
                    }
                }
                if ($end > $shift->start_odometer) {
                    $totalDistanceAll += ($end - $shift->start_odometer);
                }
            }
        }
        
        $totalDelivered = $deliveredCount; // Untuk output json total_trip yang sudah delivered
        
        // Performa = (Selesai / (Total Tugas - Kendala)) * 100
        $onTimePercentage = $totalValid > 0 ? round(($deliveredCount / $totalValid) * 100) : 100;
        $fuelEfficiency = $totalFuelAll > 0 ? round($totalDistanceAll / $totalFuelAll, 1) : 12.5; // default 12.5 km/l jika blm ada data bbm

        // 8. Active Shift
        $shiftRecord = \App\Models\Shift::with('vehicle')->where('driver_id', $user->id)
            ->whereNull('check_out_at')
            ->first();

        $activeShift = null;
        if ($shiftRecord) {
            $activeShift = [
                'id' => $shiftRecord->id,
                'start_time' => $shiftRecord->check_in_at,
                'start_odometer' => $shiftRecord->start_odometer,
                'vehicle_plate_number' => $shiftRecord->vehicle ? $shiftRecord->vehicle->plate_number : null,
                'vehicle_name' => $shiftRecord->vehicle ? $shiftRecord->vehicle->name : null,
                'status' => 'active'
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'active_shift' => $activeShift,
                'today_trips_count' => $totalTrips,
                'completed_trips_count' => $completedTrips,
                'in_progress_trips_count' => $inProgressTrips,
                'distance_today' => $distanceKm,
                'today_tasks' => $todayTasks,
                'active_task' => $activeTask,
                'performance' => [
                    'on_time_percentage' => $onTimePercentage,
                    'fuel_efficiency' => $fuelEfficiency,
                    'total_trip' => $totalValid,
                    'on_time_trip' => $deliveredCount,
                    'kendala_trip' => $kendalaCount,
                    'late_trip' => $lateCount,
                    'failed_trip' => $failedCount,
                    'distance_period' => $totalDistanceAll,
                ]
            ]
        ]);
    }
}
