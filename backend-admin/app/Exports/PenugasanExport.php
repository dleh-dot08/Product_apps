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

        $rawDate = !empty($manifest->dispatch_date) ? $manifest->dispatch_date : ($manifest->date ?? null);
        $dateValue = $this->parseExcelDate($rawDate);

        return [
            $no++,
            $manifest->manifest_number ?? ($manifest->no_do ?? '-'),
            $dateValue,
            $manifest->driver ? ($manifest->driver->full_name ?? $manifest->driver->name) : '-',
            $manifest->coDriver ? ($manifest->coDriver->full_name ?? $manifest->coDriver->name) : '-',
            $manifest->vehicle ? '[' . $manifest->vehicle->plate_number . '] ' . $manifest->vehicle->name : '-',
            $taskCount,
            $manifest->assignedBy ? $manifest->assignedBy->name : ($manifest->assigned_by_name ?? 'Sistem/Admin'),
            str_replace('_', ' ', strtoupper($manifest->status)),
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
            'C' => 'm/d/yyyy',
        ];
    }
}
