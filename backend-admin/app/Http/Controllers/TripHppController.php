<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Services\HppCalculationService;

class TripHppController extends Controller
{
    protected $hppService;

    public function __construct(HppCalculationService $hppService)
    {
        $this->hppService = $hppService;
    }

    public function index()
    {
        $trips = Trip::with(['vehicle', 'driver'])->orderBy('date', 'desc')->get();

        $totalTrips = $trips->count();
        $totalCost = $trips->sum(function($trip) {
            return $trip->total_cost;
        });

        // Hitung rata-rata HPP per barang
        $totalItems = 0;
        foreach ($trips as $trip) {
            $totalItems += $trip->items->count();
        }
        $avgHppPerItem = $totalItems > 0 ? $totalCost / $totalItems : 0;

        // Komposisi Biaya
        $costComposition = [
            'BBM' => $trips->sum('fuel_cost'),
            'Manpower' => $trips->sum('manpower_cost'),
            'Tol' => $trips->sum('toll_cost'),
            'Parkir' => $trips->sum('parking_cost'),
            'Lainnya' => $trips->sum('other_cost'),
        ];

        return view('hpp.index', compact('trips', 'totalTrips', 'totalCost', 'avgHppPerItem', 'costComposition'));
    }

    public function show($id)
    {
        $trip = Trip::with(['vehicle', 'driver', 'items'])->findOrFail($id);
        
        $prorataDetails = $this->hppService->calculateProrata($trip);

        return view('hpp.show', compact('trip', 'prorataDetails'));
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\TripHppExport, 'HPP_Ritase_Log.xlsx');
    }
}
