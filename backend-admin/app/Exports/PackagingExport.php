<?php

namespace App\Exports;

use App\Models\PackagingJob;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PackagingExport implements FromCollection, WithHeadings, WithMapping
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
            'Packaging Date',
            'Harga Total',
            'Status'
        ];
    }

    public function map($job): array
    {
        static $no = 1;
        $firstItem = $job->items->first();

        return [
            $no++,
            $job->packaging_number ?? '-',
            $firstItem ? $firstItem->no_so : '-',
            $firstItem ? $firstItem->customer : '-',
            $job->type_packaging ?? '-',
            $job->created_at ? $job->created_at->format('Y-m-d H:i') : '-',
            $job->harga_total ?? 0,
            strtoupper($job->status)
        ];
    }
}
