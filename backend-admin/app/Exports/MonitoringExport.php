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
            'Tgl Clock In',
            'Jam Clock In',
            'Tgl Clock Out',
            'Jam Clock Out',
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

        $checkInDate = null;
        $checkInTime = null;
        if (!empty($shift->check_in_at)) {
            $dtIn = Carbon::parse($shift->check_in_at);
            $checkInDate = Date::dateTimeToExcel($dtIn->copy()->startOfDay());
            $checkInTime = ($dtIn->hour * 3600 + $dtIn->minute * 60 + $dtIn->second) / 86400;
        }

        $checkOutDate = null;
        $checkOutTime = null;
        if (!empty($shift->check_out_at)) {
            $dtOut = Carbon::parse($shift->check_out_at);
            $checkOutDate = Date::dateTimeToExcel($dtOut->copy()->startOfDay());
            $checkOutTime = ($dtOut->hour * 3600 + $dtOut->minute * 60 + $dtOut->second) / 86400;
        }

        return [
            $no++,
            $shift->driver->full_name ?? ($shift->driver->name ?? '-'),
            $shift->vehicle ? '[' . $shift->vehicle->plate_number . '] ' . $shift->vehicle->name : '-',
            $checkInDate,
            $checkInTime,
            $checkOutDate,
            $checkOutTime,
            $totalDistance . ' KM',
            $hasTasks ? 'Ya' : 'Tidak',
            $hasExpenses ? 'Ya' : 'Tidak',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => 'm/d/yyyy',
            'E' => 'hh:mm',
            'F' => 'm/d/yyyy',
            'G' => 'hh:mm',
        ];
    }
}
