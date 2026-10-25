<?php

namespace App\Exports;

use App\Models\PackagingJob;
use App\Models\PackagingJobItem;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PackingListFormatGroupedExport
{
    /**
     * @var array|string|null
     */
    protected $selectedSos;

    /**
     * @var string|null
     */
    protected $startDate;

    /**
     * @var string|null
     */
    protected $endDate;

    public function __construct($selectedSos = null, $startDate = null, $endDate = null)
    {
        if (is_string($selectedSos)) {
            $this->selectedSos = $selectedSos === 'all' || empty($selectedSos) ? null : [$selectedSos];
        } elseif (is_array($selectedSos)) {
            $filtered = array_values(array_filter($selectedSos));
            $this->selectedSos = in_array('all', $filtered) || empty($filtered) ? null : $filtered;
        } else {
            $this->selectedSos = null;
        }

        $this->startDate = $startDate ? date('Y-m-d', strtotime($startDate)) : null;
        $this->endDate = $endDate ? date('Y-m-d', strtotime($endDate)) : null;
    }

    /**
     * Load template Excel dan isi data dikelompokkan berdasarkan No. SO (1 Sheet per No. SO)
     *
     * @return Spreadsheet
     */
    public function export(): Spreadsheet
    {
        $templatePath = resource_path('views/packaging/format/LOG_A03_F05-Rev.0 Packing List.xlsx');

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template packing list tidak ditemukan di: {$templatePath}");
        }

        $query = PackagingJobItem::with(['packagingJob']);

        if (!empty($this->selectedSos)) {
            $query->whereIn('no_so', $this->selectedSos);
        } else {
            $query->whereNotNull('no_so')->where('no_so', '!=', '');
        }

        if (!empty($this->startDate)) {
            $query->where(function ($q) {
                $q->whereDate('created_at', '>=', $this->startDate)
                  ->orWhereHas('packagingJob', function ($jq) {
                      $jq->whereDate('created_at', '>=', $this->startDate);
                  });
            });
        }

        if (!empty($this->endDate)) {
            $query->where(function ($q) {
                $q->whereDate('created_at', '<=', $this->endDate)
                  ->orWhereHas('packagingJob', function ($jq) {
                      $jq->whereDate('created_at', '<=', $this->endDate);
                  });
            });
        }

        $allItems = $query->orderBy('no_so')->orderBy('id')->get();

        // Jika tidak ada item yang cocok dengan filter
        if ($allItems->isEmpty()) {
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Packing List');
            $sheet->setCellValue('E4', '-');
            $sheet->setCellValue('E5', '-');
            $sheet->setCellValue('E6', !empty($this->selectedSos) ? implode(', ', $this->selectedSos) : '-');
            $sheet->setCellValue('E7', ''); // PROJECT (Kosongkan)
            $sheet->setCellValue('N4', 'PT AQPA INDONESIA');
            $sheet->setCellValue('N5', ''); // DATE (Kosongkan)
            $sheet->setCellValue('N6', ''); // DO NO. (Kosongkan)
            $sheet->setCellValue('N7', '-');
            return $spreadsheet;
        }

        $groupedSos = $allItems->groupBy('no_so');

        $spreadsheet = IOFactory::load($templatePath);
        $firstTemplateSheet = $spreadsheet->getActiveSheet();
        $protoSheet = clone $firstTemplateSheet;

        $sheetIndex = 0;
        $usedSheetTitles = [];

        foreach ($groupedSos as $soNumber => $items) {
            // Tentukan sheet yang akan digunakan
            if ($sheetIndex === 0) {
                $sheet = $firstTemplateSheet;
            } else {
                $sheet = clone $protoSheet;
                $spreadsheet->addSheet($sheet);
            }

            // Generate judul sheet unik (maksimal 31 karakter sesuai aturan Excel)
            $cleanSo = preg_replace('/[\\/*?:\[\]]/', '-', (string) ($soNumber ?: 'NON_SO'));
            $baseTitle = substr(trim($cleanSo), 0, 28) ?: 'SO';
            $sheetTitle = $baseTitle;
            $suffix = 1;
            while (isset($usedSheetTitles[strtolower($sheetTitle)])) {
                $sheetTitle = substr($baseTitle, 0, 26) . '_' . $suffix;
                $suffix++;
            }
            $usedSheetTitles[strtolower($sheetTitle)] = true;
            $sheet->setTitle($sheetTitle);

            // Ekstrak Informasi Header untuk No. SO ini
            $customer = $items->pluck('customer')->filter(function ($c) {
                return !empty($c) && $c !== '-';
            })->first() ?: ($items->first()->customer ?: '-');

            $jobs = $items->pluck('packagingJob')->filter()->unique('id');
            $packagingNumbers = $jobs->pluck('packaging_number')->filter()->implode(', ') ?: '-';

            $dateDelivery = $items->pluck('date_delivery')->filter()->first();
            $dateFormatted = $dateDelivery
                ? \Carbon\Carbon::parse($dateDelivery)->format('d/m/Y')
                : date('d/m/Y');

            // 1. Set Header
            $sheet->setCellValue('E4', $customer);
            $sheet->setCellValue('E5', '-'); // PIC
            $sheet->setCellValue('E6', (string) $soNumber); // PURCHASE ORDER / SO NO.
            $sheet->setCellValue('E7', ''); // PROJECT (dikosongkan)

            $sheet->setCellValue('N4', 'PT AQPA INDONESIA'); // VENDOR
            $sheet->setCellValue('N5', ''); // DATE (dikosongkan)
            $sheet->setCellValue('N6', ''); // DO NO. (dikosongkan)
            $sheet->setCellValue('N7', '-'); // DRIVER NAME

            // 2. Data Items per No. SO
            $startRow = 12;
            $rowCounter = 0;

            foreach ($items as $item) {
                $job = $item->packagingJob;

                $panjangMm = ($job && $job->panjang) ? (float) $job->panjang : 0;
                $lebarMm   = ($job && $job->lebar)   ? (float) $job->lebar : 0;
                $tinggiMm  = ($job && $job->tinggi)  ? (float) $job->tinggi : 0;

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

                $r = $startRow + ($rowCounter * 2);

                // Jika melebihi 8 slot default, sisipkan baris baru (merge 2 baris)
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
                }

                $totalItemsInSo = $items->count();
                $packingNo = ($rowCounter + 1) . '/' . ($totalItemsInSo > 0 ? $totalItemsInSo : 1);

                $sheet->setCellValue("A{$r}", $rowCounter + 1);
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

                $rowCounter++;
            }

            // 3. Update Tanggal di Footer Pengesahan (Bekasi, ...)
            $highestRow = $sheet->getHighestRow();
            for ($r = 28; $r <= $highestRow; $r++) {
                $val = (string) $sheet->getCell("R{$r}")->getValue();
                if (str_contains($val, 'Bekasi')) {
                    $sheet->setCellValue("R{$r}", "Bekasi, ");
                    break;
                }
            }

            $sheetIndex++;
        }

        // Set active sheet ke sheet pertama
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }
}
