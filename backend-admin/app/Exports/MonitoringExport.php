<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MonitoringExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    protected $shifts;

    public function __construct($shifts)
    {
        $this->shifts = $shifts;
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
            'Mobil',
            'Clock In',
            'Clock Out',
            'Total KM',
            'Laporan Tugas',
            'Laporan Pengeluaran',
        ];
    }

    public function map($shift): array
    {
        static $no = 1;
        
        $hasTasks = \App\Models\PickupTask::where('shift_id', $shift->id)->exists() 
            || \App\Models\DeliveryAssignment::where('shift_id', $shift->id)->exists();
            
        $hasExpenses = $shift->expenses ? $shift->expenses->count() > 0 : false;
        
        $totalDistance = ($shift->end_odometer && $shift->start_odometer) 
            ? max(0, $shift->end_odometer - $shift->start_odometer) 
            : 0;

        $checkIn = !empty($shift->check_in_at) ? Date::dateTimeToExcel(Carbon::parse($shift->check_in_at)) : '-';
        $checkOut = !empty($shift->check_out_at) ? Date::dateTimeToExcel(Carbon::parse($shift->check_out_at)) : 'Berjalan';

        return [
            $no++,
            $shift->driver->full_name ?? ($shift->driver->name ?? '-'),
            $shift->vehicle ? '[' . $shift->vehicle->plate_number . '] ' . $shift->vehicle->name : '-',
            $checkIn,
            $checkOut,
            $totalDistance . ' KM',
            $hasTasks ? 'Ya' : 'Tidak',
            $hasExpenses ? 'Ya' : 'Tidak',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => 'yyyy-mm-dd hh:mm',
            'E' => 'yyyy-mm-dd hh:mm',
        ];
    }
}
