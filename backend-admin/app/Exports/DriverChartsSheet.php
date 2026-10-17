<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DriverChartsSheet implements FromCollection, WithTitle, WithHeadings, WithCustomStartCell, WithStyles, WithDrawings
{
    protected $summary;
    protected $chartImages;

    public function __construct(array $summary, array $chartImages = [])
    {
        $this->summary = $summary;
        $this->chartImages = $chartImages;
    }

    public function title(): string
    {
        return 'Grafik & Analitik';
    }

    public function startCell(): string
    {
        return 'B5';
    }

    public function headings(): array
    {
        return [
            'Indikator KPI',
            'Nilai / Capaian',
            'Keterangan',
        ];
    }

    public function collection()
    {
        return collect([
            [
                'Total Ritase Perjalanan',
                $this->summary['total_shifts'] ?? 0,
                'Shift perjalanan pengantaran / penjemputan',
            ],
            [
                'Total Jarak Tempuh (KM)',
                number_format($this->summary['total_km'] ?? 0, 0, ',', '.') . ' KM',
                'Akumulasi selisih odometer awal & akhir',
            ],
            [
                'Rasio Armada Operasional',
                ($this->summary['operasional_vehicles'] ?? 0) . ' / ' . ($this->summary['total_vehicles'] ?? 0) . ' Armada',
                ($this->summary['pct_operasional'] ?? 0) . '% Operasional, ' . ($this->summary['pct_standby'] ?? 0) . '% Standby',
            ],
            [
                'Total Tugas Selesai',
                $this->summary['total_tasks'] ?? 0,
                'Tugas Pickup & Delivery diselesaikan',
            ],
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Title
        $sheet->setCellValue('B2', 'LAPORAN ANALITIK AKTIVITAS DRIVER & KENDARAAN');
        $sheet->setCellValue('B3', 'Periode: ' . ($this->summary['period'] ?? '-'));

        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('EA580C'));
        $sheet->getStyle('B3')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));
        
        $sheet->getStyle('B5:D5')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('B5:D5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFEA580C');

        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(24);
        $sheet->getColumnDimension('D')->setWidth(42);
        $sheet->getColumnDimension('E')->setWidth(5);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(25);

        return [];
    }

    public function drawings()
    {
        $drawings = [];
        $positions = [
            'chart_vehicle_pct' => ['cell' => 'B12', 'width' => 450, 'height' => 260],
            'chart_usage'       => ['cell' => 'H12', 'width' => 520, 'height' => 260],
            'chart_km_vehicle'  => ['cell' => 'B28', 'width' => 450, 'height' => 270],
            'chart_km_driver'   => ['cell' => 'H28', 'width' => 520, 'height' => 270],
            'chart_tasks_driver'=> ['cell' => 'B44', 'width' => 600, 'height' => 270],
        ];

        foreach ($positions as $key => $pos) {
            if (!empty($this->chartImages[$key]) && file_exists($this->chartImages[$key])) {
                $drawing = new Drawing();
                $drawing->setName($key);
                $drawing->setDescription($key);
                $drawing->setPath($this->chartImages[$key]);
                $drawing->setCoordinates($pos['cell']);
                $drawing->setWidth($pos['width']);
                $drawing->setHeight($pos['height']);
                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }
}
