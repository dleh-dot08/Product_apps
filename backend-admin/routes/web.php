<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtaUpdateController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Find Driver Map
    Route::get('/find-driver', function () {
        return view('find-driver');
    })->name('find-driver');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('users', UserController::class);
    Route::post('/divisions', [\App\Http\Controllers\DivisionController::class, 'store'])->name('divisions.store');
    Route::post('/roles', [\App\Http\Controllers\RoleController::class, 'store'])->name('roles.store');
    
    // Master Kendaraan
    Route::resource('vehicles', \App\Http\Controllers\VehicleController::class)->except(['create', 'show', 'edit']);
    
    // Pembelian (PO) & Penjualan (SO) - Data Akurasi
    Route::get('/sales-orders', [\App\Http\Controllers\SalesOrderController::class, 'index'])->name('sales-orders.index');
    Route::get('/purchase-orders', [\App\Http\Controllers\PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    
    // Route Daftar Tugas
    Route::get('/daftar-tugas', function () {
        return view('daftar-tugas.index');
    })->name('daftar-tugas.index');

    // Route Tugas Driver
    Route::get('/pickup-tasks/{pickup_task}/edit-detail', [\App\Http\Controllers\PickupTaskController::class, 'editDetail'])->name('pickup-tasks.edit-detail');
    Route::put('/pickup-tasks/{pickup_task}/update-detail', [\App\Http\Controllers\PickupTaskController::class, 'updateDetail'])->name('pickup-tasks.update-detail');
    Route::resource('pickup-tasks', \App\Http\Controllers\PickupTaskController::class)->except(['create', 'edit']);
    
    // Route HPP Ritase
    Route::get('/hpp-ritase', [\App\Http\Controllers\TripHppController::class, 'index'])->name('hpp.index');
    Route::get('/hpp-ritase/export', [\App\Http\Controllers\TripHppController::class, 'export'])->name('hpp.export');
    Route::get('/hpp-ritase/{id}', [\App\Http\Controllers\TripHppController::class, 'show'])->name('hpp.show');

    // Route Pengeluaran (Expenses)
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class)->except(['create', 'show', 'edit', 'update']);

    // Route Delivery Orders
    // (Delivery Order routes have been merged into pickup-tasks / Tugas Driver)
    
    // Routes untuk Packaging
    Route::prefix('packaging')->name('packaging.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PackagingController::class, 'index'])->name('index');
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
        })->name('calculations.create');
        
        // Route untuk JS fetch di show.blade.php
        Route::post('/simulate', [\App\Http\Controllers\PackagingCalculationController::class, 'simulate'])->name('calculations.simulate');
        Route::post('/calc-store', [\App\Http\Controllers\PackagingCalculationController::class, 'store'])->name('calculations.store');
        Route::put('/calc-update/{id}', [\App\Http\Controllers\PackagingCalculationController::class, 'update'])->name('calculations.update');
        Route::match(['get', 'post'], '/calc-print/{id}', [\App\Http\Controllers\PackagingCalculationController::class, 'print'])->name('calculations.print');

        Route::post('/store', [\App\Http\Controllers\PackagingController::class, 'store'])->name('store');
        Route::get('/{packagingJob}/edit', [\App\Http\Controllers\PackagingController::class, 'edit'])->name('edit');
        Route::put('/{packagingJob}', [\App\Http\Controllers\PackagingController::class, 'update'])->name('update');
        Route::patch('/{packagingJob}/status', [\App\Http\Controllers\PackagingController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{packagingJob}', [\App\Http\Controllers\PackagingController::class, 'destroy'])->name('destroy');
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
    // Laporan Driver
    Route::get('/driver-reports', [\App\Http\Controllers\DriverReportController::class, 'index'])->name('driver-reports.index');
    Route::get('/driver-reports/{id}', [\App\Http\Controllers\DriverReportController::class, 'show'])->name('driver-reports.show');
});

// Jangan di ganggu ini OTA Android 
Route::get('/updates', [OtaUpdateController::class, 'manifest'])
    ->name('ota.manifest');

Route::get('/updates/assets', [OtaUpdateController::class, 'asset'])
    ->name('ota.asset');

require __DIR__.'/auth.php';
