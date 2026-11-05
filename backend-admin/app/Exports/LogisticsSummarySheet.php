<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LogisticsSummarySheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
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
        return 'REPORT BIAYA LOGISTIK';
    }

    public function headings(): array
    {
        return [
            'Nomor SO',
            'Costumer',
            'Biaya Pengiriman Internal',
            'Biaya Expedisi',
            'Biaya Packaging',
            'Total',
        ];
    }

    public function map($row): array
    {
        return [
            $row['ref_no'] ?? '-',
            $row['customer'] ?? '-',
            (float)($row['internal_cost'] ?? 0),
            (float)($row['expedition_cost'] ?? 0),
            (float)($row['packaging_cost'] ?? 0),
            (float)($row['total_logistics_cost'] ?? 0),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => '"Rp "#,##0',
            'D' => '"Rp "#,##0',
            'E' => '"Rp "#,##0',
            'F' => '"Rp "#,##0',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB']
                ]
            ],
        ];
    }
}

