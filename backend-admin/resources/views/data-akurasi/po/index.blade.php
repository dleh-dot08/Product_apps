<x-app-layout>
    <!-- Custom CSS untuk Header Banner & Table (Premium Design) -->
    <style>
        /* =========================================================
           PURCHASE ORDER — MODERN / PROPORTIONAL UI
           Fokus perubahan: spacing, hierarchy, table density,
           search, card, pagination, modal, light/dark consistency.
        ========================================================== */
        :root {
            --po-bg: #f8fafc;
            --po-card: #ffffff;
            --po-border: #e2e8f0;
            --po-border-soft: #eef2f7;
            --po-text: #0f172a;
            --po-muted: #64748b;
            --po-subtle: #94a3b8;
            --po-primary: #ea580c;
            --po-primary-soft: rgba(234, 88, 12, .10);
            --po-primary-border: rgba(234, 88, 12, .20);
            --po-success: #059669;
            --po-danger: #dc2626;
            --po-warning: #d97706;
            --po-radius: 14px;
            --po-shadow: 0 6px 22px rgba(15, 23, 42, .055);
        }

        html[data-bs-theme="dark"] {
            --po-bg: #0b0d10;
            --po-card: #111419;
            --po-border: rgba(255,255,255,.09);
            --po-border-soft: rgba(255,255,255,.06);
            --po-text: #f8fafc;
            --po-muted: #94a3b8;
            --po-subtle: #64748b;
            --po-primary: #fb923c;
            --po-primary-soft: rgba(251, 146, 60, .10);
            --po-primary-border: rgba(251, 146, 60, .22);
            --po-shadow: 0 12px 28px rgba(0,0,0,.25);
        }

        /* -------------------------
           HEADER / HERO
        -------------------------- */
        .dashboard-banner {
            position: relative;
            overflow: hidden;
            border-radius: var(--po-radius);
            padding: 16px 18px;
            border: 1px solid var(--po-border);
            background: var(--po-card);
            box-shadow: var(--po-shadow);
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        html:not([data-bs-theme="dark"]) .dashboard-banner {
            background:
                radial-gradient(circle at 92% 0%, rgba(234, 88, 12, .09), transparent 30%),
                linear-gradient(135deg, #ffffff 0%, #fffcf5 65%, #fff7ed 100%);
        }

        html[data-bs-theme="dark"] .dashboard-banner {
            background:
                radial-gradient(circle at 92% 0%, rgba(251, 146, 60, .10), transparent 30%),
                var(--po-card);
        }

        .banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 9px;
            margin-bottom: 5px;
            border: 1px solid var(--po-primary-border);
            border-radius: 999px;
            background: var(--po-primary-soft);
            color: var(--po-primary);
            font-size: 10px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .banner-title {
            margin: 0;
            color: var(--po-text);
            font-size: clamp(1.15rem, 1.6vw, 1.5rem);
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -.025em;
        }

        .dashboard-banner .text-muted {
            color: var(--po-muted) !important;
        }

        /* -------------------------
           SEARCH
        -------------------------- */
        .search-container {
            position: relative;
            width: min(100%, 380px);
            max-width: 380px;
        }

        .search-container i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            color: var(--po-subtle);
            font-size: 13px;
            pointer-events: none;
        }

        .search-input {
            min-height: 42px;
            padding: 8px 14px 8px 40px;
            border: 1px solid var(--po-border);
            border-radius: 10px;
            background: rgba(255,255,255,.88);
            color: var(--po-text);
            font-size: 13px;
            box-shadow: none !important;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .search-input::placeholder {
            color: #94a3b8;
        }

        .search-input:focus {
            border-color: rgba(234, 88, 12, .65);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, .10) !important;
        }

        html[data-bs-theme="dark"] .search-input {
            background: rgba(255,255,255,.035);
            border-color: var(--po-border);
            color: #f8fafc;
        }

        html[data-bs-theme="dark"] .search-input:focus {
            background: rgba(255,255,255,.055);
        }

        /* -------------------------
           MAIN DATA CARD
        -------------------------- */
        .data-card {
            overflow: hidden;
            border: 1px solid var(--po-border) !important;
            border-radius: var(--po-radius);
            background: var(--po-card) !important;
            color: var(--po-text);
            box-shadow: var(--po-shadow) !important;
        }

        .data-card .card-header {
            min-height: 58px;
            background: transparent !important;
            border-bottom: 1px solid var(--po-border-soft) !important;
        }

        .data-card .card-header h6 {
            color: var(--po-text);
            font-size: 13px;
            letter-spacing: -.01em;
        }

        .data-card .btn-refresh {
            border: 1px solid var(--po-primary-border);
            border-radius: 9px !important;
            background: var(--po-primary-soft);
            color: var(--po-primary) !important;
            box-shadow: none !important;
            font-size: 11px;
            font-weight: 700 !important;
            transition: background .18s ease, border-color .18s ease, transform .18s ease;
        }

        .data-card .btn-refresh:hover {
            background: rgba(234, 88, 12, .16);
            border-color: rgba(234, 88, 12, .35);
        }

        /* -------------------------
           TABLE
        -------------------------- */
        .table-premium {
            --bs-table-bg: transparent;
            margin-bottom: 0;
            color: var(--po-text);
        }

        .table-premium thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            padding: 10px 9px !important;
            border-top: 0;
            border-bottom: 1px solid var(--po-border) !important;
            background: #f8fafc !important;
            color: #64748b;
            font-size: 10px;
            line-height: 1.25;
            font-weight: 800;
            letter-spacing: .035em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .table-premium tbody td {
            padding: 10px 9px !important;
            border-color: var(--po-border-soft);
            color: #334155;
            font-size: 12px;
            line-height: 1.35;
            vertical-align: middle;
        }

        .table-premium tbody tr {
            transition: background .15s ease;
        }

        .table-premium tbody tr:hover > * {
            --bs-table-accent-bg: #fff7ed;
            background-color: #fff7ed !important;
        }

        .table-premium .badge {
            font-size: 10px;
            line-height: 1.1;
            font-weight: 700;
            box-shadow: none !important;
        }

        html[data-bs-theme="dark"] .table-premium thead th {
            background: #15191f !important;
            color: #94a3b8;
            border-color: var(--po-border) !important;
        }

        html[data-bs-theme="dark"] .table-premium tbody td {
            color: #cbd5e1;
            border-color: var(--po-border-soft);
        }

        html[data-bs-theme="dark"] .table-premium tbody tr:hover > * {
            --bs-table-accent-bg: rgba(255,255,255,.025);
            background-color: rgba(255,255,255,.025) !important;
        }

        .table-responsive {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
            width: 8px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        /* -------------------------
           PAGINATION
        -------------------------- */
        #paginationInfo {
            color: var(--po-muted) !important;
            font-size: 11px;
        }

        #btnPrev,
        #btnNext {
            min-height: 34px;
            padding: 6px 12px !important;
            border-color: var(--po-border);
            border-radius: 8px !important;
            color: var(--po-muted);
            background: var(--po-card);
            box-shadow: none !important;
            font-size: 11px;
            font-weight: 700;
        }

        #btnPrev:not(:disabled):hover,
        #btnNext:not(:disabled):hover {
            color: var(--po-primary);
            border-color: var(--po-primary-border);
            background: var(--po-primary-soft);
        }

        #pageIndicator {
            min-width: 34px;
            padding: 7px 10px !important;
            border: 1px solid var(--po-primary-border);
            border-radius: 8px !important;
            background: var(--po-primary-soft) !important;
            color: var(--po-primary);
            box-shadow: none !important;
            font-size: 11px !important;
            font-weight: 800;
        }

        /* -------------------------
           LOADING / EMPTY
        -------------------------- */
        .loading-spinner {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 3px solid rgba(234, 88, 12, .18);
            border-right-color: var(--po-primary);
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
        }

        /* -------------------------
           DETAIL MODAL
        -------------------------- */
        .modal-detail-content {
            overflow: hidden;
            border: 1px solid var(--po-border);
            border-radius: 16px;
            background: var(--po-card);
            box-shadow: 0 22px 55px rgba(15,23,42,.18);
        }

        .modal-detail-header {
            padding: 16px 18px;
            border: 0;
            border-bottom: 1px solid rgba(255,255,255,.10);
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            color: #fff;
        }

        .modal-detail-header .modal-title {
            font-size: 15px;
        }

        .detail-row {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 0 0 10px;
            margin-bottom: 10px;
            border-bottom: 1px dashed var(--po-border);
        }

        .detail-label {
            width: 34%;
            color: var(--po-muted);
            font-size: 10px;
            line-height: 1.35;
            font-weight: 800;
            letter-spacing: .035em;
            text-transform: uppercase;
        }

        .detail-value {
            width: 66%;
            min-width: 0;
            color: var(--po-text);
            font-size: 12px;
            line-height: 1.45;
            font-weight: 650;
            overflow-wrap: anywhere;
        }

        html[data-bs-theme="dark"] .modal-detail-content {
            background: var(--po-card);
        }

        /* -------------------------
           RESPONSIVE
        -------------------------- */
        @media (max-width: 767.98px) {
            .dashboard-banner {
                padding: 14px;
            }

            .search-container {
                width: 100%;
                max-width: none;
            }

            .banner-title {
                font-size: 1.2rem;
            }

            .data-card .card-header {
                align-items: flex-start !important;
                gap: 10px;
            }

            .detail-row {
                display: block;
            }

            .detail-label,
            .detail-value {
                width: 100%;
            }

            .detail-label {
                margin-bottom: 4px;
            }
        }

        /* Sticky Columns */
        .table-premium th.sticky-col-1,
        .table-premium td.sticky-col-1 {
            position: sticky;
            left: 0;
            z-index: 2;
            background-color: #ffffff;
        }
        
        .table-premium th.sticky-col-2,
        .table-premium td.sticky-col-2 {
            position: sticky;
            left: 50px; /* Fixed width of col 1 */
            z-index: 2;
            background-color: #ffffff;
        }
        
        /* Shadow after the second sticky column */
        .table-premium th.sticky-col-2::after,
        .table-premium td.sticky-col-2::after {
            content: '';
            position: absolute;
            top: 0;
            right: -5px;
            bottom: 0;
            width: 5px;
            box-shadow: inset 5px 0 5px -5px rgba(0,0,0,0.15);
            pointer-events: none;
            z-index: 1;
        }

        .table-premium thead th.sticky-col-1,
        .table-premium thead th.sticky-col-2 {
            z-index: 3;
            background-color: #f8f9fa !important; /* matches .table-light */
        }

        .table-premium tbody tr {
            background-color: #ffffff;
        }
        .table-premium tbody tr:hover td,
        .table-premium tbody tr:hover td.sticky-col-1,
        .table-premium tbody tr:hover td.sticky-col-2 {
            background-color: #fff7ed;
        }
        
        html[data-bs-theme="dark"] .table-premium th.sticky-col-1,
        html[data-bs-theme="dark"] .table-premium td.sticky-col-1,
        html[data-bs-theme="dark"] .table-premium th.sticky-col-2,
        html[data-bs-theme="dark"] .table-premium td.sticky-col-2 {
            background-color: #111419;
        }
        
        html[data-bs-theme="dark"] .table-premium thead th.sticky-col-1,
        html[data-bs-theme="dark"] .table-premium thead th.sticky-col-2 {
            background-color: #15191f !important;
        }

        html[data-bs-theme="dark"] .table-premium tbody tr:hover td.sticky-col-1,
        html[data-bs-theme="dark"] .table-premium tbody tr:hover td.sticky-col-2 {
            background-color: rgba(255,255,255,.025) !important;
        }
    </style>

    <!-- 1. Header Banner -->
    <div class="row mb-3" style="position: sticky; top: .75rem; z-index: 1020;">
        <div class="col-12">
            <div class="dashboard-banner d-flex justify-content-between align-items-center flex-wrap gap-3">
                <!-- Decorative Background Element -->
                <div style="position: absolute; top: -30px; right: -20px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(234, 88, 12, 0.18) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
                
                <div style="position: relative; z-index: 1;">
                    <div class="banner-badge">
                        <i class="fa-solid fa-cart-shopping"></i>
                        AQPA PURCHASING
                    </div>
                    <h1 class="banner-title mb-2">Data Pembelian (PO)</h1>
                    <p class="text-muted small mb-0 fw-medium">Sinkronisasi Realtime API dari Akurasi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Tabel Data -->
    <div class="row mt-3 g-3">
        <div class="col-12">
            <div class="card data-card h-100">
                <div class="card-header bg-transparent px-3 py-3 d-flex flex-column flex-xxl-row justify-content-between align-items-xxl-center gap-3">
                    <h6 class="fw-bold mb-0 text-nowrap"><i class="fa-solid fa-list text-orange me-2" style="color:var(--po-primary)"></i>Daftar Transaksi Pembelian</h6>
                    
                    <div class="d-flex flex-wrap align-items-center gap-2 justify-content-xxl-end w-100">
                        <!-- Add Status / Hold filters here if needed by API in future -->
                        <div class="d-flex align-items-center gap-1 bg-white border rounded px-2">
                            <input type="date" id="filterDateFrom" class="form-control form-control-sm border-0 px-1" style="width: auto;" title="Tanggal Mulai">
                            <span class="text-muted small"><i class="fa-solid fa-arrow-right"></i></span>
                            <input type="date" id="filterDateTo" class="form-control form-control-sm border-0 px-1" style="width: auto;" title="Tanggal Akhir">
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 text-decoration-none" onclick="resetDateFilter()" title="Reset Tanggal">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                        <div class="input-group input-group-sm flex-grow-1" style="max-width: 350px;">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Cari No PO, Pemasok, Barang...">
                            <button class="btn btn-primary" type="button" onclick="fetchPurchaseOrders(1)" style="background-color: var(--po-primary); border-color: var(--po-primary);"><i class="fa-solid fa-search"></i></button>
                        </div>
                        <button class="btn btn-sm btn-light border fw-semibold" onclick="resetFilters()">
                            <i class="fa-solid fa-rotate-left me-1"></i> Reset
                        </button>
                        <button class="btn btn-sm btn-refresh px-3" onclick="fetchPurchaseOrders(1)">
                            <i class="fa-solid fa-rotate-right me-1"></i> Refresh Data
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive pb-3">
                        <table class="table table-hover table-premium mb-0 text-nowrap" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center py-2 align-middle sticky-col-1" style="width: 50px; min-width: 50px; max-width: 50px;">No</th>
                                    <th class="py-2 align-middle sortable-header sticky-col-2" data-column="no_pembelian" style="cursor:pointer; width: 140px; min-width: 140px;">No. Pembelian <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="tgl_pembelian" style="cursor:pointer;">Tgl Pembelian <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="tgl_ekspetasi" style="cursor:pointer;">Tgl Ekspetasi <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle text-center">TOP</th>
                                    <th class="py-2 align-middle text-center">Sisa Hari</th>
                                    <th class="py-2 align-middle">No. Permintaan</th>
                                    <th class="py-2 align-middle">Tgl Permintaan</th>
                                    <th class="py-2 align-middle">Tgl Target</th>
                                    <th class="py-2 align-middle">SO NO</th>
                                    <th class="py-2 align-middle">No Penerimaan</th>
                                    <th class="py-2 align-middle">Tgl Penerimaan</th>
                                    <th class="py-2 align-middle">Ekspetasi vs PB</th>
                                    <th class="py-2 align-middle sortable-header" data-column="no_pemasok" style="cursor:pointer;">No. Pemasok <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="nama_pemasok" style="cursor:pointer;">Nama Pemasok <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle">Purchaser</th>
                                    <th class="py-2 align-middle sortable-header" data-column="no_barang" style="cursor:pointer;">No. Barang <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="deskripsi_barang" style="cursor:pointer;">Deskripsi Barang <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-center py-2 align-middle sortable-header" data-column="qty" style="cursor:pointer;">Qty <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-center py-2 align-middle">UoM</th>
                                    <th class="text-end py-2 align-middle sortable-header" data-column="harga_satuan" style="cursor:pointer;">Harga Satuan <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-end py-2 align-middle sortable-header" data-column="diskon" style="cursor:pointer;">Diskon <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-center py-2 align-middle">PPN</th>
                                    <th class="text-end py-2 align-middle sortable-header" data-column="nominal_ppn" style="cursor:pointer;">Nominal PPN <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-end py-2 align-middle">PPH</th>
                                    <th class="text-end py-2 align-middle">Add Cost</th>
                                    <th class="text-end py-2 align-middle sortable-header" data-column="dpp" style="cursor:pointer;">DPP <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-end py-2 align-middle sortable-header" data-column="amount" style="cursor:pointer;">Nilai PO <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-end py-2 align-middle">Uang Muka</th>
                                    <th class="text-end py-2 align-middle">Sisa PO</th>
                                    <th class="text-center py-2 align-middle">Status Bayar</th>
                                    <th class="py-2 align-middle">No Faktur Pengajuan</th>
                                    <th class="text-end py-2 align-middle">Pengajuan Bayar</th>
                                    <th class="text-end py-2 align-middle">Dibayar FAT</th>
                                    <th class="text-end py-2 align-middle">Sisa Hutang FAT</th>
                                    <th class="text-center py-2 align-middle">Status FAT</th>
                                    <th class="text-end py-2 align-middle sortable-header" data-column="amount" style="cursor:pointer;">Amount <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Data will be populated here by Javascript -->
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Info -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3 px-3 py-3 border-top">
                        <div class="d-flex align-items-center gap-3 mb-3 mb-md-0">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted small fw-medium">Tampilkan:</span>
                                <select id="perPageSelect" class="form-select form-select-sm shadow-sm" style="width: 70px;" onchange="fetchPurchaseOrders(1)">
                                    <option value="20" selected>20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="text-muted small fw-medium" id="paginationInfo">
                                Menampilkan 0 dari 0 Item
                            </div>
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0 shadow-sm" id="paginationContainer">
                                <!-- Generated by JS -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail PO -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-detail-content">
                <div class="modal-header modal-detail-header position-relative">
                    <h5 class="modal-title fw-bold" id="detailModalLabel"><i class="fa-solid fa-file-invoice me-2"></i> Detail Pembelian (PO)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="modalDetailBody">
                    <!-- Detail content populated by JS -->
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Fetch Data, Search, & Pagination -->
    <script>
        let currentKeyword = '';
        let currentPage = 1;
        let sortCol = 'tgl_pembelian';
        let sortDir = 'desc';

        document.addEventListener('DOMContentLoaded', function() {
            fetchPurchaseOrders(1);

            let searchTimeout;
            document.getElementById('searchInput').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    currentKeyword = e.target.value;
                    fetchPurchaseOrders(1);
                } else {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        currentKeyword = e.target.value;
                        fetchPurchaseOrders(1);
                    }, 800);
                }
            });

            document.getElementById('filterDateFrom').addEventListener('change', () => fetchPurchaseOrders(1));
            document.getElementById('filterDateTo').addEventListener('change', () => fetchPurchaseOrders(1));

            // Column sorting listener
            document.querySelectorAll('.sortable-header').forEach(th => {
                th.addEventListener('click', function() {
                    const column = this.dataset.column;
                    
                    if (sortCol === column) {
                        sortDir = sortDir === 'asc' ? 'desc' : 'asc';
                    } else {
                        sortCol = column;
                        sortDir = 'asc';
                    }
                    
                    document.querySelectorAll('.sortable-header i').forEach(icon => {
                        icon.className = 'fa-solid fa-sort text-muted ms-1';
                    });
                    const currentIcon = this.querySelector('i');
                    if (sortDir === 'asc') {
                        currentIcon.className = 'fa-solid fa-sort-up text-primary ms-1';
                    } else {
                        currentIcon.className = 'fa-solid fa-sort-down text-primary ms-1';
                    }

                    fetchPurchaseOrders(1);
                });
            });
        });

        function resetFilters() {
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            document.getElementById('searchInput').value = '';
            currentKeyword = '';
            
            // Reset sorting to default
            sortCol = 'tgl_pembelian';
            sortDir = 'desc';
            document.querySelectorAll('.sortable-header i').forEach(icon => {
                icon.className = 'fa-solid fa-sort text-muted ms-1';
            });
            const defaultTh = document.querySelector('.sortable-header[data-column="tgl_pembelian"] i');
            if(defaultTh) defaultTh.className = 'fa-solid fa-sort-down text-primary ms-1';
            
            fetchPurchaseOrders(1);
        }

        function resetDateFilter() {
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            fetchPurchaseOrders(1);
        }

        function fetchPurchaseOrders(page = 1) {
            currentPage = page;
            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="37" class="text-center py-5">
                        <div class="loading-spinner mb-3" style="color: var(--po-primary);"></div>
                        <p class="mb-0 fw-semibold text-muted">Mengambil data dari server...</p>
                    </td>
                </tr>
            `;

            currentKeyword = document.getElementById('searchInput').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;
            const perPage = document.getElementById('perPageSelect').value;

            let queryParams = new URLSearchParams({
                page: page,
                per_page: perPage,
                q: currentKeyword,
                sort_col: sortCol,
                sort_dir: sortDir,
                date_from: dateFrom,
                date_to: dateTo
            });

            fetch(`{{ route("api.integration.search_po") }}?${queryParams.toString()}`)
                .then(response => response.json())
                .then(result => {
                    renderTable(result);
                    renderPagination(result);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="37" class="text-center py-5 text-danger">
                                <i class="fa-solid fa-triangle-exclamation fs-1 mb-3"></i>
                                <p class="mb-0 fw-semibold">Terjadi kesalahan saat memuat data dari database lokal.</p>
                            </td>
                        </tr>
                    `;
                });
        }

        function changePage(direction) {
            fetchPurchaseOrders(currentPage + direction);
        }
        
        function goToPage(page) {
            if(page !== currentPage) {
                fetchPurchaseOrders(page);
            }
        }

        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka || 0);
        };

        const formatTgl = (tgl) => {
            if(!tgl) return '-';
            return new Date(tgl).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        };

        function showDetail(no_po) {
            const modalBody = document.getElementById('modalDetailBody');
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="loading-spinner mb-3" style="color: var(--po-primary);"></div>
                    <p class="mb-0 fw-semibold text-muted">Memuat detail Purchase Order...</p>
                </div>
            `;
            
            document.querySelector('#detailModal .modal-dialog').classList.add('modal-xl');
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();

            fetch(`{{ url('/api/integration/detail-po') }}/${encodeURIComponent(no_po)}`)
                .then(res => res.json())
                .then(po => {
                    if(po.error) throw new Error(po.error);
                    
                    let itemsHtml = '';
                    po.items.forEach((item, i) => {
                        itemsHtml += `
                            <tr>
                                <td class="text-center">${i + 1}</td>
                                <td class="fw-medium">${item.no_barang || '-'}</td>
                                <td>${item.deskripsi_barang || '-'}</td>
                                <td>${item.category_produk || '-'}</td>
                                <td class="text-center">${item.qty || 0}</td>
                                <td class="text-center">${item.qty_received || item.qty_diterima || 0}</td>
                                <td class="text-center">${item.uom || '-'}</td>
                                <td class="text-end">${formatRupiah(item.price || item.harga_satuan)}</td>
                                <td class="text-end fw-bold" style="color: var(--po-primary);">${formatRupiah(item.amount)}</td>
                                <td><span class="badge bg-secondary">${item.status_pembayaran || item.status_bayar || '-'}</span></td>
                            </tr>
                        `;
                    });

                    let html = `
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3" style="color: var(--po-primary);"><i class="fa-solid fa-file-lines me-2"></i>Informasi PO</h6>
                                <div class="detail-row"><div class="detail-label">No PO</div><div class="detail-value"><span class="badge bg-opacity-10 border px-2 py-1" style="color:var(--po-primary); background-color:rgba(234,88,12,0.1); border-color:var(--po-primary);">${po.no_po || '-'}</span></div></div>
                                <div class="detail-row"><div class="detail-label">Tgl PO</div><div class="detail-value">${formatTgl(po.tgl_po)}</div></div>
                                <div class="detail-row"><div class="detail-label">Pemasok</div><div class="detail-value" style="color:var(--po-primary);">${po.pemasok || '-'}</div></div>
                                <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value">${po.status || '-'}</div></div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-success mb-3"><i class="fa-solid fa-truck me-2"></i>Penerimaan & Nilai</h6>
                                <div class="detail-row"><div class="detail-label">Dikirim Ke</div><div class="detail-value">${po.shipto || '-'}</div></div>
                                <div class="detail-row"><div class="detail-label">Tgl Estimasi</div><div class="detail-value">${formatTgl(po.est_kirim)}</div></div>
                                <div class="detail-row"><div class="detail-label">Total Amount</div><div class="detail-value text-success fs-5">${formatRupiah(po.total_amount)}</div></div>
                            </div>
                            <div class="col-12 mt-3">
                                <h6 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-box me-2"></i>Tabel Barang (${po.items.length} Item)</h6>
                                <div class="table-responsive rounded-3 border">
                                    <table class="table table-sm table-hover table-striped mb-0 text-nowrap" style="font-size: 0.85rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center py-2">No</th>
                                                <th class="py-2">No. Barang</th>
                                                <th class="py-2">Deskripsi Barang</th>
                                                <th class="py-2">Category</th>
                                                <th class="text-center py-2">Qty Order</th>
                                                <th class="text-center py-2">Qty Diterima</th>
                                                <th class="text-center py-2">UoM</th>
                                                <th class="text-end py-2">Harga Satuan</th>
                                                <th class="text-end py-2">Subtotal</th>
                                                <th class="py-2">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${itemsHtml}
                                        </tbody>
                                        <tfoot class="table-light fw-bold">
                                            <tr>
                                                <td colspan="8" class="text-end py-2">TOTAL KESELURUHAN:</td>
                                                <td class="text-end text-success py-2">${formatRupiah(po.total_amount)}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;
                    modalBody.innerHTML = html;
                })
                .catch(err => {
                    modalBody.innerHTML = `<div class="alert alert-danger text-center">Gagal memuat detail PO.</div>`;
                });
        }
        
        function renderPagination(result) {
            const paginationContainer = document.getElementById('paginationContainer');
            
            let current = result.current_page;
            let last = result.last_page;
            
            let html = '';
            
            // Prev button
            html += `
                <li class="page-item ${current <= 1 ? 'disabled' : ''}">
                    <button class="page-link shadow-none" onclick="goToPage(${current - 1})" ${current <= 1 ? 'disabled' : ''}>
                        <i class="fa-solid fa-chevron-left small"></i>
                    </button>
                </li>
            `;
            
            // Calculate range (show max 5 page numbers)
            let start = Math.max(1, current - 2);
            let end = Math.min(last, start + 4);
            if (end - start < 4) {
                start = Math.max(1, end - 4);
            }
            
            if (start > 1) {
                html += `<li class="page-item"><button class="page-link shadow-none" onclick="goToPage(1)">1</button></li>`;
                if (start > 2) {
                    html += `<li class="page-item disabled"><span class="page-link shadow-none">...</span></li>`;
                }
            }
            
            for (let i = start; i <= end; i++) {
                html += `
                    <li class="page-item ${i === current ? 'active' : ''}">
                        <button class="page-link shadow-none" onclick="goToPage(${i})">${i}</button>
                    </li>
                `;
            }
            
            if (end < last) {
                if (end < last - 1) {
                    html += `<li class="page-item disabled"><span class="page-link shadow-none">...</span></li>`;
                }
                html += `<li class="page-item"><button class="page-link shadow-none" onclick="goToPage(${last})">${last}</button></li>`;
            }
            
            // Next button
            html += `
                <li class="page-item ${current >= last ? 'disabled' : ''}">
                    <button class="page-link shadow-none" onclick="goToPage(${current + 1})" ${current >= last ? 'disabled' : ''}>
                        <i class="fa-solid fa-chevron-right small"></i>
                    </button>
                </li>
            `;
            
            paginationContainer.innerHTML = html;
        }

        function renderTable(result) {
            const tableBody = document.getElementById('tableBody');
            const paginationInfo = document.getElementById('paginationInfo');
            
            const dataList = result.data || [];
            
            if(!dataList || dataList.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="37" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open fs-1 mb-3 text-light"></i>
                            <p class="mb-0 fw-semibold">Data tidak ditemukan.</p>
                        </td>
                    </tr>
                `;
                paginationInfo.innerText = "Menampilkan 0 dari 0 Item";
                document.getElementById('paginationContainer').innerHTML = '';
                return;
            }

            let html = '';
            dataList.forEach((item, index) => {
                const globalIndex = result.from + index;
                
                let statusBadge = 'bg-secondary';
                const status = item.status_pembayaran || item.status_bayar || 'Draft';
                if(status.toLowerCase().includes('menunggu')) statusBadge = 'bg-warning text-dark';
                if(status.toLowerCase().includes('selesai') || status.toLowerCase().includes('lunas') || status.toLowerCase().includes('terima')) statusBadge = 'bg-success';

                html += `
                    <tr style="cursor: pointer;" onclick="showDetail('${item.no_pembelian || item.no_po || '-'}')" class="hover-bg-light" title="Klik baris untuk melihat detail PO">
                        <td class="text-center align-middle sticky-col-1">${globalIndex}</td>
                        <td class="align-middle sticky-col-2"><span class="badge bg-light text-dark border fw-bold px-2 py-1">${item.no_pembelian || item.no_po || '-'}</span></td>
                        <td class="align-middle text-muted small">${formatTgl(item.tgl_pembelian || item.tgl_po)}</td>
                        <td class="align-middle text-muted small">${formatTgl(item.tgl_ekspetasi)}</td>
                        <td class="align-middle text-center">${item.top || '-'}</td>
                        <td class="align-middle text-center">${item.sisa_hari || '-'}</td>
                        <td class="align-middle">${item.no_permintaan || '-'}</td>
                        <td class="align-middle">${formatTgl(item.tgl_permintaan)}</td>
                        <td class="align-middle">${formatTgl(item.tgl_target)}</td>
                        <td class="align-middle">${item.so_no || '-'}</td>
                        <td class="align-middle">${item.no_penerimaan || '-'}</td>
                        <td class="align-middle">${formatTgl(item.tgl_penerimaan_barang || item.tgl_penerimaan)}</td>
                        <td class="align-middle">${item.ekspetasi_vs_pb || '-'}</td>
                        <td class="align-middle fw-medium">${item.no_pemasok || '-'}</td>
                        <td class="align-middle fw-medium text-truncate" style="color:var(--po-primary); max-width: 250px;" title="${item.nama_pemasok || ''}"><i class="fa-regular fa-building me-1 opacity-50"></i> ${item.nama_pemasok || '-'}</td>
                        <td class="align-middle">${item.purchaser || '-'}</td>
                        <td class="align-middle fw-medium text-truncate" style="max-width: 200px;">${item.no_barang || '-'}</td>
                        <td class="align-middle text-truncate" style="max-width: 250px;" title="${item.deskripsi_barang || ''}">${item.deskripsi_barang || '-'}</td>
                        <td class="align-middle text-center"><span class="badge bg-opacity-10 border rounded-pill px-2 py-1" style="color:var(--po-primary); background-color:rgba(234, 88, 12, 0.1); border-color:var(--po-primary);">${item.qty || 0}</span></td>
                        <td class="align-middle text-center">${item.uom || '-'}</td>
                        <td class="align-middle text-end">${formatRupiah(item.price || item.harga_satuan)}</td>
                        <td class="align-middle text-end">${formatRupiah(item.diskon)}</td>
                        <td class="align-middle text-center">${item.ppn || '-'}</td>
                        <td class="align-middle text-end">${formatRupiah(item.ppn_amount || item.nominal_ppn)}</td>
                        <td class="align-middle text-end">${item.pph || '-'}</td>
                        <td class="align-middle text-end">${item.add_cost || '-'}</td>
                        <td class="align-middle text-end">${formatRupiah(item.dpp)}</td>
                        <td class="align-middle text-end fw-bold" style="color:var(--po-primary);">${formatRupiah(item.amount)}</td>
                        <td class="align-middle text-end">${item.uang_muka || '-'}</td>
                        <td class="align-middle text-end">${item.sisa_po || '-'}</td>
                        <td class="align-middle text-center"><span class="badge ${statusBadge} px-2 py-1 rounded-pill">${status}</span></td>
                        <td class="align-middle">${item.no_faktur_pengajuan || '-'}</td>
                        <td class="align-middle text-end">${item.pengajuan_bayar || '-'}</td>
                        <td class="align-middle text-end">${item.dibayar_fat || '-'}</td>
                        <td class="align-middle text-end">${item.sisa_hutang_fat || '-'}</td>
                        <td class="align-middle text-center">${item.status_fat || '-'}</td>
                        <td class="align-middle text-end fw-bold">${formatRupiah(item.amount)}</td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;
            paginationInfo.innerText = `Menampilkan ${result.from} - ${result.to} dari total ${result.total} Item`;
        }
    </script>
</x-app-layout>
