<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PickupTaskController;
use App\Http\Controllers\OtaUpdateController;
use App\Services\AkurasiService;


/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| ROUTER API KEY
|--------------------------------------------------------------------------
*/

Route::middleware('api.router.key')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    Route::post('/login', [
        AuthController::class,
        'login'
    ]);


    /*
    |--------------------------------------------------------------------------
    | USER READ
    |--------------------------------------------------------------------------
    */

    Route::get('/users', [
        \App\Http\Controllers\Api\UserController::class,
        'index'
    ])->name('api.users.index');

    Route::get('/users/{user}', [
        \App\Http\Controllers\Api\UserController::class,
        'show'
    ])->name('api.users.show');


    /*
    |--------------------------------------------------------------------------
    | DRIVER LOCATION PUBLIC
    |--------------------------------------------------------------------------
    */

    Route::get('/driver/locations', [
        \App\Http\Controllers\Api\LocationController::class,
        'getActiveDrivers'
    ]);


    /*
    |--------------------------------------------------------------------------
    | AUTH SANCTUM
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/user', function (Request $request) {

            $user = $request->user();

            $user->load([
                'roleRelation',
                'division'
            ]);

            $userArray = $user->toArray();

            $userArray['role'] = $user->roleRelation;

            return $userArray;
        });


        /*
        |--------------------------------------------------------------------------
        | PROFILE
        |--------------------------------------------------------------------------
        */

        Route::put('/user/profile', [
            AuthController::class,
            'updateProfile'
        ]);


        /*
        |--------------------------------------------------------------------------
        | USER MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'users',
            \App\Http\Controllers\Api\UserController::class
        )
            ->only([
                'store',
                'update',
                'destroy'
            ])
            ->names([
                'store' => 'api.users.store',
                'update' => 'api.users.update',
                'destroy' => 'api.users.destroy',
            ]);


        /*
        |--------------------------------------------------------------------------
        | LOGOUT
        |--------------------------------------------------------------------------
        */

        Route::post('/logout', [
            AuthController::class,
            'logout'
        ]);


        /*
        |--------------------------------------------------------------------------
        | PICKUP TASK
        |--------------------------------------------------------------------------
        */

        Route::get('/driver/dashboard', [
            \App\Http\Controllers\Api\Driver\DriverDashboardController::class,
            'dashboardSummary'
        ]);

        Route::get('/pickup', [
            PickupTaskController::class,
            'index'
        ]);

        Route::get('/pickup/{id}', [
            PickupTaskController::class,
            'show'
        ]);

        Route::post('/pickup', [
            PickupTaskController::class,
            'store'
        ]);

        Route::patch('/pickup/{id}/status', [
            PickupTaskController::class,
            'updateStatus'
        ]);


        /*
        |--------------------------------------------------------------------------
        | EXPENSE
        |--------------------------------------------------------------------------
        */

        Route::post('/pickup/{id}/expenses', [
            \App\Http\Controllers\Api\ExpenseController::class,
            'storeFromTask'
        ]);


        /*
        |--------------------------------------------------------------------------
        | DRIVER LOCATION
        |--------------------------------------------------------------------------
        */

        Route::post('/driver/location', [
            \App\Http\Controllers\Api\LocationController::class,
            'updateLocation'
        ]);
    });
});


/*
|--------------------------------------------------------------------------
| OTA
|--------------------------------------------------------------------------
*/

Route::post('/internal/ota/publish', [
    OtaUpdateController::class,
    'publish'
])->name('ota.publish');


Route::get('/app-version', function () {

    return response()->json([
        'latest_version' => '1.0.0',

        'apk_url' => url(
            '/downloads/driverapps-latest.apk'
        ),

        'changelog' =>
            'Perbaikan performa dan penambahan fitur baru.',

        'force_update' => false,
    ]);
});


/*
|--------------------------------------------------------------------------
| AKURASI INTEGRATION
|--------------------------------------------------------------------------
|
| Route di file api.php otomatis memiliki prefix:
|
| /api
|
| Jadi:
|
| /integration/search-so
|
| menjadi:
|
| /api/integration/search-so
|
|--------------------------------------------------------------------------
*/

Route::middleware('web')
    ->prefix('integration')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | SALES ORDER - TRIGGER SYNC
        |--------------------------------------------------------------------------
        */

        Route::post('/trigger-sync-so', function () {

            try {

                /*
                 * NOTE:
                 *
                 * Artisan::call() berjalan synchronous.
                 * Ini bukan queue/background job sebenarnya.
                 *
                 * Tetapi tetap dipertahankan agar kompatibel
                 * dengan Blade yang sekarang.
                 */

                set_time_limit(300);

                if (
                    array_key_exists(
                        'akurasi:sync',
                        Artisan::all()
                    )
                ) {

                    Artisan::call(
                        'akurasi:sync',
                        [
                            '--only' => 'so'
                        ]
                    );

                    return response()->json([
                        'success' => true,
                        'status' => 'success',
                        'message' => 'Sinkronisasi SO berhasil dijalankan.',
                    ]);
                }

                /*
                 * Kalau command sync tidak ada,
                 * data sekarang tetap menggunakan API realtime.
                 */

                return response()->json([
                    'success' => true,
                    'status' => 'success',
                    'message' => 'Data SO menggunakan API Akurasi realtime.',
                ]);

            } catch (\Throwable $e) {

                report($e);

                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 500);
            }

        })->name('api.integration.trigger_sync_so');


        /*
        |--------------------------------------------------------------------------
        | SALES ORDER - SEARCH
        |--------------------------------------------------------------------------
        */

        Route::get('/search-so', function (
            Request $request,
            AkurasiService $service
        ) {

            try {

                /*
                 * Blade mengirim:
                 *
                 * q
                 *
                 * sedangkan AkurasiService memakai:
                 *
                 * search
                 */

                $filters = [
                    'page' => $request->input(
                        'page',
                        1
                    ),

                    'per_page' => $request->input(
                        'per_page',
                        20
                    ),

                    'search' => $request->input(
                        'q'
                    ),

                    'date_from' => $request->input(
                        'date_from'
                    ),

                    'date_to' => $request->input(
                        'date_to'
                    ),

                    'status' => $request->input(
                        'status'
                    ),

                    'status_hold' => $request->input(
                        'status_hold'
                    ),

                    'sort_col' => $request->input(
                        'sort_col',
                        'tgl_so'
                    ),

                    'sort_dir' => $request->input(
                        'sort_dir',
                        'desc'
                    ),
                ];

                $result = $service->searchSo(
                    $filters
                );

                return response()->json(
                    $result
                );

            } catch (\Throwable $e) {

                report($e);

                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage(),

                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                    'from' => 0,
                    'to' => 0,
                ], 500);
            }

        })->name('api.integration.search_so');


        /*
        |--------------------------------------------------------------------------
        | SALES ORDER - DETAIL
        |--------------------------------------------------------------------------
        */

        Route::get('/detail-so/{no_so}', function (
            string $no_so,
            AkurasiService $service
        ) {

            try {

                $noSo = urldecode(
                    trim($no_so)
                );

                if ($noSo === '') {

                    return response()->json([
                        'error' => true,
                        'message' => 'Nomor Sales Order kosong.',
                    ], 422);
                }

                $result = $service->detailSo(
                    $noSo
                );

                return response()->json(
                    $result
                );

            } catch (\Throwable $e) {

                report($e);

                /*
                 * JANGAN 404.
                 *
                 * Karena route sebenarnya ADA.
                 *
                 * 422 menunjukkan bahwa request masuk,
                 * tetapi proses detail gagal.
                 */

                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage(),
                    'no_so' => $no_so,
                ], 422);
            }

        })
            ->where('no_so', '.*')
            ->name('api.integration.detail_so');


        /*
        |--------------------------------------------------------------------------
        | PURCHASE ORDER - SEARCH
        |--------------------------------------------------------------------------
        */

        Route::get('/search-po', function (
            Request $request,
            AkurasiService $service
        ) {

            try {

                $filters = [
                    'page' => $request->input(
                        'page',
                        1
                    ),

                    'per_page' => $request->input(
                        'per_page',
                        20
                    ),

                    /*
                     * Blade = q
                     * Service = search
                     */
                    'search' => $request->input(
                        'q'
                    ),

                    'date_from' => $request->input(
                        'date_from'
                    ),

                    'date_to' => $request->input(
                        'date_to'
                    ),

                    'sort_col' => $request->input(
                        'sort_col',
                        'tgl_pembelian'
                    ),

                    'sort_dir' => $request->input(
                        'sort_dir',
                        'desc'
                    ),
                ];

                $result = $service->searchPo(
                    $filters
                );

                return response()->json(
                    $result
                );

            } catch (\Throwable $e) {

                report($e);

                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage(),

                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                    'from' => 0,
                    'to' => 0,
                ], 500);
            }

        })->name('api.integration.search_po');


        /*
        |--------------------------------------------------------------------------
        | PURCHASE ORDER - DETAIL
        |--------------------------------------------------------------------------
        */

        Route::get('/detail-po/{no_po}', function (
            string $no_po,
            AkurasiService $service
        ) {

            try {

                $noPo = urldecode(
                    trim($no_po)
                );

                if ($noPo === '') {

                    return response()->json([
                        'error' => true,
                        'message' => 'Nomor Purchase Order kosong.',
                    ], 422);
                }

                $result = $service->detailPo(
                    $noPo
                );

                return response()->json(
                    $result
                );

            } catch (\Throwable $e) {

                report($e);

                /*
                 * Sama dengan SO:
                 * route ada, jadi jangan return 404.
                 */

                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage(),
                    'no_po' => $no_po,
                ], 422);
            }

        })
            ->where('no_po', '.*')
            ->name('api.integration.detail_po');
    });