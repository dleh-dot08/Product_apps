<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PickupTask;
use App\Models\DeliveryAssignment;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Store a newly created expense from mobile app during a task.
     */
    public function storeFromTask(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'category' => 'required|string', // e.g. BBM, Tol, Parkir, Lainnya
        ]);

        $task = PickupTask::find($id);
        if (!$task) {
            $task = DeliveryAssignment::find($id);
        }

        if (!$task) {
            return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
        }

        if (!$task->shift_id) {
            // Auto create shift for backward compatibility if task is already on_route or arrived
            if (in_array($task->status, ['on_route', 'arrived'])) {
                $shift = \App\Models\Shift::create([
                    'driver_id' => $task->driver_id,
                    'vehicle_id' => $task->vehicle_id ?? null,
                    'work_date' => now()->toDateString(),
                    'check_in_at' => $task->started_at ?? now(),
                    'start_odometer' => $task->start_odometer ?? 0,
                    'source' => 'task',
                    'task_reference' => $task->reference_number ?? ('TASK-' . $task->id),
                ]);
                $task->update(['shift_id' => $shift->id]);
                // Re-fetch or update the task's shift_id in memory
                $task->shift_id = $shift->id;
            } else {
                return response()->json(['message' => 'Mulai perjalanan terlebih dahulu (Shift belum aktif)'], 409);
            }
        }

        // Map UI category to DB enum
        $dbCategory = 'other';
        $uiCategory = strtolower($request->category);
        if ($uiCategory === 'bbm') $dbCategory = 'fuel';
        elseif ($uiCategory === 'tol') $dbCategory = 'toll';
        elseif ($uiCategory === 'parkir') $dbCategory = 'parking';
        
        $expense = new Expense();
        $expense->shift_id = $task->shift_id;
        $expense->driver_id = Auth::id() ?? $task->driver_id;
        $expense->category = $dbCategory;
        $expense->amount = $request->amount;
        $expense->description = $request->description; // Custom category if 'Lainnya'
        $expense->notes = $request->notes; // Additional notes
        
        if ($request->hasFile('receipt')) {
            $expense->receipt_url = $request->file('receipt')->store('expenses', 'public');
        }

        $expense->save();

        return response()->json([
            'message' => 'Pengeluaran berhasil disimpan',
            'data' => $expense
        ], 201);
    }
}
