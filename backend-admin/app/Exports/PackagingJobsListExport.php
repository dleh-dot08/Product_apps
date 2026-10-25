<?php

namespace App\Exports;

use App\Models\PackagingJob;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PackagingJobsListExport
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
     * Export list detail packaging ke Excel spreadsheet
     *
     * @return Spreadsheet
     */
    public function export(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('List Data Packaging');

        // Pastikan gridlines tampil
        $sheet->setShowGridlines(true);

        $packagingJobs = $this->jobs ?? PackagingJob::with(['items', 'details'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 1. JUDUL / BANNER ATAS
        $sheet->setCellValue('A2', 'REKAPITULASI DATA PERMINTAAN & UKURAN PACKAGING');
        $sheet->setCellValue('A3', 'Tanggal Unduh: ' . date('d F Y, H:i') . ' WIB | Total Data: ' . $packagingJobs->count() . ' Packaging');

        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('EA580C'));
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));

        // 2. HEADER TABEL (Baris 5)
        $headers = [
            'A' => 'NO',
            'B' => 'NO. PACKAGING',
            'C' => 'TANGGAL BUAT',
            'D' => 'CUSTOMER',
            'E' => 'NO. SO',
            'F' => 'TIPE PACKAGING',
            'G' => 'DIMENSI (P x L x T mm)',
            'H' => 'PANJANG (mm)',
            'I' => 'LEBAR (mm)',
            'J' => 'TINGGI (mm)',
            'K' => 'VOLUME (m³)',
            'L' => 'WEIGHT (KG)',
            'M' => 'QTY PACK',
            'N' => 'NO. BARANG / PART',
            'O' => 'NAMA BARANG',
            'P' => 'QTY BARANG',
            'Q' => 'SUBTOTAL MATERIAL',
            'R' => 'SUBTOTAL PAKU',
            'S' => 'MAN POWER',
            'T' => 'TOTAL HARGA',
            'U' => 'STATUS',
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue("{$col}5", $title);
        }

        // Style Header
        $headerRange = 'A5:U5';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 9,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'], // Dark navy slate
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '0F172A'],
                ],
            ],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(28);

        // 3. ISI DATA (Mulai Baris 6)
        $currentRow = 6;
        $noCounter = 1;

        $totalQtyPackaging = 0;
        $totalQtyBarang = 0;
        $totalVolume = 0;
        $totalWeight = 0;
        $totalBiaya = 0;

        foreach ($packagingJobs as $job) {
            $panjang = (float) ($job->panjang ?? 0);
            $lebar = (float) ($job->lebar ?? 0);
            $tinggi = (float) ($job->tinggi ?? 0);

            $panjangM = $panjang > 0 ? round($panjang / 1000, 3) : 0;
            $lebarM = $lebar > 0 ? round($lebar / 1000, 3) : 0;
            $tinggiM = $tinggi > 0 ? round($tinggi / 1000, 3) : 0;
            $volumeM3 = ($panjangM > 0 && $lebarM > 0 && $tinggiM > 0) ? round($panjangM * $lebarM * $tinggiM, 3) : 0;

            // Weight (KG) = P (mm) * L (mm) * T (mm) / 1.000.000 (1JT)
            $weightKg = ($panjang > 0 && $lebar > 0 && $tinggi > 0) ? round(($panjang * $lebar * $tinggi) / 1000000, 2) : 0;

            $qtyPack = (int) ($job->qty_packaging ?? 1);
            $totalQtyPackaging += $qtyPack;
            $totalVolume += ($volumeM3 * $qtyPack);
            $totalWeight += ($weightKg * $qtyPack);
            $totalBiaya += (float) ($job->harga_total ?? 0);

            $dimensiStr = ($panjang > 0 || $lebar > 0 || $tinggi > 0)
                ? "{$panjang} x {$lebar} x {$tinggi}"
                : '-';

            $items = $job->items && $job->items->count() > 0 ? $job->items : [null];

            foreach ($items as $item) {
                $customer = $item ? ($item->customer ?: '-') : '-';
                $noSo = $item ? ($item->no_so ?: '-') : '-';
                $noProduct = $item ? ($item->no_product ?: '-') : '-';
                $descProduct = $item ? ($item->desc_product ?: '-') : '-';
                $qtyBarang = $item ? (int) ($item->qty ?? 1) : 0;
                $totalQtyBarang += $qtyBarang;

                $sheet->setCellValue("A{$currentRow}", $noCounter);
                $sheet->setCellValue("B{$currentRow}", $job->packaging_number ?: ('JOB-' . $job->id));
                $sheet->setCellValue("C{$currentRow}", $job->created_at ? $job->created_at->format('d/m/Y') : '-');
                $sheet->setCellValue("D{$currentRow}", $customer);
                $sheet->setCellValue("E{$currentRow}", $noSo);
                $sheet->setCellValue("F{$currentRow}", strtoupper($job->type_packaging ?: 'BOX'));
                $sheet->setCellValue("G{$currentRow}", $dimensiStr);
                $sheet->setCellValue("H{$currentRow}", $panjang);
                $sheet->setCellValue("I{$currentRow}", $lebar);
                $sheet->setCellValue("J{$currentRow}", $tinggi);
                $sheet->setCellValue("K{$currentRow}", $volumeM3);
                $sheet->setCellValue("L{$currentRow}", $weightKg > 0 ? $weightKg : 0);
                $sheet->setCellValue("M{$currentRow}", $qtyPack);
                $sheet->setCellValue("N{$currentRow}", $noProduct);
                $sheet->setCellValue("O{$currentRow}", $descProduct);
                $sheet->setCellValue("P{$currentRow}", $qtyBarang > 0 ? $qtyBarang : '-');
                $sheet->setCellValue("Q{$currentRow}", (float) ($job->subtotal_harga_material ?? 0));
                $sheet->setCellValue("R{$currentRow}", (float) ($job->subtotal_harga_paku ?? 0));
                $sheet->setCellValue("S{$currentRow}", (float) ($job->subtotal_man_power ?? 0));
                $sheet->setCellValue("T{$currentRow}", (float) ($job->harga_total ?? 0));
                $sheet->setCellValue("U{$currentRow}", strtoupper($job->status ?: 'DRAFT'));

                // Format angka
                $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("H{$currentRow}:J{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("K{$currentRow}")->getNumberFormat()->setFormatCode('0.000');
                $sheet->getStyle("L{$currentRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("M{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("P{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("Q{$currentRow}:T{$currentRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
                $sheet->getStyle("U{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Alternating row styling
                if ($noCounter % 2 === 0) {
                    $sheet->getStyle("A{$currentRow}:U{$currentRow}")->getFill()->applyFromArray([
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8FAFC'],
                    ]);
                }

                $sheet->getRowDimension($currentRow)->setRowHeight(20);
                $currentRow++;
                $noCounter++;
            }
        }

        // 4. BORDER SELURUH DATA
        $lastDataRow = $currentRow - 1;
        if ($lastDataRow >= 6) {
            $sheet->getStyle("A6:U{$lastDataRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // 5. TOTAL ROW (Baris Ringkasan)
        $sheet->setCellValue("A{$currentRow}", 'TOTAL KESELURUHAN');
        $sheet->mergeCells("A{$currentRow}:J{$currentRow}");
        $sheet->setCellValue("K{$currentRow}", $totalVolume);
        $sheet->setCellValue("L{$currentRow}", $totalWeight);
        $sheet->setCellValue("M{$currentRow}", $totalQtyPackaging);
        $sheet->setCellValue("P{$currentRow}", $totalQtyBarang);
        $sheet->setCellValue("T{$currentRow}", $totalBiaya);

        $sheet->getStyle("A{$currentRow}:U{$currentRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F1F5F9'],
            ],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '0F172A']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0F172A']],
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("K{$currentRow}")->getNumberFormat()->setFormatCode('0.000');
        $sheet->getStyle("L{$currentRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("M{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("P{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("T{$currentRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getRowDimension($currentRow)->setRowHeight(24);

        // 6. AUTO-FIT COLUMN WIDTHS
        foreach (range('A', 'U') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
