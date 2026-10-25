<?php

namespace App\Exports;

use App\Models\PackagingJob;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PackingListExport
{
    protected PackagingJob $job;

    public function __construct(PackagingJob $job)
    {
        $this->job = $job;
    }

    /**
     * Load template Excel dan isi dengan data Packaging Job
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

        $job = $this->job->loadMissing(['items', 'details']);
        $firstItem = $job->items->first();

        // 1. Header Information
        $customer = $firstItem && !empty($firstItem->customer) ? $firstItem->customer : '-';
        $soNumber = $firstItem && !empty($firstItem->no_so) ? $firstItem->no_so : '-';
        $projectName = !empty($job->project_name) ? $job->project_name : ($job->packaging_number ?? '-');
        $dateFormatted = $job->created_at ? $job->created_at->format('d/m/Y') : date('d/m/Y');
        $deliveryDate = $firstItem && !empty($firstItem->date_delivery)
            ? \Carbon\Carbon::parse($firstItem->date_delivery)->format('d/m/Y')
            : $dateFormatted;

        $sheet->setCellValue('E4', $customer);
        $sheet->setCellValue('E5', '-'); // PIC
        $sheet->setCellValue('E6', $soNumber); // PURCHASE ORDER NO.
        $sheet->setCellValue('E7', ''); // PROJECT (dikosongkan)

        $sheet->setCellValue('N4', 'PT AQPA INDONESIA'); // VENDOR
        $sheet->setCellValue('N5', ''); // DATE (dikosongkan)
        $sheet->setCellValue('N6', ''); // DO NO. (dikosongkan)
        $sheet->setCellValue('N7', '-'); // DRIVER NAME

        // 2. Dimensi dalam Meter, Volume & Weight
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

        // 3. Data Items (Template mulai dari baris 12, tiap baris data memakai merge 2 baris)
        $items = $job->items;
        $startRow = 12;

        if ($items && $items->count() > 0) {
            foreach ($items as $idx => $item) {
                $r = $startRow + ($idx * 2);

                // Jika item lebih dari 8 (melebihi slot baris 26), sisipkan 2 baris baru
                if ($idx >= 8) {
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
                }

                $totalItems = $items->count();
                $packingNo = ($idx + 1) . '/' . ($totalItems > 0 ? $totalItems : 1);

                $sheet->setCellValue("A{$r}", $idx + 1);
                $sheet->setCellValueExplicit("B{$r}", $packingNo, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $goodsDesc = !empty($item->desc_product) ? $item->desc_product : ($job->type_packaging ?? '-');
                $sheet->setCellValue("D{$r}", $goodsDesc);

                $sheet->setCellValue("J{$r}", $item->qty ?? 1);
                $sheet->setCellValue("K{$r}", 'PCS');
                $sheet->setCellValue("L{$r}", $weightKg > 0 ? $weightKg : '-'); // Gross Weight (KG) = P x L x T / 1JT

                $sheet->setCellValue("M{$r}", $panjangM);
                $sheet->setCellValue("N{$r}", '*');
                $sheet->setCellValue("O{$r}", $lebarM);
                $sheet->setCellValue("P{$r}", '*');
                $sheet->setCellValue("Q{$r}", $tinggiM);
                $sheet->setCellValue("R{$r}", $volumeM3);
                $sheet->setCellValue("S{$r}", '-');
            }
        } else {
            // Jika packaging job belum memiliki items terdaftar
            $sheet->setCellValue("A12", 1);
            $sheet->setCellValueExplicit("B12", "1/1", \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("D12", $job->type_packaging ?? 'Packaging');
            $sheet->setCellValue("J12", $job->qty_packaging ?? 1);
            $sheet->setCellValue("K12", 'UNIT');
            $sheet->setCellValue("L12", $weightKg > 0 ? $weightKg : '-');
            $sheet->setCellValue("M12", $panjangM);
            $sheet->setCellValue("N12", '*');
            $sheet->setCellValue("O12", $lebarM);
            $sheet->setCellValue("P12", '*');
            $sheet->setCellValue("Q12", $tinggiM);
            $sheet->setCellValue("R12", $volumeM3);
            $sheet->setCellValue("S12", '-');
        }

        // 4. Update Tanggal di Footer Pengesahan (Bekasi, ...)
        $dateId = $job->created_at ? $job->created_at->format('d/m/Y') : date('d/m/Y');
        $highestRow = $sheet->getHighestRow();
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
