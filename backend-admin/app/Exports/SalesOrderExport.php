<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SalesOrderExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        return collect($this->orders);
    }

    public function headings(): array
    {
        return [
            'No',
            'No SO',
            'Tanggal SO',
            'Pelanggan',
            'Qty Pesanan',
            'Qty Terkirim',
            'Sisa Qty',
            'Status',
            'Estimasi Tgl Kirim',
        ];
    }

    public function map($so): array
    {
        static $no = 1;

        $soDate = '-';
        if (!empty($so->so_date) && $so->so_date !== '-') {
            try {
                $soDate = Date::dateTimeToExcel(Carbon::parse($so->so_date));
            } catch (\Exception $e) {
                $soDate = $so->so_date;
            }
        }

        $estDate = '-';
        if (!empty($so->estimated_delivery_date) && $so->estimated_delivery_date !== '-') {
            try {
                $estDate = Date::dateTimeToExcel(Carbon::parse($so->estimated_delivery_date));
            } catch (\Exception $e) {
                $estDate = $so->estimated_delivery_date;
            }
        }

        return [
            $no++,
            $so->so_number ?? '-',
            $soDate,
            $so->customer_name ?? '-',
            (float) ($so->ordered_quantity ?? 0),
            (float) ($so->shipped_quantity ?? 0),
            (float) ($so->remaining_quantity ?? 0),
            strtoupper($so->status ?? 'DRAFT'),
            $estDate,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD,
        ];
    }
}
