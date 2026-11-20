<?php

namespace App\Services;

use App\Models\DeliveryAssignment;
use App\Models\Expedition;
use App\Models\HppRitase;
use App\Models\PackagingJob;
use App\Models\Shift;
use Illuminate\Support\Collection;
use RuntimeException;

class DeliveryOrderCostService
{
    public function resolve(DeliveryAssignment $delivery): array
    {
        $delivery->loadMissing(['salesOrder.items']);
        $salesOrder = $delivery->salesOrder;
        $noDo = trim((string) $delivery->do_easy);

        if ($noDo === '') {
            throw new RuntimeException('Delivery Order tidak memiliki nomor DO.');
        }

        $soNumber = trim((string) ($salesOrder?->so_number ?? ''));
        $expeditions = $this->findExpeditions($noDo, $soNumber);
        $itemValue = max(
            (float) ($salesOrder?->total_amount ?? 0),
            $this->sumExpeditionItemValues($expeditions)
        );

        $packagingCost = $this->findPackagingCost($soNumber);
        $internalCost = $this->findInternalCost($delivery, $soNumber);
        $expeditionCost = (float) $expeditions->sum(fn (Expedition $item) => (float) $item->cost_amount);

        return [
            'no_do' => $noDo,
            'delivery_order_id' => (string) $delivery->id,
            'so_number' => $soNumber !== '' ? $soNumber : null,
            'status' => $delivery->status,
            'customer_name' => $salesOrder?->customer_name,
            'item_value' => $itemValue,
            'internal_cost' => $internalCost,
            'expedition_cost' => $expeditionCost,
            'packaging_cost' => $packagingCost,
            'total_logistics_cost' => $internalCost + $expeditionCost + $packagingCost,
            'currency' => 'IDR',
            'cost_calculated_at' => now()->toIso8601String(),
        ];
    }

    private function findExpeditions(string $noDo, string $soNumber): Collection
    {
        return Expedition::with('items')
            ->where(function ($query) use ($noDo, $soNumber) {
                $query->where('do_easy', $noDo);
                if ($soNumber !== '') {
                    $query->orWhere('reference_number', $soNumber);
                }
            })
            ->get();
    }

    private function sumExpeditionItemValues(Collection $expeditions): float
    {
        return (float) $expeditions->sum(function (Expedition $expedition) {
            return $expedition->items->sum(function ($item) {
                return (float) ($item->total_price > 0
                    ? $item->total_price
                    : (($item->quantity ?? 1) * ($item->unit_price ?? 0)));
            });
        });
    }

    private function findPackagingCost(string $soNumber): float
    {
        if ($soNumber === '') {
            return 0.0;
        }

        $jobs = PackagingJob::with(['items', 'details'])
            ->whereHas('items', fn ($query) => $query->where('no_so', $soNumber))
            ->get();

        return (float) $jobs->sum(function (PackagingJob $job) {
            $total = (float) $job->harga_total;
            if ($total <= 0) {
                $total = (float) $job->subtotal_harga_material
                    + (float) $job->subtotal_harga_paku
                    + (float) $job->subtotal_man_power;
            }
            if ($total <= 0) {
                $total = (float) $job->details->sum('subtotal_price');
            }

            return $total / max(1, $job->items->count());
        });
    }

    private function findInternalCost(DeliveryAssignment $delivery, string $soNumber): float
    {
        $shifts = Shift::with(['vehicle', 'driver', 'deliveryAssignments.salesOrder.items', 'pickupTasks.items', 'expenses'])
            ->whereHas('deliveryAssignments', fn ($query) => $query->whereKey($delivery->id))
            ->get();

        $total = 0.0;
        foreach ($shifts as $shift) {
            $hpp = HppRitase::with('items')->where('shift_id', $shift->id)->first();
            if (! $hpp) {
                continue;
            }

            $allocations = $hpp->items->map(fn ($item) => [
                'task_id' => $item->task_id,
                'reference_number' => $item->reference_number,
                'hpp_per_baris' => $item->hpp_per_baris,
            ]);
            $total += (float) $allocations
                ->filter(function (array $allocation) use ($delivery, $soNumber) {
                    return (string) ($allocation['task_id'] ?? '') === (string) $delivery->id
                        || ($soNumber !== '' && ($allocation['reference_number'] ?? '') === $soNumber);
                })
                ->sum('hpp_per_baris');
        }

        return $total;
    }
}
