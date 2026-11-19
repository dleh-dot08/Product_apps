<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExpenseExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    protected $expenses;

    public function __construct($expenses)
    {
        $this->expenses = $expenses;
    }

    public function collection()
    {
        return collect($this->expenses);
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Jam',
            'Driver',
            'Shift',
            'Kode Delivery',
            'Kategori',
            'Deskripsi',
            'Nominal (Rp)',
        ];
    }

    public function map($expense): array
    {
        static $no = 1;
        $driverName = $expense->driver->full_name ?? $expense->driver->name ?? 'Belum ditentukan';
        
        $shiftDate = null;
        if (!empty($expense->shift->work_date)) {
            try {
                $shiftDate = Date::dateTimeToExcel(Carbon::parse($expense->shift->work_date)->startOfDay());
            } catch (\Exception $e) {
                $shiftDate = null;
            }
        }
        
        $deliveryCode = '-';
        if ($expense->shift_id) {
            $taskData = \App\Models\PickupTask::where('shift_id', $expense->shift_id)->first();
            if ($taskData && $taskData->reference_number) {
                $deliveryCode = $taskData->reference_number;
            }
        }

        $tanggal = null;
        $jam = null;
        if ($expense->occurred_at) {
            try {
                $dt = Carbon::parse($expense->occurred_at);
                $tanggal = Date::dateTimeToExcel($dt->copy()->startOfDay());
                $jam = ($dt->hour * 3600 + $dt->minute * 60 + $dt->second) / 86400;
            } catch (\Exception $e) {
                $tanggal = null;
                $jam = null;
            }
        }

        return [
            $no++,
            $tanggal,
            $jam,
            $driverName,
            $shiftDate,
            $deliveryCode,
            ucfirst($expense->category ?? '-'),
            $expense->description ?? '-',
            (float) ($expense->amount ?? 0),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => 'm/d/yyyy',
            'C' => 'hh:mm',
            'E' => 'm/d/yyyy',
            'I' => '"Rp "#,##0',
        ];
    }
}
