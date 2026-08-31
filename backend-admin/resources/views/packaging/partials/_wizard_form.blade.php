<style>

        /* =========================================================
           SIMPLE INTERACTIVE WIZARD
           ========================================================= */
        .wizard-progress-shell {
            max-width: 760px;
            margin: 0 auto 0.5rem;
            padding: .25rem 0;
        }

        .step-progress {
            position: relative;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0;
            margin: 0;
        }

        .step-progress::before {
            content: "";
            position: absolute;
            top: 22px;
            left: 16.66%;
            right: 16.66%;
            height: 3px;
            background: var(--bs-border-color);
            border-radius: 999px;
            z-index: 1;
        }

        .step-progress-bar {
            position: absolute;
            top: 22px;
            left: 16.66%;
            width: 0;
            max-width: 66.68%;
            height: 3px;
            background: #ea580c;
            border-radius: 999px;
            z-index: 2;
            transition: width .35s ease;
        }

        .step-item {
            position: relative;
            z-index: 3;
            text-align: center;
            padding: 0 .5rem;
            cursor: pointer;
            user-select: none;
        }

        .step-circle {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto .65rem;
            border: 2px solid var(--bs-border-color);
            border-radius: 50%;
            background: var(--bs-body-bg);
            color: var(--bs-secondary-color);
            font-size: 1rem;
            transition: all .22s ease;
        }

        .step-item:hover .step-circle {
            transform: translateY(-2px);
            border-color: rgba(234, 88, 12, 0.45);
        }

        .step-label {
            display: block;
            color: var(--bs-secondary-color);
            font-size: .8rem;
            font-weight: 700;
            line-height: 1.25;
            transition: color .2s ease;
        }

        .step-description,
        .step-status,
        .step-number {
            display: none;
        }

        .step-item.active .step-circle {
            color: #fff;
            background: #ea580c;
            border-color: #ea580c;
            box-shadow: 0 0 0 5px rgba(234, 88, 12, 0.10);
            transform: translateY(-2px);
        }

        .step-item.active .step-label {
            color: #ea580c;
        }

        .step-item.completed .step-circle {
            color: #fff;
            background: var(--bs-success);
            border-color: var(--bs-success);
        }

        .step-item.completed .step-label {
            color: var(--bs-body-color);
        }

        @media (max-width: 575.98px) {
            .wizard-progress-shell {
                margin-bottom: 1.75rem;
            }

            .step-progress::before,
            .step-progress-bar {
                top: 19px;
            }

            .step-circle {
                width: 40px;
                height: 40px;
                font-size: .9rem;
            }

            .step-label {
                font-size: .7rem;
            }
        }






        
        /* Form Steps Animation */
        .form-step {
            display: none;
            animation: fadeSlideUp 0.4s ease forwards;
        }
        .form-step.active {
            display: block;
        }
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Input & Controls */
        .premium-input {
            border-radius: 0.5rem;
            padding: 0.6rem 1rem;
            border: 1px solid rgba(var(--bs-secondary-rgb), 0.2);
            transition: all 0.2s ease;
            background: rgba(var(--bs-body-bg-rgb), 0.8);
        }
        .premium-input:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 4px rgba(var(--bs-primary-rgb), 0.1);
            background: var(--bs-body-bg);
        }
        
        /* Cards */
        .info-card {
            background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.05) 0%, rgba(var(--bs-body-bg-rgb), 0) 100%);
            border: 1px solid rgba(var(--bs-primary-rgb), 0.1);
            border-radius: 1rem;
            transition: transform 0.3s ease;
        }
        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        }

        /* Custom Table */
        .premium-table th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            color: rgba(var(--bs-body-color-rgb), 0.6);
            border-bottom: 2px solid rgba(var(--bs-primary-rgb), 0.1);
            padding: 1rem;
        }
        .premium-table td {
            vertical-align: middle;
            padding: 1rem;
            border-bottom: 1px solid rgba(var(--bs-secondary-rgb), 0.05);
        }
        .premium-table tr:hover td {
            background-color: rgba(var(--bs-primary-rgb), 0.02);
        }

        /* Buttons */
        .btn-premium {
            border-radius: 0.5rem;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-premium:hover {
            transform: translateY(-2px);
        }
        .btn-premium:active {
            transform: translateY(0);
        }
        
        .btn-premium.btn-primary {
            background: linear-gradient(135deg, #ea580c, #fb923c) !important;
            border: none !important;
            color: #fff !important;
            box-shadow: 0 8px 18px rgba(234, 88, 12, 0.25) !important;
        }
        .btn-premium.btn-primary:hover {
            box-shadow: 0 12px 24px rgba(234, 88, 12, 0.35) !important;
        }
        
        /* Force allow sticky behavior */
        .app-wrapper, .app-main, .app-content {
            overflow: visible !important;
        }
        
        .sticky-header {
            position: -webkit-sticky !important;
            position: sticky !important;
            top: 0 !important; /* Menempel pas di atas */
            z-index: 1020 !important;
        }
    </style>

    <div class="container-fluid pb-4">
        <!-- Header -->
        <div class="pkg-page-head sticky-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3 p-3 rounded-4 shadow-sm" style="background: linear-gradient(135deg, rgba(234, 88, 12, 0.1) 0%, transparent 100%); border: 1px solid rgba(234, 88, 12, 0.2); overflow: hidden; backdrop-filter: blur(10px);">
            <!-- Dekorasi -->
            <div style="position: absolute; top: -20px; right: 10%; width: 100px; height: 100px; background: radial-gradient(circle, rgba(234, 88, 12, 0.15) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0;"></div>
            
            <div style="position: relative; z-index: 1;">
                <h5 class="fw-bolder mb-1" style="color: var(--bs-body-color);"><i class="fa-solid fa-calculator me-2" style="color: #ea580c;"></i> Buat Kalkulasi Packaging</h5>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Ikuti langkah-langkah di bawah ini untuk mencari dan mengkalkulasi ukuran packaging.</p>
            </div>
            <a href="{{ route('packaging.index') }}" class="btn btn-light border shadow-sm px-3 py-2 fw-bold text-secondary" style="border-radius: 10px; font-size: 0.85rem; position: relative; z-index: 1;">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali
            </a>
        </div>

        <div class="card border border-secondary border-opacity-10 shadow-sm rounded-4 bg-body" style="border-radius: 1.25rem !important;">
            <div class="card-body" style="padding: 2rem !important;">

                <!-- Simple Interactive Progress Tracker -->
                <div class="wizard-progress-shell">
                    <div class="step-progress" aria-label="Tahapan kalkulasi packaging">
                        <div class="step-progress-bar" style="width: 0%;" id="progressLine"></div>

                        <div class="step-item active" id="step-nav-1" aria-current="step" data-step="1" role="button" tabindex="0">
                            <div class="step-circle">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </div>
                            <span class="step-label">Pilih Barang</span>
                        </div>

                        <div class="step-item" id="step-nav-2" data-step="2" role="button" tabindex="0">
                            <div class="step-circle">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <span class="step-label">Konfigurasi</span>
                        </div>
                    </div>
                </div>





                <!-- FORM STEPS -->
                @php
                    $isEdit = isset($mode) && $mode === 'edit';
                    $actionUrl = $isEdit ? route('packaging.update', $packagingJob->id) : route('packaging.store');
                @endphp
                <form id="wizardForm" action="{{ $actionUrl }}" method="POST">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    @include('packaging.partials.modals._product-setup')
                    <!-- Button Controls -->
                    <div class="d-flex justify-content-between mt-5 pt-4 border-top border-secondary border-opacity-10">
                        <button type="button" class="btn btn-light border px-4 text-body fw-bold btn-premium shadow-sm" id="btnPrev" style="display: none;">
                            <i class="fa-solid fa-arrow-left me-2"></i> Sebelumnya
                        </button>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-primary px-5 fw-bold btn-premium shadow-sm" id="btnNext">
                                Selanjutnya <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                            <button type="submit" class="btn btn-success px-5 fw-bold btn-premium shadow-sm" id="btnSubmit" style="display: none;">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Simpan & Buat Kalkulasi
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- SCRIPT FOR WIZARD -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 1;
            const totalSteps = 2;
            
            const btnPrev = document.getElementById('btnPrev');
            const btnNext = document.getElementById('btnNext');
            const btnSubmit = document.getElementById('btnSubmit');
            const progressLine = document.getElementById('progressLine');


            const wizardNavItems = document.querySelectorAll('.step-item[data-step]');

            wizardNavItems.forEach((navItem) => {
                const openStep = () => {
                    const targetStep = Number(navItem.dataset.step);

                    // User hanya dapat kembali ke step sebelumnya,
                    // atau membuka step yang sudah pernah dilewati.
                    if (targetStep <= currentStep) {
                        currentStep = targetStep;
                        updateWizard();
                    }
                };

                navItem.addEventListener('click', openStep);
                navItem.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        openStep();
                    }
                });
            });
            
            const searchSOInput = document.getElementById('searchSO');
            const btnSearchSO = document.getElementById('btnSearchSO');
            const tableBodySO = document.getElementById('tableBodySO');
            const checkAllItems = document.getElementById('checkAllItems');
            
            // Elements for SO Header info
            const infoNoSO = document.getElementById('infoNoSO');
            const infoCustomer = document.getElementById('infoCustomer');
            const infoDeliveryDate = document.getElementById('infoDeliveryDate');
            const infoShipto = document.getElementById('infoShipto');
            
            // State untuk menampung config per produk
            let itemConfigs = {};
            let activeItemId = null;
            
            function updateWizard() {
                // Update form steps visibility
                for(let i = 1; i <= totalSteps; i++) {
                    document.getElementById(`step-${i}`).classList.remove('active');
                    
                    const nav = document.getElementById(`step-nav-${i}`);
                    if (nav) {
                        nav.classList.remove('active', 'completed');
                        
                        nav.removeAttribute('aria-current');

                        if (i < currentStep) {
                            nav.classList.add('completed');
                            nav.innerHTML = buildStepNavigation(i, 'completed');
                        } else if (i === currentStep) {
                            nav.classList.add('active');
                            nav.setAttribute('aria-current', 'step');
                            nav.innerHTML = buildStepNavigation(i, 'active');
                        } else {
                            nav.innerHTML = buildStepNavigation(i, 'pending');
                        }
                    }
                }
                
                document.getElementById(`step-${currentStep}`).classList.add('active');
                
                const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
                if(progressLine) progressLine.style.width = progressPercentage + '%';
                
                btnPrev.style.display = currentStep === 1 ? 'none' : 'inline-block';
                
                if (currentStep === totalSteps) {
                    btnNext.style.display = 'none';
                    btnSubmit.style.display = 'inline-block';
                    updateReviewData();
                } else {
                    btnNext.style.display = 'inline-block';
                    btnSubmit.style.display = 'none';
                }
                
                // Jika masuk ke Step 2, inisialisasi daftar barang
                if (currentStep === 2) {
                    initStep2();
                }
            }
            
            function initStep2() {
                // Set Header Info
                const step2NoSO = document.getElementById('step2NoSO');
                if(step2NoSO) {
                    step2NoSO.innerText = infoNoSO.innerText;
                    document.getElementById('step2Customer').innerText = infoCustomer.innerText;
                    document.getElementById('step2DeliveryDate').innerText = infoDeliveryDate.innerText;
                    document.getElementById('step2Shipto').innerText = infoShipto.innerText;
                }

                const listContainer = document.getElementById('step2ProductList');
                if(!listContainer) return;
                listContainer.innerHTML = '';
                
                const dropdown = document.getElementById('itemDropdown');
                if (!dropdown || !dropdown.value) {
                    listContainer.innerHTML = '<div class="text-center py-4 text-muted small border rounded">Tidak ada barang yang dipilih</div>';
                    document.getElementById('step2ConfigContainerWrapper').style.display = 'none';
                    return;
                }
                
                document.getElementById('step2ConfigContainerWrapper').style.display = 'block';
                
                const selectedOption = dropdown.options[dropdown.selectedIndex];
                const noBarang = selectedOption.value;
                const itemName = selectedOption.getAttribute('data-item-desc') || '';
                const qtyInput = document.getElementById('itemQtyKirim');
                const qtyKirim = parseFloat(qtyInput?.value || 1);
                const itemId = 'prod_' + noBarang.replace(/[^a-zA-Z0-9]/g, '_');
                
                // Inisialisasi default config jika belum ada (ARRAY OF CONFIGS)
                if (!itemConfigs[itemId]) {
                    itemConfigs[itemId] = [{
                        no_barang: noBarang,
                        nama_barang: itemName,
                        qty_kirim: qtyKirim,
                        packer: '',
                        qty_pack: 1,
                        qty_per_pack: 1,
                        length: '', width: '', height: '',
                        kb_status: 'Include', kb_arah: 'Horizontal', kb_material: 'A001',
                        pb_status: 'Include', pb_arah: 'Horizontal', pb_material: 'A001',
                        ptb_status: 'Tanpa Penutup', ptb_arah: 'Horizontal', ptb_material: 'A001',
                        pa_status: 'Include', pa_arah: 'Horizontal', pa_material: 'A001',
                        pta_status: 'Tanpa Penutup', pta_arah: 'Horizontal', pta_material: 'A001',
                        jarak: '', gap_atas: '', gap_bawah: ''
                    }];
                } else {
                    itemConfigs[itemId].forEach(cfg => cfg.qty_kirim = qtyKirim);
                }

                // Buat list group item
                const a = document.createElement('a');
                a.href = '#';
                a.className = 'list-group-item list-group-item-action p-3 mb-2 rounded border border-secondary border-opacity-10';
                a.innerHTML = `<div class="fw-bold text-primary font-monospace" style="font-size:0.85rem; word-break: break-all;">${noBarang}</div>
                               <div class="small text-muted mb-2" title="${itemName}" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">${itemName}</div>
                               <div class="d-flex justify-content-between align-items-center">
                                   <span class="badge bg-secondary bg-opacity-10 text-body border shadow-sm">Kirim: ${itemConfigs[itemId][0].qty_kirim}</span>
                                   <span class="badge bg-info text-dark shadow-sm" id="badge_config_${itemId}">Config: 0</span>
                               </div>`;
                
                a.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.querySelectorAll('#step2ProductList .list-group-item').forEach(el => {
                        el.classList.remove('active', 'bg-primary', 'bg-opacity-10', 'border-primary');
                    });
                    a.classList.add('active', 'bg-primary', 'bg-opacity-10', 'border-primary');
                    renderConfigs(itemId);
                });
                
                listContainer.appendChild(a);
                updateSidebarQty(itemId); // Inisialisasi awal badge qty
                
                // Otomatis pilih item pertama
                listContainer.firstChild.click();
                validateStep2NextButton();
            }

            function updateSidebarQty(itemId) {
                const badge = document.getElementById(`badge_config_${itemId}`);
                if (!badge) return;
                
                const configs = itemConfigs[itemId];
                if (!configs) return;
                
                let totalConfigured = 0;
                configs.forEach(cfg => {
                    totalConfigured += (parseFloat(cfg.qty_pack) || 0) * (parseFloat(cfg.qty_per_pack) || 0);
                });
                
                badge.innerText = `Config: ${totalConfigured}`;
                
                const qtyKirim = parseFloat(configs[0].qty_kirim) || 0;
                if (totalConfigured > qtyKirim) {
                    badge.classList.remove('bg-info', 'text-dark', 'bg-success');
                    badge.classList.add('bg-danger', 'text-white');
                } else if (totalConfigured === qtyKirim) {
                    badge.classList.remove('bg-info', 'text-dark', 'bg-danger');
                    badge.classList.add('bg-success', 'text-white');
                } else {
                    badge.classList.remove('bg-danger', 'bg-success', 'text-white');
                    badge.classList.add('bg-info', 'text-dark');
                }
                
                validateStep2NextButton();
            }

            function validateStep2NextButton() {
                const btnNext = document.getElementById('btnNext');
                if (currentStep !== 2) return;
                
                let hasError = false;
                Object.keys(itemConfigs).forEach(itemId => {
                    const configs = itemConfigs[itemId];
                    if (!configs || configs.length === 0) return;
                    
                    const qtyKirim = parseFloat(configs[0].qty_kirim) || 0;
                    let totalConfigured = 0;
                    configs.forEach(cfg => {
                        totalConfigured += (parseFloat(cfg.qty_pack) || 0) * (parseFloat(cfg.qty_per_pack) || 0);
                    });
                    
                    if (totalConfigured > qtyKirim) {
                        hasError = true;
                    }
                });
                
                btnNext.disabled = hasError;
            }

            function renderConfigs(itemId) {
                activeItemId = itemId;
                const accordion = document.getElementById('configAccordion');
                const template = document.getElementById('packagingConfigTemplate');
                accordion.innerHTML = ''; // bersihkan konten sebelumnya
                
                const configs = itemConfigs[itemId];
                if (!configs) return;
                
                // Update judul
                const firstConfig = configs[0];
                document.getElementById('configHeaderTitle').innerText = firstConfig.nama_barang || 'Konfigurasi Barang';
                
                configs.forEach((config, idx) => {
                    const clone = template.content.cloneNode(true);
                    
                    // Replace {INDEX} with idx and {NUMBER} with idx+1
                    let html = document.createElement('div');
                    html.appendChild(clone);
                    html.innerHTML = html.innerHTML.replace(/{INDEX}/g, idx).replace(/{NUMBER}/g, idx + 1);
                    
                    // Assign values
                    const wrapper = html.firstElementChild;
                    
                    wrapper.querySelector('.item-packer').value = config.packer || '';
                    wrapper.querySelector('.item-qty-pack').value = config.qty_pack || 1;
                    wrapper.querySelector('.item-qty-per-pack').value = config.qty_per_pack || 1;
                    
                    wrapper.querySelector('.item-length').value = config.length || '';
                    wrapper.querySelector('.item-width').value = config.width || '';
                    wrapper.querySelector('.item-height').value = config.height || '';
                    
                    wrapper.querySelector('.item-kb-status').value = config.kb_status || 'Include';
                    wrapper.querySelector('.item-kb-arah').value = config.kb_arah || 'Horizontal';
                    wrapper.querySelector('.item-kb-material').value = config.kb_material || 'A001';
                    
                    wrapper.querySelector('.item-pb-status').value = config.pb_status || 'Include';
                    wrapper.querySelector('.item-pb-arah').value = config.pb_arah || 'Horizontal';
                    wrapper.querySelector('.item-pb-material').value = config.pb_material || 'A001';
                    
                    wrapper.querySelector('.item-ptb-status').value = config.ptb_status || 'Tanpa Penutup';
                    wrapper.querySelector('.item-ptb-arah').value = config.ptb_arah || 'Horizontal';
                    wrapper.querySelector('.item-ptb-material').value = config.ptb_material || 'A001';
                    
                    wrapper.querySelector('.item-pa-status').value = config.pa_status || 'Include';
                    wrapper.querySelector('.item-pa-arah').value = config.pa_arah || 'Horizontal';
                    wrapper.querySelector('.item-pa-material').value = config.pa_material || 'A001';
                    
                    wrapper.querySelector('.item-pta-status').value = config.pta_status || 'Tanpa Penutup';
                    wrapper.querySelector('.item-pta-arah').value = config.pta_arah || 'Horizontal';
                    wrapper.querySelector('.item-pta-material').value = config.pta_material || 'A001';
                    
                    wrapper.querySelector('.item-jarak').value = config.jarak || '';
                    wrapper.querySelector('.item-gap-atas').value = config.gap_atas || '';
                    wrapper.querySelector('.item-gap-bawah').value = config.gap_bawah || '';
                    
                    // Hapus tombol Hapus jika cuma 1 config
                    if(configs.length === 1) {
                        const btnRemove = wrapper.querySelector('.btn-remove-packaging');
                        if(btnRemove) btnRemove.style.display = 'none';
                    }
                    
                    accordion.appendChild(wrapper);
                });
                
                // Expand the first accordion by default
                const firstCollapse = accordion.querySelector('.accordion-collapse');
                const firstButton = accordion.querySelector('.accordion-button');
                if (firstCollapse && firstButton) {
                    firstCollapse.classList.add('show');
                    firstButton.classList.remove('collapsed');
                }
            }

            // Event Delegation untuk Input pada Accordion
            document.getElementById('configAccordion').addEventListener('change', function(e) {
                if(!activeItemId) return;
                
                const input = e.target;
                const accordionBody = input.closest('.accordion-body');
                if(!accordionBody) return;
                
                const idx = accordionBody.getAttribute('data-index');
                const config = itemConfigs[activeItemId][idx];
                if(!config) return;
                
                const cls = input.classList;
                const val = input.value;
                
                if(cls.contains('item-packer')) config.packer = val;
                if(cls.contains('item-qty-pack')) config.qty_pack = val;
                if(cls.contains('item-qty-per-pack')) config.qty_per_pack = val;
                
                if(cls.contains('item-length')) config.length = val;
                if(cls.contains('item-width')) config.width = val;
                if(cls.contains('item-height')) config.height = val;
                
                if(cls.contains('item-kb-status')) config.kb_status = val;
                if(cls.contains('item-kb-arah')) config.kb_arah = val;
                if(cls.contains('item-kb-material')) config.kb_material = val;
                
                if(cls.contains('item-pb-status')) config.pb_status = val;
                if(cls.contains('item-pb-arah')) config.pb_arah = val;
                if(cls.contains('item-pb-material')) config.pb_material = val;
                
                if(cls.contains('item-ptb-status')) config.ptb_status = val;
                if(cls.contains('item-ptb-arah')) config.ptb_arah = val;
                if(cls.contains('item-ptb-material')) config.ptb_material = val;
                
                if(cls.contains('item-pa-status')) config.pa_status = val;
                if(cls.contains('item-pa-arah')) config.pa_arah = val;
                if(cls.contains('item-pa-material')) config.pa_material = val;
                
                if(cls.contains('item-pta-status')) config.pta_status = val;
                if(cls.contains('item-pta-arah')) config.pta_arah = val;
                if(cls.contains('item-pta-material')) config.pta_material = val;
                
                if(cls.contains('item-jarak')) config.jarak = val;
                if(cls.contains('item-gap-atas')) config.gap_atas = val;
                if(cls.contains('item-gap-bawah')) config.gap_bawah = val;
                
                // Update badge info di sidebar kiri saat qty berubah
                if(cls.contains('item-qty-pack') || cls.contains('item-qty-per-pack')) {
                    updateSidebarQty(activeItemId);
                }
            });
            
            // Hapus Packaging
            document.getElementById('configAccordion').addEventListener('click', function(e) {
                if(e.target.closest('.btn-remove-packaging')) {
                    if(!activeItemId) return;
                    if(!confirm('Yakin ingin menghapus packaging ini?')) return;
                    
                    const accordionBody = e.target.closest('.accordion-body');
                    const idx = accordionBody.getAttribute('data-index');
                    
                    itemConfigs[activeItemId].splice(idx, 1);
                    renderConfigs(activeItemId);
                    updateSidebarQty(activeItemId);
                }
            });

            // Tambah Packaging
            document.getElementById('btnAddPackaging').addEventListener('click', function() {
                if(!activeItemId) return;
                
                const configs = itemConfigs[activeItemId];
                const baseConfig = configs[0]; // clone basic info from first config
                
                configs.push({
                    no_barang: baseConfig.no_barang,
                    nama_barang: baseConfig.nama_barang,
                    qty_kirim: baseConfig.qty_kirim,
                    packer: '',
                    qty_pack: 1,
                    qty_per_pack: 1,
                    length: '', width: '', height: '',
                    kb_status: 'Include', kb_arah: 'Horizontal', kb_material: 'A001',
                    pb_status: 'Include', pb_arah: 'Horizontal', pb_material: 'A001',
                    ptb_status: 'Tanpa Penutup', ptb_arah: 'Horizontal', ptb_material: 'A001',
                    pa_status: 'Include', pa_arah: 'Horizontal', pa_material: 'A001',
                    pta_status: 'Tanpa Penutup', pta_arah: 'Horizontal', pta_material: 'A001',
                    jarak: '', gap_atas: '', gap_bawah: ''
                });
                
                renderConfigs(activeItemId);
                updateSidebarQty(activeItemId);
            });
            
            function getStepName(step) {
                if (step === 1) return 'Pilih Barang';
                if (step === 2) return 'Konfigurasi';
                if (step === 3) return 'Review';
                return '';
            }

            function getStepIcon(step) {
                if (step === 1) return '<i class="fa-solid fa-magnifying-glass"></i>';
                if (step === 2) return '<i class="fa-solid fa-box-open"></i>';
                if (step === 3) return '<i class="fa-solid fa-clipboard-check"></i>';
                return '';
            }

            function buildStepNavigation(step, state) {
                const icon = state === 'completed'
                    ? '<i class="fa-solid fa-check"></i>'
                    : getStepIcon(step);

                return `
                    <div class="step-circle">${icon}</div>
                    <span class="step-label">${getStepName(step)}</span>
                `;
            }

            btnNext.addEventListener('click', () => {
                if (currentStep === 1) {
                    const dropdown = document.getElementById('itemDropdown');
                    if (!dropdown || !dropdown.value) {
                        alert("Silakan pilih barang untuk dipackaging.");
                        return;
                    }
                    
                    const qtyInput = document.getElementById('itemQtyKirim');
                    const inputQty = parseFloat(qtyInput?.value || 0);
                    const selectedOption = dropdown.options[dropdown.selectedIndex];
                    const maxQty = parseFloat(selectedOption.getAttribute('data-qty-remaining') || 999999);
                    
                    if (isNaN(inputQty) || inputQty <= 0 || inputQty > maxQty) {
                        alert(`Qty pengiriman untuk Part No ${dropdown.value} tidak valid atau melebihi Sisa Kirim (${maxQty}).`);
                        return;
                    }
                }
                
                if (currentStep === 2) {
                    // Validasi Step 2: Konfigurasi Qty vs Qty Kirim
                    let validStep2 = true;
                    Object.keys(itemConfigs).forEach(itemId => {
                        const configs = itemConfigs[itemId];
                        if (!configs || configs.length === 0) return;
                        
                        const qtyKirim = parseFloat(configs[0].qty_kirim) || 0;
                        let totalConfigured = 0;
                        configs.forEach(cfg => {
                            totalConfigured += (parseFloat(cfg.qty_pack) || 0) * (parseFloat(cfg.qty_per_pack) || 0);
                        });
                        
                        if (totalConfigured > qtyKirim) {
                            alert(`Gagal: Total konfigurasi Qty untuk barang ${configs[0].no_barang} (${totalConfigured}) melebihi Qty yang akan dikirim (${qtyKirim}). Silakan perbaiki konfigurasi Qty Packaging.`);
                            validStep2 = false;
                        }
                    });
                    if (!validStep2) return;
                }
                
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateWizard();
                }
            });

            btnPrev.addEventListener('click', () => {
                if (currentStep > 1) {
                    currentStep--;
                    updateWizard();
                }
            });
            
            // Search API Logic
            let currentSOData = null;
            btnSearchSO.addEventListener('click', async () => {
                const query = searchSOInput.value.trim();
                if (!query) {
                    alert("Masukkan Nomor SO untuk ditarik datanya.");
                    return;
                }
                
                if (typeof window.setSOSearchLoading === 'function') {
                    window.setSOSearchLoading(true);
                } else {
                    btnSearchSO.disabled = true;
                    btnSearchSO.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`;
                }
                
                if (typeof window.resetSOHeaderInfo === 'function') {
                    window.resetSOHeaderInfo();
                } else {
                    infoNoSO.innerText = '-';
                    infoCustomer.innerText = '-';
                    infoDeliveryDate.innerText = '-';
                    infoShipto.innerText = '-';
                }
                
                if (checkAllItems) {
                    checkAllItems.disabled = true;
                    checkAllItems.checked = false;
                }
                
                currentSOData = null;
                itemConfigs = {};
                
                try {
                    const response = await fetch(`{{ url('/api/packaging/search-so') }}?q=${encodeURIComponent(query)}`);
                    const result = await response.json();
                    
                    if (result.data && result.data.length > 0) {
                        currentSOData = result.data;
                        const headerData = result.data[0];
                        
                        infoNoSO.innerText = headerData.no_so || '-';
                        infoCustomer.innerText = headerData.nama_pelanggan || '-';
                        infoDeliveryDate.innerText = headerData.tgl_estimasi || headerData.tgl_so || '-';
                        infoShipto.innerText = headerData.shipto || '-';
                        
                        // Gunakan fungsi dari step1.blade.php jika ada
                        if (typeof window.renderSOItems === 'function') {
                            window.renderSOItems(result.data);
                        } else {
                            // Fallback jika tidak ada
                            tableBodySO.innerHTML = '';
                            result.data.forEach((item, index) => {
                                const tr = document.createElement('tr');
                                tr.className = 'border-bottom border-secondary border-opacity-10';
                                tr.innerHTML = `
                                    <td class="text-center py-3">
                                        <div class="form-check d-flex justify-content-center m-0">
                                            <input class="form-check-input border-secondary shadow-sm item-checkbox" type="checkbox" value="${item.no_barang}" id="item_${index}" 
                                                data-name="${item.deskripsi_barang ? item.deskripsi_barang.replace(/"/g, '&quot;') : ''}" 
                                                data-so="${item.no_so}" data-uom="${item.uom}">
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="font-monospace text-primary fw-bold" style="word-break: break-all;">${item.no_barang}</div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-semibold text-body" style="font-size:0.85rem; line-height:1.4;">${item.deskripsi_barang}</div>
                                    </td>
                                    <td class="text-center py-3"><span class="badge bg-secondary bg-opacity-10 text-body border border-secondary border-opacity-25 px-2 py-1 shadow-sm">${item.qty} ${item.uom}</span></td>
                                    <td class="text-center py-3"><span class="badge bg-warning text-dark px-2 py-1 shadow-sm border border-warning border-opacity-25">${item.sisa_kirim} ${item.uom}</span></td>
                                    <td class="py-3">
                                        <div class="position-relative">
                                            <input type="number" class="form-control premium-input text-body item-qty w-100" style="padding-right: 2.5rem; text-align: center;" placeholder="0" min="1" max="${item.sisa_kirim}" disabled>
                                            <span class="position-absolute text-muted fw-bold" style="top: 50%; transform: translateY(-50%); right: 0.75rem; pointer-events: none; font-size: 0.8rem;">${item.uom}</span>
                                        </div>
                                    </td>
                                `;
                                tableBodySO.appendChild(tr);
                            });
                            checkAllItems.disabled = false;
                            attachCheckboxListeners();
                        }
                    } else {
                        if (typeof window.showSOEmptyState === 'function') {
                            window.showSOEmptyState('Data Tidak Ditemukan', 'Tidak ada data SO dengan nomor tersebut.');
                        } else {
                            tableBodySO.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-danger"><i class="fa-solid fa-triangle-exclamation fa-2x mb-3"></i><br>Data SO tidak ditemukan.</td></tr>`;
                        }
                    }
                } catch (error) {
                    if (typeof window.showSOEmptyState === 'function') {
                        window.showSOEmptyState('Error Jaringan', 'Terjadi kesalahan saat memuat data dari API.');
                    } else {
                        tableBodySO.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-danger"><i class="fa-solid fa-circle-xmark fa-2x mb-3"></i><br>Terjadi kesalahan saat memuat data dari API.</td></tr>`;
                    }
                } finally {
                    if (typeof window.setSOSearchLoading === 'function') {
                        window.setSOSearchLoading(false);
                    } else {
                        btnSearchSO.disabled = false;
                        btnSearchSO.innerHTML = `<i class="fa-solid fa-cloud-arrow-down me-1"></i> Tarik Data`;
                    }
                }
            });
            
            function attachCheckboxListeners() {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const qtyInput = this.closest('tr').querySelector('.item-qty');
                        if(this.checked) {
                            qtyInput.removeAttribute('disabled');
                            if(!qtyInput.value) qtyInput.value = 1; 
                        } else {
                            qtyInput.setAttribute('disabled', 'disabled');
                            qtyInput.value = '';
                        }
                        
                        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                        const someChecked = Array.from(checkboxes).some(cb => cb.checked);
                        checkAllItems.checked = allChecked;
                        checkAllItems.indeterminate = someChecked && !allChecked;
                    });
                });
            }
            
            checkAllItems.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                    cb.dispatchEvent(new Event('change'));
                });
            });
            
            function updateReviewData() {
                const reviewContainer = document.getElementById('step3ProductList');
                if(!reviewContainer) return;
                reviewContainer.innerHTML = '';
                
                let selectedSO = infoNoSO.innerText;
                const reviewSONumber = document.getElementById('reviewSONumber');
                if(reviewSONumber) reviewSONumber.innerText = selectedSO;
                
                const step3NoSO = document.getElementById('step3NoSO');
                if(step3NoSO) {
                    step3NoSO.innerText = infoNoSO.innerText;
                    document.getElementById('step3Customer').innerText = infoCustomer.innerText;
                    document.getElementById('step3DeliveryDate').innerText = infoDeliveryDate.innerText;
                    document.getElementById('step3Shipto').innerText = infoShipto.innerText;
                }
                
                const step3ConfigWrapper = document.getElementById('step3ConfigContainer');
                if(Object.keys(itemConfigs).length === 0) {
                    reviewContainer.innerHTML = '<div class="text-center py-4 text-danger small border rounded"><i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada barang yang dikonfigurasi.</div>';
                    if(step3ConfigWrapper) step3ConfigWrapper.style.display = 'none';
                    return;
                }
                
                if(step3ConfigWrapper) step3ConfigWrapper.style.display = 'block';
                
                // Render List Group untuk Step 3
                Object.keys(itemConfigs).forEach(itemId => {
                    const configs = itemConfigs[itemId];
                    if(!configs || configs.length === 0) return;
                    
                    const baseConfig = configs[0];
                    let totalConfigured = 0;
                    configs.forEach(cfg => {
                        totalConfigured += (parseFloat(cfg.qty_pack) || 0) * (parseFloat(cfg.qty_per_pack) || 0);
                    });
                    
                    const a = document.createElement('a');
                    a.href = '#';
                    a.className = 'list-group-item list-group-item-action p-3 mb-2 rounded border border-secondary border-opacity-10';
                    a.innerHTML = `<div class="fw-bold text-primary font-monospace" style="font-size:0.85rem; word-break: break-all;">${baseConfig.no_barang}</div>
                                   <div class="small text-muted mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">${baseConfig.nama_barang}</div>
                                   <div class="d-flex justify-content-between align-items-center">
                                       <span class="badge bg-secondary bg-opacity-10 text-body border shadow-sm">Kirim: ${baseConfig.qty_kirim}</span>
                                       <span class="badge ${totalConfigured > parseFloat(baseConfig.qty_kirim) ? 'bg-danger text-white' : 'bg-success text-white'} shadow-sm">Config: ${totalConfigured}</span>
                                   </div>`;
                    
                    a.addEventListener('click', (e) => {
                        e.preventDefault();
                        document.querySelectorAll('#step3ProductList .list-group-item').forEach(el => {
                            el.classList.remove('active', 'bg-primary', 'bg-opacity-10', 'border-primary');
                        });
                        a.classList.add('active', 'bg-primary', 'bg-opacity-10', 'border-primary');
                        renderReviewConfigs(itemId);
                    });
                    
                    reviewContainer.appendChild(a);
                });
                
                // Otomatis pilih item pertama
                if(reviewContainer.firstChild) {
                    reviewContainer.firstChild.click();
                }
            }
            
            function renderReviewConfigs(itemId) {
                const accordion = document.getElementById('reviewConfigAccordion');
                if(!accordion) return;
                accordion.innerHTML = '';
                
                const configs = itemConfigs[itemId];
                if (!configs) return;
                
                document.getElementById('reviewConfigHeaderTitle').innerText = configs[0].nama_barang || 'Review Konfigurasi';
                
                configs.forEach((config, idx) => {
                    const qtyConfig = `${config.qty_pack} x ${config.qty_per_pack}`;
                    const accordionId = `reviewAcc_${itemId}_${idx}`;
                    
                    const html = `
                    <div class="accordion-item border-0 mb-3 bg-transparent shadow-sm rounded-4 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button ${idx !== 0 ? 'collapsed' : ''} fw-bold bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#${accordionId}">
                                <div class="d-flex align-items-center w-100">
                                    <span class="me-3 badge bg-primary">Pkg ${idx + 1}</span>
                                    <span class="text-body flex-grow-1" style="font-size:0.95rem;">Dimensi: ${config.length || 0} x ${config.width || 0} x ${config.height || 0} mm</span>
                                    <span class="badge bg-secondary bg-opacity-10 text-body border shadow-sm me-3">Qty: ${qtyConfig}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="${accordionId}" class="accordion-collapse collapse ${idx === 0 ? 'show' : ''}" data-bs-parent="#reviewConfigAccordion">
                            <div class="accordion-body bg-white border-top border-secondary border-opacity-10 p-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-3 border border-secondary border-opacity-10 bg-light">
                                            <div class="small fw-bold text-muted mb-2 text-uppercase">Kaki Balok</div>
                                            <div class="small mb-1"><span class="text-secondary">Status:</span> ${config.kb_status}</div>
                                            <div class="small mb-1"><span class="text-secondary">Arah:</span> ${config.kb_arah}</div>
                                            <div class="small"><span class="text-secondary">Material:</span> ${config.kb_material}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-3 border border-secondary border-opacity-10 bg-light">
                                            <div class="small fw-bold text-muted mb-2 text-uppercase">Penyanggah Bawah</div>
                                            <div class="small mb-1"><span class="text-secondary">Status:</span> ${config.pb_status}</div>
                                            <div class="small mb-1"><span class="text-secondary">Arah:</span> ${config.pb_arah}</div>
                                            <div class="small"><span class="text-secondary">Material:</span> ${config.pb_material}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-3 border border-secondary border-opacity-10 bg-light">
                                            <div class="small fw-bold text-muted mb-2 text-uppercase">Penutup Bawah</div>
                                            <div class="small mb-1"><span class="text-secondary">Status:</span> ${config.ptb_status}</div>
                                            <div class="small mb-1"><span class="text-secondary">Arah:</span> ${config.ptb_arah}</div>
                                            <div class="small"><span class="text-secondary">Material:</span> ${config.ptb_material}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-3 border border-secondary border-opacity-10 bg-light">
                                            <div class="small fw-bold text-muted mb-2 text-uppercase">Penyanggah Atas</div>
                                            <div class="small mb-1"><span class="text-secondary">Status:</span> ${config.pa_status}</div>
                                            <div class="small mb-1"><span class="text-secondary">Arah:</span> ${config.pa_arah}</div>
                                            <div class="small"><span class="text-secondary">Material:</span> ${config.pa_material}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-3 border border-secondary border-opacity-10 bg-light">
                                            <div class="small fw-bold text-muted mb-2 text-uppercase">Penutup Atas</div>
                                            <div class="small mb-1"><span class="text-secondary">Status:</span> ${config.pta_status}</div>
                                            <div class="small mb-1"><span class="text-secondary">Arah:</span> ${config.pta_arah}</div>
                                            <div class="small"><span class="text-secondary">Material:</span> ${config.pta_material}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-3 border border-secondary border-opacity-10 bg-light h-100">
                                            <div class="small fw-bold text-muted mb-2 text-uppercase">Lainnya</div>
                                            <div class="small mb-1"><span class="text-secondary">Jarak Antar:</span> ${config.jarak || '-'} mm</div>
                                            <div class="small mb-1"><span class="text-secondary">Gap Atas:</span> ${config.gap_atas || '-'} mm</div>
                                            <div class="small"><span class="text-secondary">Gap Bawah:</span> ${config.gap_bawah || '-'} mm</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    accordion.insertAdjacentHTML('beforeend', html);
                });
            }

            // Form Submission Logic
            let confirmModal = null;
            
            document.getElementById('wizardForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                if(!currentSOData) {
                    alert("Data SO belum ditarik.");
                    return;
                }
                
                // Tampilkan Modal Konfirmasi
                if(!confirmModal) {
                    confirmModal = new bootstrap.Modal(document.getElementById('confirmSaveModal'));
                }
                confirmModal.show();
            });
            
            document.getElementById('btnConfirmSave').addEventListener('click', async function() {
                const btnConfirm = this;
                const originalText = btnConfirm.innerHTML;
                
                const headerData = currentSOData[0];
                
                // Flatten array of arrays menjadi 1D array dan sesuaikan key untuk validasi Controller
                const itemsToSubmit = [];
                Object.values(itemConfigs).forEach(configsArray => {
                    configsArray.forEach(config => {
                        itemsToSubmit.push({
                            ...config,
                            no_product: config.no_barang,
                            desc_product: config.nama_barang,
                            qty_barang_dikirim: (parseInt(config.qty_pack) || 0) * (parseInt(config.qty_per_pack) || 0)
                        });
                    });
                });
                
                if (itemsToSubmit.length === 0) {
                    alert("Tidak ada barang yang dikonfigurasi.");
                    confirmModal.hide();
                    return;
                }
                
                btnConfirm.disabled = true;
                btnConfirm.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';

                // Build Payload
                const payload = {
                    _token: document.querySelector('input[name="_token"]').value,
                    no_so: headerData?.no_so || (isEditMode ? preloadedJob.no_so : ''),
                    customer: headerData?.nama_pelanggan || (isEditMode ? preloadedJob.customer : ''),
                    date_delivery: headerData?.tgl_estimasi || headerData?.tgl_so || (isEditMode && preloadedJob.date_delivery ? preloadedJob.date_delivery.substring(0,10) : ''),
                    address: headerData?.shipto || (isEditMode ? preloadedJob.address : ''),
                    packType: "multiple",
                    raw_api_data: currentSOData,
                    items: itemsToSubmit
                };

                try {
                    const actionUrl = document.getElementById('wizardForm').action;
                    let fetchOptions = {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    };
                    
                    if(document.querySelector('input[name="_method"]')) {
                        fetchOptions.method = 'POST'; // Keep POST but Laravel handles _method in body or URL
                    }
                    
                    const response = await fetch(actionUrl, fetchOptions);

                    
                    const result = await response.json();
                    
                    if(response.ok && result.status === 'success') {
                        confirmModal.hide();
                        alert(result.message);
                        window.location.href = result.redirect;
                    } else {
                        alert(result.message || 'Terjadi kesalahan saat menyimpan data.');
                        btnConfirm.disabled = false;
                        btnConfirm.innerHTML = originalText;
                    }
                } catch(error) {
                    console.error('Error submitting form:', error);
                    alert('Terjadi kesalahan jaringan atau server.');
                    btnConfirm.disabled = false;
                    btnConfirm.innerHTML = originalText;
                }
            });
            
            // PRELOAD DATA JIKA MODE EDIT
            let isEditMode = {{ $isEdit ? 'true' : 'false' }};
            let preloadedJob = {!! isset($packagingJob) ? json_encode($packagingJob) : 'null' !!};
            
            if (isEditMode && preloadedJob) {
                // Initialize Header
                infoNoSO.innerText = preloadedJob.no_so || '-';
                infoCustomer.innerText = preloadedJob.customer || '-';
                infoDeliveryDate.innerText = preloadedJob.date_delivery ? preloadedJob.date_delivery.substring(0,10) : '-';
                infoShipto.innerText = preloadedJob.address || '-';
                
                searchSOInput.value = preloadedJob.no_so || '';
                
                // Initialize currentSOData (the master items)
                currentSOData = preloadedJob.daftar_iso_item_json || [];
                
                // Render tableBodySO (Step 1)
                tableBodySO.innerHTML = '';
                if(currentSOData.length > 0) {
                    currentSOData.forEach((item, index) => {
                        const matchingDetails = preloadedJob.details ? preloadedJob.details.filter(d => (d.no_product === item.no_barang || d.no_product === item.no_product)) : [];
                        const isChecked = matchingDetails.length > 0;
                        const qtyKirim = isChecked ? matchingDetails[0].qty_barang_dikirim : 1;
                        
                        const tr = document.createElement('tr');
                        tr.className = 'border-bottom border-secondary border-opacity-10';
                        tr.innerHTML = `
                            <td class="text-center py-3">
                                <div class="form-check d-flex justify-content-center m-0">
                                    <input class="form-check-input border-secondary shadow-sm item-checkbox" type="checkbox" value="${item.no_barang || item.no_product}" id="item_${index}" 
                                        data-name="${item.deskripsi_barang ? item.deskripsi_barang.replace(/"/g, '&quot;') : (item.desc_product ? item.desc_product.replace(/"/g, '&quot;') : '')}" 
                                        data-so="${item.no_so}" data-uom="${item.uom || 'Unit'}" ${isChecked ? 'checked' : ''}>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="font-monospace text-primary fw-bold" style="word-break: break-all;">${item.no_barang || item.no_product}</div>
                            </td>
                            <td class="py-3">
                                <div class="fw-semibold text-body" style="font-size:0.85rem; line-height:1.4;">${item.deskripsi_barang || item.desc_product}</div>
                            </td>
                            <td class="text-center py-3"><span class="badge bg-secondary bg-opacity-10 text-body border border-secondary border-opacity-25 px-2 py-1 shadow-sm">${item.qty || qtyKirim} ${item.uom || 'Unit'}</span></td>
                            <td class="text-center py-3"><span class="badge bg-warning text-dark px-2 py-1 shadow-sm border border-warning border-opacity-25">${item.sisa_kirim || qtyKirim} ${item.uom || 'Unit'}</span></td>
                            <td class="py-3">
                                <div class="position-relative">
                                    <input type="number" class="form-control premium-input text-body item-qty w-100" style="padding-right: 2.5rem; text-align: center;" placeholder="0" min="1" max="${item.sisa_kirim || 9999}" ${isChecked ? '' : 'disabled'} value="${isChecked ? qtyKirim : ''}">
                                    <span class="position-absolute text-muted fw-bold" style="top: 50%; transform: translateY(-50%); right: 0.75rem; pointer-events: none; font-size: 0.8rem;">${item.uom || 'Unit'}</span>
                                </div>
                            </td>
                        `;
                        tableBodySO.appendChild(tr);
                        
                        if(isChecked) {
                            const itemId = 'prod_' + index;
                            itemConfigs[itemId] = matchingDetails.map(detail => {
                                return {
                                    no_barang: detail.no_product,
                                    nama_barang: detail.desc_product,
                                    qty_kirim: detail.qty_barang_dikirim,
                                    packer: detail.packer_id,
                                    qty_pack: detail.qty_packaging,
                                    qty_per_pack: detail.qty_product_per_packaging,
                                    length: detail.panjang, width: detail.lebar, height: detail.tinggi,
                                    kb_status: detail.konfigurasi_bawah?.kaki_balok?.status || 'Include',
                                    kb_arah: detail.konfigurasi_bawah?.kaki_balok?.arah || 'Horizontal',
                                    kb_material: detail.konfigurasi_bawah?.kaki_balok?.material || 'A001',
                                    pb_status: detail.konfigurasi_bawah?.penyanggah?.status || 'Include',
                                    pb_arah: detail.konfigurasi_bawah?.penyanggah?.arah || 'Horizontal',
                                    pb_material: detail.konfigurasi_bawah?.penyanggah?.material || 'A001',
                                    ptb_status: detail.konfigurasi_bawah?.penutup?.status || 'Tanpa Penutup',
                                    ptb_arah: detail.konfigurasi_bawah?.penutup?.arah || 'Horizontal',
                                    ptb_material: detail.konfigurasi_bawah?.penutup?.material || 'A001',
                                    pa_status: detail.konfigurasi_atas?.penyanggah?.status || 'Include',
                                    pa_arah: detail.konfigurasi_atas?.penyanggah?.arah || 'Horizontal',
                                    pa_material: detail.konfigurasi_atas?.penyanggah?.material || 'A001',
                                    pta_status: detail.konfigurasi_atas?.penutup?.status || 'Tanpa Penutup',
                                    pta_arah: detail.konfigurasi_atas?.penutup?.arah || 'Horizontal',
                                    pta_material: detail.konfigurasi_atas?.penutup?.material || 'A001',
                                    jarak: detail.konfigurasi_bawah?.jarak_penyanggah || '',
                                    gap_atas: detail.konfigurasi_atas?.gap_atas || '',
                                    gap_bawah: detail.konfigurasi_bawah?.gap_bawah || ''
                                };
                            });
                        }
                    });
                    
                    checkAllItems.disabled = false;
                    attachCheckboxListeners();
                    
                    // Add _method: 'PUT' to payload if missing for update via POST
                    if (document.querySelector('input[name="_method"]')) {
                        // handled above, fetch payload will still work as long as actionUrl is correct
                    }
                }
                
                // Langsung ke step 3 (Review) untuk Edit
                currentStep = 3;
                initStep2();
                updateWizard();
            }
        });
    </script>
