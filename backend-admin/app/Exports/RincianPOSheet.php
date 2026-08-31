<?php

namespace App\Exports;

use App\Models\Trip;
use App\Services\HppCalculationService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RincianPOSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        $trips = Trip::with(['items'])->get();
        $service = app(HppCalculationService::class);
        $data = new Collection();

        foreach ($trips as $trip) {
            $prorataDetails = $service->calculateProrata($trip);
            foreach ($prorataDetails as $item) {
                $data->push([
                    'trip_id' => $trip->id,
                    'tanggal' => $trip->date->format('Y-m-d'),
                    'item_name' => $item['item_name'],
                    'type' => $item['type'],
                    'quantity' => $item['quantity'],
                    'goods_value' => $item['goods_value'],
                    'hpp_per_baris' => $item['hpp_per_baris'],
                    'hpp_per_qty' => $item['hpp_per_qty'],
                    'percentage' => $item['percentage'] . '%'
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'ID Trip',
            'Tanggal',
            'Nama Barang / PO',
            'Tipe (Kirim/Ambil)',
            'Qty',
            'Nilai Barang',
            'HPP per Baris',
            'HPP / Qty',
            '% Biaya'
        ];
    }

    public function title(): string
    {
        return 'Rincian PO';
    }
}
