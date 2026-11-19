<?php

namespace App\Exports;

use App\Models\Trip;
use App\Services\HppCalculationService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class RincianPOSheet implements FromCollection, WithHeadings, WithTitle, WithColumnFormatting
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Trip::with(['items'])->orderBy('date', 'desc');

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

        $trips = $query->get();
        $service = app(HppCalculationService::class);
        $data = new Collection();

        foreach ($trips as $trip) {
            $prorataDetails = $service->calculateProrata($trip);
            $tripDate = null;
            if (!empty($trip->date)) {
                try {
                    $tripDate = Date::dateTimeToExcel(Carbon::parse($trip->date)->startOfDay());
                } catch (\Exception $e) {
                    $tripDate = null;
                }
            }
            foreach ($prorataDetails as $item) {
                $data->push([
                    'trip_id' => $trip->id,
                    'tanggal' => $tripDate,
                    'item_name' => $item['item_name'] ?? '-',
                    'type' => $item['type'] ?? '-',
                    'quantity' => (float) ($item['quantity'] ?? 0),
                    'goods_value' => (float) ($item['goods_value'] ?? 0),
                    'hpp_per_baris' => (float) ($item['hpp_per_baris'] ?? 0),
                    'hpp_per_qty' => (float) ($item['hpp_per_qty'] ?? 0),
                    'percentage' => ($item['percentage'] ?? 0) . '%'
                ]);
            }
        }

        return $data;
    }

    public function columnFormats(): array
    {
        return [
            'B' => 'm/d/yyyy',
            'E' => '#,##0',
            'F' => '"Rp "#,##0',
            'G' => '"Rp "#,##0',
            'H' => '"Rp "#,##0',
        ];
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
