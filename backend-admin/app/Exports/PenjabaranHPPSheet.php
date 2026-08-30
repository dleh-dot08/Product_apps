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
            ['Biaya Manpower', 'Upah Supir per Ritase (dari Profil Driver)'],
            ['Total Biaya Ritase', 'Biaya BBM + Biaya Manpower + Tol + Parkir + Biaya Lainnya'],
            ['HPP per Baris (Jika Nilai Barang > 0)', 'Total Biaya Ritase * (Nilai Barang / Total Nilai Dasar Semua Barang di Trip)'],
            ['HPP per Baris (Jika Nilai Barang = 0)', 'Total Biaya Ritase / Jumlah Baris Barang di Trip'],
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
