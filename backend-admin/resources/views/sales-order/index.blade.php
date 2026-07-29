<x-app-layout>
    <!-- Custom CSS untuk Header Banner & Table (Premium Design) -->
    <style>
        .dashboard-banner {
            border-radius: 1rem;
            padding: 1rem;
            border: 1px solid rgba(251, 146, 60, 0.2);
            transition: all 0.3s ease;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
            overflow: hidden;
        }
        html:not([data-bs-theme="dark"]) .dashboard-banner {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.85) 0%, rgba(255, 247, 237, 0.85) 60%, rgba(255, 237, 213, 0.85) 100%);
            box-shadow: 0 4px 20px -2px rgba(251, 146, 60, 0.15);
        }
        html[data-bs-theme="dark"] .dashboard-banner {
            background: linear-gradient(135deg, rgba(13, 15, 18, 0.75) 0%, rgba(23, 25, 30, 0.75) 100%);
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            margin-bottom: 0.35rem;
        }
        html:not([data-bs-theme="dark"]) .banner-badge {
            background-color: rgba(251, 146, 60, 0.15);
            color: #ea580c;
            border: 1px solid rgba(234, 88, 12, 0.2);
        }
        html[data-bs-theme="dark"] .banner-badge {
            background-color: rgba(249, 115, 22, 0.2);
            color: #fb923c;
            border: 1px solid rgba(249, 115, 22, 0.3);
        }
        .banner-title {
            font-size: clamp(1.35rem, 2.2vw, 1.9rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            margin: 0;
        }
        html:not([data-bs-theme="dark"]) .banner-title { color: #0f172a; }
        html[data-bs-theme="dark"] .banner-title { color: #f8fafc; }

        .data-card {
            border-radius: 14px;
            transition: all 0.3s ease;
        }
        html[data-bs-theme="dark"] .data-card {
            background-color: #0d0f12 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }
        html:not([data-bs-theme="dark"]) .data-card {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
        }

        .table-premium th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #64748b;
            border-bottom-width: 2px;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        .table-premium td {
            vertical-align: middle;
            padding-top: 1rem;
            padding-bottom: 1rem;
            color: #334155;
        }
        html[data-bs-theme="dark"] .table-premium th { color: #94a3b8; border-color: rgba(255,255,255,0.1); }
        html[data-bs-theme="dark"] .table-premium td { color: #cbd5e1; border-color: rgba(255,255,255,0.1); }

        /* Search Input Premium */
        .search-container {
            position: relative;
            max-width: 350px;
        }
        .search-container i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .search-input {
            padding-left: 2.75rem;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .search-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(234, 88, 12, 0.25);
            border-color: #ea580c;
        }
        
        /* Spinner */
        .loading-spinner {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
        }
    </style>

    <!-- 1. Header Banner -->
    <div class="row mb-3" style="position: sticky; top: 1rem; z-index: 1020;">
        <div class="col-12">
            <div class="dashboard-banner d-flex justify-content-between align-items-center flex-wrap gap-3">
                <!-- Decorative Background Element -->
                <div style="position: absolute; top: -30px; right: -20px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(251, 146, 60, 0.18) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
                
                <div style="position: relative; z-index: 1;">
                    <div class="banner-badge">
                        <i class="fa-solid fa-cart-shopping"></i>
                        AQPA PURCHASING
                    </div>
                    <h1 class="banner-title mb-2">Data Pembelian (Realtime API)</h1>
                </div>

                <div style="position: relative; z-index: 1;" class="search-container flex-grow-1 flex-md-grow-0">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="searchInput" class="form-control form-control-lg search-input border-light-subtle shadow-sm bg-body-tertiary" placeholder="Cari No Pembelian, Pemasok, atau Barang...">
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Tabel Data -->
    <div class="row mt-3 g-3">
        <div class="col-12">
            <div class="card border-0 data-card h-100 shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Daftar Transaksi Pembelian</h6>
                    <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm text-primary" onclick="fetchSalesOrders()">
                        <i class="fa-solid fa-rotate-right me-1"></i> Refresh Data
                    </button>
                </div>
                <div class="card-body p-0 mt-2">
                    <div class="table-responsive px-4 pb-4">
                        <table class="table table-hover table-premium mb-0">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">No Pembelian</th>
                                    <th width="20%">Pemasok</th>
                                    <th width="15%">No Barang</th>
                                    <th width="30%">Deskripsi Barang</th>
                                    <th width="10%" class="text-center">QTY</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="loading-spinner text-primary mb-3"></div>
                                        <p class="mb-0 fw-semibold text-muted">Mengambil data dari API...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Controls -->
                    <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light border-top" style="border-radius: 0 0 14px 14px;">
                        <span class="text-muted small fw-semibold" id="paginationInfo">Menampilkan 0 dari 0 data</span>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" id="btnPrev" onclick="changePage(-1)" disabled><i class="fa-solid fa-chevron-left me-1"></i> Prev</button>
                            <span class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-dark" id="pageIndicator" style="pointer-events: none;">1</span>
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" id="btnNext" onclick="changePage(1)" disabled>Next <i class="fa-solid fa-chevron-right ms-1"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Fetch Data, Search, & Pagination -->
    <script>
        let allSalesOrders = [];
        let filteredData = [];
        let currentPage = 1;
        const pageSize = 10; // Jumlah item per halaman

        document.addEventListener('DOMContentLoaded', function() {
            fetchSalesOrders();

            // Setup Search Input Event Listener
            document.getElementById('searchInput').addEventListener('keyup', function(e) {
                const keyword = e.target.value.toLowerCase();
                filterTable(keyword);
            });
        });

        function fetchSalesOrders() {
            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="loading-spinner text-primary mb-3"></div>
                        <p class="mb-0 fw-semibold text-muted">Mengambil data dari API...</p>
                    </td>
                </tr>
            `;

            fetch('{{ route("api.packaging.search_so") }}')
                .then(response => response.json())
                .then(result => {
                    if(result && result.data) {
                        allSalesOrders = result.data;
                        filterTable(''); // Inisialisasi awal
                    } else {
                        throw new Error('Gagal memuat data');
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center py-5 text-danger">
                                <i class="fa-solid fa-triangle-exclamation fs-1 mb-3"></i>
                                <p class="mb-0 fw-semibold">Terjadi kesalahan saat memuat data API.</p>
                            </td>
                        </tr>
                    `;
                });
        }

        function filterTable(keyword) {
            filteredData = allSalesOrders.filter(item => {
                const no_pembelian = (item.no_so || item.no_pembelian || '').toLowerCase();
                const pemasok = (item.pelanggan || item.nama_pelanggan || item.pemasok || '').toLowerCase();
                const no_barang = (item.no_barang || '').toLowerCase();
                const deskripsi_barang = (item.deskripsi_barang || '').toLowerCase();

                return no_pembelian.includes(keyword) ||
                       pemasok.includes(keyword) ||
                       no_barang.includes(keyword) ||
                       deskripsi_barang.includes(keyword);
            });
            currentPage = 1; // Reset ke halaman 1 saat filter
            renderTable();
        }

        function changePage(direction) {
            currentPage += direction;
            renderTable();
        }

        function renderTable() {
            const tableBody = document.getElementById('tableBody');
            const btnPrev = document.getElementById('btnPrev');
            const btnNext = document.getElementById('btnNext');
            const pageIndicator = document.getElementById('pageIndicator');
            const paginationInfo = document.getElementById('paginationInfo');
            
            if(!filteredData || filteredData.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open fs-1 mb-3 text-light"></i>
                            <p class="mb-0 fw-semibold">Data tidak ditemukan.</p>
                        </td>
                    </tr>
                `;
                paginationInfo.innerText = "Menampilkan 0 dari 0 data";
                btnPrev.disabled = true;
                btnNext.disabled = true;
                pageIndicator.innerText = "1";
                return;
            }

            // Hitung pagination
            const totalPages = Math.ceil(filteredData.length / pageSize);
            if (currentPage < 1) currentPage = 1;
            if (currentPage > totalPages) currentPage = totalPages;

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = Math.min(startIndex + pageSize, filteredData.length);
            const pagedData = filteredData.slice(startIndex, endIndex);

            let html = '';
            pagedData.forEach((item, index) => {
                const globalIndex = startIndex + index + 1;
                const no_pembelian = item.no_so || item.no_pembelian || '-';
                const pemasok = item.pelanggan || item.nama_pelanggan || item.pemasok || item.nama_pemasok || '-';
                const no_barang = item.no_barang || '-';
                const deskripsi_barang = item.deskripsi_barang || '-';
                const qty = item.qty || item.kuantitas || item.kuantitas_terkirim || item.total_qty || '0';

                html += `
                    <tr>
                        <td class="text-muted fw-bold">${globalIndex}</td>
                        <td><span class="badge bg-light text-dark border fw-bold fs-6 shadow-sm">${no_pembelian}</span></td>
                        <td class="fw-semibold text-primary"><i class="fa-solid fa-building me-1 opacity-50"></i> ${pemasok}</td>
                        <td><span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border px-2">${no_barang}</span></td>
                        <td><span class="text-dark fw-medium">${deskripsi_barang}</span></td>
                        <td class="text-center"><span class="badge bg-primary rounded-pill px-3 py-2 fs-6 shadow-sm">${qty}</span></td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;

            // Update UI Pagination
            paginationInfo.innerText = \`Menampilkan \${startIndex + 1} - \${endIndex} dari \${filteredData.length} data\`;
            pageIndicator.innerText = currentPage;
            btnPrev.disabled = currentPage === 1;
            btnNext.disabled = currentPage === totalPages;
        }
    </script>
</x-app-layout>
