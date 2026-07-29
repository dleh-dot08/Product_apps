<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    /**
     * Display the sales order page.
     */
    public function index()
    {
        return view('sales-order.index');
    }

    /**
     * Dummy API to return sales orders for realtime fetching.
     */
    public function apiData(Request $request)
    {
        // Dummy data for testing. Real implementation would fetch from DB or external API.
        $data = [
            [
                'no_pembelian' => 'PO-2026-001',
                'pemasok' => 'PT. Aneka Tambang',
                'no_barang' => 'BRG-001',
                'deskripsi_barang' => 'Besi Baja Ringan',
                'qty' => 150,
            ],
            [
                'no_pembelian' => 'PO-2026-002',
                'pemasok' => 'CV. Makmur Jaya',
                'no_barang' => 'BRG-042',
                'deskripsi_barang' => 'Semen Tiga Roda',
                'qty' => 300,
            ],
            [
                'no_pembelian' => 'PO-2026-003',
                'pemasok' => 'PT. Sumber Terang',
                'no_barang' => 'BRG-015',
                'deskripsi_barang' => 'Lampu LED Philips',
                'qty' => 50,
            ],
            [
                'no_pembelian' => 'PO-2026-004',
                'pemasok' => 'PT. Aneka Tambang',
                'no_barang' => 'BRG-002',
                'deskripsi_barang' => 'Besi Beton',
                'qty' => 200,
            ],
            [
                'no_pembelian' => 'PO-2026-005',
                'pemasok' => 'Toko Sentosa',
                'no_barang' => 'BRG-099',
                'deskripsi_barang' => 'Paku Payung',
                'qty' => 5000,
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
