<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PickupTask;
use App\Models\DeliveryAssignment;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class DriverReportController extends Controller
{
    public function index()
    {
        $pickups = DB::table('pickup_tasks')
            ->leftJoin('users', 'pickup_tasks.driver_id', '=', 'users.id')
            ->leftJoin('vehicles', 'pickup_tasks.vehicle_id', '=', 'vehicles.id')
            ->select(
                'pickup_tasks.id', 
                'pickup_tasks.reference_number', 
                'users.name as driver_name', 
                'vehicles.plate_number',
                'pickup_tasks.assigned_at', 
                'pickup_tasks.status', 
                DB::raw("'pickup' as task_type")
            );

        $deliveries = DB::table('delivery_assignments')
            ->join('sales_orders', 'delivery_assignments.sales_order_id', '=', 'sales_orders.id')
            ->leftJoin('users', 'delivery_assignments.driver_id', '=', 'users.id')
            ->leftJoin('vehicles', 'delivery_assignments.vehicle_id', '=', 'vehicles.id')
            ->select(
                'delivery_assignments.id', 
                'sales_orders.so_number as reference_number', 
                'users.name as driver_name', 
                'vehicles.plate_number',
                'delivery_assignments.assigned_at', 
                'delivery_assignments.status', 
                DB::raw("'delivery' as task_type")
            );

        $tasks = $pickups->unionAll($deliveries)->orderBy('assigned_at', 'desc')->paginate(15);

        return view('reports.driver.index', compact('tasks'));
    }

    public function show($id)
    {
        // Try finding in pickup first
        $task = PickupTask::with(['driver', 'vehicle', 'shift', 'attachments'])->find($id);
        if ($task) {
            $task->task_type = 'pickup';
            $task->destination = $task->destination;
            $task->pickup_name = $task->pickup_name;
        } else {
            $task = DeliveryAssignment::with(['driver', 'vehicle', 'shift', 'attachments', 'salesOrder'])->find($id);
            if ($task) {
                $task->task_type = 'delivery';
                $task->reference_number = $task->salesOrder->so_number ?? '-';
                $task->destination = $task->salesOrder->customer_name ?? '-';
                $task->pickup_name = $task->pickup_name ?: 'Gudang AQPA';
                $task->pickup_location = $task->pickup_location ?: '-';
            }
        }

        if (!$task) {
            return redirect()->route('driver-reports.index')->with('error', 'Tugas tidak ditemukan.');
        }

        // Fetch related expenses by shift_id if exists
        $expenses = collect();
        if ($task->shift_id) {
            $expenses = Expense::where('shift_id', $task->shift_id)->get();
        }

        // Group attachments by category
        $attachments = $task->attachments->groupBy('category');

        return view('reports.driver.show', compact('task', 'attachments', 'expenses'));
    }
}
