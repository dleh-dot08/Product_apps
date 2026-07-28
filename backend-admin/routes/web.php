<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('users', UserController::class);
    Route::post('/divisions', [\App\Http\Controllers\DivisionController::class, 'store'])->name('divisions.store');
    Route::post('/roles', [\App\Http\Controllers\RoleController::class, 'store'])->name('roles.store');
    
    // Route Daftar Tugas
    Route::get('/daftar-tugas', function () {
        return view('daftar-tugas.index');
    })->name('daftar-tugas.index');
    
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
            return view('packaging.show', compact('materials')); 
        })->name('calculations.create');
        
        // Route untuk JS fetch di show.blade.php
        Route::post('/simulate', [\App\Http\Controllers\PackagingCalculationController::class, 'simulate'])->name('calculations.simulate');
        Route::post('/calc-store', [\App\Http\Controllers\PackagingCalculationController::class, 'store'])->name('calculations.store');
        Route::put('/calc-update/{id}', [\App\Http\Controllers\PackagingCalculationController::class, 'update'])->name('calculations.update');
        Route::match(['get', 'post'], '/calc-print/{id}', [\App\Http\Controllers\PackagingCalculationController::class, 'print'])->name('calculations.print');

        Route::post('/store', [\App\Http\Controllers\PackagingController::class, 'store'])->name('store');
        Route::get('/{packagingJob}/edit', [\App\Http\Controllers\PackagingController::class, 'edit'])->name('edit');
        Route::put('/{packagingJob}', [\App\Http\Controllers\PackagingController::class, 'update'])->name('update');
        Route::delete('/{packagingJob}', [\App\Http\Controllers\PackagingController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', function($id) { 
            $materials = \Illuminate\Support\Facades\DB::table('packing_material_prices')->get()->map(function($item) {
                $item->kategori = 'MASTER ' . strtoupper($item->component);
                $item->kode = $item->code;
                $item->tebal = $item->thickness;
                $item->lebar = $item->width;
                return $item;
            });
            $job = \App\Models\PackagingJob::with('details')->find($id);
            $calculation = null;
            if ($job && $job->details->count() > 0) {
                // For now, load the first detail so the view doesn't crash
                $calculation = $job->details->first();
                $calculation->no_so = $job->no_so;
                $calculation->customer = $job->customer;
                
                // Details will be automatically loaded via the 'details' relationship defined in the model.
                // We eager load 'material' to avoid N+1 query problems.
                $calculation->load('details.material');
                    
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
            return view('packaging.show', compact('materials', 'calculation', 'job')); 
        })->name('calculations.show');
        
        Route::get('/validasi/data', function() { return "Halaman Validasi Data Dummy"; })->name('validasi_data.index');
        Route::post('/validasi/data/settings', function() { 
            return redirect()->back()->with('success', 'Settings updated'); 
        })->name('validasi_data.settings.update');
    });
});

require __DIR__.'/auth.php';
