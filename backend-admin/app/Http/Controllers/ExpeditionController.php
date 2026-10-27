<?php

namespace App\Http\Controllers;

use App\Models\Expedition;
use App\Models\ExpeditionVendor;
use App\Models\ExpeditionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpeditionController extends Controller
{
    /**
     * Display a listing of expeditions.
     */
    public function index(Request $request)
    {
        $query = Expedition::with(['vendor', 'creator', 'items']);

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('expedition_number', 'ilike', "%{$search}%")
                  ->orWhere('tracking_number', 'ilike', "%{$search}%")
                  ->orWhere('vendor_name', 'ilike', "%{$search}%")
                  ->orWhere('reference_number', 'ilike', "%{$search}%")
                  ->orWhere('customer_vendor_name', 'ilike', "%{$search}%")
                  ->orWhere('item_description', 'ilike', "%{$search}%")
                  ->orWhere('destination_name', 'ilike', "%{$search}%")
                  ->orWhere('destination_address', 'ilike', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        // Vendor Filter
        if ($request->filled('vendor_id') && $request->vendor_id !== 'ALL') {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('shipping_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('shipping_date', '<=', $request->end_date);
        }

        $expeditions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Metrics Summary
        $stats = [
            'total_active' => Expedition::where('status', 'on_delivery')->count(),
            'total_delivered' => Expedition::where('status', 'delivered')->count(),
            'total_colli' => Expedition::sum('total_colli'),
            'total_cost' => Expedition::sum('cost_amount'),
        ];

        $vendors = ExpeditionVendor::where('is_active', true)->orderBy('vendor_name')->get();

        return view('ekspedisi.index', compact('expeditions', 'stats', 'vendors'));
    }

    /**
     * Store a newly created expedition.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:expedition_vendors,id',
            'vendor_name' => 'required|string|max:150',
            'service_type' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'task_type' => 'required|in:delivery,pickup',
            'reference_number' => 'nullable|string|max:100',
            'do_easy' => 'nullable|string|max:100',
            'customer_vendor_name' => 'nullable|string|max:255',
            'origin_name' => 'nullable|string|max:255',
            'origin_address' => 'required|string',
            'origin_pic_name' => 'nullable|string|max:150',
            'origin_pic_phone' => 'nullable|string|max:50',
            'destination_name' => 'nullable|string|max:255',
            'destination_address' => 'required|string',
            'receiver_pic_name' => 'nullable|string|max:150',
            'receiver_pic_phone' => 'nullable|string|max:50',
            'item_description' => 'nullable|string',
            'total_colli' => 'required|integer|min:1',
            'weight_kg' => 'nullable|numeric|min:0',
            'cost_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'shipping_date' => 'required|date',
            'estimated_arrival_date' => 'nullable|date',
            'status' => 'required|in:draft,on_delivery,delivered,returned,cancelled',
            'notes' => 'nullable|string',
            'proof_photo' => 'nullable|image|max:4096',
            'items' => 'nullable|array',
            'items.*.item_description' => 'required|string',
            'items.*.item_number' => 'nullable|string',
            'items.*.ref_no' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit' => 'nullable|string',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('proof_photo')) {
            $validated['proof_photo'] = $request->file('proof_photo')->store('expeditions/proofs', 'public');
        }

        // Auto Generate Number EXP-YYYYMMDD-XXX
        $prefix = 'EXP-' . date('Ymd') . '-';
        $latest = Expedition::where('expedition_number', 'like', $prefix . '%')
            ->orderBy('expedition_number', 'desc')
            ->first();
        
        $seq = 1;
        if ($latest) {
            $parts = explode('-', $latest->expedition_number);
            $seq = intval(end($parts)) + 1;
        }
        $validated['expedition_number'] = $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->id();

        if ($validated['status'] === 'delivered' && empty($validated['actual_arrival_date'])) {
            $validated['actual_arrival_date'] = now();
        }

        // Build item_description summary if multiple items provided
        if (!empty($validated['items']) && count($validated['items']) > 0) {
            $descList = [];
            foreach ($validated['items'] as $it) {
                $descList[] = "{$it['item_description']} ({$it['quantity']} " . ($it['unit'] ?: 'item') . ")";
            }
            $validated['item_description'] = implode(', ', $descList);
        }

        DB::beginTransaction();
        try {
            $expedition = Expedition::create($validated);

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $it) {
                    $qty = (int) ($it['quantity'] ?? 1);
                    $price = (float) ($it['unit_price'] ?? 0);
                    ExpeditionItem::create([
                        'expedition_id' => $expedition->id,
                        'ref_no' => $it['ref_no'] ?? $expedition->reference_number,
                        'item_number' => $it['item_number'] ?? null,
                        'item_description' => $it['item_description'],
                        'quantity' => $qty,
                        'unit' => $it['unit'] ?? null,
                        'unit_price' => $price,
                        'total_price' => $qty * $price,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan ekspedisi: ' . $e->getMessage());
        }

        return redirect()->route('ekspedisi.index')->with('success', "Pengiriman ekspedisi {$validated['expedition_number']} berhasil ditambahkan.");
    }

    /**
     * Update the specified expedition.
     */
    public function update(Request $request, Expedition $ekspedisi)
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:expedition_vendors,id',
            'vendor_name' => 'required|string|max:150',
            'service_type' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'task_type' => 'required|in:delivery,pickup',
            'reference_number' => 'nullable|string|max:100',
            'do_easy' => 'nullable|string|max:100',
            'customer_vendor_name' => 'nullable|string|max:255',
            'origin_name' => 'nullable|string|max:255',
            'origin_address' => 'required|string',
            'origin_pic_name' => 'nullable|string|max:150',
            'origin_pic_phone' => 'nullable|string|max:50',
            'destination_name' => 'nullable|string|max:255',
            'destination_address' => 'required|string',
            'receiver_pic_name' => 'nullable|string|max:150',
            'receiver_pic_phone' => 'nullable|string|max:50',
            'item_description' => 'nullable|string',
            'total_colli' => 'required|integer|min:1',
            'weight_kg' => 'nullable|numeric|min:0',
            'cost_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'shipping_date' => 'required|date',
            'estimated_arrival_date' => 'nullable|date',
            'actual_arrival_date' => 'nullable|date',
            'status' => 'required|in:draft,on_delivery,delivered,returned,cancelled',
            'notes' => 'nullable|string',
            'proof_photo' => 'nullable|image|max:4096',
            'items' => 'nullable|array',
            'items.*.item_description' => 'required|string',
            'items.*.item_number' => 'nullable|string',
            'items.*.ref_no' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit' => 'nullable|string',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('proof_photo')) {
            if ($ekspedisi->proof_photo && Storage::disk('public')->exists($ekspedisi->proof_photo)) {
                Storage::disk('public')->delete($ekspedisi->proof_photo);
            }
            $validated['proof_photo'] = $request->file('proof_photo')->store('expeditions/proofs', 'public');
        }

        if ($validated['status'] === 'delivered' && !$ekspedisi->actual_arrival_date && empty($validated['actual_arrival_date'])) {
            $validated['actual_arrival_date'] = now();
        }

        if (!empty($validated['items']) && count($validated['items']) > 0) {
            $descList = [];
            foreach ($validated['items'] as $it) {
                $descList[] = "{$it['item_description']} ({$it['quantity']} " . ($it['unit'] ?: 'item') . ")";
            }
            $validated['item_description'] = implode(', ', $descList);
        }

        DB::beginTransaction();
        try {
            $ekspedisi->update($validated);

            if (isset($validated['items'])) {
                $ekspedisi->items()->delete();
                foreach ($validated['items'] as $it) {
                    $qty = (int) ($it['quantity'] ?? 1);
                    $price = (float) ($it['unit_price'] ?? 0);
                    ExpeditionItem::create([
                        'expedition_id' => $ekspedisi->id,
                        'ref_no' => $it['ref_no'] ?? $ekspedisi->reference_number,
                        'item_number' => $it['item_number'] ?? null,
                        'item_description' => $it['item_description'],
                        'quantity' => $qty,
                        'unit' => $it['unit'] ?? null,
                        'unit_price' => $price,
                        'total_price' => $qty * $price,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui ekspedisi: ' . $e->getMessage());
        }

        return redirect()->route('ekspedisi.index')->with('success', "Data pengiriman {$ekspedisi->expedition_number} berhasil diperbarui.");
    }

    /**
     * Mark an expedition as delivered.
     */
    public function markDelivered(Request $request, Expedition $ekspedisi)
    {
        $request->validate([
            'arrival_type' => 'nullable|in:today,backdate',
            'actual_arrival_date' => 'nullable|date',
            'receiver_pic_name' => 'nullable|string|max:150',
            'delivery_notes' => 'nullable|string',
        ]);

        $arrivalDate = now();
        if ($request->arrival_type === 'backdate' && $request->filled('actual_arrival_date')) {
            $arrivalDate = \Carbon\Carbon::parse($request->actual_arrival_date);
        } elseif ($request->filled('actual_arrival_date') && $request->arrival_type !== 'today') {
            $arrivalDate = \Carbon\Carbon::parse($request->actual_arrival_date);
        }

        $updateData = [
            'status' => 'delivered',
            'actual_arrival_date' => $arrivalDate,
        ];

        if ($request->filled('receiver_pic_name')) {
            $updateData['receiver_pic_name'] = $request->receiver_pic_name;
        }

        if ($request->filled('delivery_notes')) {
            $updateData['notes'] = $ekspedisi->notes 
                ? $ekspedisi->notes . ' | Sampai: ' . $request->delivery_notes 
                : 'Sampai: ' . $request->delivery_notes;
        }

        $ekspedisi->update($updateData);

        return redirect()->route('ekspedisi.index')->with('success', "Status pengiriman {$ekspedisi->expedition_number} diubah menjadi Telah Terkirim / Sampai.");
    }

    /**
     * Remove the specified expedition.
     */
    public function destroy(Expedition $ekspedisi)
    {
        $number = $ekspedisi->expedition_number;
        $ekspedisi->delete();

        return redirect()->route('ekspedisi.index')->with('success', "Data pengiriman {$number} berhasil dihapus.");
    }

    /**
     * Update tracking log, checkpoint journey, and optionally mark delivered.
     */
    public function updateTrackingLog(Request $request, Expedition $ekspedisi)
    {
        $validated = $request->validate([
            'last_tracking_status' => 'nullable|string|max:100',
            'checkpoint_location' => 'nullable|string|max:255',
            'checkpoint_note' => 'nullable|string|max:1000',
            'checkpoint_time' => 'nullable|date',
            'receiver_name' => 'nullable|string|max:150',
            'is_delivered' => 'nullable|boolean',
        ]);

        $history = $ekspedisi->tracking_history ?: [];
        $now = now();
        $eventTime = $request->filled('checkpoint_time') 
            ? \Carbon\Carbon::parse($request->checkpoint_time) 
            : $now;

        // Tambahkan entri riwayat jika ada info baru
        if ($request->filled('checkpoint_note') || $request->filled('last_tracking_status')) {
            $newEntry = [
                'time' => $eventTime->format('Y-m-d H:i:s'),
                'status' => $request->last_tracking_status ?: ($ekspedisi->last_tracking_status ?: 'UPDATE_PERJALANAN'),
                'location' => $request->checkpoint_location ?: '-',
                'note' => $request->checkpoint_note ?: 'Update riwayat pengiriman',
                'logged_by' => auth()->user()->name ?? 'System',
                'logged_at' => $now->format('Y-m-d H:i:s'),
            ];
            // Push ke riwayat (unshift agar urutan terbaru di atas)
            array_unshift($history, $newEntry);
        }

        $updatePayload = [
            'tracking_history' => $history,
            'last_tracked_at' => $now,
        ];

        if ($request->filled('last_tracking_status')) {
            $updatePayload['last_tracking_status'] = $request->last_tracking_status;
        }

        if ($request->filled('receiver_name')) {
            $updatePayload['receiver_pic_name'] = $request->receiver_name;
        }

        // Jika ditandai selesai/delivered
        if ($request->boolean('is_delivered')) {
            $updatePayload['status'] = 'delivered';
            $updatePayload['actual_arrival_date'] = $eventTime;
            $statusText = 'Telah Sampai / Terkirim';
        } else {
            $statusText = 'Riwayat Perjalanan Diperbarui';
        }

        $ekspedisi->update($updatePayload);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pengiriman {$ekspedisi->expedition_number}: {$statusText}.",
                'data' => $ekspedisi->fresh(),
            ]);
        }

        return redirect()->route('ekspedisi.index')->with('success', "Pengiriman {$ekspedisi->expedition_number}: {$statusText}.");
    }

    /**
     * Otomatis sinkron dan ekstrak riwayat tracking dari vendor (100% Free / Public Endpoint)
     */
    public function autoSyncTracking(Request $request, Expedition $ekspedisi, \App\Services\ExpeditionTrackingService $trackingService)
    {
        $result = $trackingService->syncTracking($ekspedisi);

        return response()->json([
            'success' => $result['success'],
            'synced_count' => $result['synced_count'],
            'is_delivered' => $result['is_delivered'],
            'message' => $result['message'],
            'data' => $result['data'] ?? $ekspedisi->fresh(),
        ], $result['success'] ? 200 : 422);
    }
}

