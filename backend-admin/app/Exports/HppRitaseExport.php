<?php

namespace App\Exports;

use App\Models\Shift;
use App\Services\HppCalculationService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class HppRitaseExport implements FromCollection, WithHeadings, WithColumnFormatting
{
    protected $shifts;
    protected $filters;

    public function __construct($shifts = null, array $filters = [])
    {
        $this->shifts = $shifts;
        $this->filters = $filters;
    }

    public function collection()
    {
        if ($this->shifts) {
            $shifts = $this->shifts;
        } else {
            $query = Shift::with(['vehicle', 'driver', 'pickupTasks', 'expenses'])
                ->orderBy('work_date', 'desc');

            if (!empty($this->filters['start_date'])) {
                $query->whereDate('work_date', '>=', $this->filters['start_date']);
            }
            if (!empty($this->filters['end_date'])) {
                $query->whereDate('work_date', '<=', $this->filters['end_date']);
            }
            if (!empty($this->filters['driver_id'])) {
                $query->where('driver_id', $this->filters['driver_id']);
            }
            if (!empty($this->filters['vehicle_id'])) {
                $query->where('vehicle_id', $this->filters['vehicle_id']);
            }

            $shifts = $query->get();
        }
            
        $service = app(HppCalculationService::class);
        $data = new Collection();

        foreach ($shifts as $shift) {
            $calcDetails = $service->calculateProrata($shift);
            
            $driverName = $shift->driver->full_name ?? $shift->driver->name ?? '-';
            $vehicle = $shift->vehicle->plate_number ?? '-';
            $date = null;
            if (!empty($shift->work_date)) {
                try {
                    $date = Date::dateTimeToExcel(Carbon::parse($shift->work_date)->startOfDay());
                } catch (\Exception $e) {
                    $date = null;
                }
            }
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
                    'Qty' => (float) ($item['quantity'] ?? 0),
                    'Unit' => $item['unit'],
                    'Harga Satuan' => (float) $unitPrice,
                    'Nilai Barang (Total)' => (float) ($item['line_total'] ?? 0),
                    'HPP Per Baris' => (float) ($item['hpp_per_baris'] ?? 0),
                    'HPP / Qty' => (float) ($item['hpp_per_qty'] ?? 0),
                    '% Beban' => ($item['percentage'] ?? 0) . '%'
                ]);
            }
        }

        return $data;
    }

    public function columnFormats(): array
    {
        return [
            'A' => 'm/d/yyyy',
            'G' => '#,##0',
            'I' => '"Rp "#,##0',
            'J' => '"Rp "#,##0',
            'K' => '"Rp "#,##0',
            'L' => '"Rp "#,##0',
        ];
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
