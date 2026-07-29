<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\VehicleDataMaster;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = VehicleDataMaster::orderBy('active', 'desc')
            ->orderBy('plate_number', 'asc')
            ->get();

        $totalVehicles = $vehicles->count();
        $activeVehicles = $vehicles->where('active', true)->count();
        $inactiveVehicles = $vehicles->where('active', false)->count();
        $avgKmPerLiter = $vehicles->avg('km_per_liter') ?? 0;

        return view('vehicles.index', compact('vehicles', 'totalVehicles', 'activeVehicles', 'inactiveVehicles', 'avgKmPerLiter'));
    }

    public function store(VehicleRequest $request)
    {
        VehicleDataMaster::create($request->validated());
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function update(VehicleRequest $request, VehicleDataMaster $vehicle)
    {
        $vehicle->update($request->validated());
        return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function destroy(VehicleDataMaster $vehicle)
    {
        $vehicle->update(['active' => false]);
        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil dinon-aktifkan.');
    }
}
