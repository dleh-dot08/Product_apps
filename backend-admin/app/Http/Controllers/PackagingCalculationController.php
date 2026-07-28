<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PackagingJobDetail as PackagingCalculation;
use Illuminate\Support\Facades\Validator;

class PackagingCalculationController extends Controller
{
    public function index()
    {
        $calculations = PackagingCalculation::orderBy('created_at', 'desc')->get();
        return view('packaging_calc.packaging-summary', compact('calculations'));
    }

    public function store(Request $request)
    {
        \Log::info("PackagingCalculation Store Process Started");
        \Log::info("Incoming Request Data: ", $request->all());


        $validator = Validator::make($request->all(), [
            'project_name' => 'nullable|string|max:255',
            'distance_between_pillars' => 'nullable|numeric',
            'gap_atas' => 'nullable|numeric',
            'gap_bawah' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'include_pallet_base' => 'nullable|boolean',
            'jarak_balok_additional' => 'nullable|numeric',
            'cover_type' => 'nullable|string|max:255',
            'atas_penutup_tipe' => 'nullable|string|max:255',
            'bawah_penutup_tipe' => 'nullable|string|max:255',
            'arah_pemasangan' => 'nullable|string|in:Horizontal,Vertikal',
            'total_material_cost' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            \Log::warning("Validation Fails. Errors: ", $validator->errors()->toArray());
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
                'debug_request' => $request->all()
            ], 422);
        }

        try {
            $insertData = [
                'packaging_job_id' => $request->packaging_job_id ?? null,
                'no_product' => $request->no_product ?? null,
                'desc_product' => $request->desc_product ?? null,
                'packaging_number' => $request->packaging_number ?? null,
                'qty_barang_dikirim' => $request->qty_barang_dikirim ?? 1,
                'qty_packaging' => $request->qty_packaging ?? 1,
                'qty_product_per_packaging' => $request->qty_product_per_packaging ?? 1,
                'panjang' => $request->length,
                'lebar' => $request->width,
                'tinggi' => $request->height,
                'jarak_penyanggah' => $request->distance_between_pillars,
                'gap_atas' => $request->gap_atas,
                'gap_bawah' => $request->gap_bawah,
                'status' => 'draft',
                'packer_id' => auth()->id() ?? null,
                
                // Konfigurasi Bawah
                'bawah_penyanggah_status' => $request->has('bawah_penyangga_include') ? ($request->bawah_penyangga_include ? 'Include' : 'Exclude') : null,
                'bawah_penyanggah_arah' => $request->bawah_penyangga_arah,
                'bawah_penyanggah_material' => $request->bawah_penyangga_material,
                'bawah_penutup_status' => $request->bawah_penutup_tipe,
                'bawah_penutup_arah' => $request->bawah_penutup_arah,
                'bawah_penutup_material' => $request->bawah_penutup_material,
                'bawah_kaki_balok_status' => $request->has('include_pallet_base') ? ($request->include_pallet_base ? 'Include' : 'Exclude') : null,
                'bawah_kaki_balok_arah' => $request->bawah_kakibalok_arah,
                'bawah_kaki_balok_material' => $request->bawah_kakibalok_material,
                
                // Konfigurasi Atas
                'atas_penyanggah_status' => $request->has('atas_penyangga_include') ? ($request->atas_penyangga_include ? 'Include' : 'Exclude') : null,
                'atas_penyanggah_arah' => $request->atas_penyangga_arah,
                'atas_penyanggah_material' => $request->atas_penyangga_material,
                'atas_penutup_status' => $request->atas_penutup_tipe,
                'atas_penutup_arah' => $request->atas_penutup_arah,
                'atas_penutup_material' => $request->atas_penutup_material,
            ];
            \Log::info("Creating PackagingCalculation database row with: ", $insertData);
            $calculation = PackagingCalculation::create($insertData);

            \Log::info("Invoking PackagingCalculatorService...");
            $extraParams = [
                // 'atas_rangka_include' ditiadakan
                'atas_penyangga_include' => $request->input('atas_penyangga_include'),

                'bawah_penyangga_include' => $request->input('bawah_penyangga_include'),
                'bawah_penutup_tipe' => $request->input('bawah_penutup_tipe'),
                'atas_penutup_tipe' => $request->input('atas_penutup_tipe'),
            ];
            $calculator = new \App\Services\PackagingCalculatorService();
            $calculator->calculate(
                $calculation, 
                $request->input('arah_pemasangan', 'Horizontal'), 
                $request->input('details', []), 
                $request->input('nails', []),
                $extraParams
            );
            \Log::info("PackagingCalculatorService completed successfully.");
            $calculation->refresh();

            return response()->json([
                'status' => 'success',
                'message' => 'Data packaging calculation berhasil disimpan.',
                'data' => $calculation,
                'debug_request' => $request->all()
            ]);
        } catch (\Exception $e) {
            \Log::error("Exception caught during PackagingCalculation Store.");
            \Log::error("Message: {$e->getMessage()}");
            \Log::error("Request Details (including cover_type / tipe_penutup): " . json_encode($request->input('details', [])));
            \Log::error("Stack Trace: " . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
                'debug_request' => $request->all()
            ], 500);
        }
    }

    public function show($id)
    {
        $calculation = PackagingCalculation::findOrFail($id);
        $materials = \App\Models\PackingMaterialPrice::all();
        $fasteners = \Illuminate\Support\Facades\DB::table('packaging_fastener_validations')->get();
        return view('packaging_calc.show', compact('calculation', 'materials', 'fasteners'));
    }

    public function update(Request $request, $id)
    {
        \Log::info("PackagingCalculation Update Process Started for ID: {$id}");
        \Log::info("Incoming Request Data: ", $request->all());


        $calculation = PackagingCalculation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'project_name' => 'nullable|string|max:255',
            'distance_between_pillars' => 'nullable|numeric',
            'gap_atas' => 'nullable|numeric',
            'gap_bawah' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'include_pallet_base' => 'nullable|boolean',
            'jarak_balok_additional' => 'nullable|numeric',
            'cover_type' => 'nullable|string|max:255',
            'atas_penutup_tipe' => 'nullable|string|max:255',
            'bawah_penutup_tipe' => 'nullable|string|max:255',
            'arah_pemasangan' => 'nullable|string|in:Horizontal,Vertikal',
            'total_material_cost' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            \Log::warning("Validation Fails for ID: {$id}. Errors: ", $validator->errors()->toArray());
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
                'debug_request' => $request->all()
            ], 422);
        }

        try {
            $updateData = [
                'panjang' => $request->length,
                'lebar' => $request->width,
                'tinggi' => $request->height,
                'jarak_penyanggah' => $request->distance_between_pillars,
                'gap_atas' => $request->gap_atas,
                'gap_bawah' => $request->gap_bawah,

                // Tambahan dari modal Step 1 & Step 2
                'no_product' => $request->no_product ?? $calculation->no_product,
                'desc_product' => $request->desc_product ?? $calculation->desc_product,
                'qty_packaging' => $request->qty_pack ?? $calculation->qty_packaging,
                'qty_product_per_packaging' => $request->qty_per_pack ?? $calculation->qty_product_per_packaging,
                'packer_id' => $request->packer_id ?? $calculation->packer_id,
                

                // Konfigurasi Bawah
                'bawah_penyanggah_status' => $request->has('bawah_penyangga_include') ? ($request->bawah_penyangga_include ? 'Include' : 'Exclude') : $calculation->bawah_penyanggah_status,
                'bawah_penyanggah_arah' => $request->bawah_penyangga_arah ?? $calculation->bawah_penyanggah_arah,
                'bawah_penyanggah_material' => $request->bawah_penyangga_material ?? $calculation->bawah_penyanggah_material,
                'bawah_penutup_status' => $request->bawah_penutup_tipe ?? $calculation->bawah_penutup_status,
                'bawah_penutup_arah' => $request->bawah_penutup_arah ?? $calculation->bawah_penutup_arah,
                'bawah_penutup_material' => $request->bawah_penutup_material ?? $calculation->bawah_penutup_material,
                'bawah_kaki_balok_status' => $request->has('include_pallet_base') ? ($request->include_pallet_base ? 'Include' : 'Exclude') : $calculation->bawah_kaki_balok_status,
                'bawah_kaki_balok_arah' => $request->bawah_kakibalok_arah ?? $calculation->bawah_kaki_balok_arah,
                'bawah_kaki_balok_material' => $request->bawah_kakibalok_material ?? $calculation->bawah_kaki_balok_material,
                
                // Konfigurasi Atas
                'atas_penyanggah_status' => $request->has('atas_penyangga_include') ? ($request->atas_penyangga_include ? 'Include' : 'Exclude') : $calculation->atas_penyanggah_status,
                'atas_penyanggah_arah' => $request->atas_penyangga_arah ?? $calculation->atas_penyanggah_arah,
                'atas_penyanggah_material' => $request->atas_penyangga_material ?? $calculation->atas_penyanggah_material,
                'atas_penutup_status' => $request->atas_penutup_tipe ?? $calculation->atas_penutup_status,
                'atas_penutup_arah' => $request->atas_penutup_arah ?? $calculation->atas_penutup_arah,
                'atas_penutup_material' => $request->atas_penutup_material ?? $calculation->atas_penutup_material,
            ];
            \Log::info("Updating PackagingCalculation database row with: ", $updateData);
            $calculation->update($updateData);

            // Update Parent Job if no_so is provided and different
            if ($request->has('no_so') && $request->no_so !== $calculation->job->no_so) {
                \Log::info("Updating Parent Job SO Number: " . $request->no_so);
                $calculation->job->update([
                    'no_so' => $request->no_so,
                    'customer' => $request->customer ?? $calculation->job->customer,
                    'date_delivery' => $request->date_delivery ?? $calculation->job->date_delivery,
                    'address' => $request->address ?? $calculation->job->address,
                ]);
            }

            \Log::info("Invoking PackagingCalculatorService...");
            $extraParams = [
                // 'atas_rangka_include' ditiadakan
                'atas_penyangga_include' => $request->input('atas_penyangga_include'),

                'bawah_penyangga_include' => $request->input('bawah_penyangga_include'),
                'bawah_penutup_tipe' => $request->input('bawah_penutup_tipe'),
                'atas_penutup_tipe' => $request->input('atas_penutup_tipe'),
            ];
            $calculator = new \App\Services\PackagingCalculatorService();
            $calculator->calculate(
                $calculation, 
                $request->input('arah_pemasangan', 'Horizontal'), 
                $request->input('details', []), 
                $request->input('nails', []),
                $extraParams
            );
            \Log::info("PackagingCalculatorService completed successfully.");
            $calculation->refresh();

            return response()->json([
                'status' => 'success',
                'message' => 'Data packaging calculation berhasil diperbarui.',
                'data' => $calculation,
                'debug_request' => $request->all()
            ]);
        } catch (\Exception $e) {
            \Log::error("Exception caught during PackagingCalculation Update for ID: {$id}.");
            \Log::error("Message: {$e->getMessage()}");
            \Log::error("Request Details (including cover_type / tipe_penutup): " . json_encode($request->input('details', [])));
            \Log::error("Stack Trace: " . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
                'debug_request' => $request->all()
            ], 500);
        }
    }

    public function simulate(Request $request) { \Log::info('SIMULATE PAYLOAD:', $request->all());
        $validator = Validator::make($request->all(), [
            'distance_between_pillars' => 'nullable|numeric',
            'gap_atas' => 'nullable|numeric',
            'gap_bawah' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'cover_type' => 'nullable|string|max:255',
            'arah_pemasangan' => 'nullable|string|in:Horizontal,Vertikal',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $params = [
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'distance_between_pillars' => $request->distance_between_pillars,
            'gap_atas' => $request->gap_atas,
            'gap_bawah' => $request->gap_bawah,
            'cover_type' => $request->input('cover_type') ?: ($request->input('atas_penutup_tipe') ?: $request->input('bawah_penutup_tipe')),
            'include_pallet_base' => $request->boolean('include_pallet_base', false),
            'jarak_balok_additional' => $request->jarak_balok_additional,
            // 'atas_rangka_include' ditiadakan
            'atas_penyangga_include' => $request->input('atas_penyangga_include'),

            'bawah_penyangga_include' => $request->input('bawah_penyangga_include'),
            'bawah_penutup_tipe' => $request->input('bawah_penutup_tipe'),
            'atas_penutup_tipe' => $request->input('atas_penutup_tipe'),
        ];

        $arahGlobal = $request->input('arah_pemasangan', 'Horizontal');
        $customDetails = $request->input('details', []);

        $service = new \App\Services\PackagingCalculatorService();
        $details = $service->buildDetailsArray($params, $arahGlobal, $customDetails);

        $costRangka = 0;
        $costPenyangga = 0;
        $costPenutup = 0;
        $costBawah = 0;
        foreach ($details as $detail) {
            if ($detail['section'] === 'Rangka') {
                $costRangka += $detail['subtotal_price'];
            } elseif ($detail['section'] === 'Penyangga') {
                $costPenyangga += $detail['subtotal_price'];
            } elseif ($detail['section'] === 'Penutup') {
                $costPenutup += $detail['subtotal_price'];
            } elseif ($detail['section'] === 'Bawah') {
                $costBawah += $detail['subtotal_price'];
            }
        }

        $areaKerja = 0;
        $P = (float) $request->length;
        $L = (float) $request->width;
        $T = (float) $request->height;
        if ($P && $L && $T) {
            $P_m = $P / 1000;
            $L_m = $L / 1000;
            $T_m = $T / 1000;
            $areaKerja = 2 * (($P_m * $L_m) + ($P_m * $T_m) + ($L_m * $T_m));
        }

        return response()->json([
            'status' => 'success',
            'details' => $details,
            'summary' => [
                'cost_rangka' => $costRangka,
                'cost_penyangga' => $costPenyangga,
                'cost_penutup' => $costPenutup,
                'cost_bawah' => $costBawah,
                'total_cost' => $costRangka + $costPenyangga + $costPenutup + $costBawah,
                'area_kerja' => $areaKerja,
            ]
        ]);
    }

    public function print($id)
    {
        $calculation = PackagingCalculation::findOrFail($id);
        $materials = \App\Models\PackingMaterialPrice::all();
        
        $settingsPath = base_path('config/packaging_settings.json');
        $packagingSettings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [
            'manpower_rate' => 10000,
            'nails_price_per_kg' => 25000,
            'nails_weight_per_piece' => 0.025
        ];
        $manpowerRate = $packagingSettings['manpower_rate'] ?? 10000;
        $nailsPricePerKg = $packagingSettings['nails_price_per_kg'] ?? 25000;
        $nailsWeightPerPiece = $packagingSettings['nails_weight_per_piece'] ?? 0.025;

        $crateImage = request('crate_image');
        $imgStep1 = request('img_step_1');
        $imgStep2 = request('img_step_2');
        $imgStep3 = request('img_step_3');
        $imgStep4 = request('img_step_4');
        $imgStep5 = request('img_step_5');
        $imgStep6 = request('img_step_6');
        $imgStep7 = request('img_step_7');
        $imgStep8 = request('img_step_8');
        $imgFullExploded = request('img_full_exploded');
        $imgFull = request('img_full');
        $crateImageMaterials = request('crate_image_materials');
        
        \Log::info("Print Image Sizes: ", [
            '1' => strlen((string)$imgStep1),
            '2' => strlen((string)$imgStep2),
            '3' => strlen((string)$imgStep3),
            '4' => strlen((string)$imgStep4),
            '5' => strlen((string)$imgStep5),
            '6' => strlen((string)$imgStep6),
            '7' => strlen((string)$imgStep7),
            '8' => strlen((string)$imgStep8)
        ]);

        $hasPenutupAtas = request('has_penutup_atas') == '1';
        $hasPenutupBawah = request('has_penutup_bawah') == '1';

        return view('packaging._print-tamplate', compact(
            'calculation',
            'materials',
            'manpowerRate',
            'nailsPricePerKg',
            'nailsWeightPerPiece',
            'crateImage',
            'imgStep1', 'imgStep2', 'imgStep3', 'imgStep4',
            'imgStep5', 'imgStep6', 'imgStep7', 'imgStep8', 'imgFullExploded', 'imgFull',
            'crateImageMaterials',
            'hasPenutupAtas',
            'hasPenutupBawah'
        ));
    }
}

