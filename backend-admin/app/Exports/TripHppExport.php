<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TripHppExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new LogRitaseSheet();
        $sheets[] = new RincianPOSheet();
        $sheets[] = new PenjabaranHPPSheet();

        return $sheets;
    }
}
