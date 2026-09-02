<x-app-layout>
    <!-- Custom CSS untuk Header Banner & Table (Premium Design) -->
    <style>
        .dashboard-banner {
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid rgba(251, 146, 60, 0.2);
            transition: all 0.3s ease;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
            overflow: hidden;
        }
        html:not([data-bs-theme="dark"]) .dashboard-banner {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 247, 237, 0.95) 60%, rgba(255, 237, 213, 0.95) 100%);
            box-shadow: 0 4px 25px -2px rgba(251, 146, 60, 0.15);
        }
        html[data-bs-theme="dark"] .dashboard-banner {
            background: linear-gradient(135deg, rgba(13, 15, 18, 0.85) 0%, rgba(23, 25, 30, 0.85) 100%);
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
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
            white-space: nowrap;
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
            max-width: 400px;
        }
        .search-container i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .search-input {
            padding-left: 3rem;
            border-radius: 50px;
            transition: all 0.3s;
            background-color: rgba(255,255,255,0.9);
        }
        .search-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(234, 88, 12, 0.25);
            border-color: #ea580c;
            background-color: #fff;
        }
        html[data-bs-theme="dark"] .search-input { background-color: rgba(0,0,0,0.5); border-color: rgba(255,255,255,0.1); color: #fff;}
        html[data-bs-theme="dark"] .search-input:focus { background-color: rgba(0,0,0,0.8); border-color: #fb923c;}
        
        /* Spinner */
        .loading-spinner {
            display: inline-block;
            width: 2.5rem;
            height: 2.5rem;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
        }

        /* Detail Modal Styles */
        .modal-detail-content {
            border-radius: 16px;
            border: 0;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .modal-detail-header {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 1.5rem;
            border: 0;
        }
        .detail-row {
            display: flex;
            margin-bottom: 1rem;
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 1rem;
        }
        .detail-label {
            font-weight: 600;
            color: #64748b;
            width: 35%;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .detail-value {
            font-weight: 700;
            color: #1e293b;
            width: 65%;
        }
        html[data-bs-theme="dark"] .detail-row { border-bottom-color: rgba(255,255,255,0.1); }
        html[data-bs-theme="dark"] .detail-label { color: #94a3b8; }
        html[data-bs-theme="dark"] .detail-value { color: #f8fafc; }
        html[data-bs-theme="dark"] .modal-detail-content { background-color: #1e293b; }
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
                    <h1 class="banner-title mb-2">Data Pembelian (PO)</h1>
                    <p class="text-muted small mb-0 fw-medium">Sinkronisasi Realtime API dari Akurasi</p>
                </div>

                <div style="position: relative; z-index: 1;" class="search-container flex-grow-1 flex-md-grow-0">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="searchInput" class="form-control form-control-lg search-input shadow-sm" placeholder="Cari No PO, Pemasok, atau Barang...">
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Tabel Data -->
    <div class="row mt-3 g-3">
        <div class="col-12">
            <div class="card border-0 data-card h-100 shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-list text-orange me-2"></i>Daftar Transaksi Pembelian</h6>
                    <button class="btn btn-sm btn-orange rounded-pill px-3 shadow-sm text-white fw-bold" onclick="fetchPurchaseOrders()" style="background-color: #ea580c; border:none;">
                        <i class="fa-solid fa-rotate-right me-1"></i> Refresh Data
                    </button>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="table-responsive px-4 pb-4">
                        <table class="table table-hover table-premium mb-0 text-nowrap" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center py-2 align-middle">No</th>
                                    <th class="py-2 align-middle">No. PO</th>
                                    <th class="py-2 align-middle">Tgl PO</th>
                                    <th class="py-2 align-middle">Est. Kirim</th>
                                    <th class="py-2 align-middle">Pemasok</th>
                                    <th class="py-2 align-middle">No. Barang</th>
                                    <th class="py-2 align-middle">Deskripsi Barang</th>
                                    <th class="py-2 align-middle">Category Produk</th>
                                    <th class="text-center py-2 align-middle">Qty Order</th>
                                    <th class="text-center py-2 align-middle">Qty Diterima</th>
                                    <th class="text-center py-2 align-middle">UoM</th>
                                    <th class="text-end py-2 align-middle">Harga Satuan</th>
                                    <th class="text-end py-2 align-middle">Diskon</th>
                                    <th class="text-end py-2 align-middle">PPN</th>
                                    <th class="text-end py-2 align-middle">DPP</th>
                                    <th class="text-end py-2 align-middle">Subtotal</th>
                                    <th class="py-2 align-middle">Tgl Kirim</th>
                                    <th class="text-center py-2 align-middle">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="18" class="text-center py-5">
                                        <div class="loading-spinner text-orange mb-3" style="color: #ea580c;"></div>
                                        <p class="mb-0 fw-semibold text-muted">Mengambil sinkronisasi data API...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Info -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 p-4 border-top">
                        <div class="text-muted small mb-3 mb-md-0 fw-medium" id="paginationInfo">
                            Menampilkan 0 dari 0 Item
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button id="btnPrev" class="btn btn-outline-secondary btn-sm px-3 shadow-sm rounded-pill" onclick="changePage(-1)" disabled>
                                <i class="fa-solid fa-chevron-left me-1"></i> Prev
                            </button>
                            <span class="badge bg-secondary px-3 py-2 rounded-pill fs-6 shadow-sm" id="pageIndicator">1</span>
                            <button id="btnNext" class="btn btn-outline-secondary btn-sm px-3 shadow-sm rounded-pill" onclick="changePage(1)" disabled>
                                Next <i class="fa-solid fa-chevron-right ms-1"></i>
                            </button>
                        </div>
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
        let groupedPurchaseOrders = {};
        let currentKeyword = '';
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            fetchPurchaseOrders(1);

            let searchTimeout;
            document.getElementById('searchInput').addEventListener('keyup', function(e) {
                clearTimeout(searchTimeout);
                const keyword = e.target.value;
                searchTimeout = setTimeout(() => {
                    currentKeyword = keyword;
                    fetchPurchaseOrders(1);
                }, 500); // debounce 500ms
            });
        });

        function fetchPurchaseOrders(page = 1) {
            currentPage = page;
            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="18" class="text-center py-5">
                        <div class="loading-spinner mb-3" style="color: #ea580c;"></div>
                        <p class="mb-0 fw-semibold text-muted">Mengambil data dari server...</p>
                    </td>
                </tr>
            `;

            fetch(`{{ route("api.integration.search_po") }}?page=${page}&q=${encodeURIComponent(currentKeyword)}`)
                .then(response => response.json())
                .then(result => {
                    renderTable(result);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="18" class="text-center py-5 text-danger">
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
                    <div class="loading-spinner mb-3" style="color: #ea580c;"></div>
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
                                <td class="text-end">${formatRupiah(item.harga_satuan)}</td>
                                <td class="text-end fw-bold" style="color: #ea580c;">${formatRupiah(item.amount)}</td>
                                <td><span class="badge bg-secondary">${item.status_bayar || '-'}</span></td>
                            </tr>
                        `;
                    });

                    let html = `
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-orange mb-3" style="color: #ea580c;"><i class="fa-solid fa-file-lines me-2"></i>Informasi PO</h6>
                                <div class="detail-row"><div class="detail-label">No PO</div><div class="detail-value"><span class="badge bg-orange bg-opacity-10 border px-2 py-1" style="color:#ea580c; border-color:#ea580c;">${po.no_po || '-'}</span></div></div>
                                <div class="detail-row"><div class="detail-label">Tgl PO</div><div class="detail-value">${formatTgl(po.tgl_po)}</div></div>
                                <div class="detail-row"><div class="detail-label">Pemasok</div><div class="detail-value" style="color:#ea580c;">${po.pemasok || '-'}</div></div>
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

        function renderTable(result) {
            const tableBody = document.getElementById('tableBody');
            const btnPrev = document.getElementById('btnPrev');
            const btnNext = document.getElementById('btnNext');
            const pageIndicator = document.getElementById('pageIndicator');
            const paginationInfo = document.getElementById('paginationInfo');
            
            const dataList = result.data || [];
            
            if(!dataList || dataList.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="18" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open fs-1 mb-3 text-light"></i>
                            <p class="mb-0 fw-semibold">Data tidak ditemukan.</p>
                        </td>
                    </tr>
                `;
                paginationInfo.innerText = "Menampilkan 0 dari 0 Item";
                btnPrev.disabled = true;
                btnNext.disabled = true;
                pageIndicator.innerText = "1";
                return;
            }

            let html = '';
            dataList.forEach((item, index) => {
                const globalIndex = result.from + index;
                
                let statusBadge = 'bg-secondary';
                const status = item.status_bayar || 'Draft';
                if(status.toLowerCase().includes('menunggu')) statusBadge = 'bg-warning text-dark';
                if(status.toLowerCase().includes('selesai') || status.toLowerCase().includes('terima')) statusBadge = 'bg-success';

                html += `
                    <tr style="cursor: pointer;" onclick="showDetail('${item.no_pembelian}')" class="hover-bg-light" title="Klik baris untuk melihat detail PO">
                        <td class="text-center align-middle">${globalIndex}</td>
                        <td class="align-middle"><span class="badge bg-light text-dark border fw-bold px-2 py-1">${item.no_pembelian || '-'}</span></td>
                        <td class="align-middle text-muted small">${formatTgl(item.tgl_pembelian)}</td>
                        <td class="align-middle text-muted small">${formatTgl(item.tgl_ekspetasi)}</td>
                        <td class="align-middle fw-medium text-truncate" style="color:#ea580c; max-width: 350px;" title="${item.nama_pemasok || ''}"><i class="fa-regular fa-building me-1 opacity-50"></i> ${item.nama_pemasok || '-'}</td>
                        <td class="align-middle fw-medium text-truncate" style="max-width: 350px;">${item.no_barang || '-'}</td>
                        <td class="align-middle text-truncate" style="max-width: 350px;" title="${item.deskripsi_barang || ''}">${item.deskripsi_barang || '-'}</td>
                        <td class="align-middle text-truncate" style="max-width: 350px;" title="${item.category_produk || ''}">${item.category_produk || '-'}</td>
                        <td class="align-middle text-center"><span class="badge bg-opacity-10 border rounded-pill px-2 py-1" style="color:#ea580c; background-color:rgba(234, 88, 12, 0.1); border-color:#ea580c;">${item.qty || 0}</span></td>
                        <td class="align-middle text-center">${item.qty_received || item.qty_diterima || 0}</td>
                        <td class="align-middle text-center">${item.uom || '-'}</td>
                        <td class="align-middle text-end">${formatRupiah(item.harga_satuan)}</td>
                        <td class="align-middle text-end">${formatRupiah(item.diskon)}</td>
                        <td class="align-middle text-end">${formatRupiah(item.nominal_ppn)}</td>
                        <td class="align-middle text-end">${formatRupiah(item.dpp)}</td>
                        <td class="align-middle text-end fw-bold" style="color:#ea580c;">${formatRupiah(item.amount)}</td>
                        <td class="align-middle">${formatTgl(item.tgl_penerimaan)}</td>
                        <td class="align-middle text-center"><span class="badge ${statusBadge} px-2 py-1 rounded-pill">${status}</span></td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;
            paginationInfo.innerText = `Menampilkan ${result.from} - ${result.to} dari total ${result.total} Item (Server-Side Pagination)`;
            pageIndicator.innerText = result.current_page;
            
            btnPrev.disabled = result.current_page <= 1;
            btnNext.disabled = result.current_page >= result.last_page;
        }
    </script>
</x-app-layout>
