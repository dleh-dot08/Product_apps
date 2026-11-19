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

        $soDate = $this->parseExcelDate($so->so_date ?? null);
        $estDate = $this->parseExcelDate($so->estimated_delivery_date ?? null);

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
            'I' => 'm/d/yyyy',
        ];
    }
}
