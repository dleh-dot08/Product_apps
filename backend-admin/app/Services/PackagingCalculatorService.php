<?php

namespace App\Services;

use App\Models\PackagingJob as PackagingCalculation;
use App\Models\PackingMaterialPrice;
use App\Models\PackagingJob;
use Illuminate\Support\Facades\DB;

class PackagingCalculatorService
{
    /**
     * Calculate and store breakdown details for the new JobDetail system.
     *
     * @param PackagingJob $detail
     * @return PackagingJob
     */
    public function calculateForJobDetail(PackagingJob $detail, array $extraParams = [])
    {
        return DB::transaction(function () use ($detail, $extraParams) {
            // Hapus data lama terkait job detail ini
            DB::table('packing_job_calc_details')->where('job_id', $detail->id)->delete();
            DB::table('packing_job_calc_manpowers')->where('job_id', $detail->id)->delete();
            DB::table('packing_job_nails')->where('job_id', $detail->id)->delete();

            $customDetails = [];
            
            // Penyangga (Atas, Kanan, Kiri, Depan, Belakang)
            if ($detail->atas_penyanggah_status !== null) {
                foreach (['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'] as $partName) {
                    $customDetails[] = [
                        'section' => 'Penyangga',
                        'part_name' => $partName,
                        'include' => strtolower($detail->atas_penyanggah_status ?? 'include') === 'include' ? '1' : '0',
                        'direction' => $detail->atas_penyanggah_arahpemasangan ?? 'Vertikal',
                        'material_kode' => $detail->atas_penyanggah_material ?? 'BK02',
                    ];
                }
            }
            
            // Penutup (Atas, Kanan, Kiri, Depan, Belakang)
            if ($detail->atas_penutup_status !== null) {
                $tipe = $detail->atas_penutup_status ?? 'Tanpa Penutup';
                foreach (['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'] as $partName) {
                    $customDetails[] = [
                        'section' => 'Penutup',
                        'part_name' => $partName,
                        'include' => ($tipe === 'Tanpa Penutup' || empty($tipe)) ? '0' : '1',
                        'direction' => $detail->atas_penutup_arahpemasangan ?? 'Horizontal',
                        'material_kode' => $detail->atas_penutup_material ?? 'PN03',
                        'tipe_penutup' => $tipe,
                    ];
                }
            }

            // Bawah Penyangga
            if ($detail->bawah_penyanggah_status !== null) {
                $customDetails[] = [
                    'section' => 'Bawah',
                    'part_name' => 'Penyangga',
                    'include' => strtolower($detail->bawah_penyanggah_status ?? 'include') === 'include' ? '1' : '0',
                    'direction' => $detail->bawah_penyanggah_arahpemasangan ?? 'Horizontal',
                    'material_kode' => $detail->bawah_penyanggah_material ?? 'BK02',
                ];
            }
            
            // Bawah Kaki Balok
            if ($detail->bawah_kakibalok_status !== null) {
                $customDetails[] = [
                    'section' => 'Bawah',
                    'part_name' => 'Kaki Balok',
                    'include' => strtolower($detail->bawah_kakibalok_status ?? 'include') === 'include' ? '1' : '0',
                    'direction' => $detail->bawah_kakibalok_arahpemasangan ?? 'Vertikal',
                    'material_kode' => $detail->bawah_kakibalok_material ?? 'BK02',
                ];
            }
            
            // Bawah Penutup
            if ($detail->bawah_penutup_status !== null) {
                $tipe = $detail->bawah_penutup_status ?? 'Tanpa Penutup';
                $customDetails[] = [
                    'section' => 'Bawah',
                    'part_name' => 'Penutup',
                    'include' => ($tipe === 'Tanpa Penutup' || empty($tipe)) ? '0' : '1',
                    'direction' => $detail->bawah_penutup_arahpemasangan ?? 'Horizontal',
                    'material_kode' => $detail->bawah_penutup_material ?? 'PN03',
                    'tipe_penutup' => $tipe,
                ];
            }

            $params = [
                'length' => (float) ($detail->panjang ?? $detail->length ?? 0),
                'width' => (float) ($detail->lebar ?? $detail->width ?? 0),
                'height' => (float) ($detail->tinggi ?? $detail->height ?? 0),
                'jarak_penyanggah_atas' => (float) ($detail->jarak_penyanggah_atas ?? $detail->jarak_penyanggah ?? 300),
                'jarak_penyanggah_bawah' => (float) ($detail->jarak_penyanggah_bawah ?? $detail->jarak_penyanggah ?? 300),
                'distance_between_pillars' => (float) ($detail->jarak_penyanggah_bawah ?? $detail->jarak_penyanggah ?? 300),
                'gap_atas' => (float) ($detail->gap_atas ?? 0),
                'gap_bawah' => (float) ($detail->gap_bawah ?? 0),
                'cover_type' => 'Tidak makai penutup', // akan ditimpa per part
                'include_pallet_base' => strtolower($detail->bawah_kakibalok_status ?? 'include') === 'include' ? 1 : 0,
                'jarak_balok_additional' => 0,
                'bawah_penyangga_include' => strtolower($detail->bawah_penyanggah_status ?? 'include') === 'include' ? '1' : '0',
                'bawah_penutup_tipe' => $detail->bawah_penutup_status ?? 'Tanpa Penutup',
            ];
            $params = array_merge($params, $extraParams);

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
    public function saveNailsForJobDetail(PackagingJob $detail, array $customDetails = [])
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

            // Map typeFrom ke kategori nail_size_rules: Balok, Triplek
            $nailFrom = 'Balok';
            if (stripos($typeFrom, 'Triplek') !== false || stripos($typeFrom, 'Triplex') !== false) {
                $nailFrom = 'Triplek';
            }
            $nailTo = 'Balok';

            $matchedNailSize = null;
            if ($thkFrom > 0 && $thkTo > 0) {
                // Exact match
                $matchedRule = DB::table('nail_size_rules')
                    ->where('from', $nailFrom)->where('to', $nailTo)
                    ->where('thk_from', (int)$thkFrom)->where('thk_to', (int)$thkTo)
                    ->first();

                // Fallback: nearest thk_from >= actual, nearest thk_to >= actual
                if (!$matchedRule) {
                    $matchedRule = DB::table('nail_size_rules')
                        ->where('from', $nailFrom)->where('to', $nailTo)
                        ->where('thk_from', '>=', (int)$thkFrom)
                        ->where('thk_to', '>=', (int)$thkTo)
                        ->orderBy('thk_from')->orderBy('thk_to')
                        ->first();
                }

                // Fallback: highest available thk_from, highest thk_to
                if (!$matchedRule) {
                    $matchedRule = DB::table('nail_size_rules')
                        ->where('from', $nailFrom)->where('to', $nailTo)
                        ->orderByDesc('thk_from')->orderByDesc('thk_to')
                        ->first();
                }

                if ($matchedRule) {
                    $matchedNailSize = $matchedRule->size_nails;
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
                    'kode_material' => $matchedNailSize,
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
    public function saveManpowerForJobDetail(PackagingJob $detail)
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
                // Penyangga (Atas, Kanan, Kiri, Depan, Belakang)
                if ($calculation->atas_penyanggah_status !== null) {
                    foreach (['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'] as $partName) {
                        $customDetails[] = [
                            'section' => 'Penyangga',
                            'part_name' => $partName,
                            'include' => strtolower($calculation->atas_penyanggah_status ?? 'include') === 'include' ? '1' : '0',
                            'direction' => $calculation->atas_penyanggah_arahpemasangan ?? $calculation->atas_penyanggah_arah ?? 'Vertikal',
                            'material_kode' => $calculation->atas_penyanggah_material ?? 'BK02',
                        ];
                    }
                }
                
                // Penutup (Atas, Kanan, Kiri, Depan, Belakang)
                if ($calculation->atas_penutup_status !== null) {
                    $tipe = $calculation->atas_penutup_status ?? 'Tanpa Penutup';
                    foreach (['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'] as $partName) {
                        $customDetails[] = [
                            'section' => 'Penutup',
                            'part_name' => $partName,
                            'include' => ($tipe === 'Tanpa Penutup' || empty($tipe)) ? '0' : '1',
                            'direction' => $calculation->atas_penutup_arahpemasangan ?? $calculation->atas_penutup_arah ?? 'Horizontal',
                            'material_kode' => $calculation->atas_penutup_material ?? 'PN03',
                            'tipe_penutup' => $tipe,
                        ];
                    }
                }

                // Bawah Penyangga
                if ($calculation->bawah_penyanggah_status !== null) {
                    $customDetails[] = [
                        'section' => 'Bawah',
                        'part_name' => 'Penyangga',
                        'include' => strtolower($calculation->bawah_penyanggah_status ?? 'include') === 'include' ? '1' : '0',
                        'direction' => $calculation->bawah_penyanggah_arahpemasangan ?? $calculation->bawah_penyanggah_arah ?? 'Horizontal',
                        'material_kode' => $calculation->bawah_penyanggah_material ?? 'BK02',
                    ];
                }
                
                // Bawah Kaki Balok
                if ($calculation->bawah_kakibalok_status !== null || $calculation->bawah_kaki_balok_status !== null) {
                    $customDetails[] = [
                        'section' => 'Bawah',
                        'part_name' => 'Kaki Balok',
                        'include' => strtolower($calculation->bawah_kakibalok_status ?? $calculation->bawah_kaki_balok_status ?? 'include') === 'include' ? '1' : '0',
                        'direction' => $calculation->bawah_kakibalok_arahpemasangan ?? $calculation->bawah_kakibalok_arah ?? $calculation->bawah_kaki_balok_arah ?? 'Vertikal',
                        'material_kode' => $calculation->bawah_kakibalok_material ?? $calculation->bawah_kaki_balok_material ?? 'BK02',
                    ];
                }
                
                // Bawah Penutup
                if ($calculation->bawah_penutup_status !== null) {
                    $tipe = $calculation->bawah_penutup_status ?? 'Tanpa Penutup';
                    $customDetails[] = [
                        'section' => 'Bawah',
                        'part_name' => 'Penutup',
                        'include' => ($tipe === 'Tanpa Penutup' || empty($tipe)) ? '0' : '1',
                        'direction' => $calculation->bawah_penutup_arahpemasangan ?? $calculation->bawah_penutup_arah ?? 'Horizontal',
                        'material_kode' => $calculation->bawah_penutup_material ?? 'PN03',
                        'tipe_penutup' => $tipe,
                    ];
                }
            }

            $params = [
                'length' => (float) ($calculation->panjang ?? $calculation->length ?? 0),
                'width' => (float) ($calculation->lebar ?? $calculation->width ?? 0),
                'height' => (float) ($calculation->tinggi ?? $calculation->height ?? 0),
                'jarak_penyanggah_atas' => (float) ($calculation->jarak_penyanggah_atas ?? $calculation->distance_between_pillars_top ?? $calculation->jarak_penyanggah ?? $calculation->distance_between_pillars ?? 300),
                'jarak_penyanggah_bawah' => (float) ($calculation->jarak_penyanggah_bawah ?? $calculation->distance_between_pillars_bottom ?? $calculation->jarak_penyanggah ?? $calculation->distance_between_pillars ?? 300),
                'distance_between_pillars' => (float) ($calculation->jarak_penyanggah_bawah ?? $calculation->distance_between_pillars_bottom ?? $calculation->jarak_penyanggah ?? $calculation->distance_between_pillars ?? 300),
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
            $perTitik = 3;
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

            // Map typeFrom ke kategori nail_size_rules
            $nailFrom = 'Balok';
            if (stripos($typeFrom, 'Triplek') !== false || stripos($typeFrom, 'Triplex') !== false) {
                $nailFrom = 'Triplek';
            }
            $nailTo = 'Balok';

            $matchedNailSize = null;
            if ($thkFrom > 0 && $thkTo > 0) {
                // Exact match
                $matchedRule = DB::table('nail_size_rules')
                    ->where('from', $nailFrom)->where('to', $nailTo)
                    ->where('thk_from', (int)$thkFrom)->where('thk_to', (int)$thkTo)
                    ->first();

                // Fallback: nearest thk_from >= actual, nearest thk_to >= actual
                if (!$matchedRule) {
                    $matchedRule = DB::table('nail_size_rules')
                        ->where('from', $nailFrom)->where('to', $nailTo)
                        ->where('thk_from', '>=', (int)$thkFrom)
                        ->where('thk_to', '>=', (int)$thkTo)
                        ->orderBy('thk_from')->orderBy('thk_to')
                        ->first();
                }

                // Fallback: highest available
                if (!$matchedRule) {
                    $matchedRule = DB::table('nail_size_rules')
                        ->where('from', $nailFrom)->where('to', $nailTo)
                        ->orderByDesc('thk_from')->orderByDesc('thk_to')
                        ->first();
                }

                if ($matchedRule) {
                    $matchedNailSize = $matchedRule->size_nails;
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
                'kode_material' => $matchedNailSize,
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
        $materialLengthM = ($material && (float)$material->length > 0) ? (float)$material->length / 1000 : 1;
        $pricePerUnit = $material ? (float)$material->unit_price / $materialLengthM : 0;
        
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

    private function calculateOuterDimensions($P, $L, $tebalPenutupAtas) {
        return [
            'outerP' => $P + (2 * $tebalPenutupAtas),
            'outerL' => $L + (2 * $tebalPenutupAtas)
        ];
    }

    private function calculateBottomLegLayout($outerP, $outerL, $arah, $include) {
        if ($include == 0 || $include === '0' || $include === 'Exclude' || strtolower($include) === 'exclude') {
            return ['qty' => 0, 'length' => 0];
        }
        $dimensiSusun = ($arah === 'Horizontal') ? $outerP : $outerL;
        $qty = max(0, ceil($dimensiSusun / 800));
        $length = ($arah === 'Horizontal') ? $outerL : $outerP;
        return ['qty' => $qty, 'length' => $length];
    }

    private function calculateBottomSupportLayout($outerP, $outerL, $arah, $lebarPenyangga, $celahPenyangga, $include) {
        if ($include == 0 || $include === '0' || $include === 'Exclude' || strtolower($include) === 'exclude' || $lebarPenyangga <= 0) {
            return ['qty' => 0, 'length' => 0, 'sisa_ujung' => 0];
        }
        $dimensiSusun = ($arah === 'Horizontal') ? $outerP : $outerL;
        $length = ($arah === 'Horizontal') ? $outerL : $outerP;
        
        $areaPerSisi = ($dimensiSusun - $lebarPenyangga) / 2;
        $langkahPenyangga = $celahPenyangga + $lebarPenyangga;
        
        $qtyPerSisi = ($langkahPenyangga > 0) ? floor($areaPerSisi / $langkahPenyangga) : 0;
        
        $totalQty = 1 + (2 * $qtyPerSisi);
        $sisaUjungTotal = $dimensiSusun - ($totalQty * $lebarPenyangga) - (($totalQty - 1) * $celahPenyangga);
        $sisaUjung = max(0, $sisaUjungTotal / 2);
        
        return ['qty' => $totalQty, 'length' => $length, 'sisa_ujung' => $sisaUjung];
    }

    private function calculateBottomCoverLayout($qtyPenyangga, $arah, $outerP, $outerL, $celahPenyangga, $celahPenutup, $lebarPenutup, $include, $tipePenutup, $isTripleks) {
        if ($include == 0 || $include === '0' || $include === 'Exclude' || strtolower($include) === 'exclude' || empty($tipePenutup) || $tipePenutup === 'Tanpa Penutup' || $tipePenutup === 'Tidak makai penutup') {
            return ['qty' => 0, 'length' => 0];
        }
        
        $length = ($arah === 'Horizontal') ? $outerL : $outerP;
        
        if ($isTripleks || $lebarPenutup <= 0) {
            return ['qty' => 1, 'length' => $length];
        }
        
        $qtyTotal = 0;
        if ($qtyPenyangga > 1) {
            $totalSpaces = $qtyPenyangga - 1;
            $langkahPenutup = $lebarPenutup + $celahPenutup;
            $qtyCoverPerSpace = ($langkahPenutup > 0) ? floor(($celahPenyangga + $celahPenutup) / $langkahPenutup) : 0;
            $qtyTotal = 2 + ($totalSpaces * $qtyCoverPerSpace);
        } elseif ($qtyPenyangga == 1) {
            $qtyTotal = 2; // only left and right ends
        } else {
            $qtyTotal = 0;
        }

        return ['qty' => $qtyTotal, 'length' => $length];
    }

    public function buildDetailsArray(array $params, string $arahGlobal = 'Horizontal', array $customDetails = [])
    {
        $P = (float)($params['length'] ?? 0);
        $L = (float)($params['width'] ?? 0);
        $T = (float)($params['height'] ?? 0);
        
        $jarakAtas = (float) (
            $params['jarak_penyanggah_atas'] ?? 
            $params['distance_between_pillars_top'] ?? 
            $params['distance_between_pillars'] ?? 
            300
        );
        $jarakBawah = (float) (
            $params['jarak_penyanggah_bawah'] ?? 
            $params['distance_between_pillars_bottom'] ?? 
            $params['distance_between_pillars'] ?? 
            300
        );

        if ($jarakAtas <= 0) $jarakAtas = 300;
        if ($jarakBawah <= 0) $jarakBawah = 300;

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

        $ptkKode = $getMatKode('Penutup', 'Kanan', 'PN03');
        $ptkMat = $getMat($ptkKode);
        $tebalPenutupKanan = $ptkMat ? (float)$ptkMat->thickness : 0;

        $ptklKode = $getMatKode('Penutup', 'Kiri', 'PN03');
        $ptklMat = $getMat($ptklKode);
        $tebalPenutupKiri = $ptklMat ? (float)$ptklMat->thickness : 0;

        $paKode = $getMatKode('Penyangga', 'Atas', 'BK02');
        $paMat = $getMat($paKode);
        $paOverride = collect($customDetails)->where('section', 'Penyangga')->where('part_name', 'Atas')->first();
        $tebalPenyanggaAtas = $paMat ? (float)$paMat->thickness : (float)($paOverride['calculated_thickness'] ?? 0);
        $lebarPenyanggaAtas = $paMat ? (float)$paMat->width : (float)($paOverride['calculated_width'] ?? 0);

        $pkananKode = $getMatKode('Penyangga', 'Kanan', 'BK02');
        $pkananMat = $getMat($pkananKode);
        $tebalPenyanggaKanan = $pkananMat ? (float)$pkananMat->thickness : 0;
        
        $pkiriKode = $getMatKode('Penyangga', 'Kiri', 'BK02');
        $pkiriMat = $getMat($pkiriKode);
        $tebalPenyanggaKiri = $pkiriMat ? (float)$pkiriMat->thickness : 0;

        // --- 2. HITUNG AREA BAWAH ---
        $kbInclude = $params['include_pallet_base'] ?? 1;
        $pbInclude = $params['bawah_penyangga_include'] ?? 1;

        // Ambil ketebalan penutup atas untuk menghitung Dimensi Luar
        $ptaKode = $getMatKode('Penutup', 'Atas', 'PN03');
        $ptaMat = $getMat($ptaKode);
        $tebalPenutupAtas = $ptaMat ? (float)$ptaMat->thickness : 0;
        
        $outerDims = $this->calculateOuterDimensions($P, $L, $tebalPenutupAtas);
        $outerP = $outerDims['outerP'];
        $outerL = $outerDims['outerL'];

        // Kaki Balok
        $kbOverride = collect($customDetails)->where('section', 'Bawah')->where('part_name', 'Kaki Balok')->first();
        $kbDirection = $kbOverride['direction'] ?? $arahGlobal;
        $kbIncludeVal = $kbOverride['include'] ?? $kbInclude;
        $kbLayout = $this->calculateBottomLegLayout($outerP, $outerL, $kbDirection, $kbIncludeVal);
        
        $qtyKB = $kbLayout['qty'];
        if ($kbLayout['qty'] > 0) {
            $details[] = $this->formatDetailRow('Bawah', 'Kaki Balok', $kbMat, $kbKode, $kbDirection, '', $tebalKakiBalok, $lebarKakiBalok, $kbLayout['length'], $kbLayout['qty'], 1);
        }

        // Penyangga Bawah
        $pbOverride = collect($customDetails)->where('section', 'Bawah')->where('part_name', 'Penyangga')->first();
        $pbDirection = $pbOverride['direction'] ?? ($pbOverride['arah'] ?? $arahGlobal);
        $pbIncludeVal = $pbOverride['include'] ?? $pbInclude;
        $pbLayout = $this->calculateBottomSupportLayout($outerP, $outerL, $pbDirection, $lebarPenyanggaBawah, $jarakBawah, $pbIncludeVal);
        
        $qtyPenyanggaBawah = $pbLayout['qty'];
        if ($pbLayout['qty'] > 0) {
            $details[] = $this->formatDetailRow('Bawah', 'Penyangga', $pbMat, $pbKode, $pbDirection, '', $tebalPenyanggaBawah, $lebarPenyanggaBawah, $pbLayout['length'], $pbLayout['qty'], 1);
        }

        // Penutup Bawah
        $bawahPenutupOverride = collect($customDetails)->where('section', 'Bawah')->where('part_name', 'Penutup')->first();
        if ($bawahPenutupOverride) {
            $ptbInclude = $bawahPenutupOverride['include'] ?? 1;
            $ptbTipe = $bawahPenutupOverride['tipe_penutup'] ?? $ptbTipe;
            $ptbKode = $bawahPenutupOverride['material_kode'] ?? $ptbKode;
            $ptbMat = $getMat($ptbKode);
            $tebalPenutupBawah = $ptbMat ? (float)$ptbMat->thickness : (float)($bawahPenutupOverride['calculated_thickness'] ?? $tebalPenutupBawah);
        } else {
            $ptbInclude = 1;
        }
        
        $isTripleks = stripos($ptbTipe, 'Tripleks') !== false;
        $lebarPB = $ptbMat ? (float)$ptbMat->width : 0;
        
        // Celah penutup mengikuti gap_bawah, atau jika 'Setengah' pakai $celahBawah
        $celahPB = (stripos($ptbTipe, 'Setengah') !== false) ? $celahBawah : $celahBawah; // menggunakan gap_bawah yang dipassing
        
        $ptbDirection = $bawahPenutupOverride['direction'] ?? ($bawahPenutupOverride['arah'] ?? $arahGlobal);
        $ptbLayout = $this->calculateBottomCoverLayout($qtyPenyanggaBawah, $ptbDirection, $outerP, $outerL, $jarakBawah, $celahPB, $lebarPB, $ptbInclude, $ptbTipe, $isTripleks);
        
        if ($ptbLayout['qty'] > 0) {
            $partWidth = $isTripleks ? (($ptbDirection === 'Horizontal') ? $outerP : $outerL) : $lebarPB; // Jika tripleks, lebar disesuaikan dengan sisi menyilang
            $details[] = $this->formatDetailRow('Bawah', 'Penutup', $ptbMat, $ptbKode, $ptbDirection, $ptbTipe, $tebalPenutupBawah, $partWidth, $ptbLayout['length'], $ptbLayout['qty'], 1);
        }

        // --- 3. HITUNG PENYANGGA VERTIKAL ---
        $atasPenyanggaInclude = collect($customDetails)->where('section', 'Penyangga')->where('part_name', 'Atas')->first()['include'] ?? ($params['atas_penyangga_include'] ?? 1);
        $penyanggaParts = ['Atas', 'Kanan', 'Kiri', 'Depan', 'Belakang'];
        
        $tinggiTiang = $T + $tebalPenutupBawah + $tebalPenyanggaBawah + $lebarKakiBalok + $lebarPenyanggaAtas;

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
                
                $jarakSamping = $jarakAtas * 2;
                
                // Using outerP because Kanan and Kiri run along the length (Panjang) of the box
                $qtySamping = $jarakSamping > 0 ? (int) floor($outerP / $jarakSamping) : 0;
                
                $pQty = max(1, $qtySamping);
            } else {
                $pPanjang = $tinggiTiang;
                $pQty = $qtyKB; 
            }

            $arah = $override['direction'] ?? ($override['arah'] ?? ($atasOverride['direction'] ?? ($atasOverride['arah'] ?? 'Vertikal')));
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
