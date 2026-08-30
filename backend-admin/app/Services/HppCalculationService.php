<?php

namespace App\Services;

use App\Models\Trip;

class HppCalculationService
{
    /**
     * Menghitung HPP (Harga Pokok Penjualan) Prorata per Barang dalam 1 Ritase
     */
    public function calculateProrata(Trip $trip)
    {
        $items = $trip->items;
        $totalCost = $trip->total_cost;
        
        $totalGoodsValue = $items->sum('goods_value');
        $itemCount = $items->count();
        
        $result = [];
        
        foreach ($items as $item) {
            $hppPerBaris = 0;
            
            if ($totalGoodsValue > 0) {
                // Alokasi proporsional berdasarkan nilai barang
                $hppPerBaris = $totalCost * ($item->goods_value / $totalGoodsValue);
            } else {
                // Dibagi rata jika total nilai barang 0
                if ($itemCount > 0) {
                    $hppPerBaris = $totalCost / $itemCount;
                }
            }
            
            $hppPerQty = 0;
            if ($item->quantity > 0) {
                $hppPerQty = $hppPerBaris / $item->quantity;
            }
            
            $result[] = [
                'item_id' => $item->id,
                'item_name' => $item->item_name,
                'type' => $item->type,
                'quantity' => $item->quantity,
                'goods_value' => $item->goods_value,
                'hpp_per_baris' => $hppPerBaris,
                'hpp_per_qty' => $hppPerQty,
                'percentage' => $totalCost > 0 ? ($hppPerBaris / $totalCost) * 100 : 0
            ];
        }
        
        return $result;
    }
}
