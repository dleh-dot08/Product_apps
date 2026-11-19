<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PenugasanExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    protected $assignments;

    public function __construct($assignments)
    {
        $this->assignments = $assignments;
    }

    public function collection()
    {
        return collect($this->assignments);
    }

    public function headings(): array
    {
        return [
            'No',
            'No. DO / Manifest',
            'Tanggal',
            'Driver Utama',
            'Driver Cadangan',
            'Kendaraan',
            'Jumlah Tugas',
            'Diassign Oleh',
            'Status',
        ];
    }

    public function map($manifest): array
    {
        static $no = 1;

        $taskCount = 0;
        if (isset($manifest->historicalPickupTasks) && isset($manifest->historicalDeliveryAssignments)) {
            $taskCount = $manifest->historicalPickupTasks->count() + $manifest->historicalDeliveryAssignments->count();
        } elseif (isset($manifest->task_count)) {
            $taskCount = $manifest->task_count;
        }

        $dateValue = null;
        if (!empty($manifest->dispatch_date)) {
            $dateValue = Date::dateTimeToExcel(Carbon::parse($manifest->dispatch_date));
        } elseif (!empty($manifest->date) && $manifest->date !== '-') {
            try {
                $dateValue = Date::dateTimeToExcel(Carbon::parse($manifest->date));
            } catch (\Exception $e) {
                $dateValue = $manifest->date;
            }
        }

        return [
            $no++,
            $manifest->manifest_number ?? ($manifest->no_do ?? '-'),
            $dateValue ?? '-',
            $manifest->driver ? ($manifest->driver->full_name ?? $manifest->driver->name) : '-',
            $manifest->coDriver ? ($manifest->coDriver->full_name ?? $manifest->coDriver->name) : '-',
            $manifest->vehicle ? '[' . $manifest->vehicle->plate_number . '] ' . $manifest->vehicle->name : '-',
            $taskCount,
            $manifest->assignedBy ? $manifest->assignedBy->name : ($manifest->assigned_by_name ?? 'Sistem/Admin'),
            str_replace('_', ' ', strtoupper($manifest->status)),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }
}
