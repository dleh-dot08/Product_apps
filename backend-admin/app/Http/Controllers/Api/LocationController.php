<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DriverLocation;

class LocationController extends Controller
{
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'heading' => 'nullable|numeric',
            'task_id' => 'nullable|string',
        ]);

        $user = $request->user();

        // Update or Create the latest location for this user
        $location = DriverLocation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'heading' => $request->heading,
                'task_id' => $request->task_id,
            ]
        );

        return response()->json([
            'message' => 'Location updated successfully',
            'data' => $location
        ]);
    }

    public function getActiveDrivers(Request $request)
    {
        // Get all drivers who have updated their location in the last hour
        // and have an active task_id.
        $locations = DriverLocation::with('user:id,name,email')
            ->whereNotNull('task_id')
            ->where('updated_at', '>=', now()->subHours(1))
            ->get();

        return response()->json([
            'data' => $locations
        ]);
    }
}
