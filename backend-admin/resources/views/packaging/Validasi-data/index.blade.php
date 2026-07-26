<x-app-layout>
    @include('ejf.data_validasi.partials.css.style')

    @php
        $materials = \Illuminate\Support\Facades\DB::table('packaging_validasi_data_material')->get();
        $totalMaterial = $materials->count();
        $totalBalok = $materials->where('kategori', 'MASTER BALOK')->count();
        $totalPapan = $materials->where('kategori', 'MASTER PAPAN')->count();
        $totalTripleks = $materials->where('kategori', 'MASTER TRIPLEKS')->count();

        // Modules switcher
        $allModules = [
            [
                'key' => 'overview',
                'title' => 'Summary',
                'icon' => 'dashboard',
                'href' => request()->url(),
                'issues' => 0,
            ],
            [
                'key' => 'material',
                'title' => 'Master Material',
                'icon' => 'inventory',
                'href' => request()->url() . '?tab=material',
                'issues' => 0,
            ],
            [
                'key' => 'fastener',
                'title' => 'Fastener Validation',
                'description' => 'Validasi Ukuran Paku dan Sekrup.',
                'icon' => 'nail',
                'href' => request()->url() . '?tab=fastener',
                'total' => 3,
                'tone' => 'red',
            ],
            [
                'key' => 'pendukung',
                'title' => 'Data Pendukung',
                'icon' => 'settings',
                'href' => request()->url() . '?tab=pendukung',
                'issues' => 0,
            ]
        ];

        $modules = [
            [
                'key' => 'material',
                'title' => 'Master Material Packaging',
                'description' => 'Validasi data master material balok, papan, dan tripleks.',
                'icon' => 'inventory',
                'href' => request()->url() . '?tab=material',
                'total' => $totalMaterial,
                'tone' => 'indigo',
            ],
            [
                'key' => 'pendukung',
                'title' => 'Data Pendukung (Settings)',
                'description' => 'Validasi tarif manpower, harga paku, berat paku, dll.',
                'icon' => 'settings',
                'href' => request()->url() . '?tab=pendukung',
                'total' => 3,
                'tone' => 'emerald',
            ], 
            [
                'key' => 'fastener',
                'title' => 'Fastener Validation',
                'description' => 'Validasi Ukuran Paku dan Sekrup.',
                'icon' => 'nail',
                'href' => request()->url() . '?tab=fastener',
                'total' => 3,
                'tone' => 'red',
            ]
        ];

        $globalSummary = [
            'total_modules' => 1,
            'total_checked' => $totalMaterial,
            'total_issues' => 0,
            'total_warnings' => 0,
            'total_valid' => $totalMaterial,
            'last_sync' => now()->format('d M Y'),
        ];
    @endphp

    <div class="min-h-screen w-full bg-transparent">
        <div class="w-full px-3 py-4 space-y-4">

            <!-- Back button & Quick switcher header -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-3">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('packaging.index') ?? '#' }}" 
                           class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm"
                           title="Back to Packaging Summary" style="text-decoration: none">
                            <span class="material-symbols-rounded text-xl">arrow_back</span>
                        </a>
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">Data Validasi</h2>
                            <p class="text-xs text-slate-500 font-medium">Kalkulator Produk GTE - Packaging</p>
                        </div>
                    </div>
                    
                    <!-- Quick tab switcher -->
                    <div class="flex flex-wrap items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200">
                        @foreach ($allModules as $mod)
                            @php $isModActive = request('tab') ? request('tab') == $mod['key'] : $mod['key'] === 'overview'; @endphp
                            <a href="{{ $mod['href'] }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 {{ $isModActive ? 'bg-white text-slate-900 shadow-sm border border-slate-200/50' : 'text-slate-600 hover:text-slate-900' }}" style="text-decoration: none">
                                <span class="material-symbols-rounded text-base">{{ $mod['icon'] }}</span>
                                <span>{{ $mod['title'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- BAGIAN ATAS: GRID UTAMA -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                
                {{-- KPI GLOBAL (COLS 8) --}}
                <div class="lg:col-span-8 rounded-xl border border-slate-200 bg-white shadow-sm p-4 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Ringkasan KPI Global</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Ringkasan cepat seluruh modul validasi packaging.</p>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-2">
                        <div class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs shadow-sm">
                            <span class="font-bold text-slate-500 text-[10px] uppercase tracking-wide">Total Modul:</span>
                            <span class="font-black text-slate-900 text-sm">{{ number_format((int) ($globalSummary['total_modules'] ?? 0)) }}</span>
                        </div>
                        
                        <div class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs shadow-sm">
                            <span class="font-bold text-slate-500 text-[10px] uppercase tracking-wide">Total Checked:</span>
                            <span class="font-black text-slate-900 text-sm">{{ number_format((int) ($globalSummary['total_checked'] ?? 0)) }}</span>
                        </div>

                        <div class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs shadow-sm">
                            <span class="font-bold text-emerald-600 text-[10px] uppercase tracking-wide">Valid:</span>
                            <span class="font-black text-emerald-700 text-sm">{{ number_format((int) ($globalSummary['total_valid'] ?? 0)) }}</span>
                        </div>
                    </div>
                </div>

                {{-- SYNC UPDATE (COLS 4) --}}
                <div class="lg:col-span-4 rounded-xl border border-sky-200 bg-sky-50 p-4 flex flex-col justify-between shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wide text-sky-600 flex items-center gap-1">
                                <span class="material-symbols-rounded text-[13px]">inventory</span> Material Stats
                            </div>
                            <div class="mt-1 flex items-baseline gap-1.5">
                                <span class="text-2xl font-extrabold text-sky-700">{{ $totalMaterial }}</span>
                                <span class="text-[10px] font-medium text-sky-600/80">Item Master</span>
                            </div>
                        </div>
                        <span class="text-[9px] font-bold bg-sky-200/50 text-sky-700 px-2 py-0.5 rounded-full">{{ $globalSummary['last_sync'] ?? '-' }}</span>
                    </div>
                    
                    <div class="mt-2 pt-2 border-t border-sky-200/50 grid grid-cols-3 gap-2 text-xs">
                        <div class="flex flex-col bg-white/50 p-1.5 rounded-lg border border-sky-100">
                            <span class="text-sky-600/70 font-bold uppercase tracking-wider text-[8px]">Balok</span>
                            <span class="font-bold text-sm text-sky-800 mt-0.5">{{ $totalBalok }}</span>
                        </div>
                        <div class="flex flex-col bg-white/50 p-1.5 rounded-lg border border-sky-100">
                            <span class="text-sky-600/70 font-bold uppercase tracking-wider text-[8px]">Papan</span>
                            <span class="font-bold text-sm text-sky-800 mt-0.5">{{ $totalPapan }}</span>
                        </div>
                        <div class="flex flex-col bg-white/50 p-1.5 rounded-lg border border-sky-100">
                            <span class="text-sky-600/70 font-bold uppercase tracking-wider text-[8px]">Tripleks</span>
                            <span class="font-bold text-sm text-sky-800 mt-0.5">{{ $totalTripleks }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- BAGIAN BAWAH: CARD SUBMENU MODUL ATAU PARTIAL -->
            @if(request('tab') == 'material')
                @include('packaging_calc.Validasi-data.partials._material')
            @elseif(request('tab') == 'pendukung')
                @include('packaging_calc.Validasi-data.partials._data-pendukung')
            @elseif(request('tab') == 'fastener')
                @include('packaging_calc.Validasi-data.partials._fastener')
            @else
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    {{-- SUB MENU DASHBOARD CARD --}}
                    <div class="lg:col-span-12 rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-slate-900">Sub Menu Dashboard</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Masuk ke masing-masing domain validasi.</p>
                        </div>

                        <div class="grid gap-3 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($modules as $module)
                                <a href="{{ $module['href'] }}"
                                   class="group rounded-xl border border-slate-100 bg-slate-50 p-4 transition hover:-translate-y-0.5 hover:shadow-sm flex items-start gap-3 text-decoration-none">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 shrink-0">
                                        <span class="material-symbols-rounded text-[20px]">{{ $module['icon'] ?? 'dashboard' }}</span>
                                    </div>
                                    <div class="min-w-0 flex-grow">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm font-bold text-slate-900 group-hover:text-slate-800">
                                                {{ $module['title'] }}
                                            </div>
                                            <span class="text-xs font-bold text-slate-500">
                                                {{ number_format((int) ($module['total'] ?? 0)) }} data
                                            </span>
                                        </div>
                                        <p class="mt-1 text-xs leading-relaxed text-slate-500 mb-0">
                                            {{ $module['description'] }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
