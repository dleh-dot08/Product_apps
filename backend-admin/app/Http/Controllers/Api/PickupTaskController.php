<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PickupTask;
use App\Http\Requests\StorePickupTaskRequest;
use App\Http\Requests\UpdatePickupTaskStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PickupTaskController extends Controller
{
    /**
     * GET /api/pickup
     * Menampilkan daftar tugas
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PickupTask::with(['driver', 'vehicle']);

        // Jika user adalah Driver, hanya lihat tugasnya sendiri
        $roleName = strtolower($user->role->name ?? '');
        if ($roleName === 'driver') {
            $query->where('driver_id', $user->id);
        }

        $tasks = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }

    /**
     * POST /api/pickup
     * Membuat tugas baru
     */
    public function store(StorePickupTaskRequest $request)
    {
        $data = $request->validated();
        $data['assigned_by'] = Auth::id();
        
        // Generate Reference Number
        if (empty($data['reference_number'])) {
            $data['reference_number'] = 'MAN-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
        }

        // Hitung line_total
        if (isset($data['quantity']) && isset($data['unit_price'])) {
            $data['line_total'] = $data['quantity'] * $data['unit_price'];
        }
        
        $task = PickupTask::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Tugas pickup berhasil dibuat.',
            'data' => $task
        ], 201);
    }

    /**
     * PATCH /api/pickup/{id}/status
     * Mengupdate status tugas
     */
    public function updateStatus(UpdatePickupTaskStatusRequest $request, $id)
    {
        $task = PickupTask::findOrFail($id);
        $user = Auth::user();
        $roleName = strtolower($user->role->name ?? '');
        
        // Validasi hak akses (driver hanya boleh update tugasnya sendiri)
        if ($roleName === 'driver' && $task->driver_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $newStatus = $request->input('status');
        $updateData = ['status' => $newStatus];

        // Validasi khusus untuk status delivered
        if ($newStatus === 'delivered') {
            // Cek shift aktif driver (check_out_at masih NULL)
            $activeShift = DB::table('shifts')
                ->where('driver_id', $task->driver_id)
                ->whereNull('check_out_at')
                ->latest()
                ->first();

            if (!$activeShift) {
                return response()->json([
                    'message' => 'Mulai trip/berangkat terlebih dahulu'
                ], 409);
            }
            
            $updateData['shift_id'] = $activeShift->id;
            $updateData['completed_odometer'] = $request->input('completed_odometer');
            
            // Handle proof_photo upload atau URL
            if ($request->hasFile('proof_photo')) {
                $updateData['proof_photo'] = $request->file('proof_photo')->store('proofs', 'public');
            } else {
                $updateData['proof_photo'] = $request->input('proof_photo');
            }
        }

        // Alasan gagal
        if ($newStatus === 'failed') {
            $updateData['failure_reason'] = $request->input('failure_reason');
        }

        // Update timestamps
        switch ($newStatus) {
            case 'on_route':
                if (!$task->started_at) $updateData['started_at'] = now();
                break;
            case 'arrived':
                if (!$task->arrived_at) $updateData['arrived_at'] = now();
                break;
            case 'delivered':
            case 'failed':
            case 'cancelled':
                if (!$task->completed_at) $updateData['completed_at'] = now();
                break;
        }

        $task->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => "Status tugas berhasil diperbarui menjadi {$newStatus}.",
            'data' => $task
        ]);
    }
}
