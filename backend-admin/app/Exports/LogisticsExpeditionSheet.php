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
        $shippingDate = $this->parseExcelDate($row['shipping_date'] ?? null);
        $etaDate = $this->parseExcelDate($row['estimated_arrival_date'] ?? null);

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

    private function parseExcelDate($val): ?float
    {
        if (empty($val) || $val === '-') {
            return null;
        }
        if ($val instanceof \DateTimeInterface) {
            return Date::dateTimeToExcel($val);
        }
        $val = trim((string)$val);
        try {
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $val, $m)) {
                $c = Carbon::createFromDate((int)$m[3], (int)$m[2], (int)$m[1])->startOfDay();
                return Date::dateTimeToExcel($c);
            }
            $c = Carbon::parse($val);
            return Date::dateTimeToExcel($c);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function columnFormats(): array
    {
        return [
            'G' => 'm/d/yyyy',
            'H' => 'm/d/yyyy',
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

