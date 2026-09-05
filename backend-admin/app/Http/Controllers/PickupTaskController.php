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
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $pickupQuery = PickupTask::with(['driver', 'vehicle', 'assignedBy'])->latest();
        $deliveryQuery = DeliveryAssignment::with(['driver', 'vehicle', 'assigner', 'salesOrder'])->latest('assigned_at');
        
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

        return view('pickup-tasks.index', compact('tasks', 'drivers', 'vehicles', 'totalTasks', 'assignedTasks', 'onRouteTasks', 'completedTasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_type' => 'required|in:pickup,delivery',
            'driver_id' => 'required|uuid|exists:users,id',
            'vehicle_id' => 'required|uuid|exists:vehicles,id',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
        ]);

        $baseReference = 'MAN-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

        if ($request->task_type === 'pickup') {
            $request->validate([
                'pickup_name' => 'required|string',
                'pickup_location' => 'required|string',
                'pickup_destination' => 'nullable|string',
            ]);

            $referenceNumber = $request->pickup_reference ?: $baseReference;
            
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
                'reference_number' => $referenceNumber,
                'driver_id' => $request->driver_id,
                'vehicle_id' => $request->vehicle_id,
                'assigned_by' => Auth::id(),
                'status' => 'assigned',
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
            ]);

            // Save items to task_items table
            foreach ($sourceItems as $item) {
                $pickupTask->items()->create($item);
            }
        } else {
            // Delivery
            $request->validate([
                'customer_name' => 'required|string',
                'delivery_address' => 'required|string',
                'delivery_pickup_name' => 'required|string',
                'delivery_pickup_location' => 'required|string',
            ]);

            $soNumber = $request->delivery_so_number ?: $baseReference;

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
                    'estimated_delivery_date' => $request->estimated_arrival ? date('Y-m-d', strtotime($request->estimated_arrival)) : now()->toDateString(),
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
            DeliveryAssignment::create([
                'sales_order_id' => $salesOrder->id,
                'driver_id' => $request->driver_id,
                'vehicle_id' => $request->vehicle_id,
                'assigned_by' => Auth::id(),
                'status' => 'assigned',
                'priority' => $request->priority,
                'pickup_name' => $request->delivery_pickup_name,
                'delivery_sender_pic' => $request->delivery_sender_pic,
                'pickup_location' => $request->delivery_pickup_location,
                'delivery_origin_point' => $request->delivery_origin_point,
                'delivery_receiver_pic' => $request->delivery_receiver_pic,
                'delivery_target_point' => $request->delivery_target_point,
                'assigned_at' => now(),
                'dispatch_date' => $request->dispatch_date,
                'estimated_arrival' => $request->estimated_arrival,
            ]);
        }

        return redirect()->route('pickup-tasks.index')->with('success', 'Tugas berhasil dibuat dengan ' . count($request->items) . ' barang.');
    }

    public function show(Request $request, $id)
    {
        $type = $request->query('task_type', 'pickup');

        if ($type === 'pickup') {
            $task = PickupTask::with(['driver', 'vehicle', 'assignedBy', 'attachments'])->findOrFail($id);
            $task->task_type = 'pickup';
        } else {
            $task = DeliveryAssignment::with(['driver', 'vehicle', 'assigner', 'salesOrder', 'attachments'])->findOrFail($id);
            $task->task_type = 'delivery';
        }

        return view('pickup-tasks.show', compact('task'));
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
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
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

            $task->update([
                'reference_number' => $request->pickup_reference ?: $task->reference_number,
                'driver_id' => $request->driver_id,
                'vehicle_id' => $request->vehicle_id,
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
            ]);

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

            $task->update([
                'driver_id' => $request->driver_id,
                'vehicle_id' => $request->vehicle_id,
                'priority' => $request->priority,
                'pickup_name' => $request->delivery_pickup_name,
                'delivery_sender_pic' => $request->delivery_sender_pic,
                'pickup_location' => $request->delivery_pickup_location,
                'delivery_origin_point' => $request->delivery_origin_point,
                'delivery_receiver_pic' => $request->delivery_receiver_pic,
                'delivery_target_point' => $request->delivery_target_point,
                'dispatch_date' => $request->dispatch_date,
                'estimated_arrival' => $request->estimated_arrival,
            ]);

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
                    'estimated_delivery_date' => $request->estimated_arrival ? date('Y-m-d', strtotime($request->estimated_arrival)) : now()->toDateString(),
                    'source_data' => $sourceData,
                ]);

                $salesOrder->items()->delete();
                foreach ($sourceItems as $item) {
                    $salesOrder->items()->create($item);
                }
            }
        }

        return redirect()->route('pickup-tasks.index')->with('success', 'Tugas berhasil diperbarui.');
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
}
