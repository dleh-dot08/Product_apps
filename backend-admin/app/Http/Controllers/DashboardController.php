<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PickupTask;
use App\Models\PackagingJob;
use App\Models\User;
use App\Models\Shift;
use App\Services\HppCalculationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(HppCalculationService $hppService)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // 1. Total Tugas Driver (Bulan Ini)
        $totalTugasDriver = PickupTask::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        
        // 2. Packaging Selesai (Bulan Ini)
        $totalPackaging = PackagingJob::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $completedPackaging = PackagingJob::whereBetween('created_at', [$startOfMonth, $endOfMonth])->whereNotNull('completion_date')->count();
        $packagingPercentage = $totalPackaging > 0 ? round(($completedPackaging / $totalPackaging) * 100) : 0;

        // 3. User Aktif
        $totalUserAktif = User::count();

        // 4. HPP Ritase Bulan Ini
        $shifts = Shift::with(['vehicle', 'driver', 'pickupTasks', 'deliveryAssignments.salesOrder', 'expenses'])
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get();
            
        $totalHpp = 0;
        $hppBensin = 0;
        $hppTol = 0;
        $hppParkir = 0;
        $hppLainnya = 0;
        
        foreach ($shifts as $shift) {
            $calc = $hppService->calculateProrata($shift);
            $totalHpp += $calc['costs']['total'] ?? 0;
            $hppBensin += $calc['costs']['fuel'] ?? 0;
            $hppTol += $calc['costs']['toll'] ?? 0;
            $hppParkir += $calc['costs']['parking'] ?? 0;
            $hppLainnya += ($calc['costs']['other'] ?? 0) + ($calc['costs']['manpower'] ?? 0);
        }

        // --- 5. Data Chart Multidimensional (Yearly, Monthly, Weekly) ---
        $chartData = [
            'yearly' => ['labels' => [], 'tugas' => [], 'packaging' => []],
            'monthly' => ['labels' => [], 'tugas' => [], 'packaging' => []],
            'weekly' => ['labels' => [], 'tugas' => [], 'packaging' => []],
        ];

        // A. Yearly (12 Bulan)
        for ($i = 1; $i <= 12; $i++) {
            $monthDate = Carbon::create($currentYear, $i, 1);
            $chartData['yearly']['labels'][] = $monthDate->isoFormat('MMM'); // Jan, Feb, dst.
            $chartData['yearly']['tugas'][] = PickupTask::whereYear('created_at', $currentYear)->whereMonth('created_at', $i)->count();
            $chartData['yearly']['packaging'][] = PackagingJob::whereYear('completion_date', $currentYear)->whereMonth('completion_date', $i)->count();
        }

        // B. Monthly (Hari dalam bulan ini: 1-30/31)
        $daysInMonth = Carbon::now()->daysInMonth;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $chartData['monthly']['labels'][] = $i;
            $dateString = Carbon::create($currentYear, $currentMonth, $i)->toDateString();
            $chartData['monthly']['tugas'][] = PickupTask::whereDate('created_at', $dateString)->count();
            $chartData['monthly']['packaging'][] = PackagingJob::whereDate('completion_date', $dateString)->count();
        }

        // C. Weekly (7 Hari Terakhir / H-6 sampai H)
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartData['weekly']['labels'][] = $date->isoFormat('ddd'); // Sen, Sel, dst
            $chartData['weekly']['tugas'][] = PickupTask::whereDate('created_at', $date)->count();
            $chartData['weekly']['packaging'][] = PackagingJob::whereDate('completion_date', $date)->count();
        }

        // 6. Daftar Tugas Aktif (Terbaru)
        $activeTasks = PickupTask::with('driver')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalTugasDriver', 
            'packagingPercentage', 
            'totalUserAktif', 
            'totalHpp',
            'hppBensin',
            'hppTol',
            'hppParkir',
            'hppLainnya',
            'chartData',
            'activeTasks'
        ));
    }
}
