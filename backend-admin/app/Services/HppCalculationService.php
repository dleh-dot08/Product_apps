<?php

namespace App\Services;

use App\Models\Shift;

class HppCalculationService
{
    /**
     * Mendapatkan rate Manpower per jam (Hardcoded sementara)
     */
    private function getManpowerRatePerHour(): float
    {
        return 20000.0; // Hardcode 20.000
    }

    /**
     * Menghitung HPP (Harga Pokok Penjualan) Prorata per Barang dalam 1 Ritase (Shift)
     */
    public function calculateProrata(Shift $shift)
    {
        // Pastikan relasi diload
        $shift->loadMissing(['pickupTasks', 'deliveryAssignments.salesOrder', 'expenses']);
        $tasks = collect()->merge($shift->pickupTasks)->merge($shift->deliveryAssignments);
        
        // 1. Hitung BBM (Otomatis)
        $fuelCost = 0;
        if ($shift->end_odometer && $shift->start_odometer && $shift->km_per_liter > 0) {
            $distance = max(0, $shift->end_odometer - $shift->start_odometer);
            $fuelCost = ($distance / $shift->km_per_liter) * ($shift->fuel_price_per_liter ?? 0);
        }

        // 2. Hitung Manpower (Otomatis)
        $manpowerCost = 0;
        if ($shift->check_in_at && $shift->check_out_at) {
            $durationMinutes = $shift->check_in_at->diffInMinutes($shift->check_out_at);
            $ratePerHour = $this->getManpowerRatePerHour(); // Menggunakan function terpisah
            $manpowerCount = $shift->manpower_count ?? 1;
            $manpowerCost = ($durationMinutes / 60) * $ratePerHour * $manpowerCount;
        }

        // 3. Biaya Manual dari Expenses
        $expenses = $shift->expenses;
        $tollCost = $expenses->where('category', 'toll')->sum('amount');
        $parkingCost = $expenses->where('category', 'parking')->sum('amount');
        $otherCost = $expenses->whereNotIn('category', ['toll', 'parking', 'fuel'])->sum('amount');

        // Total Biaya Ritase
        $totalCost = $fuelCost + $manpowerCost + $tollCost + $parkingCost + $otherCost;
        
        // 1. Hitung Total Nilai Barang dalam 1 Ritase
        $totalNilai = $tasks->sum(function($task) {
            if ($task instanceof \App\Models\DeliveryAssignment) {
                return $task->salesOrder->total_amount ?? 0;
            }
            return $task->line_total ?? 0;
        });
        
        $result = [];
        
        foreach ($tasks as $task) {
            // Cek apakah ini DeliveryAssignment atau PickupTask
            $isDelivery = $task instanceof \App\Models\DeliveryAssignment;
            
            $qtyBaris = $isDelivery 
                ? ($task->salesOrder->ordered_quantity ?? 0) 
                : ($task->quantity ?? 0);
                
            $nilaiBaris = $isDelivery 
                ? ($task->salesOrder->total_amount ?? 0) 
                : ($task->line_total ?? 0);
            
            $refNumber = $isDelivery 
                ? ($task->salesOrder->so_number ?? '-') 
                : $task->reference_number;
                
            $description = $isDelivery 
                ? ($task->salesOrder->item_description ?? 'Barang Pengiriman') 
                : ($task->item_description ?? 'Paket/Barang');
                
            $unit = $isDelivery 
                ? ($task->salesOrder->unit ?? 'pcs') 
                : ($task->unit ?? 'pcs');
            
            // 2. Hitung Rasio Nilai
            $rasioNilai = $totalNilai > 0 ? ($nilaiBaris / $totalNilai) : ($tasks->count() > 0 ? 1 / $tasks->count() : 0);
            
            // 3. Hitung HPP per Baris
            $hppPerBaris = $totalCost * $rasioNilai;
            
            // 4. Hitung HPP per QTY
            $hppPerQty = $qtyBaris > 0 ? ($hppPerBaris / $qtyBaris) : 0;
            
            $result[] = [
                'task_id' => $task->id,
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
