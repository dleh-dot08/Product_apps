<?php

namespace App\Exports;

use App\Models\PackagingJob;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PackingListAllExport
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection|null
     */
    protected $jobs;

    public function __construct($jobs = null)
    {
        $this->jobs = $jobs;
    }

    /**
     * Load template Excel dan isi dengan seluruh data Packaging Job
     *
     * @return Spreadsheet
     */
    public function export(): Spreadsheet
    {
        $templatePath = resource_path('views/packaging/format/LOG_A03_F05-Rev.0 Packing List.xlsx');

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template packing list tidak ditemukan di: {$templatePath}");
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $packagingJobs = $this->jobs ?? PackagingJob::with(['items', 'details'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 1. Ekstrak Customer & PO Info untuk Header
        $allCustomers = $packagingJobs->flatMap(function ($j) {
            return $j->items->pluck('customer');
        })->filter()->unique()->values();

        $allSo = $packagingJobs->flatMap(function ($j) {
            return $j->items->pluck('no_so');
        })->filter()->unique()->values();

        if ($allCustomers->count() === 1) {
            $customerHeader = $allCustomers->first();
        } elseif ($allCustomers->count() > 1) {
            $customerHeader = 'ALL CUSTOMERS (' . $allCustomers->count() . ' Customers)';
        } else {
            $customerHeader = 'ALL CUSTOMERS';
        }

        if ($allSo->count() === 1) {
            $soHeader = $allSo->first();
        } elseif ($allSo->count() > 1) {
            $soHeader = 'MULTIPLE SO (' . $allSo->count() . ' SO)';
        } else {
            $soHeader = 'ALL SO';
        }

        $sheet->setCellValue('E4', $customerHeader);
        $sheet->setCellValue('E5', '-'); // PIC
        $sheet->setCellValue('E6', $soHeader); // PURCHASE ORDER NO.
        $sheet->setCellValue('E7', ''); // PROJECT (dikosongkan)

        $sheet->setCellValue('N4', 'PT AQPA INDONESIA'); // VENDOR
        $sheet->setCellValue('N5', ''); // DATE (dikosongkan)
        $sheet->setCellValue('N6', ''); // DO NO. (dikosongkan)
        $sheet->setCellValue('N7', '-'); // DRIVER NAME

        // 2. Data Items (Template mulai dari baris 12, tiap baris data memakai merge 2 baris)
        $startRow = 12;
        $rowCounter = 0;

        $totalAllItems = 0;
        foreach ($packagingJobs as $j) {
            $totalAllItems += ($j->items && $j->items->count() > 0) ? $j->items->count() : 1;
        }

        foreach ($packagingJobs as $job) {
            $panjangMm = $job->panjang ? (float) $job->panjang : 0;
            $lebarMm   = $job->lebar   ? (float) $job->lebar : 0;
            $tinggiMm  = $job->tinggi  ? (float) $job->tinggi : 0;

            $panjangM = $panjangMm > 0 ? round($panjangMm / 1000, 3) : 0;
            $lebarM   = $lebarMm > 0   ? round($lebarMm / 1000, 3) : 0;
            $tinggiM  = $tinggiMm > 0  ? round($tinggiMm / 1000, 3) : 0;
            $volumeM3 = ($panjangM > 0 && $lebarM > 0 && $tinggiM > 0)
                ? round($panjangM * $lebarM * $tinggiM, 3)
                : 0;

            // Weight (KG) = panjang (mm) * lebar (mm) * tinggi (mm) / 1.000.000 (1JT)
            $weightKg = ($panjangMm > 0 && $lebarMm > 0 && $tinggiMm > 0)
                ? round(($panjangMm * $lebarMm * $tinggiMm) / 1000000, 2)
                : 0;

            $items = ($job->items && $job->items->count() > 0) ? $job->items : [null];

            foreach ($items as $item) {
                $r = $startRow + ($rowCounter * 2);

                // Jika baris item melebihi 8 slot default (baris 26), sisipkan 2 baris baru
                if ($rowCounter >= 8) {
                    $sheet->insertNewRowBefore($r, 2);
                    $rNext = $r + 1;
                    $sheet->mergeCells("A{$r}:A{$rNext}");
                    $sheet->mergeCells("B{$r}:C{$rNext}");
                    $sheet->mergeCells("D{$r}:I{$rNext}");
                    $sheet->mergeCells("J{$r}:J{$rNext}");
                    $sheet->mergeCells("K{$r}:K{$rNext}");
                    $sheet->mergeCells("L{$r}:L{$rNext}");
                    $sheet->mergeCells("M{$r}:M{$rNext}");
                    $sheet->mergeCells("N{$r}:N{$rNext}");
                    $sheet->mergeCells("O{$r}:O{$rNext}");
                    $sheet->mergeCells("P{$r}:P{$rNext}");
                    $sheet->mergeCells("Q{$r}:Q{$rNext}");
                    $sheet->mergeCells("R{$r}:R{$rNext}");
                    $sheet->mergeCells("S{$r}:U{$rNext}");

                    // Duplikasi style border dan font dari baris acuan
                    foreach (range('A', 'U') as $col) {
                        $sheet->duplicateStyle($sheet->getStyle("{$col}12"), "{$col}{$r}");
                        $sheet->duplicateStyle($sheet->getStyle("{$col}13"), "{$col}{$rNext}");
                    }
                }

                $packingNo = ($rowCounter + 1) . '/' . ($totalAllItems > 0 ? $totalAllItems : 1);
                $sheet->setCellValue("A{$r}", $rowCounter + 1);
                $sheet->setCellValueExplicit("B{$r}", $packingNo, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                if ($item) {
                    $goodsDesc = !empty($item->desc_product) ? $item->desc_product : ($job->type_packaging ?? '-');
                    $qty = $item->qty ?? 1;
                    $remarks = !empty($item->customer) ? $item->customer : ($job->type_packaging ?? '-');
                } else {
                    $goodsDesc = $job->type_packaging ?? 'Packaging';
                    $qty = $job->qty_packaging ?? 1;
                    $remarks = '-';
                }

                $sheet->setCellValue("D{$r}", $goodsDesc);
                $sheet->setCellValue("J{$r}", $qty);
                $sheet->setCellValue("K{$r}", 'PCS');
                $sheet->setCellValue("L{$r}", $weightKg > 0 ? $weightKg : '-'); // Gross Weight (KG) = P x L x T / 1JT

                $sheet->setCellValue("M{$r}", $panjangM);
                $sheet->setCellValue("N{$r}", '*');
                $sheet->setCellValue("O{$r}", $lebarM);
                $sheet->setCellValue("P{$r}", '*');
                $sheet->setCellValue("Q{$r}", $tinggiM);
                $sheet->setCellValue("R{$r}", $volumeM3);
                $sheet->setCellValue("S{$r}", $remarks);

                $rowCounter++;
            }
        }

        // 3. Update Tanggal di Footer Pengesahan (Bekasi, ...)
        $highestRow = $sheet->getHighestRow();
        $dateFormatted = date('d/m/Y');
        for ($r = 28; $r <= $highestRow; $r++) {
            $val = (string) $sheet->getCell("R{$r}")->getValue();
            if (str_contains($val, 'Bekasi')) {
                $sheet->setCellValue("R{$r}", "Bekasi, ");
                break;
            }
        }

        return $spreadsheet;
    }
}
