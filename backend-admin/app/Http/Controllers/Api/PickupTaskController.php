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
        
        // Cek Role
        $roleName = strtolower($user->roleRelation->name ?? $user->role->name ?? '');
        
        $pickups = DB::table('pickup_tasks')
            ->leftJoin('vehicles', 'pickup_tasks.vehicle_id', '=', 'vehicles.id')
            ->select(
                'pickup_tasks.id', 
                'pickup_tasks.reference_number', 
                'pickup_tasks.pickup_name', 
                'pickup_tasks.pickup_location', 
                'pickup_tasks.destination', 
                'pickup_tasks.assigned_at', 
                'pickup_tasks.status', 
                DB::raw("'pickup' as task_type"),
                'pickup_tasks.quantity',
                'pickup_tasks.unit',
                DB::raw("NULL as item_category"),
                'vehicles.plate_number as vehicle_plate_number',
                'vehicles.name as vehicle_name',
                'pickup_tasks.dispatch_date',
                'pickup_tasks.estimated_arrival',
                'pickup_tasks.proof_photo',
                'pickup_tasks.failure_reason',
                'pickup_tasks.completed_odometer',
                'pickup_tasks.start_odometer',
                'pickup_tasks.start_fuel',
                'pickup_tasks.departure_notes',
                'pickup_tasks.receiver_name',
                'pickup_tasks.receiver_role',
                'pickup_tasks.item_condition'
            );
            
        $deliveries = DB::table('delivery_assignments')
            ->join('sales_orders', 'delivery_assignments.sales_order_id', '=', 'sales_orders.id')
            ->leftJoin('vehicles', 'delivery_assignments.vehicle_id', '=', 'vehicles.id')
            ->select(
                'delivery_assignments.id', 
                'sales_orders.so_number as reference_number', 
                DB::raw("COALESCE(delivery_assignments.pickup_name, 'Gudang AQPA') as pickup_name"), 
                DB::raw("COALESCE(delivery_assignments.pickup_location, '-') as pickup_location"), 
                'sales_orders.customer_name as destination', 
                'delivery_assignments.assigned_at', 
                'delivery_assignments.status', 
                DB::raw("'delivery' as task_type"),
                'sales_orders.ordered_quantity as quantity',
                'sales_orders.unit',
                'sales_orders.item_description as item_category',
                'vehicles.plate_number as vehicle_plate_number',
                'vehicles.name as vehicle_name',
                'delivery_assignments.dispatch_date',
                'delivery_assignments.estimated_arrival',
                'delivery_assignments.proof_photo',
                'delivery_assignments.failure_reason',
                'delivery_assignments.completed_odometer',
                'delivery_assignments.start_odometer',
                'delivery_assignments.start_fuel',
                'delivery_assignments.departure_notes',
                'delivery_assignments.receiver_name',
                'delivery_assignments.receiver_role',
                'delivery_assignments.item_condition'
            );

        if ($roleName === 'driver') {
            $pickups->where('driver_id', $user->id);
            $deliveries->where('delivery_assignments.driver_id', $user->id);
        }

        $unionQuery = $pickups->unionAll($deliveries);
        
        $query = DB::query()->fromSub($unionQuery, 'tasks');

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'berlangsung') {
                $query->whereIn('status', ['on_route', 'arrived']);
            } elseif ($request->status === 'menunggu') {
                $query->where('status', 'assigned');
            } else {
                $query->where('status', $request->status);
            }
        }

        // Fitur Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('pickup_name', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        // Filter date
        if ($request->filled('date')) {
            $query->whereDate('assigned_at', $request->date);
        }

        // Paginasi: 15 item per halaman
        $tasks = $query->orderBy('assigned_at', 'desc')->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $tasks->items(),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'total' => $tasks->total(),
            ]
        ]);
    }

    /**
     * GET /api/pickup/{id}
     * Menampilkan detail satu tugas
     */
    public function show($id)
    {
        if (!\Illuminate\Support\Str::isUuid($id)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid ID format'], 404);
        }

        $task = PickupTask::with(['driver', 'vehicle', 'shift.expenses', 'items', 'attachments'])->find($id);

        if ($task) {
            $task->task_type = 'pickup';
            return response()->json([
                'status' => 'success',
                'data' => $task
            ]);
        }

        $delivery = \App\Models\DeliveryAssignment::with(['driver', 'vehicle', 'shift.expenses', 'salesOrder.items', 'attachments'])->find($id);

        if ($delivery) {
            $delivery->task_type = 'delivery';
            // Mapping for frontend
            $delivery->reference_number = $delivery->salesOrder->so_number ?? '-';
            $delivery->pickup_name = $delivery->pickup_name ?: 'Gudang AQPA';
            $delivery->pickup_location = $delivery->pickup_location ?: '-';
            $delivery->destination = $delivery->salesOrder->customer_name ?? '-';
            $delivery->quantity = $delivery->salesOrder->ordered_quantity ?? 0;
            $delivery->unit_measure = $delivery->salesOrder->unit ?? '-';
            $delivery->item_description = $delivery->salesOrder->item_description ?? '-';
            $delivery->items = $delivery->salesOrder->items ?? [];

            return response()->json([
                'status' => 'success',
                'data' => $delivery
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Tugas tidak ditemukan'
        ], 404);
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
        $task = PickupTask::find($id);
        $isPickup = true;
        
        if (!$task) {
            $task = \App\Models\DeliveryAssignment::find($id);
            $isPickup = false;
        }

        if (!$task) {
            return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
        }

        $user = Auth::user();
        $roleName = strtolower($user->roleRelation->name ?? $user->role->name ?? '');
        
        if ($roleName === 'driver' && $task->driver_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $newStatus = $request->input('status');
        $updateData = ['status' => $newStatus];

        // 1. Handle scalar operational fields
        $scalarFields = [
            'start_odometer', 'start_fuel', 'departure_notes', 
            'arrival_notes',
            'receiver_name', 'receiver_role', 'item_condition', 
            'completed_odometer', 'failure_reason'
        ];
        foreach ($scalarFields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->input($field);
            }
        }

        // Handle departure_checklist (JSON)
        if ($request->has('departure_checklist')) {
            $checklist = $request->input('departure_checklist');
            $updateData['departure_checklist'] = is_string($checklist) ? json_decode($checklist, true) : $checklist;
        }

        // Handle arrival_checklist (JSON)
        if ($request->has('arrival_checklist')) {
            $checklist = $request->input('arrival_checklist');
            $updateData['arrival_checklist'] = is_string($checklist) ? json_decode($checklist, true) : $checklist;
        }

        // Backward compatibility for proof_photo
        if ($newStatus === 'delivered') {
            if ($request->hasFile('proof_photo')) {
                $updateData['proof_photo'] = $request->file('proof_photo')->store('proofs', 'public');
            } elseif ($request->has('proof_photo')) {
                $updateData['proof_photo'] = $request->input('proof_photo');
            }
        }

        switch ($newStatus) {
            case 'on_route':
                if (!$task->started_at) $updateData['started_at'] = now();
                
                // Auto-generate shift if task doesn't have one
                if (!$task->shift_id) {
                    $shift = \App\Models\Shift::create([
                        'driver_id' => $task->driver_id,
                        'vehicle_id' => $task->vehicle_id ?? null,
                        'work_date' => now()->toDateString(),
                        'check_in_at' => now(),
                        'start_odometer' => $request->input('start_odometer', 0),
                        'source' => 'task',
                        'task_reference' => $task->reference_number ?? ('TASK-' . $task->id),
                    ]);
                    $updateData['shift_id'] = $shift->id;
                }
                break;
            case 'arrived':
                if ($isPickup && !$task->arrived_at) $updateData['arrived_at'] = now();
                break;
            case 'delivered':
            case 'failed':
            case 'cancelled':
                if (!$task->completed_at) $updateData['completed_at'] = now();
                
                // Auto check-out shift
                if ($task->shift_id && in_array($newStatus, ['delivered', 'failed'])) {
                    $completedOdo = $request->input('completed_odometer');
                    $shiftUpdate = ['check_out_at' => now()];
                    if ($completedOdo) {
                        $shiftUpdate['end_odometer'] = $completedOdo;
                    }
                    \App\Models\Shift::where('id', $task->shift_id)->update($shiftUpdate);
                }
                break;
        }

        $task->update($updateData);

        // 2. Handle Attachments (Polymorphic) – legacy single-file categories
        $attachmentCategories = [
            'keberangkatan_depan', 'keberangkatan_muatan', 'keberangkatan_surat',
            'tiba_lokasi', 'tiba_gudang',
            'serah_terima_barang', 'serah_terima_penerima', 'serah_terima_surat', 'serah_terima_ttd'
        ];
        
        foreach ($attachmentCategories as $category) {
            if ($request->hasFile($category)) {
                $filePath = $request->file($category)->store("task_attachments/{$id}", 'public');
                $task->attachments()->create([
                    'category' => $category,
                    'file_path' => $filePath,
                ]);
            }
        }

        // 3. Handle dynamic attachments[] array
        if ($request->hasFile('attachments')) {
            $categoryMap = [
                'on_route' => 'bukti_keberangkatan',
                'arrived' => 'bukti_kedatangan',
                'delivered' => 'bukti_serah_terima'
            ];
            $attCategory = $request->input('attachment_category', $categoryMap[$newStatus] ?? 'attachments');

            $files = $request->file('attachments');
            foreach ($files as $file) {
                $filePath = $file->store("task_attachments/{$id}", 'public');
                $task->attachments()->create([
                    'category' => $attCategory,
                    'file_path' => $filePath,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Status tugas berhasil diperbarui menjadi {$newStatus}.",
            'data' => $task
        ]);
    }
    /**
     * GET /api/driver/dashboard
     * Mengambil ringkasan dashboard driver (real-time)
     */
    public function dashboardSummary(Request $request)
    {
        $user = Auth::user();
        
        $today = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        $pickups = DB::table('pickup_tasks')
            ->leftJoin('vehicles', 'pickup_tasks.vehicle_id', '=', 'vehicles.id')
            ->select(
                'pickup_tasks.id', 
                'pickup_tasks.reference_number', 
                'pickup_tasks.pickup_name', 
                'pickup_tasks.pickup_location', 
                'pickup_tasks.destination', 
                'pickup_tasks.assigned_at', 
                'pickup_tasks.status', 
                DB::raw("'pickup' as task_type"),
                'pickup_tasks.quantity',
                'pickup_tasks.unit',
                DB::raw("NULL as item_category"),
                'vehicles.plate_number as vehicle_plate_number',
                'vehicles.name as vehicle_name',
                'pickup_tasks.dispatch_date',
                'pickup_tasks.estimated_arrival',
                'pickup_tasks.proof_photo',
                'pickup_tasks.failure_reason',
                'pickup_tasks.completed_odometer',
                'pickup_tasks.start_odometer',
                'pickup_tasks.start_fuel',
                'pickup_tasks.departure_notes',
                'pickup_tasks.receiver_name',
                'pickup_tasks.receiver_role',
                'pickup_tasks.item_condition'
            )
            ->where('pickup_tasks.driver_id', $user->id);

        $deliveries = DB::table('delivery_assignments')
            ->join('sales_orders', 'delivery_assignments.sales_order_id', '=', 'sales_orders.id')
            ->leftJoin('vehicles', 'delivery_assignments.vehicle_id', '=', 'vehicles.id')
            ->select(
                'delivery_assignments.id', 
                'sales_orders.so_number as reference_number', 
                DB::raw("COALESCE(delivery_assignments.pickup_name, 'Gudang AQPA') as pickup_name"), 
                DB::raw("COALESCE(delivery_assignments.pickup_location, '-') as pickup_location"), 
                'sales_orders.customer_name as destination', 
                'delivery_assignments.assigned_at', 
                'delivery_assignments.status', 
                DB::raw("'delivery' as task_type"),
                'sales_orders.ordered_quantity as quantity',
                'sales_orders.unit',
                'sales_orders.item_description as item_category',
                'vehicles.plate_number as vehicle_plate_number',
                'vehicles.name as vehicle_name',
                'delivery_assignments.dispatch_date',
                'delivery_assignments.estimated_arrival',
                'delivery_assignments.proof_photo',
                'delivery_assignments.failure_reason',
                'delivery_assignments.completed_odometer',
                'delivery_assignments.start_odometer',
                'delivery_assignments.start_fuel',
                'delivery_assignments.departure_notes',
                'delivery_assignments.receiver_name',
                'delivery_assignments.receiver_role',
                'delivery_assignments.item_condition'
            )
            ->where('delivery_assignments.driver_id', $user->id);

        $unionQuery = $pickups->unionAll($deliveries);
        
        // Query untuk HARI INI
        $todayQuery = DB::query()->fromSub($unionQuery, 'tasks')
            ->whereBetween('assigned_at', [$today, $endOfDay]);



        // 1. Total Trip Hari Ini
        $totalTrips = (clone $todayQuery)->count();

        // 2. Selesai (Hari ini)
        $completedTrips = (clone $todayQuery)->where('status', 'delivered')->count();

        // 3. Berlangsung (Hari ini)
        $inProgressTrips = (clone $todayQuery)->whereIn('status', ['assigned', 'on_route', 'arrived'])->count();

        // 4. Jarak Tempuh
        $distanceKm = 0;

        // 5. List Trip Hari ini
        $todayTasks = (clone $todayQuery)
            ->orderByRaw("CASE 
                WHEN status IN ('on_route', 'arrived') THEN 1 
                WHEN status = 'assigned' THEN 2 
                ELSE 3 END")
            ->orderBy('assigned_at', 'desc')
            ->limit(5)
            ->get();

        // 6. Active Task (Tanpa dibatasi hari ini)
        $activeTask = DB::query()->fromSub($unionQuery, 'tasks')
            ->whereIn('status', ['on_route', 'arrived'])
            ->orderBy('assigned_at', 'desc')
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'today_trips_count' => $totalTrips,
                'completed_trips_count' => $completedTrips,
                'in_progress_trips_count' => $inProgressTrips,
                'distance_today' => $distanceKm,
                'today_tasks' => $todayTasks,
                'active_task' => $activeTask
            ]
        ]);
    }
}
