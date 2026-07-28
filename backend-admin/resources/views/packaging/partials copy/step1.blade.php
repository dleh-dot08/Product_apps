{{-- Step 1 Sales Order - Bootstrap 5 + Font Awesome + Dark Mode --}}

<style>
#step-1{
    --so-primary: #ea580c;
    --so-primary-rgb: 234, 88, 12;
    --so-card:#fff;
    --so-soft:#f7f9fc;
    --so-text:#162033;
    --so-muted:#6b778c;
    --so-border:#e5eaf1;
    --so-shadow:0 14px 35px rgba(15,23,42,.075);
    color:var(--so-text)
}
[data-bs-theme="dark"] #step-1,
body.dark-mode #step-1{
    --so-card:#121a27;
    --so-soft:#172130;
    --so-text:#edf3fb;
    --so-muted:#98a6ba;
    --so-border:rgba(148,163,184,.18);
    --so-shadow:0 14px 35px rgba(0,0,0,.34)
}
#step-1 .so-shell{
    position:relative;
    overflow:hidden;
    background:radial-gradient(circle at top right,rgba(var(--so-primary-rgb),.08),transparent 28%),var(--so-card);
    border:1px solid var(--so-border);
    border-radius:22px;
    box-shadow:var(--so-shadow)
}
#step-1 .so-shell:before{
    content:"";
    position:absolute;
    inset:0 0 auto;
    height:4px;
    background:linear-gradient(90deg,var(--so-primary),#5b8def,#6f7bf7)
}
#step-1 .so-body{padding:1.5rem}
#step-1 .so-heading{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:1rem;
    flex-wrap:wrap;
    margin-bottom:1.5rem
}
#step-1 .so-heading-main{display:flex;align-items:center;gap:1rem}
#step-1 .so-heading-icon,
#step-1 .so-icon{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:var(--so-primary);
    background:rgba(var(--so-primary-rgb),.09);
    border:1px solid rgba(var(--so-primary-rgb),.13)
}
#step-1 .so-heading-icon{width:54px;height:54px;border-radius:17px;font-size:1.15rem}
#step-1 .so-icon{width:39px;height:39px;border-radius:12px;flex:0 0 39px}
#step-1 .so-title{margin:0;color:var(--so-text);font-size:1.18rem;font-weight:800}
#step-1 .so-subtitle{margin:.25rem 0 0;color:var(--so-muted);font-size:.87rem;line-height:1.55}
#step-1 .so-step-badge,
#step-1 .so-soft-badge{
    display:inline-flex;
    align-items:center;
    gap:.45rem;
    color:var(--so-primary);
    background:rgba(var(--so-primary-rgb),.075);
    border:1px solid rgba(var(--so-primary-rgb),.14);
    border-radius:999px;
    font-size:.74rem;
    font-weight:800;
    white-space:nowrap
}
#step-1 .so-step-badge{padding:.48rem .8rem}
#step-1 .so-soft-badge{padding:.42rem .75rem}
#step-1 .so-card{
    height:100%;
    background:var(--so-card);
    border:1px solid var(--so-border);
    border-radius:18px;
    box-shadow:0 5px 18px rgba(15,23,42,.055)
}
[data-bs-theme="dark"] #step-1 .so-card,
body.dark-mode #step-1 .so-card{box-shadow:0 5px 18px rgba(0,0,0,.24)}
#step-1 .so-card-header{padding:1.1rem 1.1rem 0}
#step-1 .so-card-body{padding:1rem 1.1rem 1.1rem}
#step-1 .so-card-title{display:flex;align-items:center;gap:.7rem;margin:0;color:var(--so-text);font-size:.95rem;font-weight:800}
#step-1 .so-card-desc{margin:.6rem 0 0;color:var(--so-muted);font-size:.81rem;line-height:1.55}
#step-1 .so-label{display:flex;align-items:center;gap:.45rem;margin-bottom:.65rem;color:var(--so-text);font-size:.83rem;font-weight:700}
#step-1 .so-search-wrap{position:relative}
#step-1 .so-search-icon{position:absolute;top:50%;left:1rem;z-index:2;color:var(--so-muted);transform:translateY(-50%)}
#step-1 .premium-input{
    height:50px;
    padding-left:2.8rem;
    color:var(--so-text)!important;
    background:var(--so-soft)!important;
    border:1px solid var(--so-border);
    border-radius:14px;
    box-shadow:none!important
}
#step-1 .premium-input::placeholder{color:var(--so-muted);opacity:.78}
#step-1 .premium-input:focus{
    background:var(--so-card)!important;
    border-color:rgba(var(--so-primary-rgb),.45);
    box-shadow:0 0 0 .22rem rgba(var(--so-primary-rgb),.11)!important
}
#step-1 .btn-premium{
    min-height:50px;
    border:0;
    border-radius:14px;
    font-size:.86rem;
    font-weight:800;
    background:linear-gradient(135deg,var(--so-primary),#fb923c);
    box-shadow:0 8px 18px rgba(var(--so-primary-rgb),.23);
    transition:.18s ease
}
#step-1 .btn-premium:hover{transform:translateY(-1px);box-shadow:0 12px 24px rgba(var(--so-primary-rgb),.28)}
#step-1 .so-helper{
    display:flex;
    align-items:flex-start;
    gap:.65rem;
    margin-top:.9rem;
    padding:.85rem .9rem;
    color:var(--so-muted);
    background:rgba(var(--so-primary-rgb),.045);
    border:1px dashed rgba(var(--so-primary-rgb),.18);
    border-radius:14px;
    font-size:.78rem;
    line-height:1.5
}
#step-1 .so-helper i{margin-top:.15rem;color:var(--so-primary)}
#step-1 .so-info-card{
    height:100%;
    padding:1.1rem;
    background:linear-gradient(180deg,rgba(var(--so-primary-rgb),.025),transparent),var(--so-card);
    border:1px solid var(--so-border);
    border-radius:18px;
    box-shadow:0 5px 18px rgba(15,23,42,.055)
}
#step-1 .so-info-top{display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1rem}
#step-1 .so-info-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.85rem}
#step-1 .so-info-item{
    position:relative;
    min-height:108px;
    overflow:hidden;
    padding:1rem;
    background:var(--so-soft);
    border:1px solid var(--so-border);
    border-radius:14px;
    transition:.18s ease
}
#step-1 .so-info-item:hover{transform:translateY(-2px);border-color:rgba(var(--so-primary-rgb),.22)}
#step-1 .so-info-label{display:flex;align-items:center;gap:.5rem;margin-bottom:.65rem;color:var(--so-muted);font-size:.7rem;font-weight:800;letter-spacing:.42px;text-transform:uppercase}
#step-1 .so-info-label i{color:var(--so-primary)}
#step-1 .so-info-value{color:var(--so-text);font-size:.92rem;font-weight:800;line-height:1.45;overflow-wrap:anywhere}
#step-1 .so-table-section{margin-top:1.75rem}
#step-1 .so-table-heading{display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:.9rem}
#step-1 .so-table-title-wrap{display:flex;align-items:center;gap:.75rem}
#step-1 .so-table-title{margin:0;color:var(--so-text);font-size:.94rem;font-weight:800}
#step-1 .so-table-subtitle{margin:.18rem 0 0;color:var(--so-muted);font-size:.79rem}
#step-1 .so-table-wrap{
    overflow:hidden;
    background:var(--so-card);
    border:1px solid var(--so-border);
    border-radius:18px;
    box-shadow:0 5px 18px rgba(15,23,42,.055)
}
#step-1 .premium-table{width:100%;margin:0;color:var(--so-text)}
#step-1 .premium-table thead th{
    padding:.9rem .85rem;
    color:var(--so-muted);
    background:rgba(var(--so-primary-rgb),.055);
    border-bottom:1px solid var(--so-border);
    font-size:.69rem;
    font-weight:800;
    letter-spacing:.45px;
    text-transform:uppercase;
    white-space:nowrap
}
#step-1 .premium-table tbody tr{border-bottom:1px solid var(--so-border)}
#step-1 .premium-table tbody tr:last-child{border-bottom:0}
#step-1 .premium-table tbody tr:hover{background:rgba(var(--so-primary-rgb),.035)}
#step-1 .premium-table tbody td{padding:.9rem .85rem;color:var(--so-text);background:transparent;border:0;vertical-align:middle}
#step-1 .premium-table .form-check-input{width:1.05rem;height:1.05rem;margin-top:0;background-color:var(--so-card);border-color:rgba(100,116,139,.48);box-shadow:none!important}
#step-1 .premium-table .form-check-input:checked{background-color:var(--so-primary);border-color:var(--so-primary)}
#step-1 .qty-chip{
    display:inline-flex;
    justify-content:center;
    min-width:42px;
    padding:.35rem .58rem;
    color:var(--so-primary);
    background:rgba(var(--so-primary-rgb),.08);
    border:1px solid rgba(var(--so-primary-rgb),.13);
    border-radius:999px;
    font-size:.77rem;
    font-weight:800
}
#step-1 .remaining-chip{color:#f59e0b;background:rgba(245,158,11,.09);border-color:rgba(245,158,11,.18)}
#step-1 .qty-input{
    min-width:105px;
    max-width:135px;
    min-height:40px;
    color:var(--so-text);
    background:var(--so-soft);
    border:1px solid var(--so-border);
    border-radius:11px;
    font-size:.82rem;
    font-weight:700;
    box-shadow:none
}
#step-1 .qty-input:focus{color:var(--so-text);background:var(--so-card);border-color:rgba(var(--so-primary-rgb),.42);box-shadow:0 0 0 .2rem rgba(var(--so-primary-rgb),.1)}
#step-1 .so-empty-state{padding:2.8rem 1rem;text-align:center;color:var(--so-muted)}
#step-1 .so-empty-icon{
    width:78px;height:78px;
    display:inline-flex;align-items:center;justify-content:center;
    margin-bottom:1rem;
    color:var(--so-primary);
    background:rgba(var(--so-primary-rgb),.08);
    border:1px solid rgba(var(--so-primary-rgb),.11);
    border-radius:24px;
    font-size:1.7rem
}
#step-1 .so-empty-title{margin-bottom:.35rem;color:var(--so-text);font-size:.94rem;font-weight:800}
#step-1 .so-empty-text{margin:0;color:var(--so-muted);font-size:.82rem}
#step-1 .so-spinner{width:1rem;height:1rem;border:2px solid rgba(255,255,255,.42);border-top-color:#fff;border-radius:50%;animation:soSpin .7s linear infinite}
@keyframes soSpin{to{transform:rotate(360deg)}}
@media(max-width:1199.98px){#step-1 .so-info-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:767.98px){
    #step-1 .so-body{padding:1rem}
    #step-1 .so-heading-icon{width:48px;height:48px}
    #step-1 .so-title{font-size:1.03rem}
    #step-1 .so-info-grid{grid-template-columns:1fr}
}
</style>

<div class="form-step active" id="step-1">
    <div class="so-shell">
        <div class="so-body">
            <!-- Search -->
            <div class="so-card" style="padding: 10px; margin-bottom: 5px;">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="searchSO" class="so-label mb-2">
                            <i class="fa-solid fa-file-invoice"></i>
                            Nomor Sales Order
                            <span class="text-danger">*</span>
                        </label>

                        <div class="d-flex flex-column flex-md-row gap-2">
                            <div class="so-search-wrap flex-grow-1">
                                <i class="fa-solid fa-search so-search-icon"></i>

                                <input
                                    type="text"
                                    class="form-control premium-input"
                                    id="searchSO"
                                    name="search_so"
                                    placeholder="Contoh: AI-PP-261840"
                                    autocomplete="off"
                                >
                            </div>

                            <button
                                type="button"
                                class="btn btn-premium text-white px-4 flex-shrink-0"
                                id="btnSearchSO"
                            >
                                <i class="fa-solid fa-cloud-arrow-down me-2"></i>
                                Tarik Data
                            </button>
                        </div>
                    </div>

                    <div class="col-12" id="itemSelectionSection">
                        <label for="itemDropdown" class="so-label mb-2">
                            <i class="fa-solid fa-box-open"></i>
                            Pilih Barang
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            class="form-select premium-input w-100"
                            id="itemDropdown"
                            style="padding-left:1rem;cursor:pointer;"
                            disabled
                        >
                            <option value="">Pilih Barang...</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Informasi Sales Order -->
            <div class="so-info-card" style="padding: 10px; margin-bottom: 5px;">
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                    <span class="so-icon">
                        <i class="fa-solid fa-file-invoice"></i>
                    </span>

                    <div>
                        <h6 class="so-table-title mb-1">Informasi Sales Order</h6>
                        <p class="so-table-subtitle mb-0">
                            Detail utama dari Sales Order yang dipilih.
                        </p>
                    </div>
                </div>

                <div class="so-detail-table">
                    <div class="so-detail-row">
                        <div class="so-detail-label">
                            <i class="fa-solid fa-file-invoice"></i>
                            No. SO
                        </div>

                        <div class="so-detail-value font-monospace" id="infoNoSO">
                            -
                        </div>
                    </div>

                    <div class="so-detail-row">
                        <div class="so-detail-label">
                            <i class="fa-regular fa-calendar-days"></i>
                            Delivery Date
                        </div>

                        <div class="so-detail-value" id="infoDeliveryDate">
                            -
                        </div>
                    </div>

                    <div class="so-detail-row">
                        <div class="so-detail-label">
                            <i class="fa-solid fa-building"></i>
                            Customer
                        </div>

                        <div class="so-detail-value" id="infoCustomer">
                            -
                        </div>
                    </div>

                    <div class="so-detail-row">
                        <div class="so-detail-label">
                            <i class="fa-solid fa-location-dot"></i>
                            Alamat Pengiriman
                        </div>

                        <div class="so-detail-value" id="infoShipto">
                            -
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Barang -->
            <div class="so-info-card" id="selectedItemDetails" style="padding: 10px; margin-bottom: 5px;">
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                    <span class="so-icon">
                        <i class="fa-solid fa-box-open"></i>
                    </span>

                    <div>
                        <h6 class="so-table-title mb-1">Informasi Barang</h6>
                        <p class="so-table-subtitle mb-0">
                            Detail produk yang dipilih untuk proses packaging.
                        </p>
                    </div>
                </div>

                <div class="so-detail-table">
                    <div class="so-detail-row">
                        <div class="so-detail-label">
                            <i class="fa-solid fa-barcode"></i>
                            Part Number
                        </div>

                        <div class="so-detail-value">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <span class="font-monospace flex-grow-1" id="detailPartNo">-</span>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-light border rounded-3 flex-shrink-0"
                                    id="btnCopyPartNo"
                                    title="Salin Part Number"
                                >
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="so-detail-row">
                        <div class="so-detail-label">
                            <i class="fa-solid fa-align-left"></i>
                            Description
                        </div>

                        <div class="so-detail-value" id="detailDesc">
                            -
                        </div>
                    </div>

                    <div class="so-detail-row">
                        <div class="so-detail-label">
                            <i class="fa-solid fa-boxes-stacked"></i>
                            Qty Product
                        </div>

                        <div class="so-detail-value">
                            <span id="detailQtyOrder">-</span>
                            <span class="text-muted ms-1">Unit</span>
                        </div>
                    </div>

                    <div class="so-detail-row">
                        <div class="so-detail-label">
                            <i class="fa-solid fa-layer-group"></i>
                            Qty Sisa
                        </div>

                        <div class="so-detail-value">
                            <span class="qty-remaining-badge">
                                <i class="fa-solid fa-box-open"></i>
                                Sisa
                                <strong id="detailQtyRemaining">-</strong>
                                Unit
                            </span>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="itemQtyKirim" value="1">
            </div>
        </div>
    </div>
</div>

<style>
#step-1 .so-detail-table{
    width:100%;
    overflow:hidden;
    background:var(--so-card);
    border:1px solid var(--so-border);
    border-radius:14px;
}

#step-1 .so-detail-row{
    display:grid;
    grid-template-columns:190px minmax(0,1fr);
    align-items:stretch;
    min-height:46px;
    border-bottom:1px solid var(--so-border);
}

#step-1 .so-detail-row:last-child{
    border-bottom:0;
}

#step-1 .so-detail-label{
    display:flex;
    align-items:center;
    gap:.55rem;
    padding:.72rem .9rem;
    color:var(--so-muted);
    background:var(--so-soft);
    border-right:1px solid var(--so-border);
    font-size:12px;
    font-weight:800;
}

#step-1 .so-detail-label i{
    width:16px;
    color:var(--so-primary);
    text-align:center;
    flex-shrink:0;
}

#step-1 .so-detail-value{
    min-width:0;
    display:flex;
    align-items:center;
    padding:.72rem .9rem;
    color:var(--so-text);
    font-size:12px;
    font-weight:700;
    line-height:1.45;
    overflow-wrap:anywhere;
}

#step-1 .so-detail-row:hover .so-detail-value{
    background:rgba(var(--so-primary-rgb),.025);
}

#step-1 .qty-remaining-badge{
    display:inline-flex;
    align-items:center;
    gap:.42rem;
    padding:.38rem .65rem;
    color:var(--so-primary);
    background:rgba(var(--so-primary-rgb),.10);
    border:1px solid rgba(var(--so-primary-rgb),.18);
    border-radius:999px;
    font-size:.76rem;
    font-weight:700;
}

#step-1 #detailPartNo{
    word-break:break-all;
}

#step-1 #detailDesc{
    line-height:1.55;
}

#step-1 .btn-light{
    color:var(--so-text);
    background:var(--so-soft);
    border-color:var(--so-border)!important;
}

[data-bs-theme="dark"] #step-1 .btn-light,
body.dark-mode #step-1 .btn-light{
    color:var(--so-text);
    background:var(--so-soft);
}

@media(max-width:575.98px){
    #step-1 .so-detail-row{
        grid-template-columns:1fr;
    }

    #step-1 .so-detail-label{
        padding:.6rem .75rem;
        border-right:0;
        border-bottom:1px solid var(--so-border);
    }

    #step-1 .so-detail-value{
        padding:.7rem .75rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchSO');
    const searchButton = document.getElementById('btnSearchSO');
    const checkAllItems = document.getElementById('checkAllItems');

    window.setSOSearchLoading = function (isLoading) {
        if (!searchButton) return;

        searchButton.disabled = Boolean(isLoading);
        searchButton.innerHTML = isLoading
            ? '<span class="d-inline-flex align-items-center gap-2"><span class="so-spinner"></span><span>Menarik Data...</span></span>'
            : '<i class="fa-solid fa-cloud-arrow-down me-2"></i><span>Tarik Data SO</span>';
    };

    window.resetSOHeaderInfo = function () {
        ['infoNoSO', 'infoCustomer', 'infoDeliveryDate', 'infoShipto', 'detailPartNo', 'detailDesc', 'detailQtyOrder', 'detailQtyRemaining'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.textContent = '-';
        });
        
        const dropdown = document.getElementById('itemDropdown');
        if(dropdown) dropdown.innerHTML = '<option value="">Pilih Barang...</option>';
    };

    window.showSOEmptyState = function (
        title = 'Belum Ada Data Sales Order',
        message = 'Masukkan nomor SO kemudian tekan tombol Tarik Data SO.'
    ) {
        const dropdown = document.getElementById('itemDropdown');
        if (dropdown) {
            dropdown.innerHTML = '<option value="">Pilih Barang...</option>';
            dropdown.disabled = true;
        }
    };

    window.renderSOItems = function (items) {
        const dropdown = document.getElementById('itemDropdown');
        
        if (!dropdown) return;

        if (!Array.isArray(items) || items.length === 0) {
            dropdown.innerHTML = '<option value="">Pilih Barang...</option>';
            dropdown.disabled = true;
            return;
        }

        dropdown.disabled = false;
        
        dropdown.innerHTML = '<option value="">Pilih Barang...</option>' + items.map(function (item, index) {
            const itemId = item.id ?? index;
            const itemNo = item.no_barang ?? item.item_no ?? item.part_no ?? '-';
            const description = item.deskripsi_barang ?? item.description ?? item.item_description ?? '-';
            const qtyOrder = Number(item.qty ?? item.qty_order ?? 0);
            const qtyRemaining = Number(item.sisa_kirim ?? item.qty_remaining ?? 0);

            return `<option value="${escapeSOHtml(String(itemId))}" 
                        data-item-no="${escapeSOHtml(String(itemNo))}" 
                        data-item-desc="${escapeSOHtml(String(description))}"
                        data-qty-order="${qtyOrder}" 
                        data-qty-remaining="${qtyRemaining}"
                        data-short-text="${escapeSOHtml(String(itemNo))}"
                        data-full-text="${escapeSOHtml(String(itemNo))} - ${escapeSOHtml(String(description))}">
                        ${escapeSOHtml(String(itemNo))} - ${escapeSOHtml(String(description))}
                    </option>`;
        }).join('');
    };
    
    // Setup listener untuk dropdown
    const itemDropdown = document.getElementById('itemDropdown');
    const inputQtyEl = document.getElementById('itemQtyKirim');
    
    if (itemDropdown) {
        function restoreDropdownFullText() {
            Array.from(itemDropdown.options).forEach(opt => {
                if (opt.value && opt.getAttribute('data-full-text')) {
                    opt.text = opt.getAttribute('data-full-text');
                }
            });
        }
        
        function applyDropdownShortText() {
            if (itemDropdown.selectedIndex > 0) {
                const opt = itemDropdown.options[itemDropdown.selectedIndex];
                if (opt.getAttribute('data-short-text')) {
                    opt.text = opt.getAttribute('data-short-text');
                }
            }
        }

        itemDropdown.addEventListener('mousedown', restoreDropdownFullText);
        itemDropdown.addEventListener('focus', restoreDropdownFullText);
        itemDropdown.addEventListener('blur', applyDropdownShortText);

        itemDropdown.addEventListener('change', function() {
            applyDropdownShortText();
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const qtyOrder = selectedOption.getAttribute('data-qty-order');
                const qtyRemaining = selectedOption.getAttribute('data-qty-remaining');
                const partNo = selectedOption.getAttribute('data-item-no');
                const desc = selectedOption.getAttribute('data-item-desc');
                
                document.getElementById('detailPartNo').innerText = partNo;
                document.getElementById('detailDesc').innerText = desc;
                document.getElementById('detailQtyOrder').innerText = formatSONumber(qtyOrder);
                document.getElementById('detailQtyRemaining').innerText = formatSONumber(qtyRemaining);
                
                if (inputQtyEl) {
                    inputQtyEl.max = qtyRemaining;
                    inputQtyEl.value = Math.min(1, qtyRemaining);
                }
            } else {
                ['detailPartNo', 'detailDesc', 'detailQtyOrder', 'detailQtyRemaining'].forEach(id => {
                    document.getElementById(id).innerText = '-';
                });
            }
        });
    }
    
    if (inputQtyEl) {
        inputQtyEl.addEventListener('input', function() {
            const max = parseFloat(this.max) || 0;
            const val = parseFloat(this.value) || 0;
            if (val > max || val <= 0) {
                this.classList.add('is-invalid', 'border-danger', 'text-danger');
            } else {
                this.classList.remove('is-invalid', 'border-danger', 'text-danger');
            }
        });
    }

    searchInput?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchButton?.click();
        }
    });

    function escapeSOHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function formatSONumber(value) {
        return new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 0
        }).format(Number(value || 0));
    }


    const btnCopyPartNo = document.getElementById('btnCopyPartNo');

    btnCopyPartNo?.addEventListener('click', async function () {
        const value = document.getElementById('detailPartNo')?.textContent?.trim();

        if (!value || value === '-') return;

        try {
            await navigator.clipboard.writeText(value);

            const originalHtml = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-check text-success"></i>';

            setTimeout(() => {
                this.innerHTML = originalHtml;
            }, 1200);
        } catch (error) {
            console.error('Gagal menyalin Part Number:', error);
        }
    });

    const btnUseSelectedItem = document.getElementById('btnUseSelectedItem');

    btnUseSelectedItem?.addEventListener('click', function () {
        const dropdown = document.getElementById('itemDropdown');
        const selectedItem = dropdown?.value;

        if (!selectedItem) {
            alert('Silakan pilih barang terlebih dahulu.');
            return;
        }

        document.dispatchEvent(new CustomEvent('salesOrderItemSelected', {
            detail: {
                itemId: selectedItem,
                partNumber: document.getElementById('detailPartNo')?.textContent?.trim(),
                description: document.getElementById('detailDesc')?.textContent?.trim(),
                qtyOrder: document.getElementById('detailQtyOrder')?.textContent?.trim(),
                qtyRemaining: document.getElementById('detailQtyRemaining')?.textContent?.trim()
            }
        }));

        const modalElement = this.closest('.modal');

        if (modalElement && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(modalElement).hide();
        }
    });
});
</script>