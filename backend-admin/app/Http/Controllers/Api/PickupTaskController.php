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
        
        $statusPickupSelect = $roleName === 'driver' 
            ? DB::raw("CASE WHEN (pickup_tasks.driver_id IS NULL OR (pickup_tasks.driver_id != '" . $user->id . "' AND pickup_tasks.co_driver_id != '" . $user->id . "') OR (pickup_tasks.dispatch_date < CURRENT_DATE AND pickup_tasks.status NOT IN ('completed', 'delivered') AND COALESCE(pickup_tasks.is_out_of_city, false) = false)) THEN 'Tidak Terkirim' ELSE pickup_tasks.status END as status")
            : 'pickup_tasks.status';

        $statusDeliverySelect = $roleName === 'driver'
            ? DB::raw("CASE WHEN (delivery_assignments.driver_id IS NULL OR (delivery_assignments.driver_id != '" . $user->id . "' AND delivery_assignments.co_driver_id != '" . $user->id . "') OR (delivery_assignments.dispatch_date < CURRENT_DATE AND delivery_assignments.status NOT IN ('completed', 'delivered') AND COALESCE(delivery_assignments.is_out_of_city, false) = false)) THEN 'Tidak Terkirim' ELSE delivery_assignments.status END as status")
            : 'delivery_assignments.status';

        $pickups = DB::table('pickup_tasks')
            ->leftJoin('vehicles', 'pickup_tasks.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('users as driver', 'pickup_tasks.driver_id', '=', 'driver.id')
            ->leftJoin('users as co_driver', 'pickup_tasks.co_driver_id', '=', 'co_driver.id')
            ->select(
                'pickup_tasks.id', 
                'pickup_tasks.reference_number', 
                'pickup_tasks.pickup_name', 
                'pickup_tasks.pickup_location', 
                'pickup_tasks.destination', 
                'pickup_tasks.assigned_at', 
                $statusPickupSelect, 
                DB::raw("'pickup' as task_type"),
                'pickup_tasks.quantity',
                'pickup_tasks.unit',
                DB::raw("NULL as item_category"),
                'vehicles.plate_number as vehicle_plate_number',
                'vehicles.name as vehicle_name',
                'driver.full_name as driver_name',
                'co_driver.full_name as co_driver_name',
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
            ->leftJoin('users as driver', 'delivery_assignments.driver_id', '=', 'driver.id')
            ->leftJoin('users as co_driver', 'delivery_assignments.co_driver_id', '=', 'co_driver.id')
            ->select(
                'delivery_assignments.id', 
                'sales_orders.so_number as reference_number', 
                DB::raw("COALESCE(delivery_assignments.pickup_name, 'Gudang AQPA') as pickup_name"), 
                DB::raw("COALESCE(delivery_assignments.pickup_location, '-') as pickup_location"), 
                'sales_orders.customer_name as destination', 
                'delivery_assignments.assigned_at', 
                $statusDeliverySelect, 
                DB::raw("'delivery' as task_type"),
                'sales_orders.ordered_quantity as quantity',
                'sales_orders.unit',
                'sales_orders.item_description as item_category',
                'vehicles.plate_number as vehicle_plate_number',
                'vehicles.name as vehicle_name',
                'driver.full_name as driver_name',
                'co_driver.full_name as co_driver_name',
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
            $pickups->where(function ($q) use ($user) {
                $q->where('pickup_tasks.driver_id', $user->id)
                  ->orWhere('pickup_tasks.co_driver_id', $user->id)
                  ->orWhereExists(function ($query) use ($user) {
                      $query->select(DB::raw(1))
                            ->from('tasks_manifest_history')
                            ->join('task_manifests', 'tasks_manifest_history.manifest_id', '=', 'task_manifests.id')
                            ->whereColumn('tasks_manifest_history.task_id', 'pickup_tasks.id')
                            ->where('tasks_manifest_history.task_type', 'pickup')
                            ->where(function ($sub) use ($user) {
                                $sub->where('task_manifests.driver_id', $user->id)
                                    ->orWhere('task_manifests.co_driver_id', $user->id);
                            });
                  });
            });
            $deliveries->where(function ($q) use ($user) {
                $q->where('delivery_assignments.driver_id', $user->id)
                  ->orWhere('delivery_assignments.co_driver_id', $user->id)
                  ->orWhereExists(function ($query) use ($user) {
                      $query->select(DB::raw(1))
                            ->from('tasks_manifest_history')
                            ->join('task_manifests', 'tasks_manifest_history.manifest_id', '=', 'task_manifests.id')
                            ->whereColumn('tasks_manifest_history.task_id', 'delivery_assignments.id')
                            ->where('tasks_manifest_history.task_type', 'delivery')
                            ->where(function ($sub) use ($user) {
                                $sub->where('task_manifests.driver_id', $user->id)
                                    ->orWhere('task_manifests.co_driver_id', $user->id);
                            });
                  });
            });
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

        $user = Auth::user();
        $roleName = strtolower($user->roleRelation->name ?? $user->role->name ?? '');

        $task = PickupTask::with(['driver', 'coDriver', 'vehicle', 'shift.expenses', 'items', 'attachments.uploader'])->find($id);

        if ($task) {
            $task->task_type = 'pickup';
            if ($roleName === 'driver' && ($task->driver_id === null || ($task->driver_id != $user->id && $task->co_driver_id != $user->id))) {
                $task->status = 'Tidak Terkirim';
            }
            return response()->json([
                'status' => 'success',
                'data' => $task
            ]);
        }

        $delivery = \App\Models\DeliveryAssignment::with(['driver', 'coDriver', 'vehicle', 'shift.expenses', 'salesOrder.items', 'attachments.uploader'])->find($id);

        if ($delivery) {
            $delivery->task_type = 'delivery';
            
            if ($roleName === 'driver' && ($delivery->driver_id === null || ($delivery->driver_id != $user->id && $delivery->co_driver_id != $user->id))) {
                $delivery->status = 'Tidak Terkirim';
            }
            
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
        $isPickup = true;
        
        // Prevent searching for non-uuid strings which crashes Postgres
        if (!\Illuminate\Support\Str::isUuid($id)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid ID format'], 400);
        }

        $task = PickupTask::find($id);
        
        if (!$task) {
            $task = \App\Models\DeliveryAssignment::find($id);
            $isPickup = false;
        }

        if (!$task) {
            return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
        }

        $user = Auth::user();
        $roleName = strtolower($user->roleRelation->name ?? $user->role->name ?? '');
        
        if ($roleName === 'driver' && $task->driver_id !== $user->id && $task->co_driver_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $newStatus = $request->input('status');

        if ($newStatus === 'arrived' && filter_var($request->input('has_issue'), FILTER_VALIDATE_BOOLEAN)) {
            $newStatus = 'pending';
        }

        $updateData = ['status' => $newStatus];

        $scalarFields = [
            'departure_notes', 'arrival_notes',
            'receiver_name', 'receiver_role', 'item_condition', 
            'failure_reason', 'completed_odometer', 'start_odometer', 'start_fuel'
        ];
        foreach ($scalarFields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->input($field);
            }
        }

        if ($request->has('has_issue')) {
            $updateData['has_issue'] = filter_var($request->input('has_issue'), FILTER_VALIDATE_BOOLEAN);
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
                $file = $request->file('proof_photo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('uploads/proofs', $fileName, 'public');
                $updateData['proof_photo'] = "storage/" . $path;
            } elseif ($request->has('proof_photo')) {
                $updateData['proof_photo'] = $request->input('proof_photo');
            }
        }

        switch ($newStatus) {
            case 'on_route':
                if (!$task->started_at) $updateData['started_at'] = now();
                
                // Link to active manual shift if not linked
                if (!$task->shift_id) {
                    $activeShift = \App\Models\Shift::where('driver_id', $task->driver_id)
                                    ->whereNull('check_out_at')
                                    ->first();
                    if ($activeShift) {
                        $updateData['shift_id'] = $activeShift->id;
                    }
                }
                break;
            case 'pending':
            case 'arrived':
                if ($isPickup && !$task->arrived_at) $updateData['arrived_at'] = now();
                break;
            case 'delivered':
            case 'failed':
            case 'cancelled':
                if (!$task->completed_at) $updateData['completed_at'] = now();
                break;
        }

        $task->update($updateData);

        if ($task->manifest_id) {
            \DB::table('tasks_manifest_history')
                ->where('manifest_id', $task->manifest_id)
                ->where('task_id', $task->id)
                ->update(['status' => $newStatus, 'updated_at' => now()]);
        }

        // 2. Handle Attachments (Polymorphic) – legacy single-file categories
        $attachmentCategories = [
            'keberangkatan_depan', 'keberangkatan_muatan', 'keberangkatan_surat',
            'tiba_lokasi', 'tiba_gudang',
            'serah_terima_barang', 'serah_terima_penerima', 'serah_terima_surat', 'serah_terima_ttd'
        ];
        
        foreach ($attachmentCategories as $category) {
            if ($request->hasFile($category)) {
                $file = $request->file($category);
                $fileName = time() . '_' . $category . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $file->getClientOriginalName());
                $path = "task-driver/{$id}/admin-docs/{$fileName}";
                
                $minio = new \App\Services\Storage\MinioService();
                try {
                    $minio->getClient()->putObject([
                        'Bucket' => 'driver-apps',
                        'Key'    => $path,
                        'SourceFile' => $file->getRealPath(),
                        'ContentType' => $file->getMimeType(),
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("MinIO Upload Error: " . $e->getMessage());
                    return response()->json(['status' => 'error', 'message' => 'Gagal upload file lampiran: ' . $e->getMessage()], 500);
                }
                
                $task->attachments()->create([
                    'category' => $category,
                    'file_path' => $path,
                    'uploaded_by' => Auth::id(),
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
            foreach ($files as $index => $file) {
                $fileName = time() . '_' . $index . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $file->getClientOriginalName());
                $path = "task-driver/{$id}/admin-docs/{$fileName}";

                $minio = new \App\Services\Storage\MinioService();
                try {
                    $minio->getClient()->putObject([
                        'Bucket' => 'driver-apps',
                        'Key'    => $path,
                        'SourceFile' => $file->getRealPath(),
                        'ContentType' => $file->getMimeType(),
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("MinIO Upload Error: " . $e->getMessage());
                    return response()->json(['status' => 'error', 'message' => 'Gagal upload file attachments: ' . $e->getMessage()], 500);
                }

                $task->attachments()->create([
                    'category' => $attCategory,
                    'file_path' => $path,
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Status tugas berhasil diperbarui menjadi {$newStatus}.",
            'data' => $task
        ]);
    }
}
