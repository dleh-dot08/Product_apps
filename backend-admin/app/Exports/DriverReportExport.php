<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DriverReportExport implements WithMultipleSheets
{
    protected $shifts;
    protected $summary;
    protected $chartImages;

    public function __construct($shifts, array $summary = [], array $chartImages = [])
    {
        $this->shifts = $shifts;
        $this->summary = $summary;
        $this->chartImages = $chartImages;
    }

    public function sheets(): array
    {
        return [
            new DriverChartsSheet($this->summary, $this->chartImages),
            new DriverDataSheet($this->shifts),
        ];
    }
}
