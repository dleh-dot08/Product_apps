<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\TaskManifest;
use Illuminate\Http\Request;

class DriverManifestController extends Controller
{
    /**
     * Get all manifests assigned to the authenticated driver.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $manifests = TaskManifest::with(['vehicle'])
            ->where(function ($q) use ($user) {
                $q->where('driver_id', $user->id)
                  ->orWhere('co_driver_id', $user->id);
            })
            ->withCount(['historicalPickupTasks as pickup_tasks_count', 'historicalDeliveryAssignments as delivery_assignments_count'])
            ->orderBy('dispatch_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $manifests
        ]);
    }

    /**
     * Get detail of a specific manifest including its tasks.
     */
    public function show($id, Request $request)
    {
        $user = $request->user();

        $manifest = TaskManifest::with([
            'vehicle',
            'driver',
            'coDriver',
            'historicalPickupTasks' => function ($q) {
                $q->select(
                    'pickup_tasks.id', 'pickup_tasks.manifest_id', 'reference_number', 'pickup_name', 'pickup_location', 
                    'destination', 'assigned_at', 'pickup_tasks.status', 'quantity', 'unit', 
                    'estimated_arrival', 'completed_odometer', 'start_odometer', 'start_fuel',
                    'receiver_name', 'receiver_role', 'item_condition'
                )->with('attachments');
            },
            'historicalDeliveryAssignments.salesOrder' => function ($q) {
                $q->select('id', 'so_number', 'customer_name', 'ordered_quantity', 'unit', 'item_description');
            },
            'historicalDeliveryAssignments.attachments'
        ])
        ->where(function ($q) use ($user) {
            $q->where('driver_id', $user->id)
              ->orWhere('co_driver_id', $user->id);
        })
        ->findOrFail($id);

        // Format delivery assignments to match pickup structure for consistency if needed,
        // or let the frontend handle the mapping.
        $manifest->historicalDeliveryAssignments->transform(function ($delivery) {
            // Overwrite status with pivot status to reflect the status AT THE TIME OF THIS MANIFEST if necessary
            // Or if it was expired, pivot status is 'pending', let's format it.
            $status = $delivery->pivot->status;
            if (in_array($status, ['Tidak Terkirim'])) {
                $status = 'Tidak Terkirim';
            }

            return [
                'id' => $delivery->id,
                'manifest_id' => $delivery->pivot->manifest_id,
                'reference_number' => $delivery->salesOrder ? $delivery->salesOrder->so_number : null,
                'pickup_name' => $delivery->pickup_name ?? 'Gudang AQPA',
                'pickup_location' => $delivery->pickup_location ?? '-',
                'destination' => $delivery->salesOrder ? $delivery->salesOrder->customer_name : null,
                'assigned_at' => $delivery->assigned_at,
                'status' => $status,
                'quantity' => $delivery->salesOrder ? $delivery->salesOrder->ordered_quantity : null,
                'unit' => $delivery->salesOrder ? $delivery->salesOrder->unit : null,
                'item_category' => $delivery->salesOrder ? $delivery->salesOrder->item_description : null,
                'estimated_arrival' => $delivery->estimated_arrival,
                'completed_odometer' => $delivery->completed_odometer,
                'start_odometer' => $delivery->start_odometer,
                'start_fuel' => $delivery->start_fuel,
                'receiver_name' => $delivery->receiver_name,
                'receiver_role' => $delivery->receiver_role,
                'item_condition' => $delivery->item_condition,
                'arrival_notes' => $delivery->arrival_notes,
                'failure_reason' => $delivery->failure_reason,
                'attachments' => $delivery->attachments,
                'task_type' => 'delivery' // Help frontend distinguish
            ];
        });

        // Add task type to pickups
        $manifest->historicalPickupTasks->transform(function ($pickup) {
            $pickupArray = $pickup->toArray();
            
            $status = $pickup->pivot->status;
            if (in_array($status, ['Tidak Terkirim'])) {
                $status = 'Tidak Terkirim';
            }
            $pickupArray['status'] = $status;
            $pickupArray['task_type'] = 'pickup';
            return $pickupArray;
        });

        // Re-assign back so the frontend sees them as pickupTasks and deliveryAssignments
        $manifest->setRelation('pickupTasks', $manifest->historicalPickupTasks);
        $manifest->setRelation('deliveryAssignments', $manifest->historicalDeliveryAssignments);
        $manifest->unsetRelation('historicalPickupTasks');
        $manifest->unsetRelation('historicalDeliveryAssignments');

        // Find the corresponding shift for this driver on the dispatch date
        $shift = \App\Models\Shift::with('expenses')
            ->where('driver_id', $user->id)
            ->whereDate('work_date', $manifest->dispatch_date)
            ->latest('created_at')
            ->first();
            
        if ($shift) {
            if ($shift->start_evidence_photo && !str_starts_with($shift->start_evidence_photo, 'http')) {
                $shift->start_evidence_photo = config('filesystems.disks.minio.endpoint') 
                    ? config('filesystems.disks.minio.endpoint') . '/' . config('filesystems.disks.minio.bucket') . '/' . $shift->start_evidence_photo
                    : asset('storage/' . $shift->start_evidence_photo);
            }
            if ($shift->end_evidence_photo && !str_starts_with($shift->end_evidence_photo, 'http')) {
                $shift->end_evidence_photo = config('filesystems.disks.minio.endpoint') 
                    ? config('filesystems.disks.minio.endpoint') . '/' . config('filesystems.disks.minio.bucket') . '/' . $shift->end_evidence_photo
                    : asset('storage/' . $shift->end_evidence_photo);
            }
            
            if ($shift->expenses) {
                $shift->expenses->transform(function ($expense) {
                    if ($expense->receipt_url && !str_starts_with($expense->receipt_url, 'http')) {
                        $expense->receipt_url = config('filesystems.disks.minio.endpoint') 
                            ? config('filesystems.disks.minio.endpoint') . '/' . config('filesystems.disks.minio.bucket') . '/' . $expense->receipt_url
                            : asset('storage/' . $expense->receipt_url);
                    }
                    return $expense;
                });
            }
        }
            
        $manifest->shift = $shift;

        return response()->json([
            'success' => true,
            'data' => $manifest
        ]);
    }
}
