<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesOrderExport implements FromCollection, WithHeadings, WithMapping
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

        return [
            $no++,
            $so->so_number ?? '-',
            $so->so_date ? \Carbon\Carbon::parse($so->so_date)->format('d/m/Y') : '-',
            $so->customer_name ?? '-',
            (float) ($so->ordered_quantity ?? 0),
            (float) ($so->shipped_quantity ?? 0),
            (float) ($so->remaining_quantity ?? 0),
            strtoupper($so->status ?? 'DRAFT'),
            $so->estimated_delivery_date ? \Carbon\Carbon::parse($so->estimated_delivery_date)->format('d/m/Y') : '-',
        ];
    }
}
