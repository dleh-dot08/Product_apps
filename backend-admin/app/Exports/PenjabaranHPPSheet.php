<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class PenjabaranHPPSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        $data = [
            ['Biaya BBM', '(Jarak Tempuh / Konsumsi BBM per Liter) * Harga BBM per Liter'],
            ['Biaya Manpower', 'Upah Supir per Jam/Menit/Detik * Durasi Shift * Jumlah Supir'],
            ['Total Biaya Ritase', 'Biaya BBM + Manpower + Tol + Parkir + Lainnya + Depresiasi'],
            ['Bobot Pembebanan', '20% Beban Jarak (Rasio Baris/Tujuan) + 80% Beban Nilai Barang'],
            ['HPP per Baris', 'Total Biaya Ritase * ((20% * Rasio Jarak) + (80% * Rasio Nilai Barang))'],
            ['HPP / Qty', 'HPP per Baris / Qty Barang'],
        ];

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Komponen / Variabel',
            'Rumus Perhitungan'
        ];
    }

    public function title(): string
    {
        return 'Penjabaran Rumus HPP';
    }
}
