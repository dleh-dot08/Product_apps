<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        // Global stats (unfiltered)
        $allVehicles = Vehicle::all();
        $totalVehicles = $allVehicles->count();
        $activeVehicles = $allVehicles->where('active', true)->count();
        $inactiveVehicles = $allVehicles->where('active', false)->count();
        $avgKmPerLiter = $allVehicles->avg('km_per_liter') ?? 0;

        // Query for table
        $query = Vehicle::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('plate_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('operational', 'like', "%{$search}%");
            });
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('active', $request->status === 'active' ? 1 : 0);
        }

        $query->orderBy('active', 'desc')
              ->orderBy('plate_number', 'asc');

        // Pagination
        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') {
            $perPage = $totalVehicles > 0 ? $totalVehicles : 1;
        }

        $vehicles = $query->paginate((int)$perPage)->appends($request->query());

        return view('vehicles.index', compact('vehicles', 'totalVehicles', 'activeVehicles', 'inactiveVehicles', 'avgKmPerLiter'));
    }

    public function store(VehicleRequest $request)
    {
        Vehicle::create($request->validated());
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update($request->validated());
        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->update(['active' => false]);
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil dinon-aktifkan.');
    }
}
