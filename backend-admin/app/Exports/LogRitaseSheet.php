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
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Trip::with(['vehicle', 'driver'])->orderBy('date', 'desc');

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('date', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('date', '<=', $this->filters['end_date']);
        }
        if (!empty($this->filters['driver_id'])) {
            $query->where('driver_id', $this->filters['driver_id']);
        }
        if (!empty($this->filters['vehicle_id'])) {
            $query->where('vehicle_id', $this->filters['vehicle_id']);
        }

        return $query->get();
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
            'B' => 'm/d/yyyy',
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
