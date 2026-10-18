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
        $sheet->setCellValue('E7', 'ALL PACKAGING'); // PROJECT

        $sheet->setCellValue('N4', 'PT AQPA INDONESIA'); // VENDOR
        $sheet->setCellValue('N5', date('d/m/Y')); // DATE
        $sheet->setCellValue('N6', 'ALL'); // DO NO.
        $sheet->setCellValue('N7', '-'); // DRIVER NAME

        // 2. Data Items (Template mulai dari baris 12, tiap baris data memakai merge 2 baris)
        $startRow = 12;
        $rowCounter = 0;

        foreach ($packagingJobs as $job) {
            $panjangM = $job->panjang ? round(((float) $job->panjang) / 1000, 3) : 0;
            $lebarM   = $job->lebar   ? round(((float) $job->lebar) / 1000, 3) : 0;
            $tinggiM  = $job->tinggi  ? round(((float) $job->tinggi) / 1000, 3) : 0;
            $volumeM3 = ($panjangM > 0 && $lebarM > 0 && $tinggiM > 0)
                ? round($panjangM * $lebarM * $tinggiM, 3)
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

                $sheet->setCellValue("A{$r}", $rowCounter + 1);
                $sheet->setCellValue("B{$r}", $job->packaging_number ?? '-');

                if ($item) {
                    $descParts = [];
                    if (!empty($item->no_product)) {
                        $descParts[] = $item->no_product;
                    }
                    if (!empty($item->desc_product)) {
                        $descParts[] = $item->desc_product;
                    }
                    $goodsDesc = count($descParts) > 0 ? implode(' - ', $descParts) : ($job->type_packaging ?? '-');
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
                $sheet->setCellValue("L{$r}", '-'); // Gross Weight

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
                $sheet->setCellValue("R{$r}", "Bekasi, {$dateFormatted}");
                break;
            }
        }

        return $spreadsheet;
    }
}
