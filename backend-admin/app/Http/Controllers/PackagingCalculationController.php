<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PackagingJob as PackagingCalculation;
use Illuminate\Support\Facades\Validator;

class PackagingCalculationController extends Controller
{
    // Konstan untuk Rate Manpower (menit/m³)
    const RATE_POTONG = 30;
    const RATE_SERUT = 30;
    const RATE_PERAKITAN = 105;
    const RATE_PREPARE = 20;

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
            'jarak_penyanggah_atas' => 'nullable|numeric|min:0',
            'jarak_penyanggah_bawah' => 'nullable|numeric|min:0',
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

        $bawahPenyanggaArah = $request->bawah_penyangga_arah;
        $bawahPenutupArah = $request->bawah_penutup_arah;
        if ($bawahPenyanggaArah && $bawahPenutupArah && $bawahPenyanggaArah !== $bawahPenutupArah) {
            \Log::warning("Arah Penutup Bawah ({$bawahPenutupArah}) dinormalisasi agar sama dengan Arah Penyangga Bawah ({$bawahPenyanggaArah})");
            $bawahPenutupArah = $bawahPenyanggaArah;
        }

        try {
            $item0 = (is_array($request->items) && count($request->items) > 0) ? $request->items[0] : [];
            
            $additionalMat = $request->additional_mat ?? ($item0['additional_mat'] ?? null);
            
            $insertData = [
                'packaging_number' => $request->packaging_number ?? null,
                'qty_packaging' => $request->qty_packaging ?? 1,
                'panjang' => $request->length ?? ($item0['length'] ?? null),
                'lebar' => $request->width ?? ($item0['width'] ?? null),
                'tinggi' => $request->height ?? ($item0['height'] ?? null),
                'jarak_penyanggah_atas' => $request->jarak_penyanggah_atas ?? $request->distance_between_pillars ?? ($item0['jarak_penyanggah_atas'] ?? 0),
                'jarak_penyanggah_bawah' => $request->jarak_penyanggah_bawah ?? $request->distance_between_pillars ?? ($item0['jarak_penyanggah_bawah'] ?? 0),
                'gap_atas' => $request->gap_atas ?? ($item0['gap_atas'] ?? 0),
                'gap_bawah' => $request->gap_bawah ?? ($item0['gap_bawah'] ?? 0),
                'status' => 'draft',
                'packer_id' => auth()->id() ?? ($item0['packer'] ?? null),
                'type_packaging' => $request->type_packaging ?? ($item0['type_packaging'] ?? null),
                'inner_carton_boxes' => $request->has('inner_carton_boxes') 
                    ? json_encode($request->inner_carton_boxes) 
                    : (isset($item0['inner_carton_boxes']) ? json_encode($item0['inner_carton_boxes']) : null),
                'additional_mat' => $additionalMat,
                'carton_material' => stripos($additionalMat ?? '', 'Carton') !== false 
                    ? ($request->carton_material ?? ($item0['carton_material'] ?? null)) : null,
                'carton_type_sablon' => stripos($additionalMat ?? '', 'Carton') !== false 
                    ? ($request->carton_type_sablon ?? ($item0['carton_type_sablon'] ?? null)) : null,
                'terpal_material' => stripos($additionalMat ?? '', 'Terpal') !== false 
                    ? ($request->terpal_material ?? ($item0['terpal_material'] ?? null)) : null,
                
                // Konfigurasi Bawah
                'bawah_penyanggah_status' => $request->has('bawah_penyangga_include') ? ($request->bawah_penyangga_include ? 'Include' : 'Exclude') : (isset($item0['pb_status']) ? ($item0['pb_status'] ? 'Include' : 'Exclude') : null),
                'bawah_penyanggah_arahpemasangan' => $bawahPenyanggaArah ?? ($item0['pb_arah'] ?? null),
                'bawah_penyanggah_material' => $request->bawah_penyangga_material ?? ($item0['pb_material'] ?? null),
                'bawah_penutup_status' => $request->bawah_penutup_tipe ?? ($item0['ptb_status'] ?? null),
                'bawah_penutup_arahpemasangan' => $bawahPenutupArah ?? ($item0['ptb_arah'] ?? null),
                'bawah_penutup_material' => $request->bawah_penutup_material ?? ($item0['ptb_material'] ?? null),
                'bawah_kakibalok_status' => $request->has('include_pallet_base') ? ($request->include_pallet_base ? 'Include' : 'Exclude') : (isset($item0['kb_status']) ? ($item0['kb_status'] ? 'Include' : 'Exclude') : null),
                'bawah_kakibalok_arahpemasangan' => $request->bawah_kakibalok_arah ?? ($item0['kb_arah'] ?? null),
                'bawah_kakibalok_material' => $request->bawah_kakibalok_material ?? ($item0['kb_material'] ?? null),
                
                // Konfigurasi Atas
                'atas_penyanggah_status' => $request->has('atas_penyangga_include') ? ($request->atas_penyangga_include ? 'Include' : 'Exclude') : (isset($item0['pa_status']) ? ($item0['pa_status'] ? 'Include' : 'Exclude') : null),
                'atas_penyanggah_arahpemasangan' => $request->atas_penyangga_arah ?? ($item0['pa_arah'] ?? null),
                'atas_penyanggah_material' => $request->atas_penyangga_material ?? ($item0['pa_material'] ?? null),
                'atas_penutup_status' => $request->atas_penutup_tipe ?? ($item0['pta_status'] ?? null),
                'atas_penutup_arahpemasangan' => $request->atas_penutup_arah ?? ($item0['pta_arah'] ?? null),
                'atas_penutup_material' => $request->atas_penutup_material ?? ($item0['pta_material'] ?? null),
            ];
            \Log::info("Creating PackagingCalculation database row with: ", $insertData);
            $calculation = PackagingCalculation::create($insertData);

            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemData) {
                    $calculation->items()->create([
                        'no_product' => $itemData['no_product'] ?? null,
                        'desc_product' => $itemData['desc_product'] ?? null,
                        'qty' => $itemData['qty'] ?? 1,
                        'no_so' => $itemData['no_so'] ?? null,
                        'customer' => $itemData['customer'] ?? null,
                    ]);
                }
            } else {
                $calculation->items()->create([
                    'no_product' => $request->no_product ?? null,
                    'desc_product' => $request->desc_product ?? null,
                    'qty' => $request->qty_order ?? ($request->qty_barang_dikirim ?? 1),
                    'no_so' => $request->no_so ?? null,
                    'customer' => $request->customer ?? null,
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

            // Hitung Manpower
            $panjang = $calculation->panjang ?? 0;
            $lebar = $calculation->lebar ?? 0;
            $tinggi = $calculation->tinggi ?? 0;
            
            // Asumsi dimensi dalam mm, maka konversi ke m³ dibagi 1.000.000.000
            $totalM3 = ($panjang * $lebar * $tinggi) / 1000000000;

            $potong = $totalM3 * self::RATE_POTONG;
            $serut = $totalM3 * self::RATE_SERUT;
            $perakitan = $totalM3 * self::RATE_PERAKITAN;
            $prepare = $totalM3 * self::RATE_PREPARE;
            
            $totalwaktuManpower = $potong + $serut + $perakitan + $prepare;

            $calculation->update([
                'manpower_potong' => $potong,
                'manpower_serut' => $serut,
                'manpower_perakitan' => $perakitan,
                'manpower_prepare' => $prepare,
                'total_waktu_manpower' => $totalwaktuManpower
            ]);

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
            'jarak_penyanggah_atas' => 'nullable|numeric|min:0',
            'jarak_penyanggah_bawah' => 'nullable|numeric|min:0',
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

            // Data Step 1
            'qty_pack' => 'nullable|integer|min:1',
            'packer_id' => 'nullable|uuid',
            'type_packaging' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.no_so' => 'required|string|max:255',
            'items.*.customer' => 'nullable|string|max:255',
            'items.*.no_product' => 'required|string|max:255',
            'items.*.desc_product' => 'nullable|string',
            'items.*.qty' => 'required|integer|min:1',
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

        $bawahPenyanggaArahUpdate = $request->bawah_penyangga_arah ?? $calculation->bawah_penyanggah_arahpemasangan;
        $bawahPenutupArahUpdate = $request->bawah_penutup_arah ?? $calculation->bawah_penutup_arahpemasangan;
        if ($bawahPenyanggaArahUpdate && $bawahPenutupArahUpdate && $bawahPenyanggaArahUpdate !== $bawahPenutupArahUpdate) {
            \Log::warning("Arah Penutup Bawah ({$bawahPenutupArahUpdate}) dinormalisasi agar sama dengan Arah Penyangga Bawah ({$bawahPenyanggaArahUpdate}) pada Update");
            $bawahPenutupArahUpdate = $bawahPenyanggaArahUpdate;
        }

        try {
            $updateData = [
                'panjang' => $request->length,
                'lebar' => $request->width,
                'tinggi' => $request->height,
                'jarak_penyanggah_atas' => $request->jarak_penyanggah_atas ?? $request->distance_between_pillars ?? 0,
                'jarak_penyanggah_bawah' => $request->jarak_penyanggah_bawah ?? $request->distance_between_pillars ?? 0,
                'gap_atas' => $request->gap_atas,
                'gap_bawah' => $request->gap_bawah,

                // Tambahan dari modal Step 1 & Step 2
                'qty_packaging' => $request->qty_pack ?? $calculation->qty_packaging,
                'packer_id' => $request->packer_id ?? $calculation->packer_id,
                'type_packaging' => $request->type_packaging ?? $calculation->type_packaging,
                // Memperbarui nilai "Bahan Penutup" (contoh: Papan / Triplek) dari front-end.
                // Disimpan di database karena nilai ini menentukan rules di step 3 Konfigurasi Area Atas & Bawah,
                // contoh: jika Peti Kayu maka Triplek dilarang, Palet Kayu Konfigurasi Atas disembunyikan.
                'tipe_penutup' => $request->tipe_penutup ?? $calculation->tipe_penutup,
                'additional_mat' => $request->additional_mat ?? $calculation->additional_mat,
                'carton_material' => stripos($request->additional_mat ?? $calculation->additional_mat ?? '', 'Carton') !== false ? ($request->carton_material ?? $calculation->carton_material) : null,
                'carton_type_sablon' => stripos($request->additional_mat ?? $calculation->additional_mat ?? '', 'Carton') !== false ? ($request->carton_type_sablon ?? $calculation->carton_type_sablon) : null,
                'terpal_material' => stripos($request->additional_mat ?? $calculation->additional_mat ?? '', 'Terpal') !== false ? ($request->terpal_material ?? $calculation->terpal_material) : null,
                'inner_carton_boxes' => $request->has('inner_carton_boxes') ? json_encode($request->inner_carton_boxes) : $calculation->inner_carton_boxes,
                'completion_date' => $request->completion_date ?? $calculation->completion_date,


                // Konfigurasi Bawah
                'bawah_penyanggah_status' => $request->has('bawah_penyangga_include') ? ($request->bawah_penyangga_include ? 'Include' : 'Exclude') : $calculation->bawah_penyanggah_status,
                'bawah_penyanggah_arahpemasangan' => $bawahPenyanggaArahUpdate,
                'bawah_penyanggah_material' => $request->bawah_penyangga_material ?? $calculation->bawah_penyanggah_material,
                'bawah_penutup_status' => $request->bawah_penutup_tipe ?? $calculation->bawah_penutup_status,
                'bawah_penutup_arahpemasangan' => $bawahPenutupArahUpdate,
                'bawah_penutup_material' => $request->bawah_penutup_material ?? $calculation->bawah_penutup_material,
                'bawah_kakibalok_status' => $request->has('include_pallet_base') ? ($request->include_pallet_base ? 'Include' : 'Exclude') : $calculation->bawah_kakibalok_status,
                'bawah_kakibalok_arahpemasangan' => $request->bawah_kakibalok_arah ?? $calculation->bawah_kakibalok_arahpemasangan,
                'bawah_kakibalok_material' => $request->bawah_kakibalok_material ?? $calculation->bawah_kakibalok_material,
                
                // Konfigurasi Atas
                'atas_penyanggah_status' => $request->has('atas_penyangga_include') ? ($request->atas_penyangga_include ? 'Include' : 'Exclude') : $calculation->atas_penyanggah_status,
                'atas_penyanggah_arahpemasangan' => $request->atas_penyangga_arah ?? $calculation->atas_penyanggah_arahpemasangan,
                'atas_penyanggah_material' => $request->atas_penyangga_material ?? $calculation->atas_penyanggah_material,
                'atas_penutup_status' => $request->atas_penutup_tipe ?? $calculation->atas_penutup_status,
                'atas_penutup_arahpemasangan' => $request->atas_penutup_arah ?? $calculation->atas_penutup_arahpemasangan,
                'atas_penutup_material' => $request->atas_penutup_material ?? $calculation->atas_penutup_material,
            ];
            \Log::info("Updating PackagingCalculation database row with: ", $updateData);
            $calculation->update($updateData);

            $calculation->refresh(); // Refresh agar calculate() baca JSON terbaru

            // =========================================================
            // Sync item Step 1
            // Seluruh item lama diganti dengan isi tabel terbaru dari modal.
            // Customer disimpan per item, bukan hanya dari header.
            // =========================================================
            $items = collect($request->input('items', []))
                ->map(function (array $itemData) {
                    return [
                        'no_so' => trim(
                            (string) ($itemData['no_so'] ?? '')
                        ),
                        'customer' => trim(
                            (string) ($itemData['customer'] ?? '')
                        ),
                        'no_product' => trim(
                            (string) ($itemData['no_product'] ?? '')
                        ),
                        'desc_product' => trim(
                            (string) ($itemData['desc_product'] ?? '')
                        ),
                        'qty' => max(
                            1,
                            (int) ($itemData['qty'] ?? 1)
                        ),
                    ];
                })
                ->filter(function (array $itemData) {
                    return $itemData['no_product'] !== '';
                })
                ->values();

            if ($items->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data produk Step 1 kosong.',
                ], 422);
            }

            $calculation->items()->delete();

            foreach ($items as $itemData) {
                $calculation->items()->create($itemData);
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

            // Hitung Manpower
            $panjang = $calculation->panjang ?? 0;
            $lebar = $calculation->lebar ?? 0;
            $tinggi = $calculation->tinggi ?? 0;
            
            // Asumsi dimensi dalam mm, maka konversi ke m³ dibagi 1.000.000.000
            $totalM3 = ($panjang * $lebar * $tinggi) / 1000000000;

            $potong = $totalM3 * self::RATE_POTONG;
            $serut = $totalM3 * self::RATE_SERUT;
            $perakitan = $totalM3 * self::RATE_PERAKITAN;
            $prepare = $totalM3 * self::RATE_PREPARE;
            
            $totalwaktuManpower = $potong + $serut + $perakitan + $prepare;

            $calculation->update([
                'manpower_potong' => $potong,
                'manpower_serut' => $serut,
                'manpower_perakitan' => $perakitan,
                'manpower_prepare' => $prepare,
                'total_waktu_manpower' => $totalwaktuManpower
            ]);

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
            'jarak_penyanggah_atas' => $request->input('jarak_penyanggah_atas', $request->input('distance_between_pillars', 300)),
            'jarak_penyanggah_bawah' => $request->input('jarak_penyanggah_bawah', $request->input('distance_between_pillars', 300)),
            'distance_between_pillars' => $request->input('jarak_penyanggah_bawah', $request->input('distance_between_pillars', 300)),
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

        $potong = 0;
        $serut = 0;
        $perakitan = 0;
        $prepare = 0;
        $totalM3 = 0;
        
        if ($P && $L && $T) {
            $totalM3 = ($P * $L * $T) / 1000000000;
            $potong = $totalM3 * self::RATE_POTONG;
            $serut = $totalM3 * self::RATE_SERUT;
            $perakitan = $totalM3 * self::RATE_PERAKITAN;
            $prepare = $totalM3 * self::RATE_PREPARE;
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
                'manpower_potong' => $potong,
                'manpower_serut' => $serut,
                'manpower_perakitan' => $perakitan,
                'manpower_prepare' => $prepare,
                'total_waktu_manpower' => $potong + $serut + $perakitan + $prepare,
                'volume_m3' => $totalM3,
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

