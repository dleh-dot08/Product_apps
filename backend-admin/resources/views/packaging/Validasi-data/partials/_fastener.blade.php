@php
    $fasteners = \Illuminate\Support\Facades\DB::table('packaging_fastener_validations')
        ->orderBy('type_from')
        ->orderBy('thk_from_min_mm')
        ->orderBy('type_to')
        ->get();
@endphp

<style>
    /* Paksa Scrollbar Selalu Muncul & Lebih Tebal */
    #fastener-scroll-container {
        width: 100%;
        overflow-x: scroll !important;
        overflow-y: auto !important;
        max-height: 55vh;
        padding-bottom: 15px; /* Memberi ruang untuk scrollbar horizontal */
    }
    #fastener-scroll-container::-webkit-scrollbar {
        width: 16px !important;
        height: 16px !important;
        display: block !important;
    }
    #fastener-scroll-container::-webkit-scrollbar-track {
        background: #f8fafc !important;
        border-radius: 8px !important;
        border: 1px solid #e2e8f0 !important;
    }
    #fastener-scroll-container::-webkit-scrollbar-thumb {
        background: #94a3b8 !important;
        border-radius: 8px !important;
        border: 3px solid #f8fafc !important;
    }
    #fastener-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #64748b !important;
    }
    #fastener-scroll-container::-webkit-scrollbar-corner {
        background: #f8fafc !important;
    }
</style>

<div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col" style="max-height: 70vh;">
    <div class="p-3 border-b border-slate-200 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 bg-slate-50 flex-shrink-0">
        <div>
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-rounded text-indigo-600 text-lg">construction</span>
                Data Validasi Fastener Packaging
            </h3>
            <p class="text-[11px] text-slate-500 mt-1">
                Validasi rekomendasi paku dan sekrup berdasarkan material serta ketebalan sambungan.
            </p>
        </div>

        <span class="text-[11px] font-bold text-slate-500 bg-white px-2.5 py-1 rounded-full border border-slate-200 shadow-sm">
            Total: {{ $fasteners->count() }} Item
        </span>
    </div>

    <!-- TOOLBAR SEARCH & FILTER -->
    <div class="p-3 border-b border-slate-200 bg-white flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 flex-shrink-0">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 w-full xl:w-auto">
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-medium whitespace-nowrap">Tampil</span>
                <select id="fastenerPerPage"
                    class="w-full text-xs border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-8 shadow-sm text-slate-700 bg-white">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="all">All</option>
                </select>
            </div>

            <select id="fastenerFilterFrom"
                class="text-xs border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-8 shadow-sm text-slate-700 bg-white">
                <option value="all">Semua Material Awal</option>
                @foreach($fasteners->pluck('type_from')->filter()->unique()->sort()->values() as $typeFrom)
                    <option value="{{ $typeFrom }}">{{ $typeFrom }}</option>
                @endforeach
            </select>

            <select id="fastenerFilterTo"
                class="text-xs border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-8 shadow-sm text-slate-700 bg-white">
                <option value="all">Semua Material Tujuan</option>
                @foreach($fasteners->pluck('type_to')->filter()->unique()->sort()->values() as $typeTo)
                    <option value="{{ $typeTo }}">{{ $typeTo }}</option>
                @endforeach
            </select>

            <select id="fastenerFilterType"
                class="text-xs border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-1.5 pl-3 pr-8 shadow-sm text-slate-700 bg-white">
                <option value="all">Semua Fastener</option>
                <option value="nail">Memiliki Paku</option>
                <option value="screw">Memiliki Sekrup</option>
                <option value="both">Paku & Sekrup</option>
            </select>
        </div>

        <div class="w-full xl:w-80">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <span class="material-symbols-rounded text-slate-400 text-[18px]">search</span>
                </div>
                <input
                    type="text"
                    id="fastenerSearchInput"
                    class="bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-9 pr-3 py-1.5 shadow-sm transition-colors hover:bg-slate-100 focus:bg-white"
                    placeholder="Cari material, paku, atau sekrup...">
            </div>
        </div>
    </div>

    <div id="fastener-scroll-container" class="flex-1 min-h-0" style="width: 100%; overflow-x: scroll; overflow-y: auto;">
        <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap" style="min-width: 1400px;">
            <thead class="bg-slate-100 text-xs uppercase text-slate-700 border-b border-slate-200 sticky top-0 z-10 shadow-sm" style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th rowspan="2" class="px-4 py-3 font-semibold tracking-wide align-middle">Material Awal</th>
                    <th colspan="2" class="px-3 py-2 font-semibold tracking-wide text-center border-l border-slate-200">
                        Ketebalan Awal
                    </th>
                    <th rowspan="2" class="px-4 py-3 font-semibold tracking-wide align-middle border-l border-slate-200">
                        Material Tujuan
                    </th>
                    <th colspan="2" class="px-3 py-2 font-semibold tracking-wide text-center border-l border-slate-200">
                        Ketebalan Tujuan
                    </th>
                    <th colspan="4" class="px-3 py-2 font-semibold tracking-wide text-center border-l border-slate-200 bg-amber-50 text-amber-800">
                        Rekomendasi Paku
                    </th>
                    <th colspan="4" class="px-3 py-2 font-semibold tracking-wide text-center border-l border-slate-200 bg-indigo-50 text-indigo-800">
                        Rekomendasi Sekrup
                    </th>
                    <th rowspan="2" class="px-4 py-3 font-semibold tracking-wide align-middle border-l border-slate-200">
                        Status
                    </th>
                    <th rowspan="2" class="px-4 py-3 font-semibold tracking-wide align-middle">
                        Catatan
                    </th>
                </tr>
                <tr class="border-t border-slate-200">
                    <th class="px-3 py-2 text-center border-l border-slate-200">Min (mm)</th>
                    <th class="px-3 py-2 text-center">Max (mm)</th>

                    <th class="px-3 py-2 text-center border-l border-slate-200">Min (mm)</th>
                    <th class="px-3 py-2 text-center">Max (mm)</th>

                    <th class="px-4 py-2 border-l border-slate-200 bg-amber-50">Kode</th>
                    <th class="px-4 py-2 bg-amber-50">Tipe</th>
                    <th class="px-3 py-2 text-center bg-amber-50">Ø</th>
                    <th class="px-3 py-2 text-center bg-amber-50">Panjang</th>

                    <th class="px-4 py-2 border-l border-slate-200 bg-indigo-50">Kode</th>
                    <th class="px-4 py-2 bg-indigo-50">Tipe</th>
                    <th class="px-3 py-2 text-center bg-indigo-50 border-b border-slate-200">Ø</th>
                    <th class="px-3 py-2 text-center bg-indigo-50 border-b border-slate-200">Panjang</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100" id="fastenerTableBody">
                @forelse($fasteners as $item)
                    @php
                        $hasNail = !empty($item->fastener_type_nail);
                        $hasScrew = !empty($item->fastener_type_screw);

                        $typeFromClass = match(strtolower($item->type_from)) {
                            'triplek', 'tripleks' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
                            'papan kayu', 'papan' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'balok kayu', 'balok' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                            default => 'bg-slate-50 text-slate-700 ring-slate-600/20',
                        };

                        $searchText = strtolower(implode(' ', [
                            $item->type_from,
                            $item->type_to,
                            $item->fastener_type_nail,
                            $item->fastener_type_screw,
                            $item->nail_code,
                            $item->screw_code,
                            $item->notes,
                            $item->nail_diameter_mm,
                            $item->nail_length_mm,
                            $item->screw_diameter_mm,
                            $item->screw_length_mm,
                        ]));
                    @endphp

                    <tr
                        class="hover:bg-slate-50/80 transition-colors fastener-row"
                        data-from="{{ $item->type_from }}"
                        data-to="{{ $item->type_to }}"
                        data-has-nail="{{ $hasNail ? '1' : '0' }}"
                        data-has-screw="{{ $hasScrew ? '1' : '0' }}"
                        data-search="{{ $searchText }}">

                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset {{ $typeFromClass }}">
                                {{ $item->type_from }}
                            </span>
                        </td>

                        <td class="px-3 py-3 text-center tabular-nums text-xs font-semibold text-slate-700 bg-slate-50/50">
                            {{ number_format((float) $item->thk_from_min_mm, 2, ',', '.') }}
                        </td>
                        <td class="px-3 py-3 text-center tabular-nums text-xs font-semibold text-slate-700 bg-slate-50/50">
                            {{ number_format((float) $item->thk_from_max_mm, 2, ',', '.') }}
                        </td>

                        <td class="px-4 py-3 border-l border-slate-100">
                            <span class="font-semibold text-slate-800">{{ $item->type_to }}</span>
                        </td>

                        <td class="px-3 py-3 text-center tabular-nums text-xs font-semibold text-slate-700 bg-slate-50/50">
                            {{ number_format((float) $item->thk_to_min_mm, 2, ',', '.') }}
                        </td>
                        <td class="px-3 py-3 text-center tabular-nums text-xs font-semibold text-slate-700 bg-slate-50/50">
                            {{ number_format((float) $item->thk_to_max_mm, 2, ',', '.') }}
                        </td>

                        <td class="px-4 py-3 border-l border-slate-100 bg-amber-50/20">
                            @if($hasNail)
                                <span class="inline-flex items-center rounded bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-800">{{ $item->nail_code ?? '-' }}</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 bg-amber-50/20">
                            @if($hasNail)
                                <div class="font-semibold text-slate-800">{{ $item->fastener_type_nail }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Fastener nail</div>
                            @else
                                <span class="text-xs text-slate-400">Tidak direkomendasikan</span>
                            @endif
                        </td>

                        <td class="px-3 py-3 text-center bg-amber-50/20">
                            @if(!is_null($item->nail_diameter_mm))
                                <div class="font-bold text-slate-800 tabular-nums">
                                    {{ number_format((float) $item->nail_diameter_mm, 2, ',', '.') }} mm
                                </div>
                                <div class="text-[10px] text-slate-400 tabular-nums">
                                    {{ number_format((float) $item->nail_diameter_inch, 3, ',', '.') }} in
                                </div>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>

                        <td class="px-3 py-3 text-center bg-amber-50/20">
                            @if(!is_null($item->nail_length_mm))
                                <div class="font-bold text-slate-800 tabular-nums">
                                    {{ number_format((float) $item->nail_length_mm, 2, ',', '.') }} mm
                                </div>
                                <div class="text-[10px] text-slate-400 tabular-nums">
                                    {{ number_format((float) $item->nail_length_inch, 3, ',', '.') }} in
                                </div>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 border-l border-slate-100 bg-indigo-50/20">
                            @if($hasScrew)
                                <span class="inline-flex items-center rounded bg-indigo-100 px-2 py-0.5 text-xs font-bold text-indigo-800">{{ $item->screw_code ?? '-' }}</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 bg-indigo-50/20">
                            @if($hasScrew)
                                <div class="font-semibold text-slate-800">{{ $item->fastener_type_screw }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Fastener screw</div>
                            @else
                                <span class="text-xs text-slate-400">Tidak tersedia</span>
                            @endif
                        </td>

                        <td class="px-3 py-3 text-center bg-indigo-50/20">
                            @if(!is_null($item->screw_diameter_mm))
                                <div class="font-bold text-slate-800 tabular-nums">
                                    {{ number_format((float) $item->screw_diameter_mm, 2, ',', '.') }} mm
                                </div>
                                <div class="text-[10px] text-slate-400 tabular-nums">
                                    {{ number_format((float) $item->screw_diameter_inch, 3, ',', '.') }} in
                                </div>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>

                        <td class="px-3 py-3 text-center bg-indigo-50/20">
                            @if(!is_null($item->screw_length_mm))
                                <div class="font-bold text-slate-800 tabular-nums">
                                    {{ number_format((float) $item->screw_length_mm, 2, ',', '.') }} mm
                                </div>
                                <div class="text-[10px] text-slate-400 tabular-nums">
                                    {{ number_format((float) $item->screw_length_inch, 3, ',', '.') }} in
                                </div>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 border-l border-slate-100">
                            @if($item->is_active)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-500 ring-1 ring-inset ring-slate-500/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 max-w-xs whitespace-normal">
                            <span class="text-xs text-slate-500">
                                {{ $item->notes ?: '-' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr id="fastenerInitialEmptyState">
                        <td colspan="14" class="px-4 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <span class="material-symbols-rounded text-5xl text-slate-200 mb-3">construction</span>
                                <p class="font-medium text-slate-600">Data validasi fastener belum tersedia.</p>
                                <p class="text-xs mt-1">Jalankan migration atau tambahkan data validasi terlebih dahulu.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse

                <tr id="fastenerFilteredEmptyState" class="hidden">
                    <td colspan="14" class="px-4 py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <span class="material-symbols-rounded text-5xl text-slate-200 mb-3">search_off</span>
                            <p class="font-medium text-slate-600">Data tidak ditemukan.</p>
                            <p class="text-xs mt-1">Ubah kata pencarian atau filter yang digunakan.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION CONTROLS -->
    <div class="p-3 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row justify-between items-center gap-3 flex-shrink-0">
        <div class="text-xs text-slate-500" id="fastenerPaginationInfo">
            Menampilkan
            <span class="font-semibold text-slate-900" id="fastenerPageStart">0</span>
            -
            <span class="font-semibold text-slate-900" id="fastenerPageEnd">0</span>
            dari
            <span class="font-semibold text-slate-900" id="fastenerPageTotal">0</span>
            data
        </div>

        <div class="flex items-center gap-1" id="fastenerPaginationControls"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = Array.from(document.querySelectorAll('.fastener-row'));
        const perPageSelect = document.getElementById('fastenerPerPage');
        const filterFrom = document.getElementById('fastenerFilterFrom');
        const filterTo = document.getElementById('fastenerFilterTo');
        const filterType = document.getElementById('fastenerFilterType');
        const searchInput = document.getElementById('fastenerSearchInput');

        const pageStartEl = document.getElementById('fastenerPageStart');
        const pageEndEl = document.getElementById('fastenerPageEnd');
        const pageTotalEl = document.getElementById('fastenerPageTotal');
        const paginationControls = document.getElementById('fastenerPaginationControls');
        const filteredEmptyState = document.getElementById('fastenerFilteredEmptyState');

        let currentPage = 1;
        let filteredRows = [...rows];

        function applyFilters() {
            const searchTerm = searchInput.value.trim().toLowerCase();
            const selectedFrom = filterFrom.value;
            const selectedTo = filterTo.value;
            const selectedFastener = filterType.value;

            filteredRows = rows.filter(row => {
                const rowSearch = row.dataset.search || '';
                const rowFrom = row.dataset.from || '';
                const rowTo = row.dataset.to || '';
                const hasNail = row.dataset.hasNail === '1';
                const hasScrew = row.dataset.hasScrew === '1';

                const matchesSearch = rowSearch.includes(searchTerm);
                const matchesFrom = selectedFrom === 'all' || rowFrom === selectedFrom;
                const matchesTo = selectedTo === 'all' || rowTo === selectedTo;

                let matchesFastener = true;

                if (selectedFastener === 'nail') {
                    matchesFastener = hasNail;
                } else if (selectedFastener === 'screw') {
                    matchesFastener = hasScrew;
                } else if (selectedFastener === 'both') {
                    matchesFastener = hasNail && hasScrew;
                }

                return matchesSearch && matchesFrom && matchesTo && matchesFastener;
            });

            currentPage = 1;
            renderTable();
        }

        function renderTable() {
            const perPageValue = perPageSelect.value;
            const isAll = perPageValue === 'all';
            const totalItems = filteredRows.length;
            const itemsPerPage = isAll ? Math.max(totalItems, 1) : parseInt(perPageValue, 10);
            const totalPages = isAll || totalItems === 0 ? 1 : Math.ceil(totalItems / itemsPerPage);

            currentPage = Math.min(Math.max(currentPage, 1), totalPages);

            const startIdx = totalItems === 0 ? 0 : (currentPage - 1) * itemsPerPage;
            const endIdx = isAll ? totalItems : Math.min(startIdx + itemsPerPage, totalItems);

            rows.forEach(row => {
                row.style.display = 'none';
            });

            for (let index = startIdx; index < endIdx; index++) {
                filteredRows[index].style.display = '';
            }

            if (filteredEmptyState) {
                filteredEmptyState.classList.toggle('hidden', !(totalItems === 0 && rows.length > 0));
            }

            pageStartEl.textContent = totalItems === 0 ? 0 : startIdx + 1;
            pageEndEl.textContent = endIdx;
            pageTotalEl.textContent = totalItems;

            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            paginationControls.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            const createButton = (label, page, active = false, disabled = false) => {
                const button = document.createElement('button');

                button.type = 'button';
                button.disabled = disabled;
                button.className = [
                    'min-w-8 px-2.5 py-1 rounded text-xs font-medium border transition-colors',
                    active
                        ? 'bg-indigo-50 border-indigo-200 text-indigo-700'
                        : disabled
                            ? 'border-transparent text-slate-400 cursor-not-allowed'
                            : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'
                ].join(' ');

                button.innerHTML = label;

                if (!disabled) {
                    button.addEventListener('click', function () {
                        currentPage = page;
                        renderTable();
                    });
                }

                return button;
            };

            paginationControls.appendChild(
                createButton(
                    '<span class="material-symbols-rounded text-sm align-middle">chevron_left</span>',
                    currentPage - 1,
                    false,
                    currentPage === 1
                )
            );

            const visiblePages = new Set([
                1,
                totalPages,
                currentPage - 1,
                currentPage,
                currentPage + 1
            ]);

            let previousPage = 0;

            [...visiblePages]
                .filter(page => page >= 1 && page <= totalPages)
                .sort((a, b) => a - b)
                .forEach(page => {
                    if (previousPage && page - previousPage > 1) {
                        const dots = document.createElement('span');
                        dots.className = 'px-1 text-slate-400 text-xs';
                        dots.textContent = '...';
                        paginationControls.appendChild(dots);
                    }

                    paginationControls.appendChild(
                        createButton(String(page), page, page === currentPage)
                    );

                    previousPage = page;
                });

            paginationControls.appendChild(
                createButton(
                    '<span class="material-symbols-rounded text-sm align-middle">chevron_right</span>',
                    currentPage + 1,
                    false,
                    currentPage === totalPages
                )
            );
        }

        perPageSelect.addEventListener('change', function () {
            currentPage = 1;
            renderTable();
        });

        filterFrom.addEventListener('change', applyFilters);
        filterTo.addEventListener('change', applyFilters);
        filterType.addEventListener('change', applyFilters);
        searchInput.addEventListener('input', applyFilters);

        renderTable();
    });
</script>
