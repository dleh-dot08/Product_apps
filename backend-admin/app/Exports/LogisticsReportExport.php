<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LogisticsReportExport implements WithMultipleSheets
{
    protected array $data;
    protected string $activeTab;

    public function __construct(array $data, string $activeTab = 'logistics')
    {
        $this->data = $data;
        $this->activeTab = $activeTab;
    }

    public function sheets(): array
    {
        // Menyediakan 3 sheet sesuai referensi Excel user:
        // 1. REPORT BIAYA LOGISTIK
        // 2. Report Expedisi
        // 3. Report Biaya Pengiriman
        return [
            new LogisticsSummarySheet($this->data['logistics']['rows'] ?? []),
            new LogisticsExpeditionSheet($this->data['expedition']['rows'] ?? []),
            new LogisticsInternalSheet($this->data['internal']['rows'] ?? []),
        ];
    }
}

