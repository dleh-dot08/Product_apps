<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LogisticsExpeditionSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $rows;

    public function __construct($rows)
    {
        $this->rows = collect($rows);
    }

    public function collection()
    {
        return $this->rows;
    }

    public function title(): string
    {
        return 'Report Expedisi';
    }

    public function headings(): array
    {
        return [
            'Jenis Tugas',
            'Nomor SO / PO',
            'NAMA COSTUMER / VENDOR',
            'No DO Easy',
            'EKSPEDISI',
            'NO RESI',
            'TANGGAL KIRIM',
            'TANGGAL ETA',
            'STATUS',
            'TOTAL HARGA BARANG SESUAI SO/PO',
            'BIAYA KIRIM',
        ];
    }

    public function map($row): array
    {
        $shippingDate = '-';
        if (!empty($row['shipping_date']) && $row['shipping_date'] !== '-') {
            try {
                $shippingDate = Date::dateTimeToExcel(Carbon::parse($row['shipping_date']));
            } catch (\Exception $e) {
                $shippingDate = $row['shipping_date'];
            }
        }

        $etaDate = '-';
        if (!empty($row['estimated_arrival_date']) && $row['estimated_arrival_date'] !== '-') {
            try {
                $etaDate = Date::dateTimeToExcel(Carbon::parse($row['estimated_arrival_date']));
            } catch (\Exception $e) {
                $etaDate = $row['estimated_arrival_date'];
            }
        }

        return [
            $row['task_type'] ?? '-',
            $row['ref_no'] ?? '-',
            $row['customer'] ?? '-',
            $row['do_easy'] ?? '-',
            $row['vendor_name'] ?? '-',
            $row['tracking_number'] ?? '-',
            $shippingDate,
            $etaDate,
            $row['status'] ?? '-',
            (float)($row['item_value'] ?? 0),
            (float)($row['cost_amount'] ?? 0),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'H' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'J' => '"Rp "#,##0',
            'K' => '"Rp "#,##0',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D97706']
                ]
            ],
        ];
    }
}

