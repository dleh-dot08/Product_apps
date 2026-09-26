<?php

namespace App\Http\Controllers;

use App\Models\PickupTask;
use App\Models\DeliveryAssignment;
use App\Models\SalesOrder;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PickupTaskController extends Controller
{
    private function logHistory($task, $type, $notes = null)
    {
        \App\Models\TaskHistory::create([
            'task_id' => $task->id,
            'task_type' => $type,
            'driver_id' => $task->driver_id ?? null,
            'status' => $task->status ?? 'unknown',
            'notes' => $notes,
            'recorded_by' => auth()->id()
        ]);
    }

    private function generateReferenceNumber($type)
    {
        $prefix = $type === 'delivery' ? 'DLV' : 'PCK';
        $dateStr = now()->format('dmy'); // DDMMYY
        
        $pattern = $prefix . '-' . $dateStr . '-%';
        
        if ($type === 'delivery') {
            $lastOrder = \App\Models\SalesOrder::where('so_number', 'like', $pattern)
                ->orderBy('so_number', 'desc')
                ->first();
                
            $lastNumber = $lastOrder ? $lastOrder->so_number : null;
        } else {
            $lastTask = \App\Models\PickupTask::where('reference_number', 'like', $pattern)
                ->orderBy('reference_number', 'desc')
                ->first();
                
            $lastNumber = $lastTask ? $lastTask->reference_number : null;
        }

        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $sequence = intval(end($parts)) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . '-' . $dateStr . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $pickupQuery = PickupTask::with(['driver', 'vehicle', 'assignedBy', 'items'])->latest();
        $deliveryQuery = DeliveryAssignment::with(['driver', 'vehicle', 'assigner', 'salesOrder', 'salesOrder.items'])->latest('assigned_at');
        
        if (strtolower($user->role) === 'driver') {
            $pickupQuery->where('driver_id', $user->id);
            $deliveryQuery->where('driver_id', $user->id);
        }

        // --- FILTER LOGIC ---
        $search = $request->get('search');
        $filterStatus = $request->get('status');
        $filterDriver = $request->get('driver_id');
        $filterType = $request->get('task_type'); // 'pickup', 'delivery', or empty for both

        // Search text (Reference Number, Item, Destination)
        if ($search) {
            $pickupQuery->where(function($q) use ($search) {
                $q->where('reference_number', 'ILIKE', "%{$search}%")
                  ->orWhere('pickup_name', 'ILIKE', "%{$search}%")
                  ->orWhere('item_description', 'ILIKE', "%{$search}%");
            });

            $deliveryQuery->whereHas('salesOrder', function($q) use ($search) {
                $q->where('so_number', 'ILIKE', "%{$search}%")
                  ->orWhere('customer_name', 'ILIKE', "%{$search}%")
                  ->orWhere('item_description', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by Status
        if ($filterStatus) {
            $pickupQuery->where('status', $filterStatus);
            $deliveryQuery->where('status', $filterStatus);
        }

        // Filter by Driver
        if ($filterDriver) {
            $pickupQuery->where('driver_id', $filterDriver);
            $deliveryQuery->where('driver_id', $filterDriver);
        }

        $pickups = collect();
        if (!$filterType || $filterType === 'pickup') {
            $pickups = $pickupQuery->get()->map(function($task) {
                $task->task_type = 'pickup';
                $task->sort_date = $task->created_at;
                return $task;
            });
        }

        $deliveries = collect();
        if (!$filterType || $filterType === 'delivery') {
            $deliveries = $deliveryQuery->get()->map(function($task) {
                $task->task_type = 'delivery';
                $task->sort_date = $task->assigned_at;
                return $task;
            });
        }

        // Gabungkan koleksi dan urutkan berdasarkan tanggal descending
        $allTasks = $pickups->concat($deliveries)->sortByDesc('sort_date')->values();

        $totalTasksCount = $allTasks->count();
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $pagedTasks = $allTasks->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        $tasks = new \Illuminate\Pagination\LengthAwarePaginator($pagedTasks, $totalTasksCount, $perPage, $currentPage, [
            'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        $drivers = User::where('role', 'driver')->get();
        $vehicles = Vehicle::where('active', true)->get();

        $totalTasks = $totalTasksCount;
        $assignedTasks = $allTasks->where('status', 'assigned')->count();
        $onRouteTasks = $allTasks->where('status', 'on_route')->count();
        $completedTasks = $allTasks->where('status', 'delivered')->count();

        return view('pickup-tasks.list-tugas.index', compact('tasks', 'drivers', 'vehicles', 'totalTasks', 'assignedTasks', 'onRouteTasks', 'completedTasks'));
    }

    public function listPenugasan(Request $request)
    {
        $stats = $this->getTaskStatistics();
        
        $user = auth()->user();
        
        $query = \App\Models\TaskManifest::with(['driver', 'coDriver', 'vehicle', 'pickupTasks', 'deliveryAssignments.salesOrder'])->latest('dispatch_date');
        
        if ($user && strtolower($user->role) === 'driver') {
            $query->where('driver_id', $user->id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('manifest_number', 'like', "%{$search}%")
                  ->orWhereHas('driver', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('dispatch_date', $request->date);
        }

        if ($request->filled('driver_id')) {
            $query->where(function($q) use ($request) {
                $q->where('driver_id', $request->driver_id)
                  ->orWhere('co_driver_id', $request->driver_id);
            });
        }
        
        $assignmentsPaginated = $query->paginate(10)->withQueryString();
        
        // Format to match view expectations if necessary
        $assignmentsPaginated->getCollection()->transform(function($manifest) {
            return (object) [
                'id' => $manifest->id,
                'no_do' => $manifest->manifest_number,
                'date' => \Carbon\Carbon::parse($manifest->dispatch_date)->format('Y-m-d'),
                'driver' => $manifest->driver,
                'coDriver' => $manifest->coDriver,
                'vehicle' => $manifest->vehicle,
                'task_count' => $manifest->pickupTasks->count() + $manifest->deliveryAssignments->count(),
                'status' => $manifest->status,
                'is_out_of_city' => $manifest->is_out_of_city,
                'estimated_arrival' => $manifest->estimated_arrival,
                'manifest' => $manifest
            ];
        });
        
        $drivers = User::where('role', 'driver')->get();
        $vehicles = Vehicle::where('active', true)->get();
        
        return view('pickup-tasks.penugasan.index', array_merge($stats, [
            'assignments' => $assignmentsPaginated,
            'drivers' => $drivers,
            'vehicles' => $vehicles
        ]));
    }

    public function monitoring(Request $request)
    {
        $stats = $this->getTaskStatistics();
        return view('pickup-tasks.monitoring.index', $stats);
    }

    public function historyDo(Request $request)
    {
        $stats = $this->getTaskStatistics();
        
        $user = auth()->user();
        
        $query = \App\Models\TaskManifest::with(['driver', 'coDriver', 'vehicle', 'pickupTasks.history', 'deliveryAssignments.history'])
            ->latest('updated_at');
            
        if ($user && strtolower($user->role) === 'driver') {
            $query->where('driver_id', $user->id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('manifest_number', 'like', "%{$search}%")
                  ->orWhereHas('driver', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('dispatch_date', $request->date);
        }

        if ($request->filled('driver_id')) {
            $query->where(function($q) use ($request) {
                $q->where('driver_id', $request->driver_id)
                  ->orWhere('co_driver_id', $request->driver_id);
            });
        }
        
        $assignmentsPaginated = $query->paginate(10)->withQueryString();
        
        $assignmentsPaginated->getCollection()->transform(function($manifest) {
            return (object) [
                'id' => $manifest->id,
                'no_do' => $manifest->manifest_number,
                'date' => \Carbon\Carbon::parse($manifest->dispatch_date)->format('Y-m-d'),
                'driver' => $manifest->driver,
                'coDriver' => $manifest->coDriver,
                'vehicle' => $manifest->vehicle,
                'task_count' => $manifest->pickupTasks->count() + $manifest->deliveryAssignments->count(),
                'status' => $manifest->status,
                'manifest' => $manifest
            ];
        });
        
        $drivers = User::where('role', 'driver')->get();
        $vehicles = Vehicle::where('active', true)->get();
        
        return view('pickup-tasks.history-do.index', array_merge($stats, [
            'assignments' => $assignmentsPaginated,
            'drivers' => $drivers,
            'vehicles' => $vehicles
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_type' => 'required|in:pickup,delivery',
            'pickup_reference' => 'nullable|string',
            'delivery_so_number' => 'nullable|string',
            'driver_id' => 'nullable|uuid|exists:users,id',
            'co_driver_id' => 'nullable|uuid|exists:users,id',
            'vehicle_id' => 'nullable|uuid|exists:vehicles,id',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'manifest_id' => 'nullable|uuid|exists:task_manifests,id',
        ]);

        $manifest = null;
        if ($request->manifest_id) {
            $manifest = \App\Models\TaskManifest::find($request->manifest_id);
        }

        $driver_id = $manifest ? $manifest->driver_id : $request->driver_id;
        $co_driver_id = $manifest ? $manifest->co_driver_id : $request->co_driver_id;
        $vehicle_id = $manifest ? $manifest->vehicle_id : $request->vehicle_id;
        $assigned_status = ($driver_id && $vehicle_id) ? 'assigned' : 'draft';

        if ($request->task_type === 'pickup') {
            $request->validate([
                'pickup_name' => 'required|string',
                'pickup_location' => 'required|string',
                'pickup_destination' => 'nullable|string',
            ]);

            $referenceNumber = $request->pickup_reference;
            
            $totalQty = 0;
            $totalLine = 0;
            $itemDescriptions = [];
            $sourceItems = [];
            
            foreach ($request->items as $item) {
                $qty = floatval($item['quantity'] ?? 0);
                $price = floatval($item['unit_price'] ?? 0);
                $totalQty += $qty;
                if ($qty > 0 && $price > 0) $totalLine += ($qty * $price);
                $itemDescriptions[] = $item['item_description'] . ' (' . $qty . ' ' . ($item['unit'] ?? '') . ')';
                $sourceItems[] = [
                    'item_number' => $item['item_number'] ?? null,
                    'item_description' => $item['item_description'],
                    'quantity' => $qty,
                    'unit' => $item['unit'] ?? null,
                    'unit_price' => $price,
                    'line_total' => ($qty > 0 && $price > 0) ? ($qty * $price) : 0,
                ];
            }

            $jsonData = json_encode([
                'summary' => implode(', ', $itemDescriptions),
                'items' => $sourceItems
            ]);

            $pickupTask = PickupTask::create([
                'manifest_id' => $request->manifest_id,
                'reference_number' => $referenceNumber,
                'driver_id' => $driver_id,
                'co_driver_id' => $co_driver_id,
                'vehicle_id' => $vehicle_id,
                'assigned_by' => Auth::id(),
                'status' => $assigned_status,
                'priority' => $request->priority,
                'pickup_name' => $request->pickup_name,
                'pickup_pic_name' => $request->pickup_pic_name,
                'pickup_location' => $request->pickup_location,
                'pickup_point' => $request->pickup_point,
                'destination_name' => $request->destination_name,
                'destination_pic_name' => $request->destination_pic_name,
                'destination' => $request->pickup_destination,
                'destination_point' => $request->destination_point,
                'item_number' => 'MULTIPLE',
                'item_description' => $jsonData,
                'quantity' => $totalQty > 0 ? $totalQty : null,
                'transaction_source' => 'manual',
                'line_total' => $totalLine > 0 ? $totalLine : null,
                'dispatch_date' => $request->dispatch_date,
                'estimated_arrival' => $request->estimated_arrival,
                'is_out_of_city' => $request->boolean('is_out_of_city'),
            ]);

            $this->logHistory($pickupTask, 'pickup', 'Tugas pickup dibuat');

            // Save items to task_items table
            foreach ($sourceItems as $item) {
                $pickupTask->items()->create($item);
            }

            // Send Push Notification
            if ($request->driver_id) {
                $this->sendPushNotification($request->driver_id, 'Tugas Pickup Baru', 'Anda mendapatkan tugas pickup baru dari ' . $request->pickup_name);
            }

        } else {
            // Delivery
            $request->validate([
                'customer_name' => 'required|string',
                'delivery_address' => 'required|string',
                'delivery_pickup_name' => 'required|string',
                'delivery_pickup_location' => 'required|string',
            ]);

            $soNumber = $request->delivery_so_number;

            $totalQty = 0;
            $itemDescriptions = [];
            $sourceItems = [];
            
            foreach ($request->items as $index => $item) {
                $qty = floatval($item['quantity'] ?? 0);
                $price = floatval($item['unit_price'] ?? 0);
                $totalQty += $qty;
                $itemDescriptions[] = $item['item_description'] . ' (' . $qty . ' ' . ($item['unit'] ?? '') . ')';
                $sourceItems[] = [
                    'item_number' => $item['item_number'] ?? null,
                    'item_description' => $item['item_description'],
                    'quantity' => $qty,
                    'unit' => $item['unit'] ?? null,
                    'unit_price' => $price,
                    'line_total' => ($qty > 0 && $price > 0) ? ($qty * $price) : 0,
                ];
            }

            $externalKey = hash('sha256', implode('|', ['manual-delivery', $soNumber]));
            
            $sourceData = [
                'transaction_source' => 'manual',
                'no_pengiriman' => $soNumber,
                'no_do' => $soNumber,
                'no_so' => $soNumber,
                'nama_pelanggan' => $request->customer_name,
                'address' => $request->delivery_address,
                'items' => $sourceItems,
            ];

            // Buat Sales Order (hanya 1 untuk semua item)
            $salesOrder = SalesOrder::updateOrCreate(
                ['external_key' => $externalKey],
                [
                    'so_number' => $soNumber,
                    'so_date' => now()->toDateString(),
                    'estimated_delivery_date' => $request->estimated_arrival ? date('Y-m-d', strtotime($request->estimated_arrival)) : null,
                    'customer_name' => $request->customer_name,
                    'item_description' => implode(', ', $itemDescriptions),
                    'ordered_quantity' => $totalQty,
                    'remaining_quantity' => $totalQty,
                    'status' => 'pending',
                    'source_data' => $sourceData, // array otomatis jadi json di model
                ]
            );

            // Re-sync items in task_items
            $salesOrder->items()->delete(); // Remove old items if updating
            foreach ($sourceItems as $item) {
                $salesOrder->items()->create($item);
            }

            // Hapus assignment lama (jika ada)
            DeliveryAssignment::where('sales_order_id', $salesOrder->id)->delete();

            // Buat 1 Delivery Assignment
            $deliveryTask = DeliveryAssignment::create([
                'manifest_id' => $request->manifest_id,
                'sales_order_id' => $salesOrder->id,
                'driver_id' => $driver_id,
                'co_driver_id' => $co_driver_id,
                'vehicle_id' => $vehicle_id,
                'assigned_by' => Auth::id(),
                'status' => $assigned_status,
                'priority' => $request->priority,
                'pickup_name' => $request->delivery_pickup_name,
                'delivery_sender_pic' => $request->delivery_sender_pic,
                'pickup_location' => $request->delivery_pickup_location,
                'delivery_origin_point' => $request->delivery_origin_point,
                'delivery_receiver_pic' => $request->delivery_receiver_pic,
                'delivery_target_point' => $request->delivery_target_point,
                'assigned_at' => ($request->driver_id && $request->vehicle_id) ? now() : null,
                'dispatch_date' => $request->dispatch_date,
                'estimated_arrival' => $request->estimated_arrival,
                'is_out_of_city' => $request->boolean('is_out_of_city'),
            ]);

            $this->logHistory($deliveryTask, 'delivery', 'Tugas delivery dibuat');

            // Send Push Notification
            if ($request->driver_id) {
                $this->sendPushNotification($request->driver_id, 'Tugas Delivery Baru', 'Anda mendapatkan tugas delivery baru untuk dikirim ke ' . $request->customer_name);
            }
        }

        return redirect()->back()->with('success', 'Tugas berhasil dibuat dengan ' . count($request->items) . ' barang.');
    }

    protected function sendPushNotification($userId, $title, $body)
    {
        $user = \App\Models\User::find($userId);
        if ($user && $user->expo_push_token) {
            \Illuminate\Support\Facades\Http::post('https://exp.host/--/api/v2/push/send', [
                'to' => $user->expo_push_token,
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ]);
        }
    }

    public function show(Request $request, $id)
    {
        $type = $request->query('task_type', 'pickup');

        if ($type === 'pickup') {
            $task = PickupTask::with(['driver', 'vehicle', 'assignedBy', 'attachments', 'shift.expenses', 'items'])->findOrFail($id);
            $task->task_type = 'pickup';
        } else {
            $task = DeliveryAssignment::with(['driver', 'vehicle', 'assigner', 'salesOrder.items', 'attachments', 'shift.expenses'])->findOrFail($id);
            $task->task_type = 'delivery';
        }

        $histories = \App\Models\TaskHistory::with(['driver', 'coDriver', 'vehicle', 'recorder'])
            ->where('task_id', $id)
            ->where('task_type', $type)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pickup-tasks.list-tugas.show', compact('task', 'histories'));
    }

    public function editDetail(Request $request, $id)
    {
        $type = $request->query('task_type', 'pickup');

        if ($type === 'pickup') {
            $task = PickupTask::with(['driver', 'vehicle', 'assignedBy', 'items'])->findOrFail($id);
            $task->task_type = 'pickup';
        } else {
            $task = DeliveryAssignment::with(['driver', 'vehicle', 'assigner', 'salesOrder.items'])->findOrFail($id);
            $task->task_type = 'delivery';
        }

        if ($task->status !== 'assigned') {
            return redirect()->route('pickup-tasks.index')->with('error', 'Tugas yang sudah berjalan tidak dapat diedit.');
        }

        $drivers = User::where('role', 'driver')->get();
        $vehicles = Vehicle::where('active', true)->get();

        return view('pickup-tasks.edit', compact('task', 'drivers', 'vehicles'));
    }

    public function updateDetail(Request $request, $id)
    {
        $type = $request->query('task_type', 'pickup');

        $request->validate([
            'driver_id' => 'nullable|exists:users,id',
            'co_driver_id' => 'nullable|exists:users,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string',
        ]);

        if ($type === 'pickup') {
            $task = PickupTask::findOrFail($id);
            if ($task->status !== 'assigned') {
                return redirect()->route('pickup-tasks.index')->with('error', 'Tidak dapat mengedit tugas yang sedang berjalan.');
            }
            
            $request->validate([
                'pickup_name' => 'required|string',
                'pickup_location' => 'required|string',
                'pickup_destination' => 'nullable|string',
            ]);

            $totalQty = 0;
            $totalLine = 0;
            $itemDescriptions = [];
            $sourceItems = [];
            
            foreach ($request->items as $item) {
                $qty = floatval($item['quantity'] ?? 0);
                $price = floatval($item['unit_price'] ?? 0);
                $totalQty += $qty;
                $totalLine += ($qty > 0 && $price > 0) ? ($qty * $price) : 0;
                $itemDescriptions[] = $item['item_description'] . ' (' . $qty . ' ' . ($item['unit'] ?? '') . ')';
                $sourceItems[] = [
                    'item_number' => $item['item_number'] ?? null,
                    'item_description' => $item['item_description'],
                    'quantity' => $qty,
                    'unit' => $item['unit'] ?? null,
                    'unit_price' => $price,
                    'line_total' => ($qty > 0 && $price > 0) ? ($qty * $price) : 0,
                ];
            }

            $jsonData = json_encode([
                'summary' => implode(', ', $itemDescriptions),
                'items' => $sourceItems
            ]);

            $task->fill([
                'reference_number' => $request->pickup_reference ?: $task->reference_number,
                'driver_id' => $request->has('driver_id') ? $request->driver_id : $task->driver_id,
                'co_driver_id' => $request->has('co_driver_id') ? $request->co_driver_id : $task->co_driver_id,
                'vehicle_id' => $request->has('vehicle_id') ? $request->vehicle_id : $task->vehicle_id,
                'priority' => $request->priority,
                'pickup_name' => $request->pickup_name,
                'pickup_pic_name' => $request->pickup_pic_name,
                'pickup_location' => $request->pickup_location,
                'pickup_point' => $request->pickup_point,
                'destination_name' => $request->destination_name,
                'destination_pic_name' => $request->destination_pic_name,
                'destination' => $request->pickup_destination,
                'destination_point' => $request->destination_point,
                'item_description' => $jsonData,
                'quantity' => $totalQty > 0 ? $totalQty : null,
                'line_total' => $totalLine > 0 ? $totalLine : null,
                'dispatch_date' => $request->dispatch_date,
                'estimated_arrival' => $request->estimated_arrival,
                'is_out_of_city' => $request->boolean('is_out_of_city'),
            ]);
            
            $changes = $task->getDirty();
            $task->save();
            
            $changedFields = [];
            $fieldNames = [
                'reference_number' => 'No Referensi',
                'driver_id' => 'Supir',
                'vehicle_id' => 'Kendaraan',
                'priority' => 'Prioritas',
                'pickup_name' => 'Nama Pickup',
                'pickup_location' => 'Lokasi Pickup',
                'destination_name' => 'Nama Tujuan',
                'destination' => 'Lokasi Tujuan',
                'item_description' => 'Daftar Barang',
                'dispatch_date' => 'Tanggal Penugasan',
                'estimated_arrival' => 'Estimasi Kedatangan',
                'is_out_of_city' => 'Status Luar Kota'
            ];
            foreach ($changes as $key => $val) {
                if (isset($fieldNames[$key])) {
                    $changedFields[] = $fieldNames[$key];
                }
            }
            $notes = count($changedFields) > 0 
                ? 'Memperbarui informasi: ' . implode(', ', $changedFields) 
                : 'Detail tugas diperbarui (atau isi barang diubah)';
            
            $this->logHistory($task, 'pickup', $notes);

            $task->items()->delete();
            foreach ($sourceItems as $item) {
                $task->items()->create($item);
            }

        } else {
            $task = DeliveryAssignment::findOrFail($id);
            if ($task->status !== 'assigned') {
                return redirect()->route('pickup-tasks.index')->with('error', 'Tidak dapat mengedit tugas yang sedang berjalan.');
            }

            $request->validate([
                'customer_name' => 'required|string',
                'delivery_address' => 'required|string',
                'delivery_pickup_name' => 'required|string',
                'delivery_pickup_location' => 'required|string',
            ]);

            $totalQty = 0;
            $itemDescriptions = [];
            $sourceItems = [];
            
            foreach ($request->items as $item) {
                $qty = floatval($item['quantity'] ?? 0);
                $price = floatval($item['unit_price'] ?? 0);
                $totalQty += $qty;
                $itemDescriptions[] = $item['item_description'] . ' (' . $qty . ' ' . ($item['unit'] ?? '') . ')';
                $sourceItems[] = [
                    'item_number' => $item['item_number'] ?? null,
                    'item_description' => $item['item_description'],
                    'quantity' => $qty,
                    'unit' => $item['unit'] ?? null,
                    'unit_price' => $price,
                    'line_total' => ($qty > 0 && $price > 0) ? ($qty * $price) : 0,
                ];
            }

            $task->fill([
                'driver_id' => $request->has('driver_id') ? $request->driver_id : $task->driver_id,
                'co_driver_id' => $request->has('co_driver_id') ? $request->co_driver_id : $task->co_driver_id,
                'vehicle_id' => $request->has('vehicle_id') ? $request->vehicle_id : $task->vehicle_id,
                'priority' => $request->priority,
                'pickup_name' => $request->delivery_pickup_name,
                'delivery_sender_pic' => $request->delivery_sender_pic,
                'pickup_location' => $request->delivery_pickup_location,
                'delivery_origin_point' => $request->delivery_origin_point,
                'delivery_receiver_pic' => $request->delivery_receiver_pic,
                'delivery_target_point' => $request->delivery_target_point,
                'dispatch_date' => $request->dispatch_date,
                'estimated_arrival' => $request->estimated_arrival,
                'is_out_of_city' => $request->boolean('is_out_of_city'),
            ]);

            $changes = $task->getDirty();
            $task->save();
            
            $changedFields = [];
            $fieldNames = [
                'driver_id' => 'Supir',
                'vehicle_id' => 'Kendaraan',
                'priority' => 'Prioritas',
                'pickup_name' => 'Lokasi Pengambilan',
                'pickup_location' => 'Alamat Pengambilan',
                'delivery_target_point' => 'Tujuan',
                'dispatch_date' => 'Tanggal Penugasan',
                'estimated_arrival' => 'Estimasi Kedatangan',
                'is_out_of_city' => 'Status Luar Kota'
            ];
            foreach ($changes as $key => $val) {
                if (isset($fieldNames[$key])) {
                    $changedFields[] = $fieldNames[$key];
                }
            }

            // We also check if Sales Order changed something important like customer name
            $soWillUpdate = false;
            if ($task->salesOrder && $task->salesOrder->customer_name !== $request->customer_name) {
                $changedFields[] = 'Nama Pelanggan';
            }

            $notes = count($changedFields) > 0 
                ? 'Memperbarui informasi: ' . implode(', ', $changedFields) 
                : 'Detail tugas diperbarui (atau isi barang diubah)';

            $this->logHistory($task, 'delivery', $notes);

            $salesOrder = $task->salesOrder;
            if ($salesOrder) {
                $sourceData = is_string($salesOrder->source_data) ? json_decode($salesOrder->source_data, true) : ($salesOrder->source_data ?? []);
                $sourceData['nama_pelanggan'] = $request->customer_name;
                $sourceData['address'] = $request->delivery_address;
                $sourceData['items'] = $sourceItems;

                $salesOrder->update([
                    'so_number' => $request->delivery_so_number ?: $salesOrder->so_number,
                    'customer_name' => $request->customer_name,
                    'item_description' => implode(', ', $itemDescriptions),
                    'ordered_quantity' => $totalQty,
                    'remaining_quantity' => $totalQty,
                    'estimated_delivery_date' => $request->estimated_arrival ? date('Y-m-d', strtotime($request->estimated_arrival)) : null,
                    'source_data' => $sourceData,
                ]);

                $salesOrder->items()->delete();
                foreach ($sourceItems as $item) {
                    $salesOrder->items()->create($item);
                }
            }
        }

        return redirect()->back()->with('success', 'Tugas berhasil diperbarui.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'task_type' => 'required|in:pickup,delivery',
        ]);

        if ($request->task_type === 'pickup') {
            $task = PickupTask::findOrFail($id);
        } else {
            $task = DeliveryAssignment::findOrFail($id);
        }

        $task->update(['status' => $request->status]);
        $this->logHistory($task, $request->task_type, 'Status diubah menjadi ' . $request->status);

        return redirect()->route('pickup-tasks.index')->with('success', 'Status tugas berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        // Parameter di-pass manual karena kita tidak menggunakan Route Model Binding secara spesifik
        $type = $request->query('task_type', 'pickup');

        if ($type === 'pickup') {
            $task = PickupTask::findOrFail($id);
            $task->delete();
        } else {
            $task = DeliveryAssignment::findOrFail($id);
            $salesOrderId = $task->sales_order_id;
            $task->delete();
            // Opsional: hapus juga Sales Order nya jika ini dibuat manual dan tidak punya assignment lain
            if (DeliveryAssignment::where('sales_order_id', $salesOrderId)->count() === 0) {
                SalesOrder::where('id', $salesOrderId)->delete();
            }
        }

        return redirect()->route('pickup-tasks.index')->with('success', 'Tugas berhasil dihapus.');
    }

    /**
     * Upload dokumen pendukung ke MinIO dan simpan ke TaskAttachment
     */
    public function uploadAttachment(Request $request, $id)
    {
        $request->validate([
            'document_file' => 'required|file|max:10240', // max 10MB
            'document_type' => 'required|string|max:100',
        ]);

        // Tentukan task type
        $type = $request->input('task_type', 'pickup');
        if ($type === 'pickup') {
            $task = PickupTask::findOrFail($id);
        } else {
            $task = DeliveryAssignment::findOrFail($id);
        }

        $file = $request->file('document_file');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $file->getClientOriginalName());
        $path = "task-driver/{$id}/admin-docs/{$fileName}";

        // Upload ke MinIO
        $minio = new \App\Services\Storage\MinioService();
        try {
            $minio->getClient()->putObject([
                'Bucket' => 'driver-apps',
                'Key'    => $path,
                'SourceFile' => $file->getRealPath(),
                'ContentType' => $file->getMimeType(),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal upload ke storage: ' . $e->getMessage());
        }

        // Simpan record ke database
        $task->attachments()->create([
            'file_path'     => $path,
            'file_name'     => $file->getClientOriginalName(),
            'document_type' => $request->document_type,
            'category'      => 'dokumen_pendukung',
            'uploaded_by'   => Auth::id(),
        ]);

        return back()->with('success', 'Dokumen berhasil diupload.');
    }
    public function getUnassignedTasks(Request $request)
    {
        $pickups = PickupTask::whereNull('manifest_id')
            ->whereIn('status', ['draft', 'pending'])
            ->get()
            ->map(function($task) {
                $task->task_type = 'pickup';
                return $task;
            });

        $deliveries = DeliveryAssignment::with('salesOrder')
            ->whereNull('manifest_id')
            ->whereIn('status', ['draft', 'pending'])
            ->get()
            ->map(function($task) {
                $task->task_type = 'delivery';
                return $task;
            });

        $tasks = $pickups->concat($deliveries)->sortByDesc('created_at')->values();

        return response()->json($tasks);
    }

    /**
     * API: Get tasks currently assigned to a manifest + unassigned tasks.
     * Returns JSON with { assigned: [...], unassigned: [...] }
     */
    public function getManifestTasks(Request $request, $manifestId)
    {
        $manifest = \App\Models\TaskManifest::findOrFail($manifestId);

        // Tasks currently assigned to this manifest
        $assignedPickups = PickupTask::where('manifest_id', $manifestId)
            ->get()
            ->map(function($task) {
                $task->task_type = 'pickup';
                $task->is_assigned = true;
                return $task;
            });

        $assignedDeliveries = DeliveryAssignment::with('salesOrder')
            ->where('manifest_id', $manifestId)
            ->get()
            ->map(function($task) {
                $task->task_type = 'delivery';
                $task->is_assigned = true;
                return $task;
            });

        $assigned = $assignedPickups->concat($assignedDeliveries)->values();

        // Tasks unassigned (available to add)
        $unassignedPickups = PickupTask::whereNull('manifest_id')
            ->whereIn('status', ['draft', 'pending', 'assigned'])
            ->get()
            ->map(function($task) {
                $task->task_type = 'pickup';
                $task->is_assigned = false;
                return $task;
            });

        $unassignedDeliveries = DeliveryAssignment::with('salesOrder')
            ->whereNull('manifest_id')
            ->whereIn('status', ['draft', 'pending', 'assigned'])
            ->get()
            ->map(function($task) {
                $task->task_type = 'delivery';
                $task->is_assigned = false;
                return $task;
            });

        $unassigned = $unassignedPickups->concat($unassignedDeliveries)->sortByDesc('created_at')->values();

        return response()->json([
            'assigned' => $assigned,
            'unassigned' => $unassigned,
        ]);
    }

    /**
     * Update which tasks are assigned to a manifest (add/remove tasks).
     */
    public function updatePenugasan(Request $request, $manifestId)
    {
        $manifest = \App\Models\TaskManifest::findOrFail($manifestId);

        $request->validate([
            'selected_tasks' => 'required|array|min:1',
            'is_out_of_city' => 'nullable|boolean',
            'estimated_arrival' => 'nullable|date',
        ]);

        $isOutOfCity = $request->boolean('is_out_of_city');
        $manifest->update([
            'is_out_of_city' => $isOutOfCity,
            'estimated_arrival' => $isOutOfCity ? $request->estimated_arrival : null,
        ]);

        $selectedTasks = $request->selected_tasks;

        // Collect IDs to keep
        $keepPickupIds = [];
        $keepDeliveryIds = [];

        foreach ($selectedTasks as $taskId) {
            if (strpos($taskId, 'pickup_') === 0) {
                $keepPickupIds[] = str_replace('pickup_', '', $taskId);
            } else if (strpos($taskId, 'delivery_') === 0) {
                $keepDeliveryIds[] = str_replace('delivery_', '', $taskId);
            }
        }

        // Remove tasks no longer selected (set manifest_id = null)
        $removedPickups = PickupTask::where('manifest_id', $manifestId)
            ->whereNotIn('id', $keepPickupIds)
            ->get();
        foreach ($removedPickups as $t) {
            $t->update(['manifest_id' => null, 'status' => 'draft']);
            $this->logHistory($t, 'pickup', 'Dihapus dari manifest ' . $manifest->manifest_number);
        }

        $removedDeliveries = DeliveryAssignment::where('manifest_id', $manifestId)
            ->whereNotIn('id', $keepDeliveryIds)
            ->get();
        foreach ($removedDeliveries as $t) {
            $t->update(['manifest_id' => null, 'status' => 'draft']);
            $this->logHistory($t, 'delivery', 'Dihapus dari manifest ' . $manifest->manifest_number);
        }

        // Add newly selected tasks
        $newPickups = PickupTask::whereIn('id', $keepPickupIds)
            ->where(function($q) use ($manifestId) {
                $q->whereNull('manifest_id')->orWhere('manifest_id', $manifestId);
            })
            ->get();
        foreach ($newPickups as $t) {
            $isNew = $t->manifest_id !== $manifest->id;
            $t->update([
                'manifest_id' => $manifest->id,
                'driver_id' => $manifest->driver_id,
                'co_driver_id' => $manifest->co_driver_id,
                'vehicle_id' => $manifest->vehicle_id,
                'status' => 'assigned',
                'assigned_at' => now(),
                'dispatch_date' => $manifest->dispatch_date,
                'is_out_of_city' => $manifest->is_out_of_city,
                'estimated_arrival' => $manifest->estimated_arrival,
            ]);
            if ($isNew) {
                $this->logHistory($t, 'pickup', 'Ditugaskan ke manifest ' . $manifest->manifest_number);
            }
        }

        $newDeliveries = DeliveryAssignment::whereIn('id', $keepDeliveryIds)
            ->where(function($q) use ($manifestId) {
                $q->whereNull('manifest_id')->orWhere('manifest_id', $manifestId);
            })
            ->get();
        foreach ($newDeliveries as $t) {
            $isNew = $t->manifest_id !== $manifest->id;
            $t->update([
                'manifest_id' => $manifest->id,
                'driver_id' => $manifest->driver_id,
                'co_driver_id' => $manifest->co_driver_id,
                'vehicle_id' => $manifest->vehicle_id,
                'status' => 'assigned',
                'assigned_at' => now(),
                'dispatch_date' => $manifest->dispatch_date,
                'is_out_of_city' => $manifest->is_out_of_city,
                'estimated_arrival' => $manifest->estimated_arrival,
            ]);
            if ($isNew) {
                $this->logHistory($t, 'delivery', 'Ditugaskan ke manifest ' . $manifest->manifest_number);
            }
        }

        return redirect()->route('pickup-tasks.list-penugasan')->with('success', 'Daftar tugas pada penugasan ' . $manifest->manifest_number . ' berhasil diperbarui.');
    }

    public function storePenugasan(Request $request)
    {
        $request->validate([
            'dispatch_date' => 'required|date',
            'driver_id' => 'required|uuid|exists:users,id',
            'co_driver_id' => 'nullable|uuid|exists:users,id',
            'vehicle_id' => 'required|uuid|exists:vehicles,id',
            'selected_tasks' => 'required|array|min:1',
            'is_out_of_city' => 'nullable|boolean',
            'estimated_arrival' => 'nullable|date',
        ]);

        $dateStr = date('dmy', strtotime($request->dispatch_date));
        $prefix = 'DO-' . $dateStr . '-';
        
        $lastManifest = \App\Models\TaskManifest::where('manifest_number', 'like', $prefix . '%')
            ->orderBy('manifest_number', 'desc')
            ->first();
            
        if ($lastManifest) {
            $parts = explode('-', $lastManifest->manifest_number);
            $sequence = intval(end($parts)) + 1;
        } else {
            $sequence = 1;
        }

        $manifestNumber = $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);

        $manifest = \App\Models\TaskManifest::create([
            'manifest_number' => $manifestNumber,
            'driver_id' => $request->driver_id,
            'co_driver_id' => $request->co_driver_id,
            'vehicle_id' => $request->vehicle_id,
            'assigned_by' => Auth::id(),
            'dispatch_date' => $request->dispatch_date,
            'status' => 'assigned',
            'is_out_of_city' => $request->boolean('is_out_of_city'),
            'estimated_arrival' => $request->boolean('is_out_of_city') ? $request->estimated_arrival : null,
        ]);

        foreach ($request->selected_tasks as $taskId) {
            if (strpos($taskId, 'pickup_') === 0) {
                $id = str_replace('pickup_', '', $taskId);
                $task = PickupTask::find($id);
                if ($task) {
                    $task->update([
                        'manifest_id' => $manifest->id,
                        'driver_id' => $manifest->driver_id,
                        'co_driver_id' => $manifest->co_driver_id,
                        'vehicle_id' => $manifest->vehicle_id,
                        'status' => 'assigned',
                        'assigned_at' => now(),
                        'dispatch_date' => $manifest->dispatch_date,
                        'is_out_of_city' => $manifest->is_out_of_city,
                        'estimated_arrival' => $manifest->estimated_arrival,
                    ]);
                    $this->logHistory($task, 'pickup', 'Ditugaskan ke manifest ' . $manifestNumber);
                }
            } else if (strpos($taskId, 'delivery_') === 0) {
                $id = str_replace('delivery_', '', $taskId);
                $task = DeliveryAssignment::find($id);
                if ($task) {
                    $task->update([
                        'manifest_id' => $manifest->id,
                        'driver_id' => $manifest->driver_id,
                        'co_driver_id' => $manifest->co_driver_id,
                        'vehicle_id' => $manifest->vehicle_id,
                        'status' => 'assigned',
                        'assigned_at' => now(),
                        'dispatch_date' => $manifest->dispatch_date,
                        'is_out_of_city' => $manifest->is_out_of_city,
                        'estimated_arrival' => $manifest->estimated_arrival,
                    ]);
                    $this->logHistory($task, 'delivery', 'Ditugaskan ke manifest ' . $manifestNumber);
                }
            }
        }

        return redirect()->route('pickup-tasks.list-penugasan')->with('success', 'Penugasan berhasil dibuat dengan No DO: ' . $manifestNumber);
    }

    private function getTaskStatistics()
    {
        $user = auth()->user();
        
        $pickupQuery = PickupTask::query();
        $deliveryQuery = DeliveryAssignment::query();
        
        if ($user && strtolower($user->role) === 'driver') {
            $pickupQuery->where('driver_id', $user->id);
            $deliveryQuery->where('driver_id', $user->id);
        }
        
        $pickups = $pickupQuery->get();
        $deliveries = $deliveryQuery->get();
        $allTasks = $pickups->concat($deliveries);
        
        $drivers = \App\Models\User::where('role', 'driver')->get();
        $vehicles = \App\Models\Vehicle::where('active', true)->get();

        return [
            'totalTasks' => $allTasks->count(),
            'assignedTasks' => $allTasks->where('status', 'assigned')->count(),
            'onRouteTasks' => $allTasks->where('status', 'on_route')->count(),
            'completedTasks' => $allTasks->where('status', 'delivered')->count(),
            'drivers' => $drivers,
            'vehicles' => $vehicles,
        ];
    }
}
