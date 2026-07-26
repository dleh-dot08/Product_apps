<?php
// PHP script to replace the function using regex
$filePath = 'c:\xampp\htdocs\gte-aqpa\Product_apps\backend-admin\app\Services\PackagingCalculatorService.php';
$content = file_get_contents($filePath);

$start = '    public function buildDetailsArray(array $params, string $arahGlobal = \'Horizontal\', array $customDetails = [])';
$end = '    public function saveManpowerDetails(PackagingCalculation $calculation)';

$startPos = strpos($content, $start);
$endPos = strpos($content, $end);

if ($startPos === false || $endPos === false) {
    echo "Could not find start or end.";
    exit;
}

$newFunc = <<<'EOF'
    private function formatDetailRow($section, $partName, $material, $materialKode, $direction, $tipePenutup, $calculatedThickness, $calculatedWidth, $partLength, $qty, $sideCount) {
        $totalQty = $qty * $sideCount;
        $totalLength = ($partLength * $totalQty) / 1000;
        $pricePerUnit = $material ? (float)$material->unit_price : 0;
        
        $isTripleks = stripos($tipePenutup, 'Tripleks') !== false || ($material && stripos($material->component, 'Triplek') !== false);
        
        $subtotalPrice = 0;
        if ($material && strtolower($material->satuan_harga) === 'sqm') {
            if ($isTripleks) {
                $totalAreaSqm = ($partLength * $calculatedWidth * $totalQty) / 1000000;
            } else {
                $totalAreaSqm = ($partLength * $calculatedWidth * $totalQty) / 1000000;
            }
            $subtotalPrice = $totalAreaSqm * $pricePerUnit;
        } elseif ($material && strtolower($material->satuan_harga) === 'pcs') {
            $subtotalPrice = $totalQty * $pricePerUnit;
        } else {
            $subtotalPrice = $totalLength * $pricePerUnit;
        }

        return [
            'section' => $section,
            'part_name' => $partName,
            'material_id' => $material ? $material->id : null,
            'material_kode' => $materialKode,
            'material_satuan_harga' => $material ? $material->satuan_harga : 'pcs',
            'direction' => $direction,
            'tipe_penutup' => $tipePenutup,
            'calculated_thickness' => $calculatedThickness,
            'calculated_width' => $calculatedWidth,
            'calculated_length' => $partLength,
            'quantity' => $qty,
            'side_count' => $sideCount,
            'total_quantity' => $totalQty,
            'total_length' => $totalLength,
            'price_per_unit' => $pricePerUnit,
            'subtotal_price' => $subtotalPrice,
            'nail_points' => 0,
        ];
    }

    public function buildDetailsArray(array $params, string $arahGlobal = 'Horizontal', array $customDetails = [])
    {
        $P = (float)($params['length'] ?? 0);
        $L = (float)($params['width'] ?? 0);
        $T = (float)($params['height'] ?? 0);
        $jarakTiang = (float)($params['distance_between_pillars'] ?? 0);
        $celahAtas = (float)($params['gap_atas'] ?? 0);
        $celahBawah = (float)($params['gap_bawah'] ?? 0);
        $jenisPenutup = $params['cover_type'] ?? 'Tidak makai penutup';

        $details = [];

        $getMat = function($kode) {
            if (!$kode || $kode === '-') return null;
            return \App\Models\PackingMaterialPrice::where('code', $kode)->first();
        };

        $getMatKode = function($section, $partName, $defaultCode) use ($customDetails) {
            $override = collect($customDetails)->where('section', $section)->where('part_name', $partName)->first();
            return $override['material_kode'] ?? $defaultCode;
        };

        // --- 1. AMBIL TEBAL & LEBAR DARI MASTER MATERIAL ---
        $kbKode = $getMatKode('Bawah', 'Kaki Balok', 'BK02');
        $kbMat = $getMat($kbKode);
        $lebarKakiBalok = $kbMat ? (float)$kbMat->width : 0;
        $tebalKakiBalok = $kbMat ? (float)$kbMat->thickness : 0;

        $pbKode = $getMatKode('Bawah', 'Penyangga', 'BK02');
        $pbMat = $getMat($pbKode);
        $tebalPenyanggaBawah = $pbMat ? (float)$pbMat->thickness : 0;
        $lebarPenyanggaBawah = $pbMat ? (float)$pbMat->width : 0;

        $ptbTipe = $params['bawah_penutup_tipe'] ?? 'Tanpa Penutup';
        $ptbKode = $getMatKode('Bawah', 'Penutup', (stripos($ptbTipe, 'Tripleks') !== false ? 'TP01' : 'PN03'));
        $ptbMat = $getMat($ptbKode);
        $tebalPenutupBawah = $ptbMat ? (float)$ptbMat->thickness : 0;

        $ptdKode = $getMatKode('Penutup', 'Depan', 'PN03');
        $ptdMat = $getMat($ptdKode);
        $tebalPenutupDepan = $ptdMat ? (float)$ptdMat->thickness : 0;
        
        $ptblKode = $getMatKode('Penutup', 'Belakang', 'PN03');
        $ptblMat = $getMat($ptblKode);
        $tebalPenutupBelakang = $ptblMat ? (float)$ptblMat->thickness : 0;

        $paKode = $getMatKode('Penyangga', 'Atas', 'BK02');
        $paMat = $getMat($paKode);
        $tebalPenyanggaAtas = $paMat ? (float)$paMat->thickness : 0;
        $lebarPenyanggaAtas = $paMat ? (float)$paMat->width : 0;

        $pkananKode = $getMatKode('Penyangga', 'Kanan', 'BK02');
        $pkananMat = $getMat($pkananKode);
        $tebalPenyanggaKanan = $pkananMat ? (float)$pkananMat->thickness : 0;
        
        $pkiriKode = $getMatKode('Penyangga', 'Kiri', 'BK02');
        $pkiriMat = $getMat($pkiriKode);
        $tebalPenyanggaKiri = $pkiriMat ? (float)$pkiriMat->thickness : 0;

        // --- 2. HITUNG AREA BAWAH ---
        $kbInclude = $params['include_pallet_base'] ?? 1;
        $pbInclude = $params['bawah_penyangga_include'] ?? 1;

        $qtyPenyanggaBawah = 0;
        if ($pbInclude == 1 || $pbInclude === '1') {
            $jarakBawah = ($jarakTiang > 0) ? $jarakTiang : 300;
            $qtyPenyanggaBawah = (int) ceil($L / $jarakBawah);
        }

        if ($kbInclude == 1 || $kbInclude === '1') {
            $panjangKB = $L + $tebalPenutupDepan + $tebalPenutupBelakang;
            if ($P < 1600) {
                $qtyKB = 2;
            } else {
                $qtyKB = max(2, (int)ceil($P / 800));
            }
            $details[] = $this->formatDetailRow('Bawah', 'Kaki Balok', $kbMat, $kbKode, 'Horizontal', '', $tebalKakiBalok, $lebarKakiBalok, $panjangKB, $qtyKB, 1);
        }

        if (($pbInclude == 1 || $pbInclude === '1') && $qtyPenyanggaBawah > 0) {
            $details[] = $this->formatDetailRow('Bawah', 'Penyangga', $pbMat, $pbKode, 'Horizontal', '', $tebalPenyanggaBawah, $lebarPenyanggaBawah, $P, $qtyPenyanggaBawah, 1);
        }

        $bawahPenutupOverride = collect($customDetails)->where('section', 'Bawah')->where('part_name', 'Penutup')->first();
        if ($bawahPenutupOverride) {
            $ptbInclude = $bawahPenutupOverride['include'] ?? 1;
            $ptbTipe = $bawahPenutupOverride['tipe_penutup'] ?? $ptbTipe;
            if ($ptbInclude == 1 && $ptbTipe !== 'Tanpa Penutup' && $ptbTipe !== 'Tidak makai penutup' && !empty($ptbTipe)) {
                $isTripleks = stripos($ptbTipe, 'Tripleks') !== false;
                $qtyBasis = $L;
                $lebar = $ptbMat ? (float)$ptbMat->width : 0;
                $partCelah = (stripos($ptbTipe, 'Setengah') !== false) ? $celahBawah : 0;
                $qtyPTB = ($isTripleks || $lebar <= 0) ? 1 : $this->hitungQtyPenutup($ptbTipe, $qtyBasis, $lebar, $partCelah);
                $partWidth = $isTripleks ? $qtyBasis : $lebar;
                $details[] = $this->formatDetailRow('Bawah', 'Penutup', $ptbMat, $ptbKode, 'Horizontal', $ptbTipe, $tebalPenutupBawah, $partWidth, $P, $qtyPTB, 1);
            }
        }

        // --- 3. HITUNG PENYANGGA VERTIKAL ---
        $atasPenyanggaInclude = collect($customDetails)->where('section', 'Penyangga')->where('part_name', 'Atas')->first()['include'] ?? ($params['atas_penyangga_include'] ?? 1);
        $penyanggaParts = ['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'];
        
        $tinggiTiang = $T + $tebalPenutupBawah + $tebalPenyanggaBawah + $lebarKakiBalok + $tebalPenyanggaAtas;

        foreach ($penyanggaParts as $pName) {
            $override = collect($customDetails)->where('section', 'Penyangga')->where('part_name', $pName)->first();
            $isIncluded = $override['include'] ?? $atasPenyanggaInclude;
            if ($isIncluded == 0 || $isIncluded === '0') continue;

            $pKode = $override['material_kode'] ?? 'BK02';
            $pMat = $getMat($pKode);
            $tebal = $pMat ? (float)$pMat->thickness : 0;
            $lebar = $pMat ? (float)$pMat->width : 0;

            if ($pName === 'Atas') {
                $pPanjang = $P; 
                $pQty = max(2, (int)ceil($L / (($jarakTiang > 0) ? $jarakTiang : 300))); 
                $pQty = $qtyPenyanggaBawah > 0 ? $qtyPenyanggaBawah : $pQty; // Match bawah
            } elseif ($pName === 'Kanan' || $pName === 'Kiri') {
                $pPanjang = $tinggiTiang;
                if ($qtyPenyanggaBawah <= 3) {
                    $pQty = 1;
                } else {
                    $pQty = $qtyPenyanggaBawah - 2;
                }
            } else {
                $pPanjang = $tinggiTiang;
                $jarak = ($jarakTiang > 0) ? $jarakTiang : 300;
                $pQty = max(1, (int)ceil($L / $jarak)); 
            }

            $details[] = $this->formatDetailRow('Penyangga', $pName, $pMat, $pKode, ($pName === 'Atas' ? 'Horizontal' : 'Vertikal'), '', $tebal, $lebar, $pPanjang, max(0, $pQty), 1);
        }

        // --- 4. HITUNG PAPAN PENUTUP ---
        $penutupParts = ['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'];
        foreach ($penutupParts as $pName) {
            $override = collect($customDetails)->where('section', 'Penutup')->where('part_name', $pName)->first();
            $tipePenutup = $override['tipe_penutup'] ?? $jenisPenutup;
            $isIncluded = $override['include'] ?? 1;

            if ($isIncluded == 0 || $isIncluded === '0' || $tipePenutup == 'Tanpa Penutup' || $tipePenutup == 'Tidak Pakai Papan' || $tipePenutup == 'Tidak makai penutup' || empty($tipePenutup)) {
                continue;
            }

            $isTripleks = stripos($tipePenutup, 'Tripleks') !== false;
            $pKode = $override['material_kode'] ?? ($isTripleks ? 'TP01' : 'PN03');
            $pMat = $getMat($pKode);
            $tebal = $pMat ? (float)$pMat->thickness : 0;
            $lebar = $pMat ? (float)$pMat->width : 0;

            if ($pName === 'Atas') {
                $pPanjang = $P;
                $qtyBasis = $L;
            } elseif ($pName === 'Kanan' || $pName === 'Kiri') {
                $pPanjang = $P;
                $qtyBasis = $T + $tebalPenutupBawah + $tebalPenyanggaBawah + $lebarKakiBalok;
            } else { 
                $pPanjang = $L + ($tebalPenyanggaKanan * 2); 
                $qtyBasis = $T + $tebalPenutupBawah + $tebalPenyanggaBawah + $lebarKakiBalok;
            }

            $partCelah = $celahAtas;
            $pQty = ($isTripleks || $lebar <= 0) ? 1 : $this->hitungQtyPenutup($tipePenutup, $qtyBasis, $lebar, $partCelah);
            $partWidth = $isTripleks ? $qtyBasis : $lebar;

            $details[] = $this->formatDetailRow('Penutup', $pName, $pMat, $pKode, 'Horizontal', $tipePenutup, $tebal, $partWidth, $pPanjang, max(0, $pQty), 1);
        }

        return $details;
    }

    private function hitungQtyPenutup(?string $jenisPenutup, float $basisQty, float $lebarPenutup, float $celah)
    {
        if (empty($jenisPenutup) || $jenisPenutup == 'Tidak Pakai Papan' || $jenisPenutup == 'Tidak makai penutup') {
            return 0;
        }
        
        $divisor = $lebarPenutup;
        if (stripos($jenisPenutup, 'Setengah') !== false || stripos($jenisPenutup, 'Celahan') !== false) {
            $divisor = $lebarPenutup + $celah;
        }

        if ($divisor <= 0) return 1;

        $qty = (int) ceil($basisQty / $divisor) - 1;
        return max(1, $qty);
    }
EOF;

$newContent = substr($content, 0, $startPos) . $newFunc . "\n" . substr($content, $endPos);

file_put_contents($filePath, $newContent);
echo "SUCCESS REPLACING FILE!\n";
?>
