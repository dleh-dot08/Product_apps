
    document.addEventListener('DOMContentLoaded', () => {
        'use strict';

        /* =====================================================
           ELEMENT REFERENCES
           ===================================================== */
        const modalElement = document.getElementById('productSetupModal');
        const searchInput = document.getElementById('searchSO');
        const searchButton = document.getElementById('btnSearchSO');
        const itemDropdown = document.getElementById('itemDropdown');
        const inputQtyItem = document.getElementById('inputQtyItem');
        const btnAddItemSO = document.getElementById('btnAddItemSO');
        const nextButton = document.getElementById('btn_next_step');
        const previousButton = document.getElementById('btn_prev_step');
        const statusText = document.getElementById('productSetupStatus');
        const tableBody = document.getElementById('productSelectionBody');

        let currentStep = 1;
        window.selectedItemsList = [];
        let selectedItemIndex = -1;

        /* =====================================================
           HELPERS
           ===================================================== */
        const setStep = (step) => {
            currentStep = Math.max(1, Math.min(3, Number(step)));

            document.querySelectorAll('#productSetupModal [data-setup-step]').forEach((panel) => {
                panel.classList.toggle(
                    'is-active',
                    Number(panel.dataset.setupStep) === currentStep
                );
            });

            modalElement?.querySelectorAll('[data-wizard-indicator]').forEach((indicator) => {
                const indicatorStep = Number(indicator.dataset.wizardIndicator);
                indicator.classList.toggle('is-active', indicatorStep === currentStep);
                indicator.classList.toggle('is-complete', indicatorStep < currentStep);
            });

            modalElement?.querySelectorAll('[data-wizard-line]').forEach((line) => {
                const lineStep = Number(line.dataset.wizardLine);
                line.classList.toggle('is-complete', currentStep > lineStep);
            });

            previousButton?.classList.toggle('d-none', currentStep === 1);

            if (nextButton) {
                nextButton.innerHTML = currentStep < 3
                    ? '<span>Next Step</span><i class="fa-solid fa-arrow-right"></i>'
                    : '<i class="fa-solid fa-floppy-disk"></i><span>Save Configuration</span>';
            }

            if (statusText) {
                const messages = {
                    1: 'Langkah 1 dari 3: Pilih produk yang akan dipacking.',
                    2: 'Langkah 2 dari 3: Pilih type packing dan bahan penutup.',
                    3: 'Langkah 3 dari 3: Lengkapi konfigurasi packaging.',
                };
                statusText.textContent = messages[currentStep];
            }

            modalElement?.querySelector('.ps-modal-body')?.scrollTo({ top: 0, behavior: 'smooth' });
        };

        const getSelectedItemData = () => {
            const option = itemDropdown?.selectedOptions?.[0];

            if (!option || !option.value) {
                return null;
            }

            return {
                id: option.value,
                partNumber: option.dataset.itemNo || '-',
                description: option.dataset.itemDesc || '-',
                customer:
                    option.dataset.customer ||
                    window.currentFetchedCustomer ||
                    '-',
                qtyOrder: Number(option.dataset.qtyOrder || 0),
                qtyRemaining: Number(option.dataset.qtyRemaining || 0),
            };
        };

        const renderSelectedItemsTable = () => {
            tableBody.innerHTML = '';
            window.selectedItemsList.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.soNumber}</td>
                    <td>${item.customer}</td>
                    <td>${item.itemNumber}</td>
                    <td>${item.description}</td>
                    <td class="text-center fw-bold align-middle">
                        <input type="number" class="form-control form-control-sm text-center row-qty" value="${item.qty}" min="1" data-index="${index}" style="width: 80px; margin: 0 auto;">
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" data-index="${index}" style="padding: 2px 6px;">
                            <i class="fa-solid fa-minus"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        };

        tableBody?.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-remove-item');
            if (btn) {
                const index = parseInt(btn.dataset.index, 10);
                window.selectedItemsList.splice(index, 1);
                renderSelectedItemsTable();
            }
        });

        tableBody?.addEventListener('input', (e) => {
            if (e.target.classList.contains('row-qty')) {
                const index = parseInt(e.target.dataset.index, 10);
                window.selectedItemsList[index].qty = parseInt(e.target.value, 10) || 1;
            }
        });

        /* =====================================================
           PUBLIC SO FUNCTIONS
           ===================================================== */
        window.setSOSearchLoading = (isLoading) => {
            if (!searchButton) return;

            searchButton.disabled = Boolean(isLoading);

            searchButton.innerHTML = isLoading
                ? `
                    <span class="ps-spinner"></span>
                    <span>Menarik Data...</span>
                `
                : `
                    <i class="fa-solid fa-cloud-arrow-down"></i>
                    <span>Tarik Data</span>
                `;
        };

        const escapeSOHtml = (str) => {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        let rawFetchedItems = [];

        window.renderSOItems = (items) => {
            if (!itemDropdown) return;

            if (!Array.isArray(items) || items.length === 0) {
                itemDropdown.innerHTML = '<option value="">Pilih Barang...</option>';
                itemDropdown.disabled = true;
                return;
            }

            rawFetchedItems = items;

            const options = items.map((item, index) => {
                const itemId = item.id ?? index;
                const itemNumber =
                    item.no_barang ??
                    item.item_no ??
                    item.part_no ??
                    '-';

                const description =
                    item.deskripsi_barang ??
                    item.description ??
                    item.item_description ??
                    '-';

                const customer = String(
                    item.nama_pelanggan ??
                    item.nama_customer ??
                    item.customer_name ??
                    item.nm_customer ??
                    item.customer ??
                    window.currentFetchedCustomer ??
                    '-'
                ).trim() || '-';

                const qtyOrder = Number(item.qty ?? item.qty_order ?? 0);
                const qtyRemaining = Number(item.sisa_kirim ?? item.qty_remaining ?? 0);

                const fullText = `${itemNumber} - ${description}`;

                return `
                    <option
                        value="${escapeSOHtml(itemId)}"
                        data-item-no="${escapeSOHtml(itemNumber)}"
                        data-item-desc="${escapeSOHtml(description)}"
                        data-customer="${escapeSOHtml(customer)}"
                        data-qty-order="${qtyOrder}"
                        data-qty-remaining="${qtyRemaining}"
                        data-short-text="${escapeSOHtml(itemNumber)}"
                        data-full-text="${escapeSOHtml(fullText)}"
                    >
                        ${escapeSOHtml(fullText)}
                    </option>
                `;
            }).join('');

            itemDropdown.innerHTML = `
                <option value="">Pilih Barang...</option>
                ${options}
            `;
            itemDropdown.disabled = false;
        };

        itemDropdown?.addEventListener('change', () => {
            const item = getSelectedItemData();
            if (item && inputQtyItem) {
                // Default input qty to remaining qty or 1
                inputQtyItem.max = item.qtyRemaining > 0 ? item.qtyRemaining : item.qtyOrder;
                inputQtyItem.value = inputQtyItem.max;
            }
        });

        btnAddItemSO?.addEventListener('click', () => {
            const item = getSelectedItemData();
            if (!item) {
                alert('Silakan pilih barang dari dropdown terlebih dahulu.');
                return;
            }

            const qty = parseInt(inputQtyItem?.value) || 1;
            
            // Check if item already exists in table
            const exists = window.selectedItemsList.find(i => i.itemNumber === item.partNumber);
            if (exists) {
                alert('Barang ini sudah ada di dalam tabel.');
                return;
            }

            // Push to selectedItemsList
            window.selectedItemsList.push({
                soNumber: searchInput?.value?.trim() || '-',
                itemNumber: item.partNumber,
                description: item.description,
                customer: item.customer || window.currentFetchedCustomer || '-',
                qty: qty
            });

            // Re-render
            renderSelectedItemsTable();

            // Reset selection
            itemDropdown.value = '';
            if (inputQtyItem) inputQtyItem.value = '';
        });

        /* =====================================================
           STEP 2 — PACKING TYPE
           ===================================================== */
        const packingTypeLabels = {
            Box: 'Box Kayu',
            Palet: 'Palet Kayu',
            Peti: 'Peti Kayu',
            Kerangka: 'Kerangka Kayu',
        };

        const packingTypeDescriptions = {
            Box: 'Kotak kayu rapat yang menutup seluruh bagian barang.',
            Palet: 'Alas datar untuk menumpuk barang agar mudah dipindahkan menggunakan forklift.',
            Peti: 'Kotak kayu yang menutup sebagian bagian barang.',
            Kerangka: 'Bingkai kayu terbuka yang memberikan perlindungan struktur pada barang.',
        };

        // ==========================================
        // UI & Event Binding
        // ==========================================
        const syncPackingTypeSelection = () => {
            const packingInput = document.querySelector('input[name="packing_type"]:checked');
            let coverInputs = document.querySelectorAll('input[name="cover_material"]');
            const addMatInput = document.querySelector('input[name="additional_mat"]:checked');
            const terpalContainer = document.getElementById('terpal_material_container');

            const packingValue = packingInput ? packingInput.value : '';
            
            // Handle Peti Kayu disabling Triplek in Step 2
            if (coverInputs.length > 0) {
                const triplekInput = Array.from(coverInputs).find(el => el.value === 'Triplek');
                if (triplekInput) {
                    if (packingValue === 'Peti') {
                        triplekInput.disabled = true;
                        triplekInput.closest('.pt-cover-option').classList.add('is-disabled');
                        if (triplekInput.checked) {
                            triplekInput.checked = false;
                            const papanInput = Array.from(coverInputs).find(el => el.value === 'Papan');
                            if (papanInput) {
                                papanInput.checked = true;
                                coverValue = 'Papan';
                            }
                        }
                    } else {
                        if (packingValue !== 'Box Carton') {
                            triplekInput.disabled = false;
                            triplekInput.closest('.pt-cover-option').classList.remove('is-disabled');
                        }
                    }
                }
            }

            const summaryPacking = document.getElementById('summary_packing_type');
            if (summaryPacking) {
                summaryPacking.textContent = packingValue + (coverValue ? ` + ${coverValue}` : '');
            }
            
            const summaryAddMat = document.getElementById('selectedAdditionalMatText');
            if (summaryAddMat) {
                summaryAddMat.textContent = addMatValue;
            }
            
            // Logic for Konfigurasi Atas & Bawah
            const s2_ptb_status = document.getElementById('s2_ptb_status');
            const s2_pta_status = document.getElementById('s2_pta_status');
            const topPanel = document.querySelector('.s2-panel.s2-top');
            const s2_pa_status = document.getElementById('s2_pa_status');

            // Helper to handle select state
            const setSelectState = (select, allowedValues, forcedValue) => {
                if (!select) return;
                let valueChanged = false;

                Array.from(select.options).forEach(opt => {
                    if (allowedValues.includes(opt.value) || opt.value === 'Tanpa Penutup') {
                        opt.hidden = false;
                        opt.disabled = false;
                    } else {
                        opt.hidden = true;
                        opt.disabled = true;
                    }
                });
                
                if (forcedValue) {
                    if (select.value !== forcedValue) {
                        select.value = forcedValue;
                        valueChanged = true;
                    }
                    select.disabled = true; // prevent user change
                    select.style.backgroundColor = 'var(--s2-soft, #f8fafc)'; // visual cue
                } else {
                    select.disabled = false;
                    select.style.backgroundColor = '';
                    if (!allowedValues.includes(select.value) && select.value !== 'Tanpa Penutup') {
                        select.value = allowedValues[0];
                        valueChanged = true;
                    }
                }

                if (valueChanged) {
                    select.dispatchEvent(new Event('change'));
                }
            };

            // ==========================================
            // KOMPLEKSITAS LOGIKA PACKING & BAHAN PENUTUP
            // 1. Box Kayu: Papan -> Atas & Bawah otomatis Papan Full. Triplek -> Atas & Bawah otomatis Tripleks.
            // 2. Palet Kayu: Panel Atas disembunyikan sepenuhnya (Exclude). Bawah bisa Papan Full/Setengah atau Tripleks.
            // 3. Peti Kayu: Triplek di Step 2 didisable (hanya Papan). Atas & Bawah otomatis Papan Setengah.
            // 4. Kerangka Kayu: Panel Atas TAMPIL namun Penutup Atas dikunci ke "Tanpa Penutup". Bawah bisa Papan Full/Setengah atau Tripleks.
            // ==========================================
            
            // 1. Show/Hide Konfigurasi Atas
            if (packingValue === 'Palet') {
                if (topPanel) topPanel.style.display = 'none';
                if (s2_pta_status && s2_pta_status.value !== 'Tanpa Penutup') {
                    s2_pta_status.value = 'Tanpa Penutup';
                    s2_pta_status.dispatchEvent(new Event('change'));
                }
                if (s2_pa_status && s2_pa_status.value !== 'Exclude') {
                    s2_pa_status.value = 'Exclude';
                    s2_pa_status.dispatchEvent(new Event('change'));
                }
            } else {
                if (topPanel) topPanel.style.display = '';
                if (s2_pa_status && s2_pa_status.value === 'Exclude') {
                    s2_pa_status.value = 'Include';
                    s2_pa_status.dispatchEvent(new Event('change'));
                }
            }

            // 2. Options per combination
            if (packingValue === 'Box') {
                if (coverValue === 'Papan') {
                    setSelectState(s2_pta_status, ['Papan Full'], 'Papan Full');
                    setSelectState(s2_ptb_status, ['Papan Full'], 'Papan Full');
                } else if (coverValue === 'Triplek') {
                    setSelectState(s2_pta_status, ['Tripleks'], 'Tripleks');
                    setSelectState(s2_ptb_status, ['Tripleks'], 'Tripleks');
                }
            } else if (packingValue === 'Peti') {
                setSelectState(s2_pta_status, ['Papan Setengah'], 'Papan Setengah');
                setSelectState(s2_ptb_status, ['Papan Setengah'], 'Papan Setengah');
            } else if (packingValue === 'Palet' || packingValue === 'Kerangka') {
                if (packingValue === 'Kerangka') {
                    setSelectState(s2_pta_status, ['Tanpa Penutup'], 'Tanpa Penutup');
                }
                if (coverValue === 'Papan') {
                    setSelectState(s2_ptb_status, ['Papan Full', 'Papan Setengah'], null);
                } else if (coverValue === 'Triplek') {
                    setSelectState(s2_ptb_status, ['Tripleks'], 'Tripleks');
                }
            }

            const packingText = document.getElementById('selectedPackingTypeText');
            const coverText = document.getElementById('selectedCoverMaterialText');
            const descriptionText = document.getElementById('selectedPackingDescription');

            if (packingText) {
                packingText.textContent = packingTypeLabels[packingValue] || '-';
                packingText.classList.toggle('has-value', Boolean(packingValue));
            }

            if (coverText) {
                coverText.textContent = coverValue || '-';
                coverText.classList.toggle('has-value', Boolean(coverValue));
            }

            if (descriptionText) {
                descriptionText.textContent = packingTypeDescriptions[packingValue]
                    || 'Pilih type packing untuk menampilkan detail pilihan.';
            }

            // Carton Box and Additional Mat Logic
            const cartonPanel = document.querySelector('.s2-carton');
            const cartonSablon = document.querySelector('#s2_carton_type');
            
            // Konfigurasi Area Kayu Panels (Baris 1 & Baris 2)
            const bottomPanel = document.querySelector('.s2-panel.s2-bottom');
            const topPanelContainer = document.querySelector('.s2-panel.s2-top');
            if (addMatValue.includes('Carton') || addMatValue.includes('Terpal')) {
                if (cartonPanel) cartonPanel.style.display = '';
                
                
                    if (cartonSablon) {
                        cartonSablon.value = 'Polos';
                        cartonSablon.disabled = true;
                        cartonSablon.style.backgroundColor = 'var(--s2-soft, #f8fafc)';
                    }
                    if (bottomPanel) bottomPanel.style.display = '';
                    if (topPanelContainer && packingValue !== 'Palet') topPanelContainer.style.display = '';
                } else {
                    if (cartonSablon) {
                        cartonSablon.disabled = false;
                        cartonSablon.style.backgroundColor = '';
                    }
                    if (bottomPanel) bottomPanel.style.display = 'none';
                    if (topPanelContainer) topPanelContainer.style.display = 'none';
                }
            } else {
                if (cartonPanel) cartonPanel.style.display = 'none';
                if (bottomPanel) bottomPanel.style.display = '';
                if (topPanelContainer && packingValue !== 'Palet') topPanelContainer.style.display = '';
            }
        };
        window.syncPackingTypeSelection = syncPackingTypeSelection;

        document.querySelectorAll(
            '#packingTypeStep input[name="packing_type"], #packingTypeStep input[name="cover_material"]'
        ).forEach((input) => input.addEventListener('change', syncPackingTypeSelection));

        const validatePackingTypeStep = () => {
            const packingInput = document.querySelector('#packingTypeStep input[name="packing_type"]:checked');
            const coverInput = document.querySelector('#packingTypeStep input[name="cover_material"]:checked');

            if (!packingInput) {
                if (statusText) statusText.textContent = 'Pilih type packing terlebih dahulu.';
                return false;
            }

            if (!coverInput) {
                if (statusText) statusText.textContent = 'Pilih bahan penutup terlebih dahulu.';
                return false;
            }

            return true;
        };

        /* =====================================================
           EVENTS
           ===================================================== */
        searchInput?.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter') return;

            event.preventDefault();
            searchButton?.click();
        });

        itemDropdown?.addEventListener('change', () => {
            if (statusText) {
                statusText.textContent = itemDropdown.value
                    ? 'Produk terpilih. Masukkan Qty dan klik (+)'
                    : 'Pilih satu barang untuk melanjutkan.';
            }
        });

        inputQtyItem?.addEventListener('input', function () {
            const max = Number(this.max || 0);
            const value = Number(this.value || 0);
            const invalid = value <= 0 || (max > 0 && value > max);
            this.classList.toggle('is-invalid', invalid);
        });


        nextButton?.addEventListener('click', () => {
            if (currentStep === 1) {
                if (window.selectedItemsList && window.selectedItemsList.length > 0) {
                    setStep(2);
                } else if (statusText) {
                    statusText.textContent = 'Tarik data SO dan tambahkan barang ke daftar terlebih dahulu.';
                }
                return;
            }

            if (currentStep === 2) {
                if (!validatePackingTypeStep()) return;
                setStep(3);
                return;
            }

            if (!validateStepTwo()) return;

            const payload = {
                salesOrder: searchInput?.value?.trim() || '',
                items: window.selectedItemsList,
                packingType: {
                    type: document.querySelector('input[name="packing_type"]:checked')?.value || '',
                    coverMaterial: document.querySelector('input[name="cover_material"]:checked')?.value || '',
                    additionalMat: document.querySelector('input[name="additional_mat"]:checked')?.value || '',
                    terpalMaterial: document.querySelector('#s2_terpal_material')?.value || '',
                    cartonMaterial: document.querySelector('#s2_carton_material')?.value || '',
                    cartonTypeSablon: document.querySelector('#s2_carton_type')?.value || '',
                },
                configuration: getPackagingConfiguration(),
            };

            document.dispatchEvent(new CustomEvent('productSetupSaveRequested', { detail: payload }));
            console.log('Product Setup payload:', payload);

            if (statusText) statusText.textContent = 'Konfigurasi siap disimpan.';
        });

        previousButton?.addEventListener('click', () => {
            if (currentStep > 1) setStep(currentStep - 1);
        });

        modalElement?.addEventListener('hidden.bs.modal', () => {
            setStep(1);
            document.querySelectorAll('#packingTypeStep input[type="radio"]').forEach((input) => {
                input.checked = false;
            });
            syncPackingTypeSelection();
        });


        /* =====================================================
           STEP 2 HELPERS
           ===================================================== */
        const getFieldValue = (id) => {
            const element = document.getElementById(id);
            return element ? element.value : '';
        };

        const getPackagingConfiguration = () => ({
            packagingNumber: getFieldValue('s2_pkg_number'),
            packerId: getFieldValue('s2_packer'),
            qtyPacking: Number(getFieldValue('s2_qty_pack') || 0),
            deliveryDate: getFieldValue('s2_delivery_date'),
            typePackaging: document.querySelector('input[name="packing_type"]:checked')?.value || '',

            dimensions: {
                length: Number(getFieldValue('s2_length') || 0),
                width: Number(getFieldValue('s2_width') || 0),
                height: Number(getFieldValue('s2_height') || 0),
            },

            additional: {
                supportSpacingAtas: Number(
                    getFieldValue('s2_jarak_atas') || 0
                ),
                supportSpacingBawah: Number(
                    getFieldValue('s2_jarak_bawah') || 0
                ),
                topGap: Number(
                    getFieldValue('s2_gap_atas') || 0
                ),
                bottomGap: Number(
                    getFieldValue('s2_gap_bawah') || 0
                ),
            },

            bottom: {
                support: {
                    usage: getFieldValue('s2_pb_status'),
                    direction: getFieldValue('s2_pb_arah'),
                    material: getFieldValue('s2_pb_material'),
                },
                cover: {
                    usage: getFieldValue('s2_ptb_status'),
                    direction: getFieldValue('s2_ptb_arah'),
                    material: getFieldValue('s2_ptb_material'),
                },
                blockFeet: {
                    usage: getFieldValue('s2_kb_status'),
                    direction: getFieldValue('s2_kb_arah'),
                    material: getFieldValue('s2_kb_material'),
                },
            },

            top: {
                support: {
                    usage: getFieldValue('s2_pa_status'),
                    direction: getFieldValue('s2_pa_arah'),
                    material: getFieldValue('s2_pa_material'),
                },
                cover: {
                    usage: getFieldValue('s2_pta_status'),
                    direction: getFieldValue('s2_pta_arah'),
                    material: getFieldValue('s2_pta_material'),
                },
            },
        });

        const validateStepTwo = () => {
            const requiredIds = [
                's2_packer',
                's2_qty_pack',
                's2_length',
                's2_width',
                's2_height',
            ];

            let firstInvalid = null;

            requiredIds.forEach((id) => {
                const element = document.getElementById(id);

                if (!element) return;

                const numericField = element.type === 'number';
                const value = numericField
                    ? Number(element.value || 0)
                    : String(element.value || '').trim();

                const invalid = numericField
                    ? value <= 0
                    : !value;

                element.classList.toggle('is-invalid', invalid);

                if (invalid && !firstInvalid) {
                    firstInvalid = element;
                }
            });

            if (firstInvalid) {
                firstInvalid.focus();

                if (statusText) {
                    statusText.textContent =
                        'Lengkapi Packer, Qty, dan Dimensi terlebih dahulu.';
                }

                return false;
            }

            return true;
        };


        /* =====================================================
           INITIAL VALUE FROM DATABASE
           Menggunakan json_encode agar aman untuk tanda kutip dan karakter
           khusus dari database.
           ===================================================== */
        const initialData = {!! json_encode($productSetupInitialData) !!};

        if (initialData.hasJob && initialData.items && initialData.items.length > 0) {
            // First item's SO is used for the search box
            if (searchInput) {
                searchInput.value = initialData.items[0].soNumber;
            }

            // Populate the table directly
            window.selectedItemsList = initialData.items.map(item => ({
                soNumber: item.soNumber,
                itemNumber: item.itemNumber,
                description: item.description,
                customer: item.customer,
                qty: item.qty,
            }));
            
            renderSelectedItemsTable();
        }

            // Populasikan nilai Step 2
            const setVal = (id, val) => {
                const element = document.getElementById(id);

                if (
                    !element ||
                    val === undefined ||
                    val === null ||
                    String(val).trim() === ''
                ) {
                    return;
                }

                const cleanValue = String(val).trim();

                if (element.tagName === 'SELECT') {
                    const matchingOption = Array.from(element.options).find(
                        option =>
                            String(option.value).trim().toLowerCase() ===
                            cleanValue.toLowerCase()
                    );

                    if (matchingOption) {
                        element.value = matchingOption.value;
                    } else {
                        const newOption = new Option(
                            cleanValue,
                            cleanValue,
                            true,
                            true
                        );

                        element.add(newOption);
                    }

                    element.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));

                    return;
                }

                element.value = cleanValue;
            };

            setVal('s2_pkg_number', initialData.packagingNumber);
            setVal('s2_packer', initialData.packerId);
            setVal('s2_qty_pack', initialData.qtyPacking);
            if (initialData.deliveryDate) {
                // Formatting to YYYY-MM-DD for date input
                const dateObj = new Date(initialData.deliveryDate);
                if (!isNaN(dateObj)) {
                    setVal('s2_delivery_date', dateObj.toISOString().split('T')[0]);
                }
            }
            
            if (initialData.typePackaging) {
                const radio = document.querySelector(`#packingTypeStep input[name="packing_type"][value="${initialData.typePackaging}"]`);
                if (radio) {
                    radio.checked = true;
                    syncPackingTypeSelection();
                }
            }

            setVal('s2_length', initialData.dimensions?.length);
            setVal('s2_width', initialData.dimensions?.width);
            setVal('s2_height', initialData.dimensions?.height);

            setVal('s2_jarak_atas', initialData.additional?.supportSpacingAtas);
            setVal('s2_jarak_bawah', initialData.additional?.supportSpacingBawah);
            setVal('s2_gap_atas', initialData.additional?.topGap);
            setVal('s2_gap_bawah', initialData.additional?.bottomGap);

            setVal('s2_pb_status', initialData.bottom?.support?.usage);
            setVal('s2_pb_arah', initialData.bottom?.support?.direction);
            setVal('s2_pb_material', initialData.bottom?.support?.material);
            
            setVal('s2_ptb_status', initialData.bottom?.cover?.usage);
            setVal('s2_ptb_arah', initialData.bottom?.cover?.direction);
            setVal('s2_ptb_material', initialData.bottom?.cover?.material);
            
            setVal('s2_kb_status', initialData.bottom?.blockFeet?.usage);
            setVal('s2_kb_arah', initialData.bottom?.blockFeet?.direction);
            setVal('s2_kb_material', initialData.bottom?.blockFeet?.material);
            
            setVal('s2_pa_status', initialData.top?.support?.usage);
            setVal('s2_pa_arah', initialData.top?.support?.direction);
            setVal('s2_pa_material', initialData.top?.support?.material);
            
            setVal('s2_pta_status', initialData.top?.cover?.usage);
            setVal('s2_pta_arah', initialData.top?.cover?.direction);
            setVal('s2_pta_material', initialData.top?.cover?.material);

        setStep(1);
    });
    
    // --- API HANDLING FOR MODAL ---
    document.addEventListener('DOMContentLoaded', () => {
        const searchBtn = document.getElementById('btnSearchSO');
        const searchInput = document.getElementById('searchSO');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        // 1. Handle SO Search
        if (searchBtn) {
            searchBtn.addEventListener('click', async () => {
                const soNumber = searchInput?.value?.trim();
                if (!soNumber) {
                    alert('Harap masukkan nomor SO!');
                    return;
                }

                if (window.setSOSearchLoading) window.setSOSearchLoading(true);

                try {
                    const response = await fetch(`/api/packaging/search-so?q=${encodeURIComponent(soNumber)}`);
                    const result = await response.json();
                    
                    if (result.data && result.data.length > 0) {
                        const firstItem = result.data[0];
                        
                        // Populate Header Info
                        const setText = (id, text) => {
                            const el = document.getElementById(id);
                            if (el) el.textContent = text || '-';
                        };
                        setText('infoNoSO', soNumber);
                        const customerName = String(
                            firstItem.nama_pelanggan ??
                            firstItem.nama_customer ??
                            firstItem.customer_name ??
                            firstItem.nm_customer ??
                            firstItem.customer ??
                            ''
                        ).trim() || '-';

                        setText('infoCustomer', customerName);
                        window.currentFetchedCustomer = customerName;
                        // Misal kalau ada delivery date & address dari API
                        setText('infoDeliveryDate', firstItem.tgl_estimasi || firstItem.tgl_pengiriman || firstItem.tgl_so || '-');
                        setText('infoShipto', firstItem.shipto || '-');

                        // Populate Dropdown
                        if (window.renderSOItems) {
                            window.renderSOItems(result.data);
                        }

                        if (window.statusText) {
                            window.statusText.textContent = `Ditemukan ${result.data.length} barang. Silakan pilih.`;
                        }
                    } else {
                        if (window.showSOEmptyState) {
                            window.showSOEmptyState('Data tidak ditemukan', result.message || 'SO tidak terdaftar.');
                        }
                    }
                } catch (error) {
                    console.error('Error fetching SO:', error);
                    alert('Terjadi kesalahan saat mencari SO.');
                    if (window.resetSOHeaderInfo) window.resetSOHeaderInfo();
                } finally {
                    if (window.setSOSearchLoading) window.setSOSearchLoading(false);
                }
            });
        }
    });

    // 2. Handle Save / Update Configuration
    document.addEventListener('productSetupSaveRequested', async (e) => {
        const payload = e.detail;
        const submitBtn = document.getElementById('btn_next_step');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="ps-spinner me-2"></span> Menyimpan...';
        }

        // Ambil ID calculation dari backend langsung
        const calcId = "{{ isset($calculation) ? $calculation->id : '' }}";

        try {
            let url = '';
            let finalPayload = {};

            const selectedItems = Array.isArray(window.selectedItemsList)
                ? window.selectedItemsList
                : [];

            const itemsList = selectedItems
                .map(item => ({
                    no_so: String(item.soNumber || '').trim(),
                    customer: String(item.customer || '').trim() || '-',
                    no_product: String(item.itemNumber || '').trim(),
                    desc_product: String(item.description || '').trim(),
                    qty: Math.max(1, Number(item.qty || 1)),
                }))
                .filter(item => item.no_product !== '');

            if (itemsList.length === 0) {
                throw new Error(
                    'Daftar barang Step 1 kosong. Tambahkan minimal satu produk.'
                );
            }

            if (calcId) {
                // =========================================================
                // UPDATE EXISTING CALCULATION
                // Route: PackagingCalculationController@update
                // =========================================================
                url = `/packaging/calc-update/${calcId}`;

                finalPayload = {
                    _token: document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content,
                    _method: 'PUT',

                    // Step 1: WAJIB dikirim agar SO, customer, produk,
                    // penambahan/penghapusan item dan qty dapat diperbarui.
                    items: itemsList,

                    // Step 2
                    qty_pack: payload.configuration.qtyPacking,
                    packer_id: payload.configuration.packerId,
                    completion_date: payload.configuration.deliveryDate,
                    type_packaging:
                        payload.configuration.typePackaging,
                    tipe_penutup:
                        payload.packingType.coverMaterial,
                    additional_mat:
                        payload.packingType.additionalMat,
                    carton_material:
                        payload.packingType.cartonMaterial,
                    carton_type_sablon:
                        payload.packingType.cartonTypeSablon,
                    terpal_material:
                        payload.packingType.terpalMaterial,

                    length:
                        payload.configuration.dimensions.length,
                    width:
                        payload.configuration.dimensions.width,
                    height:
                        payload.configuration.dimensions.height,

                    jarak_penyanggah_atas:
                        payload.configuration.additional
                            .supportSpacingAtas,
                    jarak_penyanggah_bawah:
                        payload.configuration.additional
                            .supportSpacingBawah,
                    gap_atas:
                        payload.configuration.additional.topGap,
                    gap_bawah:
                        payload.configuration.additional.bottomGap,

                    bawah_penyangga_include:
                        payload.configuration.bottom.support.usage ===
                        'Include' ? 1 : 0,
                    bawah_penyangga_arah:
                        payload.configuration.bottom.support.direction,
                    bawah_penyangga_material:
                        payload.configuration.bottom.support.material,

                    bawah_penutup_tipe:
                        payload.configuration.bottom.cover.usage,
                    bawah_penutup_arah:
                        payload.configuration.bottom.cover.direction,
                    bawah_penutup_material:
                        payload.configuration.bottom.cover.material,

                    include_pallet_base:
                        payload.configuration.bottom.blockFeet.usage ===
                        'Include' ? 1 : 0,
                    bawah_kakibalok_arah:
                        payload.configuration.bottom.blockFeet.direction,
                    bawah_kakibalok_material:
                        payload.configuration.bottom.blockFeet.material,

                    atas_penyangga_include:
                        payload.configuration.top.support.usage ===
                        'Include' ? 1 : 0,
                    atas_penyangga_arah:
                        payload.configuration.top.support.direction,
                    atas_penyangga_material:
                        payload.configuration.top.support.material,

                    atas_penutup_tipe:
                        payload.configuration.top.cover.usage,
                    atas_penutup_arah:
                        payload.configuration.top.cover.direction,
                    atas_penutup_material:
                        payload.configuration.top.cover.material,
                };
            } else {
                // =========================================================
                // CREATE NEW PACKAGING JOB
                // Route: PackagingController@store
                // =========================================================
                url = '/packaging/store';

                const firstItem = itemsList[0];

                finalPayload = {
                    _token: document.querySelector(
                        'meta[name="csrf-token"]'
                    )?.content,

                    no_so: firstItem.no_so,
                    customer: firstItem.customer,
                    date_delivery:
                        payload.configuration.deliveryDate ||
                        document.getElementById(
                            'infoDeliveryDate'
                        )?.textContent?.trim() || null,
                    completion_date:
                        payload.configuration.deliveryDate || null,
                    address:
                        document.getElementById(
                            'infoShipto'
                        )?.textContent?.trim() || null,

                    packType:
                        payload.configuration.typePackaging ||
                        'Box',
                    type_packaging:
                        payload.configuration.typePackaging ||
                        'Box',
                    tipe_penutup:
                        payload.packingType.coverMaterial,
                    additional_mat:
                        payload.packingType.additionalMat,
                    carton_material:
                        payload.packingType.cartonMaterial,
                    carton_type_sablon:
                        payload.packingType.cartonTypeSablon,
                    terpal_material:
                        payload.packingType.terpalMaterial,

                    items: itemsList.map(item => ({
                        // Data Step 1 per produk
                        no_so: item.no_so,
                        customer: item.customer,
                        no_product: item.no_product,
                        desc_product: item.desc_product,
                        qty: item.qty,

                        // Alias lama agar tetap kompatibel
                        qty_kirim: item.qty,

                        // Data Step 2; item pertama dipakai controller
                        // sebagai konfigurasi utama packaging.
                        packer:
                            payload.configuration.packerId,
                        qty_pack:
                            payload.configuration.qtyPacking,
                        type_packaging:
                            payload.configuration.typePackaging,
                        tipe_penutup:
                            payload.packingType.coverMaterial,

                        length:
                            payload.configuration.dimensions.length,
                        width:
                            payload.configuration.dimensions.width,
                        height:
                            payload.configuration.dimensions.height,

                        jarak_penyanggah_atas:
                            payload.configuration.additional
                                .supportSpacingAtas,
                        jarak_penyanggah_bawah:
                            payload.configuration.additional
                                .supportSpacingBawah,
                        gap_atas:
                            payload.configuration.additional.topGap,
                        gap_bawah:
                            payload.configuration.additional.bottomGap,

                        pb_status:
                            payload.configuration.bottom.support.usage,
                        pb_arah:
                            payload.configuration.bottom.support.direction,
                        pb_material:
                            payload.configuration.bottom.support.material,

                        ptb_status:
                            payload.configuration.bottom.cover.usage,
                        ptb_arah:
                            payload.configuration.bottom.cover.direction,
                        ptb_material:
                            payload.configuration.bottom.cover.material,

                        kb_status:
                            payload.configuration.bottom.blockFeet.usage,
                        kb_arah:
                            payload.configuration.bottom.blockFeet.direction,
                        kb_material:
                            payload.configuration.bottom.blockFeet.material,

                        pa_status:
                            payload.configuration.top.support.usage,
                        pa_arah:
                            payload.configuration.top.support.direction,
                        pa_material:
                            payload.configuration.top.support.material,

                        pta_status:
                            payload.configuration.top.cover.usage,
                        pta_arah:
                            payload.configuration.top.cover.direction,
                        pta_material:
                            payload.configuration.top.cover.material,
                    })),
                };
            }

            console.log('FINAL STEP 1 ITEMS:', itemsList);
            console.log('FINAL SAVE PAYLOAD:', finalPayload);

            const response = await fetch(url, {
                method: 'POST', // Walaupun _method = PUT untuk calc-update, tetap pakai POST karena HTML form limits
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(finalPayload)
            });

            const result = await response.json();
            
            if (response.ok && result.status === 'success') {
                if (result.redirect) {
                    window.location.href = result.redirect; // Ke halaman detail baru
                } else {
                    window.location.reload(); // Reload halaman untuk memuat data terbaru
                }
            } else {
                console.error('Save validation/error response:', result);

                const validationMessages = result.errors
                    ? Object.values(result.errors).flat().join('\n')
                    : '';

                alert(
                    'Gagal menyimpan: ' +
                    (result.message || 'Unknown error') +
                    (validationMessages
                        ? `\n\n${validationMessages}`
                        : '')
                );
            }
        } catch (error) {
            console.error('Save error:', error);
            alert(error.message || 'Terjadi kesalahan jaringan.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save Configuration <i class="fa-solid fa-check ms-2"></i>';
            }
        }
    });
