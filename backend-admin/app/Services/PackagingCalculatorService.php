<?php

namespace App\Services;

use App\Models\PackagingJobDetail as PackagingCalculation;
use App\Models\PackingMaterialPrice;
use App\Models\PackagingJobDetail;
use Illuminate\Support\Facades\DB;

class PackagingCalculatorService
{
    /**
     * Calculate and store breakdown details for the new JobDetail system.
     *
     * @param PackagingJobDetail $detail
     * @return PackagingJobDetail
     */
    public function calculateForJobDetail(PackagingJobDetail $detail, array $extraParams = [])
    {
        return DB::transaction(function () use ($detail, $extraParams) {
            // Hapus data lama terkait job detail ini
            DB::table('packing_job_calc_details')->where('job_id', $detail->id)->delete();
            DB::table('packing_job_calc_manpowers')->where('job_id', $detail->id)->delete();
            DB::table('packing_job_nails')->where('job_id', $detail->id)->delete();

            $konfigAtas = is_array($detail->konfigurasi_atas) ? $detail->konfigurasi_atas : json_decode($detail->konfigurasi_atas, true) ?? [];
            $konfigBawah = is_array($detail->konfigurasi_bawah) ? $detail->konfigurasi_bawah : json_decode($detail->konfigurasi_bawah, true) ?? [];

            $params = [
                'length' => (float) $detail->length,
                'width' => (float) $detail->width,
                'height' => (float) $detail->tinggi,
                'distance_between_pillars' => (float) ($konfigBawah['jarak_penyanggah'] ?? $detail->jarak_penyanggah ?? 0),
                'gap_atas' => (float) ($konfigAtas['gap_atas'] ?? $detail->gap_atas ?? 0),
                'gap_bawah' => (float) ($konfigBawah['gap_bawah'] ?? $detail->gap_bawah ?? 0),
                'cover_type' => 'Tidak makai penutup', // akan ditimpa per part
                'include_pallet_base' => strtolower($konfigBawah['kaki_balok']['status'] ?? 'include') === 'include' ? 1 : 0,
                'jarak_balok_additional' => 0,
                'bawah_penyangga_include' => strtolower($konfigBawah['penyanggah']['status'] ?? 'include') === 'include' ? '1' : '0',
                'bawah_penutup_tipe' => $konfigBawah['penutup']['status'] ?? 'Tanpa Penutup',
            ];
            $params = array_merge($params, $extraParams);

            $customDetails = [];
            
            // Penyangga (Atas, Kanan, Kiri, Depan, Belakang)
            if (isset($konfigAtas['penyanggah'])) {
                foreach (['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'] as $partName) {
                    $customDetails[] = [
                        'section' => 'Penyangga',
                        'part_name' => $partName,
                        'include' => strtolower($konfigAtas['penyanggah']['status'] ?? 'include') === 'include' ? '1' : '0',
                        'direction' => $konfigAtas['penyanggah']['arah'] ?? 'Vertikal',
                        'material_kode' => $konfigAtas['penyanggah']['material'] ?? 'BK02',
                    ];
                }
            }
            
            // Penutup (Atas, Kanan, Kiri, Depan, Belakang)
            if (isset($konfigAtas['penutup'])) {
                $tipe = $konfigAtas['penutup']['status'] ?? 'Tanpa Penutup';
                foreach (['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'] as $partName) {
                    $customDetails[] = [
                        'section' => 'Penutup',
                        'part_name' => $partName,
                        'include' => ($tipe === 'Tanpa Penutup' || empty($tipe)) ? '0' : '1',
                        'direction' => $konfigAtas['penutup']['arah'] ?? 'Horizontal',
                        'material_kode' => $konfigAtas['penutup']['material'] ?? 'PN03',
                        'tipe_penutup' => $tipe,
                    ];
                }
            }

            // Bawah Penyangga
            if (isset($konfigBawah['penyanggah'])) {
                $customDetails[] = [
                    'section' => 'Bawah',
                    'part_name' => 'Penyangga',
                    'include' => strtolower($konfigBawah['penyanggah']['status'] ?? 'include') === 'include' ? '1' : '0',
                    'direction' => 'Horizontal',
                    'material_kode' => $konfigBawah['penyanggah']['material'] ?? 'BK02',
                ];
            }
            
            // Bawah Kaki Balok
            if (isset($konfigBawah['kaki_balok'])) {
                $customDetails[] = [
                    'section' => 'Bawah',
                    'part_name' => 'Kaki Balok',
                    'include' => strtolower($konfigBawah['kaki_balok']['status'] ?? 'include') === 'include' ? '1' : '0',
                    'direction' => $konfigBawah['kaki_balok']['arah'] ?? 'Vertikal',
                    'material_kode' => $konfigBawah['kaki_balok']['material'] ?? 'BK02',
                ];
            }
            
            // Bawah Penutup
            if (isset($konfigBawah['penutup'])) {
                $tipe = $konfigBawah['penutup']['status'] ?? 'Tanpa Penutup';
                $customDetails[] = [
                    'section' => 'Bawah',
                    'part_name' => 'Penutup',
                    'include' => ($tipe === 'Tanpa Penutup' || empty($tipe)) ? '0' : '1',
                    'direction' => $konfigBawah['penutup']['arah'] ?? 'Horizontal',
                    'material_kode' => $konfigBawah['penutup']['material'] ?? 'PN03',
                    'tipe_penutup' => $tipe,
                ];
            }

            $detailsToInsert = $this->buildDetailsArray($params, 'Horizontal', $customDetails);

            $totalMaterial = 0;
            $now = now();
            foreach ($detailsToInsert as $detailData) {
                $totalMaterial += $detailData['subtotal_price'];
                
                DB::table('packing_job_calc_details')->insert([
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'job_id' => $detail->id,
                    'section' => $detailData['section'],
                    'part_name' => $detailData['part_name'],
                    'material_code' => $detailData['material_kode'] ?? null,
                    'material_satuan_harga' => $detailData['material_satuan_harga'] ?? null,
                    'direction' => $detailData['direction'] ?? null,
                    'tipe_penutup' => $detailData['tipe_penutup'] ?? null,
                    'calculated_thickness' => $detailData['calculated_thickness'] ?? 0,
                    'calculated_width' => $detailData['calculated_width'] ?? 0,
                    'calculated_length' => $detailData['calculated_length'] ?? 0,
                    'quantity' => $detailData['quantity'] ?? 0,
                    'side_count' => $detailData['side_count'] ?? 1,
                    'total_quantity' => $detailData['total_quantity'] ?? 0,
                    'total_length' => $detailData['total_length'] ?? 0,
                    'price_per_unit' => $detailData['price_per_unit'] ?? 0,
                    'subtotal_price' => $detailData['subtotal_price'] ?? 0,
                    'nail_points' => $detailData['nail_points'] ?? 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Save Manpower
            $totalManpower = $this->saveManpowerForJobDetail($detail);

            // Save Consumables
            $totalNails = $this->saveNailsForJobDetail($detail, $customDetails);

            // Update parent job detail
            $detail->update([
                'subtotal_harga_material' => $totalMaterial,
                'subtotal_harga_paku' => $totalNails,
                'subtotal_man_power' => $totalManpower,
                'harga_total' => $totalMaterial + $totalNails + $totalManpower,
            ]);

            return $detail;
        });
    }

    /**
     * Save nails for job detail
     */
    public function saveNailsForJobDetail(PackagingJobDetail $detail, array $customDetails = [])
    {
        $settingsPath = base_path('config/packaging_settings.json');
        $packagingSettings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [
            'nails_price_per_kg' => 25000,
            'nails_weight_per_piece' => 0.025
        ];
        $pricePerKg = $packagingSettings['nails_price_per_kg'] ?? 25000;
        $nailsWeightPerPiece = $packagingSettings['nails_weight_per_piece'] ?? 0.025;

        $details = DB::table('packing_job_calc_details')->where('job_id', $detail->id)->get();
        $defaults = [];

        $parts = ['Atas', 'Bawah', 'Kanan', 'Kiri', 'Depan', 'Belakang'];

        foreach ($parts as $p) {
            // Penyangga logic
            if ($p === 'Bawah') {
                $qtyPenyangga = $details->where('section', 'Bawah')->where('part_name', 'Penyangga')->sum('total_quantity');
                $qtyKakiBalok = $details->where('section', 'Bawah')->where('part_name', 'Kaki Balok')->sum('total_quantity');
                $defaults["Penyangga $p"] = $qtyPenyangga * $qtyKakiBalok;
            } else {
                $qtyPenyangga = $details->where('section', 'Penyangga')->where('part_name', $p)->sum('total_quantity');
                $defaults["Penyangga $p"] = $qtyPenyangga * 2;
            }
            
            // Penutup logic
            if ($p === 'Bawah') {
                $qtyPenutup = $details->where('section', 'Bawah')->where('part_name', 'Penutup')->sum('total_quantity');
            } else {
                $qtyPenutup = $details->where('section', 'Penutup')->where('part_name', $p)->sum('total_quantity');
            }
            
            $defaults["Penutup $p"] = $qtyPenutup * 4;
        }

        $defaults['Kaki Balok'] = 0;

        $totalHargaPaku = 0;
        $now = now();

        foreach ($defaults as $bagian => $titik) {
            $perTitik = 1;
            
            $typeFrom = 'Balok Kayu';
            $thkFrom = 0;
            $typeTo = 'Balok Kayu';
            $thkTo = 0;

            if (str_starts_with($bagian, 'Penutup')) {
                $partName = str_replace('Penutup ', '', $bagian);
                $section = $partName === 'Bawah' ? 'Bawah' : 'Penutup';
                $partName = $partName === 'Bawah' ? 'Penutup' : $partName;
                
                $fromDetail = $details->where('section', $section)->where('part_name', $partName)->first();
                if ($fromDetail) {
                    $tipePenutup = $fromDetail->tipe_penutup ?? '';
                    $typeFrom = (stripos($tipePenutup, 'Tripleks') !== false || stripos($tipePenutup, 'Triplex') !== false) ? 'Triplek' : 'Papan Kayu';
                    $thkFrom = (float) $fromDetail->calculated_thickness;
                }
                
                $toPartName = $partName === 'Penutup' ? 'Penyangga' : $partName;
                $toSection = $section === 'Bawah' ? 'Bawah' : 'Penyangga';
                $toDetail = $details->where('section', $toSection)->where('part_name', $toPartName)->first();
                if ($toDetail) {
                    $thkTo = (float) $toDetail->calculated_thickness;
                } else {
                    $toDetail = $details->where('section', 'Rangka')->first();
                    if ($toDetail) $thkTo = (float) $toDetail->calculated_thickness;
                }
            } elseif (str_starts_with($bagian, 'Penyangga')) {
                $partName = str_replace('Penyangga ', '', $bagian);
                $section = $partName === 'Bawah' ? 'Bawah' : 'Penyangga';
                $partName = $partName === 'Bawah' ? 'Penyangga' : $partName;
                
                $fromDetail = $details->where('section', $section)->where('part_name', $partName)->first();
                if ($fromDetail) {
                    $typeFrom = 'Balok Kayu';
                    $thkFrom = (float) $fromDetail->calculated_thickness;
                }
                
                if ($partName === 'Bawah') {
                    $toDetail = $details->where('section', 'Bawah')->where('part_name', 'Kaki Balok')->first();
                } else {
                    $toDetail = $details->where('section', 'Bawah')->where('part_name', 'Penyangga')->first() ?? $details->where('section', 'Bawah')->where('part_name', 'Kaki Balok')->first();
                }
                
                if ($toDetail) {
                    $thkTo = (float) $toDetail->calculated_thickness;
                }
            } elseif ($bagian === 'Kaki Balok') {
                $fromDetail = $details->where('section', 'Bawah')->where('part_name', 'Kaki Balok')->first();
                if ($fromDetail) {
                    $typeFrom = 'Balok Kayu';
                    $thkFrom = (float) $fromDetail->calculated_thickness;
                }
                $toDetail = $details->where('section', 'Bawah')->where('part_name', 'Rangka Lebar')->first() ?? $details->where('section', 'Bawah')->where('part_name', 'Penyangga')->first();
                if ($toDetail) {
                    $thkTo = (float) $toDetail->calculated_thickness;
                }
            }

            $matchedNailCode = null;
            if ($thkFrom > 0 && $thkTo > 0) {
                $matchedValidation = DB::table('packaging_fastener_validations')
                    ->where('is_active', true)
                    ->whereRaw('LOWER(type_from) LIKE ?', ['%' . strtolower($typeFrom) . '%'])
                    ->where('thk_from_min_mm', '<=', $thkFrom)
                    ->where('thk_from_max_mm', '>=', $thkFrom)
                    ->whereRaw('LOWER(type_to) LIKE ?', ['%' . strtolower($typeTo) . '%'])
                    ->where('thk_to_min_mm', '<=', $thkTo)
                    ->where('thk_to_max_mm', '>=', $thkTo)
                    ->orderBy('nail_length_mm', 'desc')
                    ->first();
                
                if (!$matchedValidation) {
                    $matchedValidation = DB::table('packaging_fastener_validations')
                        ->where('is_active', true)
                        ->whereRaw('LOWER(type_from) LIKE ?', ['%' . strtolower($typeFrom) . '%'])
                        ->where('thk_from_min_mm', '<=', $thkFrom)
                        ->where('thk_from_max_mm', '>=', $thkFrom)
                        ->whereRaw('LOWER(type_to) LIKE ?', ['%' . strtolower($typeTo) . '%'])
                        ->orderByRaw('ABS(thk_to_min_mm - ?)', [$thkTo])
                        ->orderBy('nail_length_mm', 'desc')
                        ->first();
                }

                if (!$matchedValidation) {
                    $matchedValidation = DB::table('packaging_fastener_validations')
                        ->where('is_active', true)
                        ->whereRaw('LOWER(type_from) LIKE ?', ['%' . strtolower($typeFrom) . '%'])
                        ->whereRaw('LOWER(type_to) LIKE ?', ['%' . strtolower($typeTo) . '%'])
                        ->orderByRaw('ABS(thk_from_min_mm - ?) + ABS(thk_to_min_mm - ?)', [$thkFrom, $thkTo])
                        ->orderBy('nail_length_mm', 'desc')
                        ->first();
                }
                
                if ($matchedValidation) {
                    $matchedNailCode = $matchedValidation->nail_code;
                }
            }

            $totalPaku = $titik * $perTitik;
            $estBerat = $totalPaku * $nailsWeightPerPiece;
            $totalHarga = $estBerat * $pricePerKg;

            if ($titik > 0) {
                DB::table('packing_job_nails')->insert([
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'job_id' => $detail->id,
                    'bagian' => $bagian,
                    'kode_material' => $matchedNailCode,
                    'titik_paku' => $titik,
                    'jumlah_paku_per_titik' => $perTitik,
                    'total_paku' => $totalPaku,
                    'estimasi_berat' => $estBerat,
                    'harga_per_kg' => $pricePerKg,
                    'total_harga' => $totalHarga,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $totalHargaPaku += $totalHarga;
            }
        }
        
        return $totalHargaPaku;
    }

    /**
     * Save manpower for job detail
     */
    public function saveManpowerForJobDetail(PackagingJobDetail $detail)
    {
        $settingsPath = base_path('config/packaging_settings.json');
        $packagingSettings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : ['manpower_rate' => 10000];
        $rate = $packagingSettings['manpower_rate'] ?? 10000;

        $P = (float) $detail->length;
        $L = (float) $detail->width;
        $T = (float) $detail->tinggi;

        $rows = [
            [
                'bagian' => 'Atas & Bawah',
                'panjang' => $P,
                'lebar' => $L,
                'sisi' => 2,
                'luas' => ($P / 1000) * ($L / 1000),
            ],
            [
                'bagian' => 'Kanan & Kiri',
                'panjang' => $L,
                'lebar' => $T,
                'sisi' => 2,
                'luas' => ($L / 1000) * ($T / 1000),
            ],
            [
                'bagian' => 'Depan & Belakang',
                'panjang' => $P,
                'lebar' => $T,
                'sisi' => 2,
                'luas' => ($P / 1000) * ($T / 1000),
            ],
        ];

        $totalHargaManpower = 0;
        $now = now();
        foreach ($rows as $row) {
            $totalLuas = $row['luas'] * $row['sisi'];
            $totalBiaya = $totalLuas * $rate;

            DB::table('packing_job_calc_manpowers')->insert([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'job_id' => $detail->id,
                'bagian' => $row['bagian'],
                'panjang' => $row['panjang'],
                'lebar' => $row['lebar'],
                'sisi' => $row['sisi'],
                'luas' => $row['luas'],
                'total_luas' => $totalLuas,
                'rate' => $rate,
                'total_biaya' => $totalBiaya,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $totalHargaManpower += $totalBiaya;
        }

        return $totalHargaManpower;
    }

    /**
     * Calculate and store breakdown details.
     *
     * @param PackagingCalculation $calculation
     * @param string $arahGlobal
     * @param array $customDetails
     * @return PackagingCalculation
     */
    public function calculate(PackagingCalculation $calculation, string $arahGlobal = 'Horizontal', array $customDetails = [], array $customNails = [], array $extraParams = [])
    {
        return DB::transaction(function () use ($calculation, $arahGlobal, $customDetails, $customNails, $extraParams) {
            // Delete existing details
            DB::table('packing_job_calc_details')->where('job_id', $calculation->id)->delete();

            if (empty($customDetails)) {
                $konfigAtas = is_string($calculation->konfigurasi_atas) ? json_decode($calculation->konfigurasi_atas, true) : ($calculation->konfigurasi_atas ?? []);
                $konfigBawah = is_string($calculation->konfigurasi_bawah) ? json_decode($calculation->konfigurasi_bawah, true) : ($calculation->konfigurasi_bawah ?? []);

                if (isset($konfigAtas['penyanggah'])) {
                    $customDetails[] = [
                        'section' => 'Penyangga',
                        'part_name' => 'Atas',
                        'include' => strtolower($konfigAtas['penyanggah']['status'] ?? 'include') === 'include' ? '1' : '0',
                        'direction' => $konfigAtas['penyanggah']['arah'] ?? 'Vertikal',
                        'material_kode' => $konfigAtas['penyanggah']['material'] ?? 'BK02',
                    ];
                }
                if (isset($konfigAtas['penutup'])) {
                    $tipe = $konfigAtas['penutup']['status'] ?? 'Tanpa Penutup';
                    $customDetails[] = [
                        'section' => 'Penutup',
                        'part_name' => 'Atas',
                        'include' => ($tipe === 'Tanpa Penutup' || empty($tipe)) ? '0' : '1',
                        'direction' => $konfigAtas['penutup']['arah'] ?? 'Horizontal',
                        'material_kode' => $konfigAtas['penutup']['material'] ?? 'PN03',
                        'tipe_penutup' => $tipe,
                    ];
                }
                if (isset($konfigBawah['penyanggah'])) {
                    $customDetails[] = [
                        'section' => 'Bawah',
                        'part_name' => 'Penyangga',
                        'include' => strtolower($konfigBawah['penyanggah']['status'] ?? 'include') === 'include' ? '1' : '0',
                        'direction' => 'Horizontal',
                        'material_kode' => $konfigBawah['penyanggah']['material'] ?? 'BK02',
                    ];
                }
                if (isset($konfigBawah['kaki_balok'])) {
                    $customDetails[] = [
                        'section' => 'Bawah',
                        'part_name' => 'Kaki Balok',
                        'include' => strtolower($konfigBawah['kaki_balok']['status'] ?? 'include') === 'include' ? '1' : '0',
                        'direction' => $konfigBawah['kaki_balok']['arah'] ?? 'Vertikal',
                        'material_kode' => $konfigBawah['kaki_balok']['material'] ?? 'BK02',
                    ];
                }
                if (isset($konfigBawah['penutup'])) {
                    $tipe = $konfigBawah['penutup']['status'] ?? 'Tanpa Penutup';
                    $customDetails[] = [
                        'section' => 'Bawah',
                        'part_name' => 'Penutup Bawah',
                        'include' => ($tipe === 'Tanpa Penutup' || empty($tipe)) ? '0' : '1',
                        'direction' => $konfigBawah['penutup']['arah'] ?? 'Horizontal',
                        'material_kode' => $konfigBawah['penutup']['material'] ?? 'PN03',
                        'tipe_penutup' => $tipe,
                    ];
                }
            }

            $params = [
                'length' => (float) ($calculation->panjang ?? $calculation->length ?? 0),
                'width' => (float) ($calculation->lebar ?? $calculation->width ?? 0),
                'height' => (float) ($calculation->tinggi ?? $calculation->height ?? 0),
                'distance_between_pillars' => (float) ($calculation->jarak_penyanggah ?? $calculation->distance_between_pillars ?? 0),
                'gap_atas' => (float) ($calculation->gap_atas ?? 0),
                'gap_bawah' => (float) ($calculation->gap_bawah ?? 0),
                'cover_type' => $calculation->cover_type ?? 'Tidak makai penutup',
                'include_pallet_base' => (bool) ($calculation->include_pallet_base ?? true),
                'jarak_balok_additional' => (float) ($calculation->jarak_balok_additional ?? 0),
            ];
            
            // Merge the extra frontend parameters to ensure missing customDetails can be correctly excluded
            $params = array_merge($params, $extraParams);

            $detailsToInsert = $this->buildDetailsArray($params, $arahGlobal, $customDetails);

            $totalCost = 0;
            $now = now();
            foreach ($detailsToInsert as $detailData) {
                DB::table('packing_job_calc_details')->insert([
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'job_id' => $calculation->id,
                    'section' => $detailData['section'] ?? null,
                    'part_name' => $detailData['part_name'] ?? null,
                    'material_code' => $detailData['material_kode'] ?? null,
                    'material_satuan_harga' => $detailData['material_satuan_harga'] ?? null,
                    'direction' => $detailData['direction'] ?? null,
                    'tipe_penutup' => $detailData['tipe_penutup'] ?? null,
                    'calculated_thickness' => $detailData['calculated_thickness'] ?? 0,
                    'calculated_width' => $detailData['calculated_width'] ?? 0,
                    'calculated_length' => $detailData['calculated_length'] ?? 0,
                    'quantity' => $detailData['quantity'] ?? 0,
                    'side_count' => $detailData['side_count'] ?? 1,
                    'total_quantity' => $detailData['total_quantity'] ?? 0,
                    'total_length' => $detailData['total_length'] ?? 0,
                    'price_per_unit' => $detailData['price_per_unit'] ?? 0,
                    'subtotal_price' => $detailData['subtotal_price'] ?? 0,
                    'nail_points' => $detailData['nail_points'] ?? 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $totalCost += $detailData['subtotal_price'] ?? 0;
            }

            // Save manpower details to DB
            $totalManpower = $this->saveManpowerDetails($calculation);

            // Save consumable details (nails) to DB
            $totalNails = $this->saveConsumableDetails($calculation, $customNails);

            // Update parent cost
            $calculation->update([
                'subtotal_harga_material' => $totalCost,
                'subtotal_harga_paku' => $totalNails,
                'subtotal_man_power' => $totalManpower,
                'harga_total' => $totalCost + $totalNails + $totalManpower,
            ]);

            return $calculation;
        });
    }

    /**
     * Save consumable details (nails) to database.
     *
     * @param PackagingCalculation $calculation
     * @param array $customNails
     * @return float
     */
    public function saveConsumableDetails(PackagingCalculation $calculation, array $customNails = [])
    {
        // Load rate and conversion from config/packaging_settings.json
        $settingsPath = base_path('config/packaging_settings.json');
        $packagingSettings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [
            'manpower_rate' => 10000,
            'nails_price_per_kg' => 25000,
            'nails_weight_per_piece' => 0.025
        ];
        $pricePerKg = $packagingSettings['nails_price_per_kg'] ?? 25000;
        $nailsWeightPerPiece = $packagingSettings['nails_weight_per_piece'] ?? 0.025;

        DB::table('packing_job_nails')->where('job_id', $calculation->id)->where('category', 'Paku')->delete();

        // Calculate titik paku dynamically based on Total Kebutuhan (total_quantity)
        $details = DB::table('packing_job_calc_details')->where('job_id', $calculation->id)->get();
        $defaults = [];

        // Rangka Atas, Bawah, and Tinggi have been removed as requested.

        $parts = ['Atas', 'Bawah', 'Kanan', 'Kiri', 'Depan', 'Belakang'];

        foreach ($parts as $p) {
            // Penyangga logic
            if ($p === 'Bawah') {
                $qtyPenyangga = $details->where('section', 'Bawah')->where('part_name', 'Penyangga')->sum('total_quantity');
                $qtyKakiBalok = $details->where('section', 'Bawah')->where('part_name', 'Kaki Balok')->sum('total_quantity');
                $defaults["Penyangga $p"] = $qtyPenyangga * $qtyKakiBalok;
            } else {
                $qtyPenyangga = $details->where('section', 'Penyangga')->where('part_name', $p)->sum('total_quantity');
                $defaults["Penyangga $p"] = $qtyPenyangga * 2;
            }
            
            // Penutup logic
            if ($p === 'Bawah') {
                $qtyPenutup = $details->where('section', 'Bawah')->where('part_name', 'Penutup')->sum('total_quantity');
            } else {
                $qtyPenutup = $details->where('section', 'Penutup')->where('part_name', $p)->sum('total_quantity');
            }
            
            $defaults["Penutup $p"] = $qtyPenutup * 4;
        }

        $defaults['Kaki Balok'] = 0;
        $totalHargaPaku = 0;

        foreach ($defaults as $bagian => $titik) {
            // Find custom per_titik override sent from frontend
            $perTitik = 1;
            foreach ($customNails as $cn) {
                if (($cn['bagian'] ?? '') === $bagian) {
                    $perTitik = (int) ($cn['per_titik'] ?? 1);
                    break;
                }
            }

            // Determine Material From and To for fastener matching
            $typeFrom = 'Balok Kayu'; // Default
            $thkFrom = 0;
            $typeTo = 'Balok Kayu';
            $thkTo = 0;

            if (str_starts_with($bagian, 'Penutup')) {
                $partName = str_replace('Penutup ', '', $bagian);
                $section = $partName === 'Bawah' ? 'Bawah' : 'Penutup';
                $partName = $partName === 'Bawah' ? 'Penutup' : $partName;
                
                $fromDetail = $details->where('section', $section)->where('part_name', $partName)->first();
                if ($fromDetail && isset($fromDetail->material_code)) {
                    $material = \App\Models\PackingMaterialPrice::where('code', $fromDetail->material_code)->first();
                    $typeFrom = $material && (stripos($material->jenis_material ?? '', 'Tripleks') !== false || stripos($material->component ?? '', 'TRIPLEKS') !== false) ? 'Triplek' : 'Papan Kayu';
                    $thkFrom = (float) $fromDetail->calculated_thickness;
                }
                
                // Usually attached to Penyangga
                $toPartName = $partName === 'Penutup' ? 'Penyangga' : $partName;
                $toSection = $section === 'Bawah' ? 'Bawah' : 'Penyangga';
                $toDetail = $details->where('section', $toSection)->where('part_name', $toPartName)->first();
                if ($toDetail) {
                    $thkTo = (float) $toDetail->calculated_thickness;
                } else {
                    // Fallback to Rangka if Penyangga doesn't exist
                    $toDetail = $details->where('section', 'Rangka')->first();
                    if ($toDetail) $thkTo = (float) $toDetail->calculated_thickness;
                }
            } elseif (str_starts_with($bagian, 'Penyangga')) {
                $partName = str_replace('Penyangga ', '', $bagian);
                $section = $partName === 'Bawah' ? 'Bawah' : 'Penyangga';
                $partName = $partName === 'Bawah' ? 'Penyangga' : $partName;
                
                $fromDetail = $details->where('section', $section)->where('part_name', $partName)->first();
                if ($fromDetail && isset($fromDetail->material_code)) {
                    $typeFrom = 'Balok Kayu';
                    $thkFrom = (float) $fromDetail->calculated_thickness;
                }
                
                // Attached to Penyangga Bawah or Kaki Balok
                if ($partName === 'Bawah') {
                    $toDetail = $details->where('section', 'Bawah')->where('part_name', 'Kaki Balok')->first();
                } else {
                    $toDetail = $details->where('section', 'Bawah')->where('part_name', 'Penyangga')->first() ?? $details->where('section', 'Bawah')->where('part_name', 'Kaki Balok')->first();
                }
                
                if ($toDetail) {
                    $thkTo = (float) $toDetail->calculated_thickness;
                }
            } elseif ($bagian === 'Kaki Balok') {
                $fromDetail = $details->where('section', 'Bawah')->where('part_name', 'Kaki Balok')->first();
                if ($fromDetail) {
                    $typeFrom = 'Balok Kayu';
                    $thkFrom = (float) $fromDetail->calculated_thickness;
                }
                $toDetail = $details->where('section', 'Bawah')->where('part_name', 'Rangka Lebar')->first() ?? $details->where('section', 'Bawah')->where('part_name', 'Penyangga')->first();
                if ($toDetail) {
                    $thkTo = (float) $toDetail->calculated_thickness;
                }
            }

            $matchedNailCode = null;
            if ($thkFrom > 0 && $thkTo > 0) {
                \Illuminate\Support\Facades\Log::info("Trying to match fastener for $bagian: From $typeFrom $thkFrom mm, To $typeTo $thkTo mm");
                
                // Try exact match first
                $matchedValidation = DB::table('packaging_fastener_validations')
                    ->where('is_active', true)
                    ->whereRaw('LOWER(type_from) LIKE ?', ['%' . strtolower($typeFrom) . '%'])
                    ->where('thk_from_min_mm', '<=', $thkFrom)
                    ->where('thk_from_max_mm', '>=', $thkFrom)
                    ->whereRaw('LOWER(type_to) LIKE ?', ['%' . strtolower($typeTo) . '%'])
                    ->where('thk_to_min_mm', '<=', $thkTo)
                    ->where('thk_to_max_mm', '>=', $thkTo)
                    ->orderBy('nail_length_mm', 'desc')
                    ->first();
                
                // Fallback 1: Ignore thkTo strictness if typeTo matches
                if (!$matchedValidation) {
                    $matchedValidation = DB::table('packaging_fastener_validations')
                        ->where('is_active', true)
                        ->whereRaw('LOWER(type_from) LIKE ?', ['%' . strtolower($typeFrom) . '%'])
                        ->where('thk_from_min_mm', '<=', $thkFrom)
                        ->where('thk_from_max_mm', '>=', $thkFrom)
                        ->whereRaw('LOWER(type_to) LIKE ?', ['%' . strtolower($typeTo) . '%'])
                        ->orderByRaw('ABS(thk_to_min_mm - ?)', [$thkTo])
                        ->orderBy('nail_length_mm', 'desc')
                        ->first();
                }

                // Fallback 2: Ignore thkFrom strictness as well, find closest From and To
                if (!$matchedValidation) {
                    $matchedValidation = DB::table('packaging_fastener_validations')
                        ->where('is_active', true)
                        ->whereRaw('LOWER(type_from) LIKE ?', ['%' . strtolower($typeFrom) . '%'])
                        ->whereRaw('LOWER(type_to) LIKE ?', ['%' . strtolower($typeTo) . '%'])
                        ->orderByRaw('ABS(thk_from_min_mm - ?) + ABS(thk_to_min_mm - ?)', [$thkFrom, $thkTo])
                        ->orderBy('nail_length_mm', 'desc')
                        ->first();
                }
                
                if ($matchedValidation) {
                    $matchedNailCode = $matchedValidation->nail_code;
                    \Illuminate\Support\Facades\Log::info("Matched Fastener: " . $matchedNailCode);
                } else {
                    \Illuminate\Support\Facades\Log::info("No fastener matched for $bagian (even with fallbacks)");
                }
            }

            $totalPaku = $titik * $perTitik;
            $estBerat = $totalPaku * $nailsWeightPerPiece;
            $totalHarga = $estBerat * $pricePerKg;

            $now = now();
            DB::table('packing_job_nails')->insert([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'job_id' => $calculation->id,
                'category' => 'Paku',
                'bagian' => $bagian,
                'kode_material' => $matchedNailCode,
                'titik_paku' => $titik,
                'jumlah_paku_per_titik' => $perTitik,
                'total_paku' => $totalPaku,
                'estimasi_berat' => $estBerat,
                'harga_per_kg' => $pricePerKg,
                'total_harga' => $totalHarga,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $totalHargaPaku += $totalHarga;
        }

        return $totalHargaPaku;
    }

    /**
     * Save manpower details to database.
     *
     * @param PackagingCalculation $calculation
     * @return float
     */
    public function saveManpowerDetails(PackagingCalculation $calculation)
    {
        // Load rate from config/packaging_settings.json
        $settingsPath = base_path('config/packaging_settings.json');
        $packagingSettings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : ['manpower_rate' => 10000];
        $rate = $packagingSettings['manpower_rate'] ?? 10000;

        $P = (float) ($calculation->panjang ?? $calculation->length ?? 0);
        $L = (float) ($calculation->lebar ?? $calculation->width ?? 0);
        $T = (float) ($calculation->tinggi ?? $calculation->height ?? 0);

        DB::table('packing_job_calc_manpowers')->where('job_id', $calculation->id)->delete();
        $totalHargaManpower = 0;

        $rows = [
            [
                'bagian' => 'Atas & Bawah',
                'panjang' => $P,
                'lebar' => $L,
                'sisi' => 2,
                'luas' => ($P / 1000) * ($L / 1000),
            ],
            [
                'bagian' => 'Kanan & Kiri',
                'panjang' => $L,
                'lebar' => $T,
                'sisi' => 2,
                'luas' => ($L / 1000) * ($T / 1000),
            ],
            [
                'bagian' => 'Depan & Belakang',
                'panjang' => $P,
                'lebar' => $T,
                'sisi' => 2,
                'luas' => ($P / 1000) * ($T / 1000),
            ],
        ];

        $now = now();
        foreach ($rows as $row) {
            $totalLuas = $row['luas'] * $row['sisi'];
            $totalBiaya = $totalLuas * $rate;

            DB::table('packing_job_calc_manpowers')->insert([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'job_id' => $calculation->id,
                'bagian' => $row['bagian'],
                'panjang' => $row['panjang'],
                'lebar' => $row['lebar'],
                'sisi' => $row['sisi'],
                'luas' => $row['luas'],
                'total_luas' => $totalLuas,
                'rate' => $rate,
                'total_biaya' => $totalBiaya,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $totalHargaManpower += $totalBiaya;
        }

        return $totalHargaManpower;
    }

    /**
     * Calculate details in-memory.
     *
     * @param array $params
     * @param string $arahGlobal
     * @param array $customDetails
     * @return array
     */
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
            $qtyPenyanggaBawah = max(0, (int)ceil($L / $jarakBawah) - 1);
        }

        $qtyKB = 0;
        if ($P < 1600) {
            $qtyKB = 2;
        } else {
            $qtyKB = max(2, (int)ceil($P / 800));
        }

        if ($kbInclude == 1 || $kbInclude === '1') {
            $panjangKB = $L + $tebalPenutupDepan + $tebalPenutupBelakang;
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
            $atasOverride = collect($customDetails)->where('section', 'Penyangga')->where('part_name', 'Atas')->first();
            
            $isIncluded = $override['include'] ?? ($atasOverride['include'] ?? $atasPenyanggaInclude);
            if ($isIncluded == 0 || $isIncluded === '0') continue;

            $pKode = $override['material_kode'] ?? ($atasOverride['material_kode'] ?? 'BK02');
            $pMat = $getMat($pKode);
            $tebal = $pMat ? (float)$pMat->thickness : 0;
            $lebar = $pMat ? (float)$pMat->width : 0;

            if ($pName === 'Atas') {
                $pPanjang = $P; 
                $pQty = $qtyKB; 
            } elseif ($pName === 'Kanan' || $pName === 'Kiri') {
                $pPanjang = $tinggiTiang;
                if ($qtyPenyanggaBawah <= 3) {
                    $pQty = 1;
                } else {
                    $pQty = $qtyPenyanggaBawah - 2;
                }
            } else {
                $pPanjang = $tinggiTiang;
                $pQty = $qtyKB; 
            }

            $arah = $override['direction'] ?? ($override['arah'] ?? ($atasOverride['direction'] ?? ($atasOverride['arah'] ?? ($pName === 'Atas' ? 'Horizontal' : 'Vertikal'))));
            $details[] = $this->formatDetailRow('Penyangga', $pName, $pMat, $pKode, $arah, '', $tebal, $lebar, $pPanjang, max(0, $pQty), 1);
        }

        // --- 4. HITUNG PAPAN PENUTUP ---
        $penutupParts = ['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'];
        foreach ($penutupParts as $pName) {
            $override = collect($customDetails)->where('section', 'Penutup')->where('part_name', $pName)->first();
            $atasOverride = collect($customDetails)->where('section', 'Penutup')->where('part_name', 'Atas')->first();
            
            $tipePenutup = $override['tipe_penutup'] ?? ($atasOverride['tipe_penutup'] ?? $jenisPenutup);
            $isIncluded = $override['include'] ?? ($atasOverride['include'] ?? 1);

            if ($isIncluded == 0 || $isIncluded === '0' || $tipePenutup == 'Tanpa Penutup' || $tipePenutup == 'Tidak Pakai Papan' || $tipePenutup == 'Tidak makai penutup' || empty($tipePenutup)) {
                continue;
            }

            $isTripleks = stripos($tipePenutup, 'Tripleks') !== false;
            $pKode = $override['material_kode'] ?? ($atasOverride['material_kode'] ?? ($isTripleks ? 'TP01' : 'PN03'));
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

            $arah = $override['direction'] ?? ($override['arah'] ?? ($atasOverride['direction'] ?? ($atasOverride['arah'] ?? 'Horizontal')));
            $details[] = $this->formatDetailRow('Penutup', $pName, $pMat, $pKode, $arah, $tipePenutup, $tebal, $partWidth, $pPanjang, max(0, $pQty), 1);
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
}
