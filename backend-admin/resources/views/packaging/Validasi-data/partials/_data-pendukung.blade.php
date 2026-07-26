@php
    $settingsPath = base_path('config/packaging_settings.json');
    $packagingSettings = file_exists($settingsPath) ? json_decode(file_get_contents($settingsPath), true) : [
        'manpower_rate' => 10000,
        'nails_price_per_kg' => 25000,
        'nails_weight_per_piece' => 0.025
    ];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
    <div class="lg:col-span-12 rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-slate-900">Validasi Data Pendukung</h3>
            <p class="text-xs text-slate-500 mt-0.5">Konfigurasi tarif dasar untuk kalkulasi packaging.</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-rounded text-lg">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('packaging.validasi_data.settings.update') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                <!-- Manpower Rate -->
                <div class="form-group">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Rate Manpower (Rp/Jam)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500 font-medium sm:text-sm">Rp</span>
                        </div>
                        <input type="number" step="any" name="manpower_rate" value="{{ $packagingSettings['manpower_rate'] ?? 10000 }}" 
                               class="pl-10 pr-3 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors py-2 border" required>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Tarif pengerjaan manpower per jam.</p>
                </div>

                <!-- Harga Paku -->
                <div class="form-group">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Harga Paku (Rp/Kg)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500 font-medium sm:text-sm">Rp</span>
                        </div>
                        <input type="number" step="any" name="nails_price_per_kg" value="{{ $packagingSettings['nails_price_per_kg'] ?? 25000 }}" 
                               class="pl-10 pr-3 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors py-2 border" required>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Harga dasar paku per Kilogram.</p>
                </div>

                <!-- Berat Paku -->
                <div class="form-group">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Berat Paku (Kg/Pcs)</label>
                    <div class="relative">
                        <input type="number" step="any" name="nails_weight_per_piece" value="{{ $packagingSettings['nails_weight_per_piece'] ?? 0.025 }}" 
                               class="pl-3 pr-10 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors py-2 border" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-slate-500 font-medium sm:text-sm">Kg</span>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Estimasi berat 1 buah paku dalam kilogram.</p>
                </div>

            </div>

            <div class="border-t border-slate-200 pt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-xl transition-all shadow-sm">
                    <span class="material-symbols-rounded text-lg">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>

    </div>
</div>
