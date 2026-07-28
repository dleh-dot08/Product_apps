<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PackagingJob;
use App\Models\PackagingJobDetail;
use Illuminate\Support\Facades\DB;

class PackagingController extends Controller
{
    public function index()
    {
        // Mengambil data dari tabel packaging_jobs beserta details-nya terbaru di atas
        $packagingJobs = PackagingJob::with('details')->orderBy('created_at', 'desc')->get();
        return view('packaging.index', compact('packagingJobs'));
    }

    public function destroy(PackagingJob $packagingJob)
    {
        $packagingJob->delete();
        return redirect()->route('packaging.index')->with('success', 'Data packaging berhasil dihapus.');
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('PackagingController@store Payload: ', $request->all());

        $request->validate([
            'no_so' => 'required|string',
            'customer' => 'nullable|string',
            'date_delivery' => 'nullable|date',
            'address' => 'nullable|string',
            'packType' => 'required|string',
            'items' => 'required|array',
            'items.*.no_product' => 'required|string',
            'items.*.qty_barang_dikirim' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan Parent Job
            $job = PackagingJob::create([
                'no_so' => $request->no_so,
                'customer' => $request->customer,
                'date_delivery' => $request->date_delivery,
                'address' => $request->address,
                'daftar_iso_item_json' => $request->raw_api_data ?? null,
                'status' => 'draft',
            ]);

            // 2. Simpan Detail Items (Barang yang dipilih beserta konfigurasinya)
            foreach ($request->items as $index => $item) {
                $packNumber = 'PKG-' . date('Ymd') . '-' . str_pad($job->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                
                $konfigurasi_bawah = [
                    'kaki_balok' => [
                        'status' => $item['kb_status'] ?? 'Include',
                        'arah' => $item['kb_arah'] ?? 'Horizontal',
                        'material' => $item['kb_material'] ?? 'A001'
                    ],
                    'penyanggah' => [
                        'status' => $item['pb_status'] ?? 'Include',
                        'arah' => $item['pb_arah'] ?? 'Horizontal',
                        'material' => $item['pb_material'] ?? 'A001'
                    ],
                    'penutup' => [
                        'status' => $item['ptb_status'] ?? 'Tanpa Penutup',
                        'arah' => $item['ptb_arah'] ?? 'Horizontal',
                        'material' => $item['ptb_material'] ?? 'A001'
                    ],
                    'jarak_penyanggah' => $item['jarak'] ?? 0,
                    'gap_bawah' => $item['gap_bawah'] ?? 0,
                ];

                $konfigurasi_atas = [
                    'penyanggah' => [
                        'status' => $item['pa_status'] ?? 'Include',
                        'arah' => $item['pa_arah'] ?? 'Horizontal',
                        'material' => $item['pa_material'] ?? 'A001'
                    ],
                    'penutup' => [
                        'status' => $item['pta_status'] ?? 'Tanpa Penutup',
                        'arah' => $item['pta_arah'] ?? 'Horizontal',
                        'material' => $item['pta_material'] ?? 'A001'
                    ],
                    'gap_atas' => $item['gap_atas'] ?? 0,
                ];
                
                $detail = PackagingJobDetail::create([
                    'packaging_job_id' => $job->id,
                    'packaging_number' => $packNumber,
                    'packer_id' => !empty($item['packer']) ? $item['packer'] : null,
                    'no_product' => $item['no_barang'] ?? $item['no_product'] ?? '',
                    'desc_product' => $item['nama_barang'] ?? $item['desc_product'] ?? '',
                    
                    'qty_barang_dikirim' => $item['qty_kirim'] ?? 1,
                    'qty_packaging' => $item['qty_pack'] ?? 1,
                    'qty_product_per_packaging' => $item['qty_per_pack'] ?? 1,
                    
                    // Dimensi
                    'panjang' => $item['length'] ?? 0,
                    'lebar' => $item['width'] ?? 0,
                    'tinggi' => $item['height'] ?? 0,
                    
                    'gap_atas' => $item['gap_atas'] ?? 0,
                    'gap_bawah' => $item['gap_bawah'] ?? 0,
                    'jarak_penyanggah' => $item['jarak'] ?? 0,
                    
                    // Konfigurasi
                    'konfigurasi_atas' => $konfigurasi_atas,
                    'konfigurasi_bawah' => $konfigurasi_bawah,
                    
                    // Harga default
                    'subtotal_harga_material' => 0,
                    'subtotal_harga_paku' => 0,
                    'subtotal_man_power' => 0,
                    'harga_total' => 0,
                    'status' => 'draft'
                ]);

                $extraParams = [
                    'atas_penyangga_include' => $item['pa_status'] === 'Include' ? '1' : '0',
                    'bawah_penyangga_include' => $item['pb_status'] === 'Include' ? '1' : '0',
                    'bawah_penutup_tipe' => $item['ptb_status'] ?? 'Tanpa Penutup',
                    'atas_penutup_tipe' => $item['pta_status'] ?? 'Tanpa Penutup',
                ];

                $calculator = new \App\Services\PackagingCalculatorService();
                $calculator->calculateForJobDetail($detail, $extraParams);

                $detail->refresh();

                // Log data kalkulasi lengkap (Calculation All)
                \Illuminate\Support\Facades\Log::info('Calculation Result for Pack: ' . $packNumber, [
                    'detail_summary' => $detail->toArray(),
                    'calc_details' => \Illuminate\Support\Facades\DB::table('packing_job_calc_details')->where('job_id', $detail->id)->get()->toArray(),
                    'calc_manpowers' => \Illuminate\Support\Facades\DB::table('packing_job_calc_manpowers')->where('job_id', $detail->id)->get()->toArray(),
                    'calc_nails' => \Illuminate\Support\Facades\DB::table('packing_job_nails')->where('job_id', $detail->id)->get()->toArray(),
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Kalkulasi packaging berhasil disimpan.',
                'redirect' => route('packaging.calculations.show', $job->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error in PackagingController@store: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(PackagingJob $packagingJob)
    {
        $packagingJob->load('details');
        return view('packaging.edit', compact('packagingJob'));
    }

    public function update(Request $request, PackagingJob $packagingJob)
    {
        $request->validate([
            'no_so' => 'required|string',
            'customer' => 'nullable|string',
            'date_delivery' => 'nullable|date',
            'address' => 'nullable|string',
            'packType' => 'required|string',
            'items' => 'required|array',
            'items.*.no_product' => 'required|string',
            'items.*.qty_barang_dikirim' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update Parent Job
            $packagingJob->update([
                'no_so' => $request->no_so,
                'customer' => $request->customer,
                'date_delivery' => $request->date_delivery,
                'address' => $request->address,
                'daftar_iso_item_json' => $request->raw_api_data ?? $packagingJob->daftar_iso_item_json,
            ]);

            // Hapus detail lama agar kita bisa menimpa dengan array baru yang lebih akurat (menghindari duplikasi)
            $detailIds = $packagingJob->details()->pluck('id');
            \App\Models\PackingJobCalcDetail::whereIn('job_id', $detailIds)->delete();
            $packagingJob->details()->delete();

            // 2. Simpan Ulang Detail Items
            foreach ($request->items as $index => $item) {
                $packNumber = 'PKG-' . date('Ymd') . '-' . str_pad($packagingJob->id, 4, '0', STR_PAD_LEFT) . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                
                $konfigurasi_bawah = [
                    'kaki_balok' => [
                        'status' => $item['kb_status'] ?? 'Include',
                        'arah' => $item['kb_arah'] ?? 'Horizontal',
                        'material' => $item['kb_material'] ?? 'A001'
                    ],
                    'penyanggah' => [
                        'status' => $item['pb_status'] ?? 'Include',
                        'arah' => $item['pb_arah'] ?? 'Horizontal',
                        'material' => $item['pb_material'] ?? 'A001'
                    ],
                    'penutup' => [
                        'status' => $item['ptb_status'] ?? 'Tanpa Penutup',
                        'arah' => $item['ptb_arah'] ?? 'Horizontal',
                        'material' => $item['ptb_material'] ?? 'A001'
                    ],
                    'jarak_penyanggah' => $item['jarak'] ?? 0,
                    'gap_bawah' => $item['gap_bawah'] ?? 0,
                ];

                $konfigurasi_atas = [
                    'penyanggah' => [
                        'status' => $item['pa_status'] ?? 'Include',
                        'arah' => $item['pa_arah'] ?? 'Horizontal',
                        'material' => $item['pa_material'] ?? 'A001'
                    ],
                    'penutup' => [
                        'status' => $item['pta_status'] ?? 'Tanpa Penutup',
                        'arah' => $item['pta_arah'] ?? 'Horizontal',
                        'material' => $item['pta_material'] ?? 'A001'
                    ],
                    'gap_atas' => $item['gap_atas'] ?? 0,
                ];
                
                $detail = PackagingJobDetail::create([
                    'packaging_job_id' => $packagingJob->id,
                    'packaging_number' => $packNumber,
                    'packer_id' => !empty($item['packer']) ? $item['packer'] : null,
                    'no_product' => $item['no_barang'] ?? $item['no_product'] ?? '',
                    'desc_product' => $item['nama_barang'] ?? $item['desc_product'] ?? '',
                    
                    'qty_barang_dikirim' => $item['qty_kirim'] ?? 1,
                    'qty_packaging' => $item['qty_pack'] ?? 1,
                    'qty_product_per_packaging' => $item['qty_per_pack'] ?? 1,
                    
                    // Dimensi
                    'panjang' => $item['length'] ?? 0,
                    'lebar' => $item['width'] ?? 0,
                    'tinggi' => $item['height'] ?? 0,
                    
                    'gap_atas' => $item['gap_atas'] ?? 0,
                    'gap_bawah' => $item['gap_bawah'] ?? 0,
                    'jarak_penyanggah' => $item['jarak'] ?? 0,
                    
                    // Konfigurasi
                    'konfigurasi_atas' => $konfigurasi_atas,
                    'konfigurasi_bawah' => $konfigurasi_bawah,
                    
                    // Harga default
                    'subtotal_harga_material' => 0,
                    'subtotal_harga_paku' => 0,
                    'subtotal_man_power' => 0,
                    'harga_total' => 0,
                    'status' => 'draft'
                ]);

                $extraParams = [
                    'atas_penyangga_include' => $item['pa_status'] === 'Include' ? '1' : '0',
                    'bawah_penyangga_include' => $item['pb_status'] === 'Include' ? '1' : '0',
                    'bawah_penutup_tipe' => $item['ptb_status'] ?? 'Tanpa Penutup',
                    'atas_penutup_tipe' => $item['pta_status'] ?? 'Tanpa Penutup',
                ];

                $calculator = new \App\Services\PackagingCalculatorService();
                $calculator->calculateForJobDetail($detail, $extraParams);
                
                $detail->refresh();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Kalkulasi packaging berhasil diperbarui.',
                'redirect' => route('packaging.calculations.show', $packagingJob->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error in PackagingController@update: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
