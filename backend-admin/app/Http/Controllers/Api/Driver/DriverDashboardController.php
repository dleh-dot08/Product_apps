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
            ->leftJoin('vehicles', 'pickup_tasks.vehicle_id', '=', 'vehicles.id')
            ->select(
                'pickup_tasks.id', 
                'pickup_tasks.reference_number', 
                'pickup_tasks.pickup_name', 
                'pickup_tasks.pickup_location', 
                'pickup_tasks.destination', 
                'pickup_tasks.assigned_at', 
                'pickup_tasks.status', 
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
                'pickup_tasks.completed_at'
            )
            ->where('pickup_tasks.driver_id', $user->id);

        $deliveries = DB::table('delivery_assignments')
            ->join('sales_orders', 'delivery_assignments.sales_order_id', '=', 'sales_orders.id')
            ->leftJoin('vehicles', 'delivery_assignments.vehicle_id', '=', 'vehicles.id')
            ->select(
                'delivery_assignments.id', 
                'sales_orders.so_number as reference_number', 
                DB::raw("COALESCE(delivery_assignments.pickup_name, 'Gudang AQPA') as pickup_name"), 
                DB::raw("COALESCE(delivery_assignments.pickup_location, '-') as pickup_location"), 
                'sales_orders.customer_name as destination', 
                'delivery_assignments.assigned_at', 
                'delivery_assignments.status', 
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
                'delivery_assignments.completed_at'
            )
            ->where('delivery_assignments.driver_id', $user->id);

        $unionQuery = $pickups->unionAll($deliveries);
        
        // Query untuk HARI INI
        $todayQuery = DB::query()->fromSub($unionQuery, 'tasks')
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

        // 4. Jarak Tempuh
        $distanceKm = 0;

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
        
        $kpiQuery = DB::query()->fromSub($unionQuery, 'tasks')
            ->where('status', 'delivered');
            
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
                $kpiQuery->where('completed_at', '>=', $startDate);
            }
        }
            
        $allDeliveredTrips = $kpiQuery->get();
            
        $totalDelivered = $allDeliveredTrips->count();
        $onTimeCount = 0;
        $totalDistanceAll = 0;
        $totalFuelAll = 0;
        
        foreach($allDeliveredTrips as $trip) {
            if ($trip->estimated_arrival && $trip->completed_at) {
                if (\Carbon\Carbon::parse($trip->completed_at)->lte(\Carbon\Carbon::parse($trip->estimated_arrival))) {
                    $onTimeCount++;
                }
            } else {
                // Asumsi tepat waktu jika tidak ada estimasi atau completed_at belum tersetting dengan benar di db lama
                $onTimeCount++;
            }

            // Hitung BBM rata-rata
            $dist = max(0, (float)$trip->completed_odometer - (float)$trip->start_odometer);
            $totalDistanceAll += $dist;
            $totalFuelAll += (float)$trip->start_fuel;
        }
        
        $onTimePercentage = $totalDelivered > 0 ? round(($onTimeCount / $totalDelivered) * 100) : 100;
        $fuelEfficiency = $totalFuelAll > 0 ? round($totalDistanceAll / $totalFuelAll, 1) : 12.5; // default 12.5 km/l jika blm ada data bbm

        return response()->json([
            'status' => 'success',
            'data' => [
                'today_trips_count' => $totalTrips,
                'completed_trips_count' => $completedTrips,
                'in_progress_trips_count' => $inProgressTrips,
                'distance_today' => $distanceKm,
                'today_tasks' => $todayTasks,
                'active_task' => $activeTask,
                'performance' => [
                    'on_time_percentage' => $onTimePercentage,
                    'fuel_efficiency' => $fuelEfficiency,
                    'total_trip' => $totalDelivered,
                    'on_time_trip' => $onTimeCount,
                ]
            ]
        ]);
    }
}
