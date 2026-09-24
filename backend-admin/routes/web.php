<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtaUpdateController;

Route::get('/', function () {
    return redirect()->route('login');
});

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Find Driver Map
    Route::get('/find-driver', function () {
        return view('find-driver.index');
    })->name('find-driver')->middleware('permission:View Find Driver');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:View Daftar Pengguna');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:Create Pengguna');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:Create Pengguna');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:View Daftar Pengguna');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:Edit Pengguna');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:Edit Pengguna');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:Delete Pengguna');
    Route::post('/roles/privileges', [\App\Http\Controllers\UserController::class, 'updatePrivileges'])->name('roles.privileges.update')->middleware('permission:Mengatur Hak Akses');
    Route::post('/divisions', [\App\Http\Controllers\DivisionController::class, 'store'])->name('divisions.store');
    Route::post('/roles', [\App\Http\Controllers\RoleController::class, 'store'])->name('roles.store');
    
    // Master Kendaraan
    Route::get('vehicles', [\App\Http\Controllers\VehicleController::class, 'index'])->name('vehicles.index')->middleware('permission:View Daftar Kendaraan');
    Route::post('vehicles', [\App\Http\Controllers\VehicleController::class, 'store'])->name('vehicles.store')->middleware('permission:Create Kendaraan');
    Route::put('vehicles/{vehicle}', [\App\Http\Controllers\VehicleController::class, 'update'])->name('vehicles.update')->middleware('permission:Edit Kendaraan');
    Route::delete('vehicles/{vehicle}', [\App\Http\Controllers\VehicleController::class, 'destroy'])->name('vehicles.destroy')->middleware('permission:Delete Kendaraan');
        
    // ============================================================
    // PEMBELIAN (PO) & PENJUALAN (SO) - DATA AKURASI
    // ============================================================

    // -------------------------
    // SALES ORDER
    // -------------------------
    Route::get('/sales-orders', [\App\Http\Controllers\SalesOrderController::class,'index'])->name('sales-orders.index')->middleware('permission:View Data SO');
    Route::get('/api/integration/search-so', [\App\Http\Controllers\SalesOrderController::class,'search'])->name('api.integration.search_so');
    Route::get('/api/integration/detail-so/{noSo}', [\App\Http\Controllers\SalesOrderController::class,'detail'])->where('noSo', '.*')->name('api.integration.detail_so');
    Route::post('/api/integration/trigger-sync-so', [\App\Http\Controllers\SalesOrderController::class,'triggerSync'])->name('api.integration.trigger_sync_so');

    // -------------------------
    // PURCHASE ORDER
    // -------------------------
    Route::get('/purchase-orders', [\App\Http\Controllers\PurchaseOrderController::class,'index'])->name('purchase-orders.index')->middleware('permission:View Data PO');
    Route::get('/api/integration/search-po', [\App\Http\Controllers\PurchaseOrderController::class,'search'])->name('api.integration.search_po');
    Route::get('/api/integration/detail-po/{noPo}', [\App\Http\Controllers\PurchaseOrderController::class,'detail'])->where('noPo', '.*')->name('api.integration.detail_po');
    // Route Daftar Tugas
    Route::get('/daftar-tugas', function () {
        return view('daftar-tugas.index');
    })->name('daftar-tugas.index')->middleware('permission:View Daftar Tugas');

    // Route Tugas Driver
    Route::get('pickup-tasks', [\App\Http\Controllers\PickupTaskController::class, 'index'])->name('pickup-tasks.index')->middleware('permission:View Tugas');
    Route::post('pickup-tasks', [\App\Http\Controllers\PickupTaskController::class, 'store'])->name('pickup-tasks.store')->middleware('permission:Create Tugas');
    Route::get('pickup-tasks/{pickup_task}', [\App\Http\Controllers\PickupTaskController::class, 'show'])->name('pickup-tasks.show')->middleware('permission:View Tugas');
    Route::put('pickup-tasks/{pickup_task}', [\App\Http\Controllers\PickupTaskController::class, 'update'])->name('pickup-tasks.update')->middleware('permission:Edit Tugas');
    Route::delete('pickup-tasks/{pickup_task}', [\App\Http\Controllers\PickupTaskController::class, 'destroy'])->name('pickup-tasks.destroy')->middleware('permission:Delete Tugas');
    Route::get('/pickup-tasks/{pickup_task}/edit-detail', [\App\Http\Controllers\PickupTaskController::class, 'editDetail'])->name('pickup-tasks.edit-detail')->middleware('permission:Edit Tugas');
    Route::put('/pickup-tasks/{pickup_task}/update-detail', [\App\Http\Controllers\PickupTaskController::class, 'updateDetail'])->name('pickup-tasks.update-detail')->middleware('permission:Edit Tugas');
    Route::post('/pickup-tasks/{pickup_task}/upload-attachment', [\App\Http\Controllers\PickupTaskController::class, 'uploadAttachment'])->name('pickup-tasks.upload-attachment')->middleware('permission:Edit Tugas');
    
    // Route HPP Ritase
    Route::get('/hpp-ritase', [\App\Http\Controllers\TripHppController::class, 'index'])->name('hpp.index')->middleware('permission:View HPP Ritase');
    Route::get('/hpp-ritase/export', [\App\Http\Controllers\TripHppController::class, 'export'])->name('hpp.export')->middleware('permission:Export Data');
    Route::get('/hpp-ritase/validasi', [\App\Http\Controllers\HppValidasiController::class, 'index'])->name('hpp.validasi')->middleware('permission:Validasi HPP');
    Route::put('/hpp-ritase/validasi/manpower/{id}', [\App\Http\Controllers\HppValidasiController::class, 'updateManpower'])->name('hpp.validasi.update.manpower')->middleware('permission:Validasi HPP');
    Route::put('/hpp-ritase/validasi/bbm/{id}', [\App\Http\Controllers\HppValidasiController::class, 'updateBbm'])->name('hpp.validasi.update.bbm')->middleware('permission:Validasi HPP');
    Route::get('/hpp-ritase/{id}', [\App\Http\Controllers\TripHppController::class, 'show'])->name('hpp.show')->middleware('permission:View HPP Ritase');

    // Route Pengeluaran (Expenses)
    Route::get('expenses', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.index')->middleware('permission:View Pengeluaran');
    Route::post('expenses', [\App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store')->middleware('permission:Create Pengeluaran');
    Route::delete('expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'destroy'])->name('expenses.destroy')->middleware('permission:Delete Pengeluaran');

    // Route Delivery Orders
    // (Delivery Order routes have been merged into pickup-tasks / Tugas Driver)
    
    // Routes untuk Packaging
    Route::prefix('packaging')->name('packaging.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PackagingController::class, 'index'])->name('index')->middleware('permission:View Packing');
        Route::get('/create', function() { 
            $materials = \Illuminate\Support\Facades\DB::table('packing_material_prices')->get()->map(function($item) {
                $item->kategori = 'MASTER ' . strtoupper($item->component);
                $item->kode = $item->code;
                $item->tebal = $item->thickness;
                $item->lebar = $item->width;
                return $item;
            });
            $nails = \Illuminate\Support\Facades\DB::table('nail_size_rules')->orderBy('id')->get();
            return view('packaging.show', compact('materials', 'nails')); 
        })->name('calculations.create')->middleware('permission:Create Packing');
        
        // Route untuk JS fetch di show.blade.php
        Route::post('/simulate', [\App\Http\Controllers\PackagingCalculationController::class, 'simulate'])->name('calculations.simulate');
        Route::post('/calc-store', [\App\Http\Controllers\PackagingCalculationController::class, 'store'])->name('calculations.store')->middleware('permission:Create Packing');
        Route::put('/calc-update/{id}', [\App\Http\Controllers\PackagingCalculationController::class, 'update'])->name('calculations.update')->middleware('permission:Edit Packing');
        Route::match(['get', 'post'], '/calc-print/{id}', [\App\Http\Controllers\PackagingCalculationController::class, 'print'])->name('calculations.print');

        Route::post('/store', [\App\Http\Controllers\PackagingController::class, 'store'])->name('store')->middleware('permission:Create Packing');
        Route::get('/{packagingJob}/edit', [\App\Http\Controllers\PackagingController::class, 'edit'])->name('edit')->middleware('permission:Edit Packing');
        Route::put('/{packagingJob}', [\App\Http\Controllers\PackagingController::class, 'update'])->name('update')->middleware('permission:Edit Packing');
        Route::patch('/{packagingJob}/status', [\App\Http\Controllers\PackagingController::class, 'updateStatus'])->name('update-status')->middleware('permission:Edit Packing');
        Route::delete('/{packagingJob}', [\App\Http\Controllers\PackagingController::class, 'destroy'])->name('destroy')->middleware('permission:Delete Packing');
        Route::get('/{id}', function($id) { 
            $materials = \Illuminate\Support\Facades\DB::table('packing_material_prices')->get()->map(function($item) {
                $item->kategori = 'MASTER ' . strtoupper($item->component);
                $item->kode = $item->code;
                $item->tebal = $item->thickness;
                $item->lebar = $item->width;
                return $item;
            });
            $job = \App\Models\PackagingJob::with(['details.material', 'items'])->find($id);
            $calculation = null;
            if ($job) {
                // In the new architecture, the job IS the calculation.
                $calculation = $job;
                
                // Set these for backward compatibility with the view, although we should use the first item's details if we want or let the view handle it.
                // Here we just grab the first item's SO as fallback for backward compatibility
                $firstItem = $job->items->first();
                $calculation->no_so = $firstItem ? $firstItem->no_so : '-';
                $calculation->customer = $firstItem ? $firstItem->customer : '-';
                    
                $materialsMap = $materials->keyBy('kode');
                foreach($calculation->details as $d) {
                    // Add kode attribute for backward compatibility if needed in the view
                    if ($d->material) {
                        $d->material->kode = $d->material->code;
                    }
                }

                $calculation->manpower = \Illuminate\Support\Facades\DB::table('packing_job_calc_manpowers')
                    ->where('job_id', $calculation->id)
                    ->get();

                $calculation->consumables = \Illuminate\Support\Facades\DB::table('packing_job_nails')
                    ->where('job_id', $calculation->id)
                    ->get();
            }
            $nails = \Illuminate\Support\Facades\DB::table('nail_size_rules')->orderBy('id')->get();
            return view('packaging.show', compact('materials', 'calculation', 'job', 'nails')); 
        })->name('calculations.show');
        
        Route::get('/validasi/data', function() { return "Halaman Validasi Data Dummy"; })->name('validasi_data.index');
        Route::post('/validasi/data/settings', function() { 
            return redirect()->back()->with('success', 'Settings updated'); 
        })->name('validasi_data.settings.update');
    });

});

// Jangan di ganggu ini OTA Android 
Route::get('/updates', [OtaUpdateController::class, 'manifest'])
    ->name('ota.manifest');

Route::get('/updates/assets', [OtaUpdateController::class, 'asset'])
    ->name('ota.asset');

require __DIR__.'/auth.php';
