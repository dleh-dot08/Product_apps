<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PickupTaskController;

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
        
        $query = \App\Models\SalesOrderAkurasi::query();
        
        if ($search) {
            $search = strtolower($search);
            $query->where(function($q) use ($search) {
                $q->where('no_so', 'like', "%{$search}%")
                  ->orWhere('deskripsi_barang', 'like', "%{$search}%")
                  ->orWhere('no_barang', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%");
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
        
        // Validasi kolom agar tidak error
        $allowedSorts = [
            'no_so', 'tgl_so', 'tgl_estimasi', 'no_pelanggan', 'nama_pelanggan', 
            'no_po_customer', 'salesman', 'no_barang', 'deskripsi_barang', 
            'category_produk', 'qty', 'qty_shipped', 'sisa_kirim', 'stok_tersedia', 
            'unit_price', 'discount_amount', 'ppn_amount', 'dpp', 'no_pengiriman', 'tgl_kirim'
        ];

        if (in_array($sortCol, $allowedSorts)) {
            $query->orderBy($sortCol, $sortDir);
        } else {
            $query->orderBy('tgl_so', 'desc');
        }
        
        $perPage = (int) $request->input('per_page', 20);
        $data = $query->paginate($perPage);
        return response()->json($data);
    })->name('api.integration.search_so');

    Route::get('/integration/detail-so/{no_so}', function($no_so) {
        $items = \App\Models\SalesOrderAkurasi::where('no_so', $no_so)->get();
        if($items->isEmpty()) return response()->json(['error' => 'Data tidak ditemukan'], 404);
        
        return response()->json([
            'no_so' => $items->first()->no_so,
            'tgl_so' => $items->first()->tgl_so,
            'est_kirim' => $items->first()->tgl_estimasi,
            'pelanggan' => $items->first()->nama_pelanggan,
            'shipto' => $items->first()->shipto,
            'status' => $items->first()->status,
            'total_amount' => $items->sum('subtotal'),
            'items' => $items
        ]);
    })->name('api.integration.detail_so');


    // --- PURCHASE ORDERS ---
    Route::get('/integration/search-po', function(\Illuminate\Http\Request $request) {
        $search = $request->input('q', '');
        
        $query = \App\Models\PembelianOrderAkurasi::query();
        
        if ($search) {
            $search = strtolower($search);
            $query->where(function($q) use ($search) {
                $q->where('no_pembelian', 'like', "%{$search}%")
                  ->orWhere('deskripsi_barang', 'like', "%{$search}%")
                  ->orWhere('no_barang', 'like', "%{$search}%")
                  ->orWhere('nama_pemasok', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('tgl_pembelian', 'desc')->paginate(10);
        return response()->json($data);
    })->name('api.integration.search_po');

    Route::get('/integration/detail-po/{no_po}', function($no_po) {
        $items = \App\Models\PembelianOrderAkurasi::where('no_pembelian', $no_po)->get();
        if($items->isEmpty()) return response()->json(['error' => 'Data tidak ditemukan'], 404);
        
        return response()->json([
            'no_po' => $items->first()->no_pembelian,
            'tgl_po' => $items->first()->tgl_pembelian,
            'est_kirim' => $items->first()->tgl_ekspetasi,
            'pemasok' => $items->first()->nama_pemasok,
            'shipto' => $items->first()->so_no, // shipto or SO
            'status' => $items->first()->status_bayar,
            'total_amount' => $items->sum('amount'),
            'items' => $items
        ]);
    })->name('api.integration.detail_po');
});
