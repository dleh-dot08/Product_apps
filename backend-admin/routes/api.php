<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PickupTaskController;
use App\Http\Controllers\OtaUpdateController;

Route::middleware('api.router.key')->group(function () {


    // API-key-only routes: these endpoints do not depend on the current user.
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/users', [\App\Http\Controllers\Api\UserController::class, 'index'])
        ->name('api.users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Api\UserController::class, 'show'])
        ->name('api.users.show');

    Route::get('/driver/locations', [\App\Http\Controllers\Api\LocationController::class, 'getActiveDrivers']);

    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $user->load(['roleRelation', 'division']);
        
        $userArray = $user->toArray();
        $userArray['role'] = $user->roleRelation;
        
        return $userArray;
    });
    
    // User Management writes require both router API key and user auth.
    Route::apiResource('users', App\Http\Controllers\Api\UserController::class)->only([
        'store', 'update', 'destroy',
    ])->names([
        'store' => 'api.users.store',
        'update' => 'api.users.update',
        'destroy' => 'api.users.destroy',
    ]);
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Pickup Tasks API
    Route::get('/driver/dashboard', [PickupTaskController::class, 'dashboardSummary']);
    Route::get('/pickup', [PickupTaskController::class, 'index']);
    Route::get('/pickup/{id}', [PickupTaskController::class, 'show']);
    Route::post('/pickup', [PickupTaskController::class, 'store']);
    Route::patch('/pickup/{id}/status', [PickupTaskController::class, 'updateStatus']);
    
    // Expense API
    Route::post('/pickup/{id}/expenses', [\App\Http\Controllers\Api\ExpenseController::class, 'storeFromTask']);
    
    // Driver Location API
    Route::post('/driver/location', [\App\Http\Controllers\Api\LocationController::class, 'updateLocation']);
    });

});

Route::post('/internal/ota/publish', [OtaUpdateController::class, 'publish'])
    ->name('ota.publish');

// Proxy API untuk pencarian SO & PO (Membaca dari Local Database)
Route::middleware('web')->group(function () {
    
    // --- TRIGGER BACKGROUND SYNC ---
    Route::post('/integration/trigger-sync-so', function() {
        // Prevent timeout for large syncs
        set_time_limit(300);
        \Illuminate\Support\Facades\Artisan::call('akurasi:sync', ['--only' => 'so']);
        return response()->json([
            'status' => 'success', 
            'message' => 'Sync SO berhasil dijalankan di latar belakang'
        ]);
    })->name('api.integration.trigger_sync_so');

    // --- SALES ORDERS ---
    Route::get('/integration/search-so', function(\Illuminate\Http\Request $request) {
        $search = $request->input('q', '');
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 20);
        
        // 1. Coba ambil dari database lokal terlebih dahulu
        try {
            $query = \App\Models\SalesOrderAkurasi::query();
            
            if ($search) {
                $searchStr = strtolower($search);
                $query->where(function($q) use ($searchStr) {
                    $q->where('no_so', 'like', "%{$searchStr}%")
                      ->orWhere('deskripsi_barang', 'like', "%{$searchStr}%")
                      ->orWhere('no_barang', 'like', "%{$searchStr}%")
                      ->orWhere('nama_pelanggan', 'like', "%{$searchStr}%");
                });
            }
            
            // Filter by Date Range
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            if ($dateFrom && $dateTo) {
                $query->whereBetween('tgl_so', [$dateFrom, $dateTo]);
            } elseif ($dateFrom) {
                $query->where('tgl_so', '>=', $dateFrom);
            } elseif ($dateTo) {
                $query->where('tgl_so', '<=', $dateTo);
            }

            // Filter by Status Hold
            $statusHold = $request->input('status_hold');
            if ($statusHold !== null && $statusHold !== '') {
                $query->where('is_held', $statusHold);
            }

            // Filter by Status
            $status = $request->input('status');
            if ($status) {
                $query->where('status', 'like', "%{$status}%");
            }

            $sortCol = $request->input('sort_col', 'tgl_so');
            $sortDir = $request->input('sort_dir', 'desc');
            
            // Validasi arah sort agar aman dari SQL Injection
            $sortDir = strtolower($sortDir) === 'asc' ? 'asc' : 'desc';
            
            $sortMapping = [
                'salesman' => 'nama_salesman',
                'dpp' => 'subtotal',
                'tgl_kirim' => 'tgl_pengiriman'
            ];
            
            $dbSortCol = $sortMapping[$sortCol] ?? $sortCol;

            // Validasi kolom agar tidak error
            $allowedSorts = [
                'no_so', 'tgl_so', 'tgl_estimasi', 'tgl_pengiriman', 'no_pelanggan', 'nama_pelanggan', 
                'no_po_customer', 'nama_salesman', 'no_barang', 'deskripsi_barang', 
                'category_produk', 'qty', 'qty_shipped', 'sisa_kirim', 'stok_tersedia', 
                'unit_price', 'discount_amount', 'ppn_amount', 'subtotal', 'amount', 'no_pengiriman',
                'salesman', 'dpp', 'tgl_kirim'
            ];

            if (in_array($sortCol, $allowedSorts)) {
                $query->orderBy($dbSortCol, $sortDir);
            } else {
                $query->orderBy('tgl_so', 'desc');
            }
            
            $localData = $query->paginate($perPage, ['*'], 'page', $page);
            if ($localData->total() > 0) {
                return response()->json($localData);
            }
        } catch (\Exception $e) {
            // Abaikan error jika table tidak ada, lanjut ke API
        }

        // 2. Jika lokal kosong atau table tidak ada, ambil dari API Akurasi
        $apiKey = 'Ym95Y29tcG9zaXRpb25leHBsYW5hdGlvbnRob3VnaHRwZWFjZWdpcmxjb2FjaHNlbnM=';
        $url = 'https://akurasi-api.aqpa-indonesia.com/api/integration/penjualan-so';
        
        $params = [
            'page' => $page,
            'limit' => $perPage,
            'search' => $search,
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'status' => $request->input('status'),
            'status_hold' => $request->input('status_hold'),
            'sort_col' => $request->input('sort_col'),
            'sort_dir' => $request->input('sort_dir'),
        ];
        
        // Remove empty params
        $params = array_filter($params, function($v) { return $v !== null && $v !== ''; });

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'X-API-Key' => $apiKey,
            'Accept' => 'application/json',
        ])->get($url, $params);

        if (!$response->successful()) {
            return response()->json(['error' => 'Gagal memuat dari API'], 500);
        }

        $resData = $response->json();
        $meta = $resData['meta'] ?? [];
        $data = $resData['data'] ?? [];
        
        // Memetakan ke struktur pagination Laravel
        $mapped = [
            'data' => $data,
            'current_page' => $meta['current_page'] ?? $page,
            'last_page' => $meta['total_pages'] ?? 1,
            'total' => $meta['total_rows'] ?? 0,
            'from' => count($data) > 0 ? (($page - 1) * $perPage) + 1 : 0,
            'to' => count($data) > 0 ? (($page - 1) * $perPage) + count($data) : 0,
        ];

        return response()->json($mapped);
    })->name('api.integration.search_so');

    Route::get('/integration/detail-so/{no_so}', function($no_so) {
        // 1. Coba dari database lokal
        try {
            $itemsLocal = \App\Models\SalesOrderAkurasi::where('no_so', $no_so)->get();
            if ($itemsLocal->isNotEmpty()) {
                return response()->json([
                    'no_so' => $itemsLocal->first()->no_so,
                    'tgl_so' => $itemsLocal->first()->tgl_so,
                    'est_kirim' => $itemsLocal->first()->tgl_estimasi,
                    'pelanggan' => $itemsLocal->first()->nama_pelanggan,
                    'shipto' => $itemsLocal->first()->shipto,
                    'status' => $itemsLocal->first()->status,
                    'total_amount' => $itemsLocal->sum('subtotal'),
                    'items' => $itemsLocal
                ]);
            }
        } catch (\Exception $e) {
            // Lanjut ke API
        }

        // 2. Ambil dari API
        $apiKey = 'Ym95Y29tcG9zaXRpb25leHBsYW5hdGlvbnRob3VnaHRwZWFjZWdpcmxjb2FjaHNlbnM=';
        $url = 'https://akurasi-api.aqpa-indonesia.com/api/integration/penjualan-so';
        
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'X-API-Key' => $apiKey,
            'Accept' => 'application/json',
        ])->get($url, [
            'search' => $no_so,
            'limit' => 200
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $resData = $response->json();
        $items = collect($resData['data'] ?? [])->filter(function($item) use ($no_so) {
            return strtolower($item['no_so'] ?? '') === strtolower($no_so);
        })->values();

        if($items->isEmpty()) return response()->json(['error' => 'Data tidak ditemukan'], 404);
        
        $firstItem = $items->first();
        return response()->json([
            'no_so' => $firstItem['no_so'] ?? '-',
            'tgl_so' => $firstItem['tgl_so'] ?? null,
            'est_kirim' => $firstItem['tgl_estimasi'] ?? null,
            'pelanggan' => $firstItem['nama_pelanggan'] ?? null,
            'shipto' => $firstItem['shipto'] ?? null,
            'status' => $firstItem['status'] ?? null,
            'total_amount' => $items->sum('subtotal'),
            'items' => $items
        ]);
    })->where('no_so', '.*')->name('api.integration.detail_so');


    // --- PURCHASE ORDERS ---
    Route::get('/integration/search-po', function(\Illuminate\Http\Request $request) {
        $search = $request->input('q', '');
        $page = (int) $request->input('page', 1);

        // 1. Coba ambil dari database lokal terlebih dahulu
        try {
            $query = \App\Models\PembelianOrderAkurasi::query();
            
            if ($search) {
                $searchStr = strtolower($search);
                $query->where(function($q) use ($searchStr) {
                    $q->where('no_pembelian', 'like', "%{$searchStr}%")
                      ->orWhere('deskripsi_barang', 'like', "%{$searchStr}%")
                      ->orWhere('no_barang', 'like', "%{$searchStr}%")
                      ->orWhere('nama_pemasok', 'like', "%{$searchStr}%");
                });
            }
            
            $localData = $query->orderBy('tgl_pembelian', 'desc')->paginate(20, ['*'], 'page', $page);
            
            // Jika data lokal ditemukan, gunakan data tersebut
            if ($localData->total() > 0) {
                return response()->json($localData);
            }
        } catch (\Exception $e) {
            // Abaikan error jika table tidak ada, lanjut ke API
        }

        // 2. Jika lokal kosong atau table tidak ada, ambil dari API Akurasi
        $apiKey = 'Ym95Y29tcG9zaXRpb25leHBsYW5hdGlvbnRob3VnaHRwZWFjZWdpcmxjb2FjaHNlbnM=';
        $url = 'https://akurasi-api.aqpa-indonesia.com/api/integration/pembelian';
        
        $params = [
            'page' => $page,
            'limit' => 20,
            'search' => $search,
        ];
        
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'X-API-Key' => $apiKey,
            'Accept' => 'application/json',
        ])->get($url, $params);

        if (!$response->successful()) {
            return response()->json(['error' => 'Gagal memuat dari API'], 500);
        }

        $resData = $response->json();
        $meta = $resData['meta'] ?? [];
        $data = $resData['data'] ?? [];
        
        // Memetakan ke struktur pagination Laravel
        $mapped = [
            'data' => $data,
            'current_page' => $meta['current_page'] ?? $page,
            'last_page' => $meta['total_pages'] ?? 1,
            'total' => $meta['total_rows'] ?? 0,
            'from' => count($data) > 0 ? (($page - 1) * 20) + 1 : 0,
            'to' => count($data) > 0 ? (($page - 1) * 20) + count($data) : 0,
        ];

        return response()->json($mapped);
    })->name('api.integration.search_po');

    Route::get('/integration/detail-po/{no_po}', function($no_po) {
        // 1. Coba dari database lokal
        try {
            $itemsLocal = \App\Models\PembelianOrderAkurasi::where('no_pembelian', $no_po)->get();
            if ($itemsLocal->isNotEmpty()) {
                return response()->json([
                    'no_po' => $itemsLocal->first()->no_pembelian,
                    'tgl_po' => $itemsLocal->first()->tgl_pembelian,
                    'est_kirim' => $itemsLocal->first()->tgl_ekspetasi,
                    'pemasok' => $itemsLocal->first()->nama_pemasok,
                    'shipto' => $itemsLocal->first()->so_no, 
                    'status' => $itemsLocal->first()->status_bayar,
                    'total_amount' => $itemsLocal->sum('amount'),
                    'items' => $itemsLocal
                ]);
            }
        } catch (\Exception $e) {
            // Lanjut ke API
        }

        // 2. Ambil dari API
        $apiKey = 'Ym95Y29tcG9zaXRpb25leHBsYW5hdGlvbnRob3VnaHRwZWFjZWdpcmxjb2FjaHNlbnM=';
        $url = 'https://akurasi-api.aqpa-indonesia.com/api/integration/pembelian';
        
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'X-API-Key' => $apiKey,
            'Accept' => 'application/json',
        ])->get($url, [
            'search' => $no_po,
            'limit' => 200
        ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $resData = $response->json();
        $items = collect($resData['data'] ?? [])->filter(function($item) use ($no_po) {
            return strtolower($item['no_pembelian'] ?? '') === strtolower($no_po) || strtolower($item['no_po'] ?? '') === strtolower($no_po);
        })->values();

        if($items->isEmpty()) return response()->json(['error' => 'Data tidak ditemukan'], 404);
        
        $firstItem = $items->first();
        return response()->json([
            'no_po' => $firstItem['no_pembelian'] ?? '-',
            'tgl_po' => $firstItem['tgl_pembelian'] ?? null,
            'est_kirim' => $firstItem['tgl_ekspetasi'] ?? null,
            'pemasok' => $firstItem['nama_pemasok'] ?? null,
            'shipto' => $firstItem['so_no'] ?? null,
            'status' => $firstItem['status_pembayaran'] ?? $firstItem['status_bayar'] ?? null,
            'total_amount' => $items->sum('amount'),
            'items' => $items
        ]);
    })->where('no_po', '.*')->name('api.integration.detail_po');
});
