<?php

namespace App\Exports;

use App\Models\Shift;
use App\Services\HppCalculationService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HppRitaseExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $shifts = Shift::with(['vehicle', 'driver', 'pickupTasks', 'expenses'])
            ->orderBy('work_date', 'desc')
            ->get();
            
        $service = app(HppCalculationService::class);
        $data = new Collection();

        foreach ($shifts as $shift) {
            $calcDetails = $service->calculateProrata($shift);
            
            $driverName = $shift->driver->full_name ?? $shift->driver->name ?? '-';
            $vehicle = $shift->vehicle->plate_number ?? '-';
            $date = $shift->work_date ? $shift->work_date->format('Y-m-d') : '-';
            $taskRef = $shift->task_reference ?? '-';
            
            foreach ($calcDetails['allocations'] as $item) {
                // Decode JSON description if necessary
                $descRaw = $item['item_description'] ?? '';
                $descJson = json_decode($descRaw, true);
                $descText = (is_array($descJson) && isset($descJson['summary'])) ? $descJson['summary'] : $descRaw;
                
                // Cek unit price
                $unitPrice = isset($item['unit_price']) ? $item['unit_price'] : ($item['quantity'] > 0 ? $item['line_total'] / $item['quantity'] : 0);
                
                $data->push([
                    'Tanggal' => $date,
                    'Kendaraan' => $vehicle,
                    'Driver' => $driverName,
                    'Kode Ritase' => $taskRef,
                    'Kode Barang' => $item['reference_number'],
                    'Deskripsi' => $descText,
                    'Qty' => $item['quantity'],
                    'Unit' => $item['unit'],
                    'Harga Satuan' => $unitPrice,
                    'Nilai Barang (Total)' => $item['line_total'],
                    'HPP Per Baris' => $item['hpp_per_baris'],
                    'HPP / Qty' => $item['hpp_per_qty'],
                    '% Beban' => $item['percentage'] . '%'
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kendaraan',
            'Driver',
            'Kode Ritase',
            'Kode Barang',
            'Deskripsi',
            'Qty',
            'Unit',
            'Harga Satuan',
            'Nilai Barang (Total)',
            'HPP Per Baris',
            'HPP / Qty',
            '% Beban'
        ];
    }
}
