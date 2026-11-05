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

class LogisticsInternalSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
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
        return 'Report Biaya Pengiriman';
    }

    public function headings(): array
    {
        return [
            'Jenis Tugas',
            'Nomor SO / PO',
            'NAMA COSTUMER / VENDOR',
            'No DO Easy',
            'TANGGAL TUGAS / KIRIM',
            'TOTAL HARGA BARANG SESUAI SO/PO VENDOR',
            'BIAYA KIRIM',
        ];
    }

    public function map($row): array
    {
        return [
            $row['task_type'] ?? '-',
            $row['ref_no'] ?? '-',
            $row['customer'] ?? '-',
            $row['do_easy'] ?? '-',
            $row['date'] ?? '-',
            (float)($row['item_value'] ?? 0),
            (float)($row['total_cost'] ?? 0),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '"Rp "#,##0',
            'G' => '"Rp "#,##0',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669']
                ]
            ],
        ];
    }
}

