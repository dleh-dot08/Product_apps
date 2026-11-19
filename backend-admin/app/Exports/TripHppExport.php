<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TripHppExport implements WithMultipleSheets
{
    use Exportable;

    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new LogRitaseSheet($this->filters);
        $sheets[] = new RincianPOSheet($this->filters);
        $sheets[] = new PenjabaranHPPSheet();

        return $sheets;
    }
}
