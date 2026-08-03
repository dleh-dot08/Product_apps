<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PackagingJob;
use App\Models\PackagingJobItem;
use Illuminate\Support\Facades\DB;

class PackagingController extends Controller
{
    public function index()
    {
        // Mengambil data dari tabel packaging_jobs beserta details-nya terbaru di atas
        $packagingJobs = PackagingJob::with('details')->orderBy('created_at', 'desc')->get();
        $materials = DB::table('packing_material_prices')->orderBy('id')->get();
        $nails = DB::table('nail_size_rules')->orderBy('id')->get();
        return view('packaging.index', compact('packagingJobs', 'materials', 'nails'));
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
            'packType' => 'nullable|string',
            'type_packaging' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.no_so' => 'nullable|string',
            'items.*.customer' => 'nullable|string',
            'items.*.no_product' => 'required|string',
            'items.*.desc_product' => 'nullable|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.qty_kirim' => 'nullable|integer|min:1',
            'items.*.qty_barang_dikirim' => 'nullable|integer|min:1',
            'items.*.jarak_penyanggah_atas' => 'nullable|numeric|min:0',
            'items.*.jarak_penyanggah_bawah' => 'nullable|numeric|min:0',
            'items.*.gap_atas' => 'nullable|numeric|min:0',
            'items.*.gap_bawah' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Gunakan konfigurasi dari item pertama untuk membuat 1 Box Packaging
            $items = $request->input('items', []);
            $firstItem = reset($items);
            
            $job = PackagingJob::create([
                'type_packaging' => $request->input('type_packaging', $request->packType ?? 'Box'),
                'packaging_number' => 'PKG-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'packer_id' => !empty($firstItem['packer']) ? $firstItem['packer'] : null,
                'qty_packaging' => $firstItem['qty_pack'] ?? 1,
                'deadline' => $request->date_delivery,
                
                'panjang' => $firstItem['length'] ?? 0,
                'lebar' => $firstItem['width'] ?? 0,
                'tinggi' => $firstItem['height'] ?? 0,
                'gap_atas' => $firstItem['gap_atas'] ?? 0,
                'gap_bawah' => $firstItem['gap_bawah'] ?? 0,
                'jarak_penyanggah_atas' => $firstItem['jarak_penyanggah_atas'] ?? $firstItem['jarak_atas'] ?? $firstItem['jarak'] ?? 0,
                'jarak_penyanggah_bawah' => $firstItem['jarak_penyanggah_bawah'] ?? $firstItem['jarak_bawah'] ?? $firstItem['jarak'] ?? 0,
                
                'bawah_kakibalok_status' => $firstItem['kb_status'] ?? 'Include',
                'bawah_kakibalok_material' => $firstItem['kb_material'] ?? 'A001',
                'bawah_kakibalok_arahpemasangan' => $firstItem['kb_arah'] ?? 'Horizontal',
                'bawah_penyanggah_status' => $firstItem['pb_status'] ?? 'Include',
                'bawah_penyanggah_material' => $firstItem['pb_material'] ?? 'A001',
                'bawah_penyanggah_arahpemasangan' => $firstItem['pb_arah'] ?? 'Horizontal',
                'bawah_penutup_status' => $firstItem['ptb_status'] ?? 'Tanpa Penutup',
                'bawah_penutup_material' => $firstItem['ptb_material'] ?? 'A001',
                'bawah_penutup_arahpemasangan' => $firstItem['ptb_arah'] ?? 'Horizontal',
                
                'atas_penyanggah_status' => $firstItem['pa_status'] ?? 'Include',
                'atas_penyanggah_material' => $firstItem['pa_material'] ?? 'A001',
                'atas_penyanggah_arahpemasangan' => $firstItem['pa_arah'] ?? 'Horizontal',
                'atas_penutup_status' => $firstItem['pta_status'] ?? 'Tanpa Penutup',
                'atas_penutup_material' => $firstItem['pta_material'] ?? 'A001',
                'atas_penutup_arahpemasangan' => $firstItem['pta_arah'] ?? 'Horizontal',
                
                'status' => 'draft'
            ]);

            $job->update([
                'packaging_number' => 'PKG-' . date('Ymd') . '-' . str_pad($job->id, 4, '0', STR_PAD_LEFT)
            ]);

            // Masukkan semua produk Step 1 ke dalam packaging yang sama.
            // SO dan customer diambil per item agar tidak tertukar ketika
            // pengguna menambahkan produk dari SO/customer berbeda.
            foreach ($request->input('items', []) as $item) {
                $job->items()->create([
                    'no_so' => trim((string) (
                        $item['no_so'] ??
                        $request->no_so ??
                        ''
                    )),
                    'customer' => trim((string) (
                        $item['customer'] ??
                        $request->customer ??
                        ''
                    )),
                    'date_delivery' =>
                        $item['date_delivery'] ??
                        $request->date_delivery,
                    'address' =>
                        $item['address'] ??
                        $request->address,
                    'no_product' => trim((string) (
                        $item['no_product'] ??
                        $item['no_barang'] ??
                        ''
                    )),
                    'desc_product' => trim((string) (
                        $item['desc_product'] ??
                        $item['nama_barang'] ??
                        ''
                    )),
                    'qty' => max(1, (int) (
                        $item['qty'] ??
                        $item['qty_kirim'] ??
                        $item['qty_barang_dikirim'] ??
                        1
                    )),
                ]);
            }

            // Hitung harga box sekali saja berdasarkan dimensi/konfigurasi box
            $extraParams = [
                'atas_penyangga_include' => $firstItem['pa_status'] === 'Include' ? '1' : '0',
                'bawah_penyangga_include' => $firstItem['pb_status'] === 'Include' ? '1' : '0',
                'bawah_penutup_tipe' => $firstItem['ptb_status'] ?? 'Tanpa Penutup',
                'atas_penutup_tipe' => $firstItem['pta_status'] ?? 'Tanpa Penutup',
            ];

            $calculator = new \App\Services\PackagingCalculatorService();
            $calculator->calculateForJobDetail($job, $extraParams);

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
            'packType' => 'nullable|string',
            'type_packaging' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.no_so' => 'nullable|string',
            'items.*.customer' => 'nullable|string',
            'items.*.no_product' => 'required|string',
            'items.*.desc_product' => 'nullable|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.qty_kirim' => 'nullable|integer|min:1',
            'items.*.qty_barang_dikirim' => 'nullable|integer|min:1',
            'items.*.jarak_penyanggah_atas' => 'nullable|numeric|min:0',
            'items.*.jarak_penyanggah_bawah' => 'nullable|numeric|min:0',
            'items.*.gap_atas' => 'nullable|numeric|min:0',
            'items.*.gap_bawah' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $items = $request->input('items', []);
            $firstItem = reset($items);
            
            // Update 1 Box Packaging dengan konfigurasi dari form
            $packagingJob->update([
                'type_packaging' => $request->input('type_packaging', $request->packType ?? 'Box'),
                'packer_id' => !empty($firstItem['packer']) ? $firstItem['packer'] : null,
                'qty_packaging' => $firstItem['qty_pack'] ?? 1,
                'deadline' => $request->date_delivery,
                
                'panjang' => $firstItem['length'] ?? 0,
                'lebar' => $firstItem['width'] ?? 0,
                'tinggi' => $firstItem['height'] ?? 0,
                'gap_atas' => $firstItem['gap_atas'] ?? 0,
                'gap_bawah' => $firstItem['gap_bawah'] ?? 0,
                'jarak_penyanggah_atas' => $firstItem['jarak_penyanggah_atas'] ?? $firstItem['jarak_atas'] ?? $firstItem['jarak'] ?? $packagingJob->jarak_penyanggah_atas ?? 0,
                'jarak_penyanggah_bawah' => $firstItem['jarak_penyanggah_bawah'] ?? $firstItem['jarak_bawah'] ?? $firstItem['jarak'] ?? $packagingJob->jarak_penyanggah_bawah ?? 0,
                
                'bawah_kakibalok_status' => $firstItem['kb_status'] ?? 'Include',
                'bawah_kakibalok_material' => $firstItem['kb_material'] ?? 'A001',
                'bawah_kakibalok_arahpemasangan' => $firstItem['kb_arah'] ?? 'Horizontal',
                'bawah_penyanggah_status' => $firstItem['pb_status'] ?? 'Include',
                'bawah_penyanggah_material' => $firstItem['pb_material'] ?? 'A001',
                'bawah_penyanggah_arahpemasangan' => $firstItem['pb_arah'] ?? 'Horizontal',
                'bawah_penutup_status' => $firstItem['ptb_status'] ?? 'Tanpa Penutup',
                'bawah_penutup_material' => $firstItem['ptb_material'] ?? 'A001',
                'bawah_penutup_arahpemasangan' => $firstItem['ptb_arah'] ?? 'Horizontal',
                
                'atas_penyanggah_status' => $firstItem['pa_status'] ?? 'Include',
                'atas_penyanggah_material' => $firstItem['pa_material'] ?? 'A001',
                'atas_penyanggah_arahpemasangan' => $firstItem['pa_arah'] ?? 'Horizontal',
                'atas_penutup_status' => $firstItem['pta_status'] ?? 'Tanpa Penutup',
                'atas_penutup_material' => $firstItem['pta_material'] ?? 'A001',
                'atas_penutup_arahpemasangan' => $firstItem['pta_arah'] ?? 'Horizontal',
            ]);

            // Bersihkan item lama lalu simpan isi terbaru dari Step 1.
            $packagingJob->items()->delete();

            foreach ($request->input('items', []) as $item) {
                $packagingJob->items()->create([
                    'no_so' => trim((string) (
                        $item['no_so'] ??
                        $request->no_so ??
                        ''
                    )),
                    'customer' => trim((string) (
                        $item['customer'] ??
                        $request->customer ??
                        ''
                    )),
                    'date_delivery' =>
                        $item['date_delivery'] ??
                        $request->date_delivery,
                    'address' =>
                        $item['address'] ??
                        $request->address,
                    'no_product' => trim((string) (
                        $item['no_product'] ??
                        $item['no_barang'] ??
                        ''
                    )),
                    'desc_product' => trim((string) (
                        $item['desc_product'] ??
                        $item['nama_barang'] ??
                        ''
                    )),
                    'qty' => max(1, (int) (
                        $item['qty'] ??
                        $item['qty_kirim'] ??
                        $item['qty_barang_dikirim'] ??
                        1
                    )),
                ]);
            }

            // Bersihkan kalkulasi lama
            \App\Models\PackingJobCalcDetail::where('job_id', $packagingJob->id)->delete();
            \Illuminate\Support\Facades\DB::table('packing_job_calc_manpowers')->where('job_id', $packagingJob->id)->delete();
            \Illuminate\Support\Facades\DB::table('packing_job_nails')->where('job_id', $packagingJob->id)->delete();

            // Hitung harga box sekali saja berdasarkan konfigurasi yang diperbarui
            $extraParams = [
                'atas_penyangga_include' => $firstItem['pa_status'] === 'Include' ? '1' : '0',
                'bawah_penyangga_include' => $firstItem['pb_status'] === 'Include' ? '1' : '0',
                'bawah_penutup_tipe' => $firstItem['ptb_status'] ?? 'Tanpa Penutup',
                'atas_penutup_tipe' => $firstItem['pta_status'] ?? 'Tanpa Penutup',
            ];

            $calculator = new \App\Services\PackagingCalculatorService();
            $calculator->calculateForJobDetail($packagingJob, $extraParams);

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

    public function updateStatus(Request $request, PackagingJob $packagingJob)
    {
        $request->validate([
            'status' => 'required|in:draft,assigned,process,done'
        ]);

        $packagingJob->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status packaging berhasil diperbarui.');
    }
}
