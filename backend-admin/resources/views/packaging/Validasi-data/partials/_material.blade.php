@php
    $materials = \Illuminate\Support\Facades\DB::table('packaging_validasi_data_material')->get();
@endphp

<div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col" style="max-height: 70vh;">
    <div class="p-3 border-b border-slate-200 flex justify-between items-center bg-slate-50 flex-shrink-0">
        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
            <span class="material-symbols-rounded text-indigo-600 text-lg">inventory</span>
            Data Master Material Packaging
        </h3>
        <span class="text-[11px] font-bold text-slate-500 bg-white px-2.5 py-1 rounded-full border border-slate-200 shadow-sm">
            Total: {{ $materials->count() }} Item
        </span>
    </div>
    
    <!-- TOOLBAR SEARCH & FILTER -->
    <div class="p-3 border-b border-slate-200 bg-white flex flex-col sm:flex-row justify-between items-center gap-3 flex-shrink-0">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-medium">Tampil</span>
                <select id="perPage" class="text-xs border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block py-1.5 pl-3 pr-8 shadow-sm text-slate-700 bg-white">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="all">All</option>
                </select>
            </div>
            <select id="filterKategori" class="text-xs border-slate-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block py-1.5 pl-3 pr-8 shadow-sm text-slate-700 bg-white">
                <option value="all">Semua Kategori</option>
                <option value="MASTER BALOK">Balok</option>
                <option value="MASTER PAPAN">Papan</option>
                <option value="MASTER TRIPLEKS">Tripleks</option>
            </select>
        </div>
        <div class="w-full sm:w-64">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <span class="material-symbols-rounded text-slate-400 text-[18px]">search</span>
                </div>
                <input type="text" id="searchInput" class="bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-9 pr-3 py-1.5 shadow-sm transition-colors hover:bg-slate-100 focus:bg-white" placeholder="Cari kode atau material...">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto overflow-y-auto flex-1 min-h-0">
        <table class="w-full text-left text-sm text-slate-600 relative">
            <thead class="bg-slate-100/80 text-xs uppercase text-slate-700 border-b border-slate-200 sticky top-0 z-10 shadow-sm" style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th class="px-4 py-3 font-semibold tracking-wide">Kategori</th>
                    <th class="px-4 py-3 font-semibold tracking-wide">Kode</th>
                    <th class="px-4 py-3 font-semibold tracking-wide">Jenis Material</th>
                    <th class="px-3 py-3 font-semibold tracking-wide text-center" title="Tebal">T (mm)</th>
                    <th class="px-3 py-3 font-semibold tracking-wide text-center" title="Lebar">L (mm)</th>
                    <th class="px-3 py-3 font-semibold tracking-wide text-center" title="Panjang">P (mm)</th>
                    <th class="px-4 py-3 font-semibold tracking-wide">Size Material</th>
                    <th class="px-4 py-3 font-semibold tracking-wide text-right">Rate</th>
                    <th class="px-4 py-3 font-semibold tracking-wide text-right">Harga</th>
                    <th class="px-4 py-3 font-semibold tracking-wide">Satuan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="materialTableBody">
                @forelse($materials as $mat)
                <tr class="hover:bg-slate-50/80 transition-colors material-row" data-kategori="{{ $mat->kategori }}" data-search="{{ strtolower($mat->kode . ' ' . $mat->jenis_material . ' ' . $mat->size_material) }}">
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset {{ $mat->kategori == 'MASTER BALOK' ? 'bg-amber-50 text-amber-700 ring-amber-600/20' : ($mat->kategori == 'MASTER PAPAN' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-sky-50 text-sky-700 ring-sky-600/20') }}">
                            {{ str_replace('MASTER ', '', $mat->kategori) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-bold text-slate-800">{{ $mat->kode }}</td>
                    <td class="px-4 py-3 font-medium">{{ $mat->jenis_material }}</td>
                    <td class="px-3 py-3 text-center text-xs font-semibold text-slate-700 bg-slate-50/50">{{ (float)$mat->tebal ?: '-' }}</td>
                    <td class="px-3 py-3 text-center text-xs font-semibold text-slate-700 bg-slate-50/50">{{ (float)$mat->lebar ?: '-' }}</td>
                    <td class="px-3 py-3 text-center text-xs font-semibold text-slate-700 bg-slate-50/50">{{ (float)$mat->panjang ?: '-' }}</td>
                    <td class="px-4 py-3 text-xs font-medium">{{ $mat->size_material }}</td>
                    <td class="px-4 py-3 text-right tabular-nums text-xs">
                        {{ $mat->rate_material ? number_format($mat->rate_material, 2, ',', '.') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums font-bold text-slate-900">
                        {{ $mat->harga ? number_format($mat->harga, 2, ',', '.') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-xs font-bold text-slate-400">{{ $mat->satuan_harga }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <span class="material-symbols-rounded text-5xl text-slate-200 mb-3">inventory_2</span>
                            <p class="font-medium text-slate-600">Data material belum tersedia.</p>
                            <p class="text-xs mt-1">Silakan lakukan sinkronisasi atau tambahkan data master.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION CONTROLS -->
    <div class="p-3 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row justify-between items-center gap-3 flex-shrink-0">
        <div class="text-xs text-slate-500" id="paginationInfo">
            Menampilkan <span class="font-semibold text-slate-900" id="pageStart">0</span> - <span class="font-semibold text-slate-900" id="pageEnd">0</span> dari <span class="font-semibold text-slate-900" id="pageTotal">0</span> data
        </div>
        <div class="flex items-center gap-1" id="paginationControls">
            <!-- Buttons injected by JS -->
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = Array.from(document.querySelectorAll('.material-row'));
        const perPageSelect = document.getElementById('perPage');
        const filterKategori = document.getElementById('filterKategori');
        const searchInput = document.getElementById('searchInput');
        
        const pageStartEl = document.getElementById('pageStart');
        const pageEndEl = document.getElementById('pageEnd');
        const pageTotalEl = document.getElementById('pageTotal');
        const paginationControls = document.getElementById('paginationControls');

        let currentPage = 1;
        let filteredRows = [...rows];
        
        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const kategori = filterKategori.value;
            
            filteredRows = rows.filter(row => {
                const rowSearch = row.getAttribute('data-search');
                const rowKategori = row.getAttribute('data-kategori');
                
                const matchesSearch = rowSearch.includes(searchTerm);
                const matchesKategori = kategori === 'all' || rowKategori === kategori;
                
                return matchesSearch && matchesKategori;
            });
            
            currentPage = 1;
            renderTable();
        }
        
        function renderTable() {
            const perPageValue = perPageSelect.value;
            const isAll = perPageValue === 'all';
            const itemsPerPage = isAll ? filteredRows.length : parseInt(perPageValue);
            
            const totalItems = filteredRows.length;
            const totalPages = isAll || totalItems === 0 ? 1 : Math.ceil(totalItems / itemsPerPage);
            
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;
            
            const startIdx = (currentPage - 1) * itemsPerPage;
            const endIdx = isAll ? totalItems : Math.min(startIdx + itemsPerPage, totalItems);
            
            // Hide all rows first
            rows.forEach(row => row.style.display = 'none');
            
            // Show only the page rows
            for (let i = startIdx; i < endIdx; i++) {
                filteredRows[i].style.display = '';
            }
            
            // Empty state handling
            const emptyStateRow = document.getElementById('emptyStateRow');
            if (emptyStateRow) {
                if (totalItems === 0 && rows.length > 0) {
                    // We have data but filtered out everything, need a "not found" row?
                    // For now let's just show standard empty if no rows total
                }
            }
            
            // Update info
            pageStartEl.textContent = totalItems === 0 ? 0 : startIdx + 1;
            pageEndEl.textContent = endIdx;
            pageTotalEl.textContent = totalItems;
            
            renderPagination(totalPages);
        }
        
        function renderPagination(totalPages) {
            paginationControls.innerHTML = '';
            
            if (totalPages <= 1) return;
            
            // Prev button
            const prevBtn = document.createElement('button');
            prevBtn.className = `px-2 py-1 rounded text-xs font-medium border ${currentPage === 1 ? 'border-transparent text-slate-400 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'}`;
            prevBtn.innerHTML = '<span class="material-symbols-rounded text-sm align-middle">chevron_left</span>';
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => { currentPage--; renderTable(); };
            paginationControls.appendChild(prevBtn);
            
            // Pages
            for (let i = 1; i <= totalPages; i++) {
                // Show first, last, current, and adjacent
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    const btn = document.createElement('button');
                    btn.className = `px-2.5 py-1 rounded text-xs font-medium border ${currentPage === i ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'}`;
                    btn.textContent = i;
                    btn.onclick = () => { currentPage = i; renderTable(); };
                    paginationControls.appendChild(btn);
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    const span = document.createElement('span');
                    span.className = 'px-1 text-slate-400 text-xs';
                    span.textContent = '...';
                    paginationControls.appendChild(span);
                }
            }
            
            // Next button
            const nextBtn = document.createElement('button');
            nextBtn.className = `px-2 py-1 rounded text-xs font-medium border ${currentPage === totalPages ? 'border-transparent text-slate-400 cursor-not-allowed' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'}`;
            nextBtn.innerHTML = '<span class="material-symbols-rounded text-sm align-middle">chevron_right</span>';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => { currentPage++; renderTable(); };
            paginationControls.appendChild(nextBtn);
        }

        perPageSelect.addEventListener('change', () => { currentPage = 1; renderTable(); });
        filterKategori.addEventListener('change', applyFilters);
        searchInput.addEventListener('input', applyFilters);
        
        // Initial render
        if (rows.length > 0) {
            renderTable();
        }
    });
</script>
