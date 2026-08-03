<?php

namespace App\Http\Controllers;

use App\Models\PickupTask;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PickupTaskController extends Controller
{
    public function index()
    {
        // Hanya nampilin untuk Super Admin / Operator / dll. 
        // Kalau Driver, mungkin cuma bisa lihat punya sendiri
        $query = PickupTask::with(['driver', 'vehicle', 'assignedBy'])->latest();
        
        $user = Auth::user();
        if (strtolower($user->role) === 'driver') {
            $query->where('driver_id', $user->id);
        }

        $tasks = $query->get();
        $drivers = User::where('role', 'driver')->get();
        $vehicles = Vehicle::where('active', true)->get();

        $totalTasks = $tasks->count();
        $assignedTasks = $tasks->where('status', 'assigned')->count();
        $onRouteTasks = $tasks->where('status', 'on_route')->count();
        $completedTasks = $tasks->where('status', 'delivered')->count();

        return view('pickup-tasks.index', compact('tasks', 'drivers', 'vehicles', 'totalTasks', 'assignedTasks', 'onRouteTasks', 'completedTasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|uuid|exists:users,id',
            'vehicle_id' => 'required|uuid|exists:vehicles,id',
            'so_number' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'address' => 'required|string',
            'item_name' => 'required|string',
            'quantity' => 'nullable|numeric',
            'remarks' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['assigned_by'] = Auth::id();
        $data['status'] = 'assigned';
        
        if (empty($data['reference_number'])) {
            $data['reference_number'] = 'MAN-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
        }

        PickupTask::create($data);

        return redirect()->route('pickup-tasks.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function update(Request $request, PickupTask $pickupTask)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $pickupTask->update(['status' => $request->status]);

        return redirect()->route('pickup-tasks.index')->with('success', 'Status tugas berhasil diperbarui.');
    }

    public function destroy(PickupTask $pickupTask)
    {
        $pickupTask->delete();
        return redirect()->route('pickup-tasks.index')->with('success', 'Tugas berhasil dihapus.');
    }
}
