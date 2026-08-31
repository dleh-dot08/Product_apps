<?php

namespace App\Exports;

use App\Models\Trip;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class LogRitaseSheet implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    public function collection()
    {
        return Trip::with(['vehicle', 'driver'])->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID Trip',
            'Tanggal',
            'Plat Nomor',
            'Driver',
            'Jarak Tempuh (KM)',
            'Biaya BBM',
            'Biaya Manpower',
            'Biaya Tol',
            'Biaya Parkir',
            'Biaya Lainnya',
            'Total Biaya',
            'Status'
        ];
    }

    public function map($trip): array
    {
        return [
            $trip->id,
            $trip->date->format('Y-m-d'),
            $trip->vehicle->plate_number ?? '-',
            $trip->driver->full_name ?? '-',
            $trip->distance_km,
            $trip->fuel_cost,
            $trip->manpower_cost,
            $trip->toll_cost,
            $trip->parking_cost,
            $trip->other_cost,
            $trip->total_cost,
            $trip->status
        ];
    }

    public function title(): string
    {
        return 'Log Ritase';
    }
}
