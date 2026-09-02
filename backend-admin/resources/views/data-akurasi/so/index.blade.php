<x-app-layout>
    <!-- Custom CSS untuk Header Banner & Table (Premium Design) -->
    <style>
        /* =========================================================
           SALES ORDER — MODERN / PROPORTIONAL UI
           Fokus perubahan: spacing, hierarchy, table density,
           search, card, pagination, modal, light/dark consistency.
        ========================================================== */
        :root {
            --so-bg: #f8fafc;
            --so-card: #ffffff;
            --so-border: #e2e8f0;
            --so-border-soft: #eef2f7;
            --so-text: #0f172a;
            --so-muted: #64748b;
            --so-subtle: #94a3b8;
            --so-primary: #f97316;
            --so-primary-soft: rgba(249, 115, 22, .10);
            --so-primary-border: rgba(249, 115, 22, .20);
            --so-success: #059669;
            --so-danger: #dc2626;
            --so-warning: #d97706;
            --so-radius: 14px;
            --so-shadow: 0 6px 22px rgba(15, 23, 42, .055);
        }

        html[data-bs-theme="dark"] {
            --so-bg: #0b0d10;
            --so-card: #111419;
            --so-border: rgba(255,255,255,.09);
            --so-border-soft: rgba(255,255,255,.06);
            --so-text: #f8fafc;
            --so-muted: #94a3b8;
            --so-subtle: #64748b;
            --so-primary: #fb923c;
            --so-primary-soft: rgba(251, 146, 60, .10);
            --so-primary-border: rgba(251, 146, 60, .22);
            --so-shadow: 0 12px 28px rgba(0,0,0,.25);
        }

        /* -------------------------
           HEADER / HERO
        -------------------------- */
        .dashboard-banner {
            position: relative;
            overflow: hidden;
            border-radius: var(--so-radius);
            padding: 16px 18px;
            border: 1px solid var(--so-border);
            background: var(--so-card);
            box-shadow: var(--so-shadow);
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        html:not([data-bs-theme="dark"]) .dashboard-banner {
            background:
                radial-gradient(circle at 92% 0%, rgba(249, 115, 22, .09), transparent 30%),
                linear-gradient(135deg, #ffffff 0%, #fffcf5 65%, #fff7ed 100%);
        }

        html[data-bs-theme="dark"] .dashboard-banner {
            background:
                radial-gradient(circle at 92% 0%, rgba(251, 146, 60, .10), transparent 30%),
                var(--so-card);
        }

        .banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 9px;
            margin-bottom: 5px;
            border: 1px solid var(--so-primary-border);
            border-radius: 999px;
            background: var(--so-primary-soft);
            color: var(--so-primary);
            font-size: 10px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .banner-title {
            margin: 0;
            color: var(--so-text);
            font-size: clamp(1.15rem, 1.6vw, 1.5rem);
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -.025em;
        }

        .dashboard-banner .text-muted {
            color: var(--so-muted) !important;
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
            color: var(--so-subtle);
            font-size: 13px;
            pointer-events: none;
        }

        .search-input {
            min-height: 42px;
            padding: 8px 14px 8px 40px;
            border: 1px solid var(--so-border);
            border-radius: 10px;
            background: rgba(255,255,255,.88);
            color: var(--so-text);
            font-size: 13px;
            box-shadow: none !important;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .search-input::placeholder {
            color: #94a3b8;
        }

        .search-input:focus {
            border-color: rgba(249, 115, 22, .65);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, .10) !important;
        }

        html[data-bs-theme="dark"] .search-input {
            background: rgba(255,255,255,.035);
            border-color: var(--so-border);
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
            border: 1px solid var(--so-border) !important;
            border-radius: var(--so-radius);
            background: var(--so-card) !important;
            color: var(--so-text);
            box-shadow: var(--so-shadow) !important;
        }

        .data-card .card-header {
            min-height: 58px;
            background: transparent !important;
            border-bottom: 1px solid var(--so-border-soft) !important;
        }

        .data-card .card-header h6 {
            color: var(--so-text);
            font-size: 13px;
            letter-spacing: -.01em;
        }

        .data-card .btn-refresh {
            border: 1px solid var(--so-primary-border);
            border-radius: 9px !important;
            background: var(--so-primary-soft);
            color: var(--so-primary) !important;
            box-shadow: none !important;
            font-size: 11px;
            font-weight: 700 !important;
            transition: background .18s ease, border-color .18s ease, transform .18s ease;
        }

        .data-card .btn-refresh:hover {
            background: rgba(249, 115, 22, .16);
            border-color: rgba(249, 115, 22, .35);
        }

        /* -------------------------
           TABLE
        -------------------------- */
        .table-premium {
            --bs-table-bg: transparent;
            margin-bottom: 0;
            color: var(--so-text);
        }

        .table-premium thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            padding: 10px 9px !important;
            border-top: 0;
            border-bottom: 1px solid var(--so-border) !important;
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
            border-color: var(--so-border-soft);
            color: #334155;
            font-size: 12px;
            line-height: 1.35;
            vertical-align: middle;
        }

        .table-premium tbody tr {
            transition: background .15s ease;
        }

        .table-premium tbody tr:hover > * {
            --bs-table-accent-bg: #f8fbff;
            background-color: #f8fbff !important;
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
            border-color: var(--so-border) !important;
        }

        html[data-bs-theme="dark"] .table-premium tbody td {
            color: #cbd5e1;
            border-color: var(--so-border-soft);
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
            color: var(--so-muted) !important;
            font-size: 11px;
        }

        #btnPrev,
        #btnNext {
            min-height: 34px;
            padding: 6px 12px !important;
            border-color: var(--so-border);
            border-radius: 8px !important;
            color: var(--so-muted);
            background: var(--so-card);
            box-shadow: none !important;
            font-size: 11px;
            font-weight: 700;
        }

        #btnPrev:not(:disabled):hover,
        #btnNext:not(:disabled):hover {
            color: var(--so-primary);
            border-color: var(--so-primary-border);
            background: var(--so-primary-soft);
        }

        #pageIndicator {
            min-width: 34px;
            padding: 7px 10px !important;
            border: 1px solid var(--so-primary-border);
            border-radius: 8px !important;
            background: var(--so-primary-soft) !important;
            color: var(--so-primary);
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
            border: 3px solid rgba(14,165,233,.18);
            border-right-color: var(--so-primary);
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
        }

        /* -------------------------
           DETAIL MODAL
        -------------------------- */
        .modal-detail-content {
            overflow: hidden;
            border: 1px solid var(--so-border);
            border-radius: 16px;
            background: var(--so-card);
            box-shadow: 0 22px 55px rgba(15,23,42,.18);
        }

        .modal-detail-header {
            padding: 16px 18px;
            border: 0;
            border-bottom: 1px solid rgba(255,255,255,.10);
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
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
            border-bottom: 1px dashed var(--so-border);
        }

        .detail-label {
            width: 34%;
            color: var(--so-muted);
            font-size: 10px;
            line-height: 1.35;
            font-weight: 800;
            letter-spacing: .035em;
            text-transform: uppercase;
        }

        .detail-value {
            width: 66%;
            min-width: 0;
            color: var(--so-text);
            font-size: 12px;
            line-height: 1.45;
            font-weight: 650;
            overflow-wrap: anywhere;
        }

        html[data-bs-theme="dark"] .modal-detail-content {
            background: var(--so-card);
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
            background-color: #f1f5f9;
        }
    </style>

    <!-- 1. Header Banner -->
    <div class="row mb-3" style="position: sticky; top: .75rem; z-index: 1020;">
        <div class="col-12">
            <div class="dashboard-banner d-flex justify-content-between align-items-center flex-wrap gap-3">
                <!-- Decorative Background Element -->
                <div style="position: absolute; top: -30px; right: -20px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(14, 165, 233, 0.18) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
                
                <div style="position: relative; z-index: 1;">
                    <div class="banner-badge">
                        <i class="fa-solid fa-bag-shopping"></i>
                        AQPA SALES
                    </div>
                    <h1 class="banner-title mb-2">Data Penjualan (SO)</h1>
                    <p class="text-muted small mb-0 fw-medium">Sinkronisasi Realtime API dari Akurasi</p>
                </div>

                </div>
            </div>
        </div>
    </div>

    <!-- 2. Tabel Data -->
    <div class="row mt-3 g-3">
        <div class="col-12">
            <div class="card data-card h-100">
                <div class="card-header bg-transparent px-3 py-3 d-flex flex-column flex-xxl-row justify-content-between align-items-xxl-center gap-3">
                    <h6 class="fw-bold mb-0 text-nowrap"><i class="fa-solid fa-list text-warning me-2"></i>Daftar Transaksi Penjualan</h6>
                    
                    <div class="d-flex flex-wrap align-items-center gap-2 justify-content-xxl-end w-100">
                        <select id="filterStatusHold" class="form-select form-select-sm" style="width: auto; min-width: 140px;">
                            <option value="">Semua Status Hold</option>
                            <option value="1">Hold</option>
                            <option value="0">Tidak Hold</option>
                        </select>
                        <select id="filterStatus" class="form-select form-select-sm" style="width: auto; min-width: 140px;">
                            <option value="">Semua Status</option>
                            <option value="Draft">Draft</option>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                        <div class="d-flex align-items-center gap-1 bg-white border rounded px-2">
                            <input type="date" id="filterDateFrom" class="form-control form-control-sm border-0 px-1" style="width: auto;">
                            <span class="text-muted small"><i class="fa-solid fa-arrow-right"></i></span>
                            <input type="date" id="filterDateTo" class="form-control form-control-sm border-0 px-1" style="width: auto;">
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 text-decoration-none" onclick="resetDateFilter()" title="Reset Tanggal">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                        <div class="input-group input-group-sm flex-grow-1" style="max-width: 350px;">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Cari no SO, pelanggan, ...">
                            <button class="btn btn-primary" type="button" onclick="fetchSalesOrders(1)"><i class="fa-solid fa-search"></i></button>
                        </div>
                        <button class="btn btn-sm btn-light border fw-semibold" onclick="resetFilters()">
                            <i class="fa-solid fa-rotate-left me-1"></i> Reset
                        </button>
                        <button class="btn btn-sm btn-refresh px-3" onclick="fetchSalesOrders(1)">
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
                                    <th class="py-2 align-middle sortable-header sticky-col-2" data-column="no_so" style="cursor:pointer; width: 140px; min-width: 140px;">No. SO <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="tgl_so" style="cursor:pointer;">Tgl SO <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="tgl_estimasi" style="cursor:pointer;">Est. Kirim <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="no_pelanggan" style="cursor:pointer;">No Pelanggan <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="nama_pelanggan" style="cursor:pointer;">Nama Pelanggan <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="no_po_customer" style="cursor:pointer;">No. PO Cust <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="salesman" style="cursor:pointer;">Salesman <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="no_barang" style="cursor:pointer;">No. Barang <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="deskripsi_barang" style="cursor:pointer;">Deskripsi Barang <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="category_produk" style="cursor:pointer;">Category <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-center py-2 align-middle sortable-header" data-column="qty" style="cursor:pointer;">Qty Order <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-center py-2 align-middle sortable-header" data-column="qty_shipped" style="cursor:pointer;">Qty Shipped <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-center py-2 align-middle sortable-header" data-column="sisa_kirim" style="cursor:pointer;">Sisa Kirim <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-center py-2 align-middle sortable-header" data-column="stok_tersedia" style="cursor:pointer;">Stok Tersedia <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-center py-2 align-middle">UoM</th>
                                    <th class="text-end py-2 align-middle sortable-header" data-column="unit_price" style="cursor:pointer;">Harga Satuan <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-end py-2 align-middle sortable-header" data-column="discount_amount" style="cursor:pointer;">Diskon <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-end py-2 align-middle sortable-header" data-column="ppn_amount" style="cursor:pointer;">PPN <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-end py-2 align-middle sortable-header" data-column="dpp" style="cursor:pointer;">DPP <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="no_pengiriman" style="cursor:pointer;">No. Pengiriman <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="py-2 align-middle sortable-header" data-column="tgl_kirim" style="cursor:pointer;">Tgl Kirim <i class="fa-solid fa-sort text-muted ms-1"></i></th>
                                    <th class="text-center py-2 align-middle">Status</th>
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
                                <select id="perPageSelect" class="form-select form-select-sm shadow-sm" style="width: 70px;" onchange="fetchSalesOrders(1)">
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

    <!-- Modal Detail SO -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-detail-content">
                <div class="modal-header modal-detail-header position-relative">
                    <h5 class="modal-title fw-bold" id="detailModalLabel"><i class="fa-solid fa-file-invoice me-2"></i> Detail Penjualan (SO)</h5>
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
        let sortCol = 'tgl_so';
        let sortDir = 'desc';

        document.addEventListener('DOMContentLoaded', function() {
            fetchSalesOrders(1);

            // Trigger background sync silently
            fetch('{{ route("api.integration.trigger_sync_so") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                console.log("Background Sync SO:", data.message);
                // Optionally refresh table if user is still on page 1
                if (currentPage === 1) fetchSalesOrders(1);
            })
            .catch(err => console.log("Background sync skipped/error:", err));

            let searchTimeout;
            document.getElementById('searchInput').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    currentKeyword = e.target.value;
                    fetchSalesOrders(1);
                } else {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        currentKeyword = e.target.value;
                        fetchSalesOrders(1);
                    }, 800); // debounce 800ms
                }
            });

            // Filter listeners (auto fetch on change)
            document.getElementById('filterStatusHold').addEventListener('change', () => fetchSalesOrders(1));
            document.getElementById('filterStatus').addEventListener('change', () => fetchSalesOrders(1));
            document.getElementById('filterDateFrom').addEventListener('change', () => fetchSalesOrders(1));
            document.getElementById('filterDateTo').addEventListener('change', () => fetchSalesOrders(1));

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

                    fetchSalesOrders(1);
                });
            });
        });

        function resetFilters() {
            document.getElementById('filterStatusHold').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            document.getElementById('searchInput').value = '';
            currentKeyword = '';
            
            // Reset sorting to default
            sortCol = 'tgl_so';
            sortDir = 'desc';
            document.querySelectorAll('.sortable-header i').forEach(icon => {
                icon.className = 'fa-solid fa-sort text-muted ms-1';
            });
            const defaultTh = document.querySelector('.sortable-header[data-column="tgl_so"] i');
            if(defaultTh) defaultTh.className = 'fa-solid fa-sort-down text-primary ms-1';
            
            fetchSalesOrders(1);
        }

        function resetDateFilter() {
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            fetchSalesOrders(1);
        }

        function fetchSalesOrders(page = 1) {
            currentPage = page;
            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="23" class="text-center py-5">
                        <div class="loading-spinner text-info mb-3"></div>
                        <p class="mb-0 fw-semibold text-muted">Mengambil data dari server...</p>
                    </td>
                </tr>
            `;

            currentKeyword = document.getElementById('searchInput').value;
            const statusHold = document.getElementById('filterStatusHold').value;
            const status = document.getElementById('filterStatus').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;
            const perPage = document.getElementById('perPageSelect').value;

            let queryParams = new URLSearchParams({
                page: page,
                per_page: perPage,
                q: currentKeyword,
                sort_col: sortCol,
                sort_dir: sortDir,
                status_hold: statusHold,
                status: status,
                date_from: dateFrom,
                date_to: dateTo
            });

            fetch(`{{ route("api.integration.search_so") }}?${queryParams.toString()}`)
                .then(response => response.json())
                .then(result => {
                    renderTable(result);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="23" class="text-center py-5 text-danger">
                                <i class="fa-solid fa-triangle-exclamation fs-1 mb-3"></i>
                                <p class="mb-0 fw-semibold">Terjadi kesalahan saat memuat data dari database lokal.</p>
                            </td>
                        </tr>
                    `;
                });
        }

        function changePage(direction) {
            fetchSalesOrders(currentPage + direction);
        }

        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka || 0);
        };

        const formatTgl = (tgl) => {
            if(!tgl) return '-';
            return new Date(tgl).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        };

        function showDetail(no_so) {
            const modalBody = document.getElementById('modalDetailBody');
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="loading-spinner text-info mb-3"></div>
                    <p class="mb-0 fw-semibold text-muted">Memuat detail Sales Order...</p>
                </div>
            `;
            
            document.querySelector('#detailModal .modal-dialog').classList.add('modal-xl');
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();

            fetch(`{{ url('/api/integration/detail-so') }}/${encodeURIComponent(no_so)}`)
                .then(res => res.json())
                .then(so => {
                    if(so.error) throw new Error(so.error);
                    
                    let itemsHtml = '';
                    so.items.forEach((item, i) => {
                        itemsHtml += `
                            <tr>
                                <td class="text-center">${i + 1}</td>
                                <td class="fw-medium">${item.no_barang || '-'}</td>
                                <td>${item.deskripsi_barang || '-'}</td>
                                <td>${item.category_produk || '-'}</td>
                                <td class="text-center">${item.qty || 0}</td>
                                <td class="text-center">${item.qty_shipped || 0}</td>
                                <td class="text-center"><span class="text-danger fw-bold">${item.sisa_kirim || 0}</span></td>
                                <td class="text-center">${item.stok_tersedia || 0}</td>
                                <td class="text-center">${item.uom || '-'}</td>
                                <td class="text-end">${formatRupiah(item.unit_price)}</td>
                                <td class="text-end">${formatRupiah(item.discount_amount)}</td>
                                <td class="text-end">${item.ppn_rate ? item.ppn_rate + '%' : '0%'}</td>
                                <td class="text-end">${formatRupiah(item.subtotal)}</td>
                                <td class="text-end fw-bold text-primary">${formatRupiah(item.amount)}</td>
                                <td>${item.no_pengiriman || '-'}</td>
                                <td>${formatTgl(item.tgl_pengiriman)}</td>
                                <td><span class="badge bg-secondary">${item.status || '-'}</span></td>
                            </tr>
                        `;
                    });

                    let html = `
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-info mb-3"><i class="fa-solid fa-file-lines me-2"></i>Informasi SO</h6>
                                <div class="detail-row"><div class="detail-label">No SO</div><div class="detail-value"><span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">${so.no_so || '-'}</span></div></div>
                                <div class="detail-row"><div class="detail-label">Tgl SO</div><div class="detail-value">${formatTgl(so.tgl_so)}</div></div>
                                <div class="detail-row"><div class="detail-label">Pelanggan</div><div class="detail-value text-primary">${so.pelanggan || '-'}</div></div>
                                <div class="detail-row"><div class="detail-label">Salesman</div><div class="detail-value">${so.items[0]?.nama_salesman || '-'}</div></div>
                                <div class="detail-row"><div class="detail-label">No PO Cust</div><div class="detail-value">${so.items[0]?.no_po_customer || '-'}</div></div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-success mb-3"><i class="fa-solid fa-truck me-2"></i>Pengiriman & Nilai</h6>
                                <div class="detail-row"><div class="detail-label">Ship To</div><div class="detail-value">${so.shipto || '-'}</div></div>
                                <div class="detail-row"><div class="detail-label">Tgl Estimasi</div><div class="detail-value">${formatTgl(so.est_kirim)}</div></div>
                                <div class="detail-row"><div class="detail-label">Status SO</div><div class="detail-value"><span class="badge bg-info text-dark">${so.status || '-'}</span></div></div>
                                <div class="detail-row"><div class="detail-label">Status Hold</div><div class="detail-value"><span class="badge ${so.items[0]?.is_held ? 'bg-danger' : 'bg-success'}">${so.items[0]?.is_held ? 'Hold' : 'Tidak Hold'}</span></div></div>
                                <div class="detail-row"><div class="detail-label">Catatan Hold</div><div class="detail-value text-danger">${so.items[0]?.hold_note || '-'}</div></div>
                            </div>
                            <div class="col-12 mt-3">
                                <h6 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-box me-2"></i>Tabel Barang (${so.items.length} Item)</h6>
                                <div class="table-responsive rounded-3 border">
                                    <table class="table table-sm table-hover table-striped mb-0 text-nowrap" style="font-size: 0.85rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center py-2">No</th>
                                                <th class="py-2">No. Barang</th>
                                                <th class="py-2">Deskripsi Barang</th>
                                                <th class="py-2">Category</th>
                                                <th class="text-center py-2">Qty Order</th>
                                                <th class="text-center py-2">Qty Shipped</th>
                                                <th class="text-center py-2">Sisa Kirim</th>
                                                <th class="text-center py-2">Stok Tersedia</th>
                                                <th class="text-center py-2">UoM</th>
                                                <th class="text-end py-2">Harga Satuan</th>
                                                <th class="text-end py-2">Diskon</th>
                                                <th class="text-end py-2">PPN</th>
                                                <th class="text-end py-2">DPP</th>
                                                <th class="text-end py-2">Subtotal</th>
                                                <th class="py-2">No Pengiriman</th>
                                                <th class="py-2">Tgl Kirim</th>
                                                <th class="py-2">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${itemsHtml}
                                        </tbody>
                                        <tfoot class="table-light fw-bold">
                                            <tr>
                                                <td colspan="13" class="text-end py-2">TOTAL KESELURUHAN:</td>
                                                <td class="text-end text-success py-2">${formatRupiah(so.total_amount)}</td>
                                                <td colspan="3"></td>
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
                    modalBody.innerHTML = `<div class="alert alert-danger text-center">Gagal memuat detail SO.</div>`;
                });
        }

        function renderTable(result) {
            const tableBody = document.getElementById('tableBody');
            const paginationInfo = document.getElementById('paginationInfo');
            
            const dataList = result.data || [];
            
            if(!dataList || dataList.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="23" class="text-center py-5 text-muted">
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
                const status = item.status || 'Draft';
                if(status.toLowerCase().includes('menunggu')) statusBadge = 'bg-warning text-dark';
                if(status.toLowerCase().includes('selesai') || status.toLowerCase().includes('kirim')) statusBadge = 'bg-success';

                html += `
                    <tr style="cursor: pointer;" onclick="showDetail('${item.no_so}')" class="hover-bg-light" title="Klik baris untuk melihat detail SO">
                        <td class="text-center align-middle sticky-col-1">${globalIndex}</td>
                        <td class="align-middle sticky-col-2"><span class="badge bg-light text-dark border fw-bold px-2 py-1">${item.no_so || '-'}</span></td>
                        <td class="align-middle text-muted small">${formatTgl(item.tgl_so)}</td>
                        <td class="align-middle text-muted small">${formatTgl(item.tgl_estimasi)}</td>
                        <td class="align-middle text-center text-truncate" style="max-width: 350px;">${item.no_pelanggan || '-'}</td>
                        <td class="align-middle fw-medium text-primary text-truncate" style="max-width: 350px;" title="${item.nama_pelanggan || ''}"><i class="fa-regular fa-building me-1 opacity-50"></i> ${item.nama_pelanggan || '-'}</td>
                        <td class="align-middle text-truncate" style="max-width: 350px;" title="${item.no_po_customer || ''}">${item.no_po_customer || '-'}</td>
                        <td class="align-middle text-truncate" style="max-width: 350px;">${item.nama_salesman || '-'}</td>
                        <td class="align-middle fw-medium text-truncate" style="max-width: 350px;">${item.no_barang || '-'}</td>
                        <td class="align-middle text-truncate" style="max-width: 350px;" title="${item.deskripsi_barang || ''}">${item.deskripsi_barang || '-'}</td>
                        <td class="align-middle text-truncate" style="max-width: 350px;" title="${item.category_produk || ''}">${item.category_produk || '-'}</td>
                        <td class="align-middle text-center"><span class="badge bg-info bg-opacity-10 text-info border border-info-subtle rounded-pill px-2 py-1">${item.qty || 0}</span></td>
                        <td class="align-middle text-center">${item.qty_shipped || 0}</td>
                        <td class="align-middle text-center fw-bold text-danger">${item.sisa_kirim || 0}</td>
                        <td class="align-middle text-center">${item.stok_tersedia || 0}</td>
                        <td class="align-middle text-center">${item.uom || '-'}</td>
                        <td class="align-middle text-end">${formatRupiah(item.unit_price)}</td>
                        <td class="align-middle text-end">${formatRupiah(item.discount_amount)}</td>
                        <td class="align-middle text-end">${item.ppn_rate ? item.ppn_rate + '%' : '0%'}</td>
                        <td class="align-middle text-end">${formatRupiah(item.subtotal)}</td>
                        <td class="align-middle text-truncate" style="max-width: 350px;">${item.no_pengiriman || '-'}</td>
                        <td class="align-middle">${formatTgl(item.tgl_pengiriman)}</td>
                        <td class="align-middle text-center"><span class="badge ${statusBadge} px-2 py-1 rounded-pill">${status}</span></td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;
            paginationInfo.innerText = `Menampilkan ${result.from} - ${result.to} dari total ${result.total} Item (Server-Side Pagination)`;
            
            renderPaginationControls(result.current_page, result.last_page);
        }

        function renderPaginationControls(currentPage, lastPage) {
            const container = document.getElementById('paginationContainer');
            if (lastPage <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '';
            
            // Prev button
            html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <button class="page-link" onclick="fetchSalesOrders(${currentPage - 1})"><i class="fa-solid fa-chevron-left"></i></button>
                     </li>`;
            
            let pages = [];
            if (lastPage <= 7) {
                for (let i = 1; i <= lastPage; i++) pages.push(i);
            } else {
                if (currentPage <= 4) {
                    pages = [1, 2, 3, 4, 5, '...', lastPage - 1, lastPage];
                } else if (currentPage >= lastPage - 3) {
                    pages = [1, 2, '...', lastPage - 4, lastPage - 3, lastPage - 2, lastPage - 1, lastPage];
                } else {
                    pages = [1, 2, '...', currentPage - 1, currentPage, currentPage + 1, '...', lastPage - 1, lastPage];
                }
            }

            pages.forEach(p => {
                if (p === '...') {
                    html += `<li class="page-item disabled"><span class="page-link text-muted">...</span></li>`;
                } else {
                    html += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                                <button class="page-link" onclick="fetchSalesOrders(${p})">${p}</button>
                             </li>`;
                }
            });

            // Next button
            html += `<li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
                        <button class="page-link" onclick="fetchSalesOrders(${currentPage + 1})"><i class="fa-solid fa-chevron-right"></i></button>
                     </li>`;

            container.innerHTML = html;
        }
    </script>
</x-app-layout>
