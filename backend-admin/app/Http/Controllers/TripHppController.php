<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Services\HppCalculationService;

class TripHppController extends Controller
{
    protected $hppService;

    public function __construct(HppCalculationService $hppService)
    {
        $this->hppService = $hppService;
    }

    public function index(Request $request)
    {
        // Load Shift
        $allShifts = Shift::with(['vehicle', 'driver', 'pickupTasks', 'expenses'])
            ->orderBy('work_date', 'desc')
            ->get();

        $totalTrips = $allShifts->count();
        
        $totalCost = 0;
        $totalItems = 0;
        $totalJarak = 0;
        $totalDurasi = 0;
        $costComposition = [
            'BBM' => 0,
            'Manpower' => 0,
            'Tol' => 0,
            'Parkir' => 0,
            'Lainnya' => 0,
        ];

        // Process each shift and attach calculated properties to display in view easily
        foreach ($allShifts as $shift) {
            $calc = $this->hppService->calculateProrata($shift);
            $shift->calc_details = $calc;
            $shift->total_cost = $calc['costs']['total'];

            $totalCost += $calc['costs']['total'];
            $totalItems += $shift->pickupTasks->sum('quantity');

            if ($shift->start_odometer && $shift->end_odometer) {
                $totalJarak += max(0, $shift->end_odometer - $shift->start_odometer);
            }
            if ($shift->check_in_at && $shift->check_out_at) {
                $totalDurasi += $shift->check_in_at->diffInMinutes($shift->check_out_at);
            }

            $costComposition['BBM'] += $calc['costs']['fuel'];
            $costComposition['Manpower'] += $calc['costs']['manpower'];
            $costComposition['Tol'] += $calc['costs']['toll'];
            $costComposition['Parkir'] += $calc['costs']['parking'];
            $costComposition['Lainnya'] += $calc['costs']['other'];
        }

        // Hitung rata-rata HPP per barang
        $avgHppPerItem = $totalItems > 0 ? $totalCost / $totalItems : 0;

        // Paginate the collection manually for the table
        $perPage = $request->get('per_page', 10);
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        
        $shifts = new \Illuminate\Pagination\LengthAwarePaginator(
            $allShifts->forPage($page, $perPage),
            $allShifts->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('hpp.index', compact('shifts', 'totalTrips', 'totalCost', 'avgHppPerItem', 'costComposition', 'totalJarak', 'totalDurasi'));
    }

    public function show($id)
    {
        $shift = Shift::with(['vehicle', 'driver', 'pickupTasks', 'expenses'])->findOrFail($id);
        
        $prorataDetails = $this->hppService->calculateProrata($shift);

        return view('hpp.show', compact('shift', 'prorataDetails'));
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\HppRitaseExport, 'Laporan_HPP_Ritase_' . date('Ymd_His') . '.xlsx');
    }
}
