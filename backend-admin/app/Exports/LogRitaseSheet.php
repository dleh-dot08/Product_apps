<?php

namespace App\Exports;

use App\Models\Trip;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LogRitaseSheet implements FromCollection, WithHeadings, WithTitle, WithMapping, WithColumnFormatting
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
        $tanggal = '-';
        if ($trip->date) {
            try {
                $tanggal = Date::dateTimeToExcel(Carbon::parse($trip->date));
            } catch (\Exception $e) {
                $tanggal = $trip->date->format('Y-m-d');
            }
        }

        return [
            $trip->id,
            $tanggal,
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

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'E' => '#,##0',
            'F' => '"Rp "#,##0',
            'G' => '"Rp "#,##0',
            'H' => '"Rp "#,##0',
            'I' => '"Rp "#,##0',
            'J' => '"Rp "#,##0',
            'K' => '"Rp "#,##0',
        ];
    }

    public function title(): string
    {
        return 'Log Ritase';
    }
}
