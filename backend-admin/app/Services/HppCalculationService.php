<?php

namespace App\Services;

use App\Models\Shift;

class HppCalculationService
{

    /**
     * Mengambil data HPP yang sudah tersimpan di database, 
     * atau menghitung ulang jika belum pernah dihitung.
     */
    public function getSavedOrCalculateProrata(Shift $shift)
    {
        $hppRitase = \App\Models\HppRitase::with('items')->where('shift_id', $shift->id)->first();
        
        if ($hppRitase) {
            $allocations = [];
            foreach ($hppRitase->items as $item) {
                $allocations[] = [
                    'task_id' => $item->task_id,
                    'reference_number' => $item->reference_number,
                    'item_description' => $item->item_description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'line_total' => $item->line_total,
                    'hpp_per_baris' => $item->hpp_per_baris,
                    'hpp_per_qty' => $item->hpp_per_qty,
                    'percentage' => $item->percentage,
                ];
            }
            
            return [
                'costs' => [
                    'fuel' => $hppRitase->fuel_cost,
                    'manpower' => $hppRitase->manpower_cost,
                    'toll' => $hppRitase->toll_cost,
                    'parking' => $hppRitase->parking_cost,
                    'other' => $hppRitase->other_cost,
                    'total' => $hppRitase->total_cost
                ],
                'base_value' => $hppRitase->base_value,
                'is_prorata' => (bool)$hppRitase->is_prorata,
                'allocations' => $allocations
            ];
        }

        return $this->calculateProrata($shift);
    }

    /**
     * Menghitung HPP (Harga Pokok Penjualan) Prorata per Barang dalam 1 Ritase (Shift)
     */
    public function calculateProrata(Shift $shift)
    {
        // Pastikan relasi diload
        $shift->loadMissing(['pickupTasks.items', 'deliveryAssignments.salesOrder.items', 'expenses']);
        $tasks = collect()->merge($shift->pickupTasks)->merge($shift->deliveryAssignments);
        
        // 1. Hitung BBM (Otomatis)
        $fuelCost = 0;
        
        $shift->loadMissing(['vehicle', 'driver']);
        $kmPerLiter = $shift->km_per_liter > 0 ? $shift->km_per_liter : ($shift->vehicle ? $shift->vehicle->km_per_liter : 0);
        $fuelPrice = $shift->fuel_price_per_liter > 0 ? $shift->fuel_price_per_liter : ($shift->vehicle ? $shift->vehicle->fuel_price_per_liter : 0);
        
        if ($shift->end_odometer !== null && $shift->start_odometer !== null && $kmPerLiter > 0) {
            $distance = max(0, $shift->end_odometer - $shift->start_odometer);
            $fuelCost = ($distance / $kmPerLiter) * $fuelPrice;
        }

        // 2. Hitung Manpower (Otomatis)
        $manpowerCost = 0;
        if ($shift->check_in_at && $shift->check_out_at) {
            $driverRateHour = $shift->driver ? ($shift->driver->manpower_rate_per_hour ?? 0) : 0;
            
            // Fallback ke tabel validasi_mp_deliverypickup jika rate di driver/shift 0
            if ($driverRateHour <= 0) {
                $validasiMP = \App\Models\ValidasiMpDeliveryPickup::first();
                if ($validasiMP) {
                    $driverRateHour = $validasiMP->rate_per_hour;
                }
            }

            $rateHour = $shift->manpower_rate_per_hour > 0 ? $shift->manpower_rate_per_hour : $driverRateHour;
            $rateMinute = $shift->manpower_rate_per_minute > 0 ? $shift->manpower_rate_per_minute : ($rateHour / 60);
            $rateSecond = $shift->manpower_rate_per_second > 0 ? $shift->manpower_rate_per_second : ($rateHour / 3600);

            $diff = $shift->check_in_at->diff($shift->check_out_at);
            
            // Total hours, minutes, and seconds directly from the difference
            // diff->days * 24 + diff->h is the total hours
            $hours = ($diff->days * 24) + $diff->h;
            $minutes = $diff->i;
            $seconds = $diff->s;

            $driverManpowerCount = $shift->driver ? ($shift->driver->manpower_count ?? 1) : 1;
            $manpowerCount = $shift->manpower_count > 0 ? $shift->manpower_count : $driverManpowerCount;
            
            // Calculate total manpower cost exactly
            $manpowerCost = (($hours * $rateHour) + ($minutes * $rateMinute) + ($seconds * $rateSecond)) * $manpowerCount;
        }

        // 3. Biaya Manual dari Expenses
        $expenses = $shift->expenses;
        $tollCost = $expenses->where('category', 'toll')->sum('amount');
        $parkingCost = $expenses->where('category', 'parking')->sum('amount');
        $otherCost = $expenses->whereNotIn('category', ['toll', 'parking', 'fuel'])->sum('amount');

        // Total Biaya Ritase
        $totalCost = $fuelCost + $manpowerCost + $tollCost + $parkingCost + $otherCost;
        
        $allItems = collect();

        foreach ($tasks as $task) {
            $isDelivery = $task instanceof \App\Models\DeliveryAssignment;
            if ($isDelivery) {
                $parent = $task->salesOrder;
                if ($parent && $parent->items && $parent->items->count() > 0) {
                    foreach ($parent->items as $item) {
                        $item->task_id = $task->id;
                        $item->refNumber = $parent->so_number ?? '-';
                        $allItems->push($item);
                    }
                } else {
                    $fallbackItem = new \stdClass();
                    $fallbackItem->task_id = $task->id;
                    $fallbackItem->refNumber = $parent->so_number ?? '-';
                    $fallbackItem->item_description = $parent->item_description ?? 'Barang Pengiriman';
                    $fallbackItem->quantity = $parent->ordered_quantity ?? 0;
                    $fallbackItem->unit = $parent->unit ?? 'pcs';
                    $fallbackItem->line_total = $parent->total_amount ?? 0;
                    $allItems->push($fallbackItem);
                }
            } else {
                if ($task->items && $task->items->count() > 0) {
                    foreach ($task->items as $item) {
                        $item->task_id = $task->id;
                        $item->refNumber = $task->reference_number ?? '-';
                        $allItems->push($item);
                    }
                } else {
                    $fallbackItem = new \stdClass();
                    $fallbackItem->task_id = $task->id;
                    $fallbackItem->refNumber = $task->reference_number ?? '-';
                    $fallbackItem->item_description = $task->item_description ?? 'Paket/Barang';
                    $fallbackItem->quantity = $task->quantity ?? 0;
                    $fallbackItem->unit = $task->unit ?? 'pcs';
                    $fallbackItem->line_total = $task->line_total ?? 0;
                    $allItems->push($fallbackItem);
                }
            }
        }
        
        // 1. Hitung Total Nilai Barang dalam 1 Ritase
        $totalNilai = $allItems->sum('line_total');
        
        $result = [];
        
        foreach ($allItems as $item) {
            $qtyBaris = $item->quantity ?? 0;
            $nilaiBaris = $item->line_total ?? 0;
            $refNumber = $item->refNumber;
            $description = $item->item_description ?? 'Paket/Barang';
            $unit = $item->unit ?? 'pcs';
            
            // 2. Hitung Rasio Nilai
            $rasioNilai = $totalNilai > 0 ? ($nilaiBaris / $totalNilai) : ($allItems->count() > 0 ? 1 / $allItems->count() : 0);
            
            // 3. Hitung HPP per Baris
            $hppPerBaris = $totalCost * $rasioNilai;
            
            // 4. Hitung HPP per QTY
            $hppPerQty = $qtyBaris > 0 ? ($hppPerBaris / $qtyBaris) : 0;
            
            $result[] = [
                'task_id' => $item->task_id,
                'reference_number' => $refNumber,
                'item_description' => $description,
                'quantity' => $qtyBaris,
                'unit' => $unit,
                'line_total' => $nilaiBaris,
                'hpp_per_baris' => $hppPerBaris,
                'hpp_per_qty' => $hppPerQty,
                'percentage' => $rasioNilai * 100
            ];
        }
        
        $hppRitase = \App\Models\HppRitase::updateOrCreate(
            ['shift_id' => $shift->id],
            [
                'fuel_cost' => $fuelCost,
                'manpower_cost' => $manpowerCost,
                'toll_cost' => $tollCost,
                'parking_cost' => $parkingCost,
                'other_cost' => $otherCost,
                'total_cost' => $totalCost,
                'base_value' => $totalNilai,
                'is_prorata' => true,
            ]
        );

        // Hapus item lama jika ada
        $hppRitase->items()->delete();
        
        foreach ($result as $item) {
            $hppRitase->items()->create($item);
        }

        return [
            'costs' => [
                'fuel' => $fuelCost,
                'manpower' => $manpowerCost,
                'toll' => $tollCost,
                'parking' => $parkingCost,
                'other' => $otherCost,
                'total' => $totalCost
            ],
            'base_value' => $totalNilai,
            'is_prorata' => true,
            'allocations' => $result
        ];
    }
}
