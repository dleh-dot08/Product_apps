<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * GET /api/vehicles
     * Get all active vehicles for dropdown selection
     */
    public function index(Request $request)
    {
        $vehicles = Vehicle::where('active', 1)
            ->select('id', 'name', 'plate_number', 'km_per_liter', 'fuel_price_per_liter')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $vehicles
        ]);
    }
}
