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