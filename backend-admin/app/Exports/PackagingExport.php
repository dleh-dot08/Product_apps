<?php

namespace App\Exports;

use App\Models\PackagingJob;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PackagingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function collection()
    {
        return PackagingJob::with(['items', 'details'])->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No Packaging',
            'No SO',
            'Customer',
            'Tipe Packaging',
            'Dimensi (P × L × T mm)',
            'Tgl Packaging',
            'Jam Packaging',
            'Harga Total',
            'Status'
        ];
    }

    public function map($job): array
    {
        static $no = 1;
        $firstItem = $job->items->first();
        $dimensi = ($job->panjang ?? 0) . ' × ' . ($job->lebar ?? 0) . ' × ' . ($job->tinggi ?? 0) . ' mm';

        $packagingDate = null;
        $packagingTime = null;
        if ($job->created_at) {
            try {
                $dt = Carbon::parse($job->created_at);
                $packagingDate = Date::dateTimeToExcel($dt->copy()->startOfDay());
                $packagingTime = ($dt->hour * 3600 + $dt->minute * 60 + $dt->second) / 86400;
            } catch (\Exception $e) {
                $packagingDate = null;
                $packagingTime = null;
            }
        }

        return [
            $no++,
            $job->packaging_number ?? '-',
            $firstItem ? $firstItem->no_so : '-',
            $firstItem ? $firstItem->customer : '-',
            $job->type_packaging ?? '-',
            $dimensi,
            $packagingDate,
            $packagingTime,
            (float) ($job->harga_total ?? 0),
            strtoupper($job->status ?? 'DRAFT')
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => 'm/d/yyyy',
            'H' => 'hh:mm',
            'I' => '"Rp "#,##0',
        ];
    }
}
