<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DriverDataSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, WithColumnFormatting
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
            'Tgl Mulai',
            'Jam Mulai',
            'Tgl Selesai',
            'Jam Selesai',
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

        $startDate = null;
        $startTime = null;
        if ($shift->check_in_at) {
            $dtStart = Carbon::parse($shift->check_in_at);
            $startDate = Date::dateTimeToExcel($dtStart->copy()->startOfDay());
            $startTime = ($dtStart->hour * 3600 + $dtStart->minute * 60 + $dtStart->second) / 86400;
        }

        $endDate = null;
        $endTime = null;
        if ($shift->check_out_at) {
            $dtEnd = Carbon::parse($shift->check_out_at);
            $endDate = Date::dateTimeToExcel($dtEnd->copy()->startOfDay());
            $endTime = ($dtEnd->hour * 3600 + $dtEnd->minute * 60 + $dtEnd->second) / 86400;
        }

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
            $startDate,
            $startTime,
            $endDate,
            $endTime,
            $durasiStr,
            $tasksStr,
            $shift->check_out_at ? 'Selesai' : 'Aktif',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:N1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A1:N1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFEA580C');

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(12);
        $sheet->getColumnDimension('L')->setWidth(18);
        $sheet->getColumnDimension('M')->setWidth(35);
        $sheet->getColumnDimension('N')->setWidth(12);

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'H' => 'm/d/yyyy',
            'I' => 'hh:mm',
            'J' => 'm/d/yyyy',
            'K' => 'hh:mm',
        ];
    }
}
