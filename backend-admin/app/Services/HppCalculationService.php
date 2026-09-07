<?php

namespace App\Services;

use App\Models\Shift;

class HppCalculationService
{
    /**
     * Menghitung HPP (Harga Pokok Penjualan) Prorata per Barang dalam 1 Ritase (Shift)
     */
    public function calculateProrata(Shift $shift)
    {
        // Pastikan relasi diload
        $shift->loadMissing(['pickupTasks', 'expenses']);
        $tasks = $shift->pickupTasks;
        
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
            $ratePerHour = $shift->manpower_rate_per_hour ?? 0;
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
        $totalNilai = $tasks->sum('line_total');
        
        $result = [];
        
        foreach ($tasks as $task) {
            $qtyBaris = $task->quantity ?? 0;
            $nilaiBaris = $task->line_total ?? 0;
            
            // 2. Hitung Rasio Nilai
            $rasioNilai = $totalNilai > 0 ? ($nilaiBaris / $totalNilai) : ($tasks->count() > 0 ? 1 / $tasks->count() : 0);
            
            // 3. Hitung HPP per Baris
            $hppPerBaris = $totalCost * $rasioNilai;
            
            // 4. Hitung HPP per QTY
            $hppPerQty = $qtyBaris > 0 ? ($hppPerBaris / $qtyBaris) : 0;
            
            $result[] = [
                'task_id' => $task->id,
                'reference_number' => $task->reference_number,
                'item_description' => $task->item_description ?? 'Paket/Barang',
                'quantity' => $qtyBaris,
                'unit' => $task->unit ?? 'pcs',
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
