<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PenugasanExport implements FromCollection, WithHeadings, WithMapping
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

        return [
            $no++,
            $manifest->manifest_number ?? ($manifest->no_do ?? '-'),
            $manifest->dispatch_date ? \Carbon\Carbon::parse($manifest->dispatch_date)->format('Y-m-d') : ($manifest->date ?? '-'),
            $manifest->driver ? ($manifest->driver->full_name ?? $manifest->driver->name) : '-',
            $manifest->coDriver ? ($manifest->coDriver->full_name ?? $manifest->coDriver->name) : '-',
            $manifest->vehicle ? '[' . $manifest->vehicle->plate_number . '] ' . $manifest->vehicle->name : '-',
            $taskCount,
            $manifest->assignedBy ? $manifest->assignedBy->name : ($manifest->assigned_by_name ?? 'Sistem/Admin'),
            str_replace('_', ' ', strtoupper($manifest->status)),
        ];
    }
}
