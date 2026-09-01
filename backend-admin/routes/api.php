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

// Proxy API untuk pencarian SO
    Route::get('/packaging/search-so', function(\Illuminate\Http\Request $request) {
    $search = $request->input('q', '');
    $apiKey = 'Ym95Y29tcG9zaXRpb25leHBsYW5hdGlvbnRob3VnaHRwZWFjZWdpcmxjb2FjaHNlbnM=';
    
    $dateFrom = $request->input('date_from', now()->subMonths(6)->format('Y-m-d'));
    $dateTo = $request->input('date_to', now()->format('Y-m-d'));
    
    $url = "https://akurasi-api.aqpa-indonesia.com/api/integration/penjualan-so?offset=0&limit=1000&date_from={$dateFrom}&date_to={$dateTo}";
    
    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'X-API-Key' => $apiKey
        ])->get($url);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if ($search && isset($data['data'])) {
                $search = strtolower($search);
                $data['data'] = array_values(array_filter($data['data'], function($item) use ($search) {
                    return str_contains(strtolower($item['no_so'] ?? ''), $search) 
                        || str_contains(strtolower($item['deskripsi_barang'] ?? ''), $search)
                        || str_contains(strtolower($item['no_barang'] ?? ''), $search);
                }));
                $data['total_rows'] = count($data['data']);
            }
            
            return response()->json($data);
        }
        
        return response()->json(['error' => 'Gagal mengambil data'], $response->status());
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    })->name('api.packaging.search_so');
});
