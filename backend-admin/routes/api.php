<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $user->load(['role', 'division']);
        return $user;
    });
    
    // User Management API
    Route::apiResource('users', App\Http\Controllers\Api\UserController::class)->names([
        'index' => 'api.users.index',
        'store' => 'api.users.store',
        'show' => 'api.users.show',
        'update' => 'api.users.update',
        'destroy' => 'api.users.destroy',
    ]);
    
    Route::post('/logout', [AuthController::class, 'logout']);
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
