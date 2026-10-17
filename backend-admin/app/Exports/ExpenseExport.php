<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpenseExport implements FromCollection, WithHeadings, WithMapping
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
            'Kategori',
            'Deskripsi',
            'Nominal (Rp)',
        ];
    }

    public function map($expense): array
    {
        static $no = 1;
        $driverName = $expense->driver->full_name ?? $expense->driver->name ?? 'Belum ditentukan';

        return [
            $no++,
            $expense->occurred_at ? $expense->occurred_at->format('d/m/Y') : '-',
            $expense->occurred_at ? $expense->occurred_at->format('H:i') : '-',
            $driverName,
            ucfirst($expense->category ?? '-'),
            $expense->description ?? '-',
            (float) ($expense->amount ?? 0),
        ];
    }
}
