<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DriverDataSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles
{
    protected $shifts;

    public function __construct($shifts)
    {
        $this->shifts = $shifts;
    }

    public function title(): string
    {
        return 'Data Detail Perjalanan';
    }

    public function collection()
    {
        return collect($this->shifts);
    }

    public function headings(): array
    {
        return [
            'No',
            'Driver',
            'Mobil / Kendaraan',
            'Plat Nomor',
            'Odo Awal (KM)',
            'Odo Akhir (KM)',
            'Total Odo (KM)',
            'Perjalanan Awal (Mulai)',
            'Sampai (Selesai)',
            'Total Durasi',
            'Tugas Dikerjakan',
            'Status',
        ];
    }

    public function map($shift): array
    {
        static $no = 1;

        $driverName = $shift->driver->full_name ?? $shift->driver->name ?? 'Belum Ditentukan';
        $vehicleName = $shift->vehicle->name ?? '-';
        $plateNumber = $shift->vehicle->plate_number ?? '-';

        $odoStart = (int) ($shift->start_odometer ?? 0);
        $odoEnd = (int) ($shift->end_odometer ?? 0);
        $totalOdo = ($odoEnd && $odoStart) ? max(0, $odoEnd - $odoStart) : 0;

        $startTime = $shift->check_in_at ? $shift->check_in_at->format('d/m/Y H:i') : '-';
        $endTime = $shift->check_out_at ? $shift->check_out_at->format('d/m/Y H:i') : ($shift->check_in_at ? 'Sedang Berjalan' : '-');

        $durasiStr = '-';
        if ($shift->check_in_at && $shift->check_out_at) {
            $diff = $shift->check_in_at->diff($shift->check_out_at);
            $durasiStr = sprintf('%d Jam %d Menit', ($diff->days * 24) + $diff->h, $diff->i);
        }

        $tasks = [];
        foreach ($shift->pickupTasks as $p) {
            $tasks[] = '[PICKUP] ' . ($p->reference_number ?? '-');
        }
        foreach ($shift->deliveryAssignments as $d) {
            $tasks[] = '[DELIVERY] ' . ($d->salesOrder->so_number ?? '-');
        }
        $tasksStr = count($tasks) > 0 ? implode(', ', $tasks) : '-';

        return [
            $no++,
            $driverName,
            $vehicleName,
            $plateNumber,
            $odoStart,
            $odoEnd,
            $totalOdo,
            $startTime,
            $endTime,
            $durasiStr,
            $tasksStr,
            $shift->check_out_at ? 'Selesai' : 'Aktif',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A1:L1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFEA580C');

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(22);
        $sheet->getColumnDimension('I')->setWidth(22);
        $sheet->getColumnDimension('J')->setWidth(18);
        $sheet->getColumnDimension('K')->setWidth(35);
        $sheet->getColumnDimension('L')->setWidth(12);

        return [];
    }
}
