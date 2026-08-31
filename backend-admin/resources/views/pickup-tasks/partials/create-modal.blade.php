<!-- Modal Create Task -->
<style>
    #createTaskModal {
        --task-orange: #f97316;
        --task-orange-dark: #ea580c;
        --task-orange-soft: #fff7ed;
        --task-border: #e5e7eb;
        --task-muted: #64748b;
        --task-text: #0f172a;
        --task-bg: #f8fafc;
    }

    #createTaskModal .modal-dialog {
        width: min(96vw, 1540px);
        max-width: 1540px;
        margin: 1.25rem auto;
    }

    #createTaskModal .modal-content {
        border: 0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
        background: #fff;
    }

    #createTaskModal .modal-header {
        min-height: 82px;
        padding: 18px 28px;
        background: #fff;
        border-bottom: 1px solid #eef2f7;
    }

    #createTaskModal .modal-title {
        color: var(--task-text);
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    #createTaskModal .title-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: linear-gradient(135deg, #fb923c 0%, var(--task-orange) 100%);
        box-shadow: 0 8px 18px rgba(249, 115, 22, .22);
    }

    #createTaskModal .modal-body {
        padding: 24px 26px;
        background: #fbfcfe;
    }

    #createTaskModal .task-panel {
        height: 100%;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .03);
    }

    #createTaskModal .section-title {
        display: flex;
        align-items: center;
        gap: 9px;
        color: var(--task-orange-dark);
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .02em;
        margin: 0;
    }

    #createTaskModal .section-title i {
        color: var(--task-orange);
        font-size: 16px;
    }

    #createTaskModal .section-rule {
        height: 1px;
        background: #e8edf3;
        margin: 16px 0 18px;
    }

    #createTaskModal .form-label {
        margin-bottom: 7px;
        color: #475569;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .035em;
    }

    #createTaskModal .form-control,
    #createTaskModal .form-select,
    #createTaskModal .input-group-text {
        min-height: 46px;
        border-color: #dbe3ec;
        background-color: #fff;
        color: #1e293b;
        font-size: 14px;
        border-radius: 9px;
        box-shadow: none;
    }

    #createTaskModal textarea.form-control {
        min-height: 78px;
        resize: vertical;
        padding-top: 12px;
    }

    #createTaskModal .form-control::placeholder {
        color: #94a3b8;
    }

    #createTaskModal .form-control:focus,
    #createTaskModal .form-select:focus {
        border-color: #fdba74;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .11);
    }

    #createTaskModal .task-type-select {
        min-height: 54px;
        font-weight: 700;
        background-color: #fff;
    }

    #createTaskModal .subsection {
        margin-top: 22px;
        padding-top: 20px;
        border-top: 1px solid #e8edf3;
    }

    #createTaskModal .subsection-title {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--task-orange-dark);
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 16px;
    }

    #createTaskModal .item-entry-box {
        padding: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 13px;
        background: #fff;
    }

    #createTaskModal .btn-add-item {
        min-height: 44px;
        width: 100%;
        border: 1px solid #fb923c;
        border-radius: 9px;
        color: var(--task-orange-dark);
        background: #fff;
        font-weight: 800;
        transition: .18s ease;
    }

    #createTaskModal .btn-add-item:hover {
        color: #fff;
        background: var(--task-orange);
        border-color: var(--task-orange);
    }

    #createTaskModal .items-table-wrap {
        margin-top: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    #createTaskModal .items-table-title {
        padding: 0 2px;
        color: var(--task-orange-dark);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    #createTaskModal .items-table {
        margin: 0;
        table-layout: fixed;
    }

    #createTaskModal .items-table thead th {
        padding: 11px 10px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
        vertical-align: middle;
    }

    #createTaskModal .items-table tbody td {
        padding: 11px 10px;
        border-color: #edf2f7;
        color: #334155;
        font-size: 12px;
        vertical-align: middle;
        word-break: break-word;
    }

    #createTaskModal .empty-items {
        min-height: 245px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #94a3b8;
        text-align: center;
        padding: 26px;
    }

    #createTaskModal .empty-items i {
        color: #fdba74;
        font-size: 42px;
    }

    #createTaskModal .empty-items strong {
        color: #334155;
        font-size: 14px;
    }

    #createTaskModal .btn-remove-row {
        width: 30px;
        height: 30px;
        padding: 0;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    #createTaskModal .modal-footer {
        padding: 16px 26px;
        background: #fff;
        border-top: 1px solid #eef2f7;
        gap: 10px;
    }

    #createTaskModal .btn-cancel {
        min-width: 118px;
        min-height: 44px;
        border-radius: 9px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #334155;
        font-weight: 700;
    }

    #createTaskModal .btn-save {
        min-width: 168px;
        min-height: 44px;
        border: 0;
        border-radius: 9px;
        color: #fff;
        font-weight: 800;
        background: linear-gradient(135deg, #fb923c 0%, var(--task-orange) 100%);
        box-shadow: 0 8px 16px rgba(249, 115, 22, .18);
    }

    #createTaskModal .btn-save:hover {
        background: linear-gradient(135deg, var(--task-orange) 0%, var(--task-orange-dark) 100%);
    }

    #createTaskModal .required-star {
        color: #ef4444;
    }

    @media (min-width: 992px) {
        #createTaskModal .main-column {
            flex: 0 0 39%;
            max-width: 39%;
        }

        #createTaskModal .items-column {
            flex: 0 0 61%;
            max-width: 61%;
        }
    }

    @media (max-width: 991.98px) {
        #createTaskModal .modal-dialog {
            width: calc(100vw - 20px);
            margin: 10px auto;
        }

        #createTaskModal .modal-body {
            padding: 16px;
        }

        #createTaskModal .task-panel {
            padding: 16px;
        }
    }
</style>

<div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <form id="createTaskForm" action="{{ route('pickup-tasks.store') }}" method="POST" class="w-100">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-3 mb-0">
                        <span class="title-icon">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                        <span>Buat Tugas Driver Baru</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3 align-items-stretch">
                        <!-- ========================
                             LEFT : DATA UTAMA
                        ========================= -->
                        <div class="col-12 main-column">
                            <section class="task-panel">
                                <h6 class="section-title">
                                    <i class="fa-solid fa-table-cells-large"></i>
                                    Data Utama
                                </h6>
                                <div class="section-rule"></div>

                                <div class="mb-4">
                                    <label class="form-label">
                                        Jenis Tugas <span class="required-star">*</span>
                                    </label>
                                    <select name="task_type" id="taskTypeSelect" class="form-select task-type-select" required>
                                        <option value="pickup">📦 Mengambil Barang (Pickup)</option>
                                        <option value="delivery">🚚 Mengirim Barang (Delivery)</option>
                                    </select>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Pilih Driver <span class="required-star">*</span>
                                        </label>
                                        <select name="driver_id" class="form-select" required>
                                            <option value="">-- Pilih Driver --</option>
                                            @foreach($drivers as $driver)
                                                <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Pilih Kendaraan <span class="required-star">*</span>
                                        </label>
                                        <select name="vehicle_id" class="form-select" required>
                                            <option value="">-- Pilih Kendaraan --</option>
                                            @foreach($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }} - {{ $vehicle->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Tanggal Pengiriman / Penugasan
                                        </label>
                                        <input type="datetime-local" name="dispatch_date" class="form-control" value="{{ date('Y-m-d\TH:i') }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Estimasi Waktu Tiba
                                        </label>
                                        <input type="datetime-local" name="estimated_arrival" class="form-control">
                                    </div>
                                </div>

                                <!-- PICKUP -->
                                <div class="subsection task-fields pickup-fields">
                                    <div class="subsection-title">
                                        <i class="fa-solid fa-box-open"></i>
                                        Informasi Pickup
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nomor Referensi (Opsional)</label>
                                            <input type="text"
                                                   name="pickup_reference"
                                                   class="form-control"
                                                   placeholder="Otomatis jika kosong">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">
                                                Nama Tempat Pengambilan <span class="required-star">*</span>
                                            </label>
                                            <input type="text"
                                                   name="pickup_name"
                                                   class="form-control"
                                                   placeholder="Gudang Supplier A">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">
                                                Alamat Pengambilan (Lokasi) <span class="required-star">*</span>
                                            </label>
                                            <textarea name="pickup_location"
                                                      class="form-control"
                                                      rows="2"
                                                      placeholder="Alamat lengkap pengambilan..."></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Nama Tempat Tujuan (Opsional)</label>
                                            <input type="text"
                                                   name="destination_name"
                                                   class="form-control"
                                                   placeholder="Gudang Utama Customer">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Alamat Tujuan (Opsional)</label>
                                            <textarea name="pickup_destination"
                                                      class="form-control"
                                                      rows="2"
                                                      placeholder="Alamat lengkap tujuan..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- DELIVERY -->
                                <div class="subsection task-fields delivery-fields" style="display:none;">
                                    <div class="subsection-title">
                                        <i class="fa-solid fa-truck-fast"></i>
                                        Informasi Delivery (Sales Order)
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nomor DO / SO (Opsional)</label>
                                            <input type="text"
                                                   name="delivery_so_number"
                                                   class="form-control"
                                                   placeholder="Otomatis jika kosong">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">
                                                Nama Pelanggan / Customer <span class="required-star">*</span>
                                            </label>
                                            <input type="text"
                                                   name="customer_name"
                                                   class="form-control"
                                                   placeholder="PT Maju Jaya">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">
                                                Nama Gedung / Lokasi Asal <span class="required-star">*</span>
                                            </label>
                                            <input type="text"
                                                   name="delivery_pickup_name"
                                                   class="form-control"
                                                   placeholder="Gudang Utama">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">
                                                Alamat Lengkap Asal <span class="required-star">*</span>
                                            </label>
                                            <textarea name="delivery_pickup_location"
                                                      class="form-control"
                                                      rows="2"
                                                      placeholder="Alamat lengkap asal barang..."></textarea>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">
                                                Alamat Pengiriman (Tujuan) <span class="required-star">*</span>
                                            </label>
                                            <textarea name="delivery_address"
                                                      class="form-control"
                                                      rows="2"
                                                      placeholder="Alamat lengkap tujuan pengiriman..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- ========================
                             RIGHT : DAFTAR BARANG
                        ========================= -->
                        <div class="col-12 items-column">
                            <section class="task-panel">
                                <h6 class="section-title">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                    Daftar Barang / Item
                                </h6>
                                <div class="section-rule"></div>

                                <!-- INPUT BARANG -->
                                <div class="item-entry-box">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">No Barang</label>
                                            <input type="text"
                                                   id="itemNumberInput"
                                                   class="form-control"
                                                   placeholder="Opsional">
                                        </div>

                                        <div class="col-md-8">
                                            <label class="form-label">
                                                Nama / Deskripsi Barang <span class="required-star">*</span>
                                            </label>
                                            <input type="text"
                                                   id="itemDescriptionInput"
                                                   class="form-control"
                                                   placeholder="Wajib diisi">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">
                                                Kuantitas <span class="required-star">*</span>
                                            </label>
                                            <input type="number"
                                                   min="0"
                                                   step="0.01"
                                                   id="itemQuantityInput"
                                                   class="form-control"
                                                   placeholder="0">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Satuan</label>
                                            <input type="text"
                                                   id="itemUnitInput"
                                                   class="form-control"
                                                   placeholder="pcs/Kg">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Harga Satuan (Opsional)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number"
                                                       min="0"
                                                       step="0.01"
                                                       id="itemPriceInput"
                                                       class="form-control"
                                                       placeholder="0">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button type="button" id="btnAddItem" class="btn-add-item">
                                                <i class="fa-solid fa-plus me-2"></i>
                                                Tambah ke Daftar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- TABLE BARANG -->
                                <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
                                    <div class="items-table-title">
                                        Daftar Item (<span id="itemCount">0</span>)
                                    </div>
                                </div>

                                <div class="items-table-wrap">
                                    <div class="table-responsive">
                                        <table class="table items-table align-middle" id="itemsTable">
                                            <colgroup>
                                                <col style="width: 6%;">
                                                <col style="width: 16%;">
                                                <col style="width: 31%;">
                                                <col style="width: 10%;">
                                                <col style="width: 12%;">
                                                <col style="width: 17%;">
                                                <col style="width: 8%;">
                                            </colgroup>
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th>No Barang</th>
                                                    <th>Nama / Deskripsi Barang</th>
                                                    <th class="text-end">Qty</th>
                                                    <th>Satuan</th>
                                                    <th class="text-end">Harga Satuan</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemsTableBody"></tbody>
                                        </table>
                                    </div>

                                    <div class="empty-items" id="emptyItemsState">
                                        <i class="fa-solid fa-box-open"></i>
                                        <strong>Belum ada item barang</strong>
                                        <span>Tambahkan barang untuk menampilkan di daftar.</span>
                                    </div>
                                </div>

                                <!-- Hidden inputs untuk dikirim ke backend -->
                                <div id="itemsHiddenInputs"></div>
                            </section>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-save">
                        <i class="fa-solid fa-floppy-disk me-2"></i>
                        Simpan Tugas
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('createTaskForm');
    const taskTypeSelect = document.getElementById('taskTypeSelect');
    const pickupFields = document.querySelector('#createTaskModal .pickup-fields');
    const deliveryFields = document.querySelector('#createTaskModal .delivery-fields');

    const pickupRequired = pickupFields.querySelectorAll(
        "input[name='pickup_name'], textarea[name='pickup_location']"
    );

    const deliveryRequired = deliveryFields.querySelectorAll(
        "input[name='customer_name'], input[name='delivery_pickup_name'], textarea[name='delivery_pickup_location'], textarea[name='delivery_address']"
    );

    function toggleFields() {
        const isPickup = taskTypeSelect.value === 'pickup';

        pickupFields.style.display = isPickup ? 'block' : 'none';
        deliveryFields.style.display = isPickup ? 'none' : 'block';

        pickupRequired.forEach(el => el.required = isPickup);
        deliveryRequired.forEach(el => el.required = !isPickup);
    }

    taskTypeSelect.addEventListener('change', toggleFields);
    toggleFields();

    // =============================================
    // ITEM TABLE
    // =============================================
    const itemNumberInput = document.getElementById('itemNumberInput');
    const itemDescriptionInput = document.getElementById('itemDescriptionInput');
    const itemQuantityInput = document.getElementById('itemQuantityInput');
    const itemUnitInput = document.getElementById('itemUnitInput');
    const itemPriceInput = document.getElementById('itemPriceInput');
    const btnAddItem = document.getElementById('btnAddItem');
    const tableBody = document.getElementById('itemsTableBody');
    const emptyState = document.getElementById('emptyItemsState');
    const hiddenInputs = document.getElementById('itemsHiddenInputs');
    const itemCount = document.getElementById('itemCount');

    let items = [];

    window.openTaskModal = function(mode, task = null, taskItems = []) {
        // reset errors
        document.querySelectorAll('#createTaskModal .is-invalid').forEach(el => el.classList.remove('is-invalid'));
        resetItemEditor();

        const titleSpan = document.querySelector('#createTaskModal .modal-title span:last-child');
        const icon = document.querySelector('#createTaskModal .title-icon i');
        const submitBtn = document.querySelector('#createTaskModal .btn-save');

        if (mode === 'create') {
            form.action = "{{ route('pickup-tasks.store') }}";
            titleSpan.textContent = 'Buat Tugas Driver Baru';
            icon.className = 'fa-solid fa-plus';
            submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i>Simpan Tugas';
            form.querySelector('input[name="_method"]')?.remove();
            form.querySelector('input[name="task_type"][type="hidden"]')?.remove();
            
            form.reset();
            taskTypeSelect.disabled = false;
            items = [];
            // Set default date
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            form.querySelector('input[name="dispatch_date"]').value = now.toISOString().slice(0,16);
            
            toggleFields();
            renderItems();
        } else if (mode === 'edit') {
            form.action = task.update_url;
            titleSpan.textContent = 'Edit Tugas Driver';
            icon.className = 'fa-solid fa-pen-to-square';
            submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan';
            
            if (!form.querySelector('input[name="_method"]')) {
                form.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');
            }

            taskTypeSelect.value = task.task_type;
            taskTypeSelect.disabled = true;
            
            if (!form.querySelector('input[name="task_type"][type="hidden"]')) {
                form.insertAdjacentHTML('beforeend', '<input type="hidden" name="task_type" value="' + task.task_type + '">');
            } else {
                form.querySelector('input[name="task_type"][type="hidden"]').value = task.task_type;
            }

            form.querySelector('select[name="driver_id"]').value = task.driver_id || '';
            form.querySelector('select[name="vehicle_id"]').value = task.vehicle_id || '';
            
            if(task.dispatch_date) form.querySelector('input[name="dispatch_date"]').value = task.dispatch_date.substring(0, 16);
            if(task.estimated_arrival) form.querySelector('input[name="estimated_arrival"]').value = task.estimated_arrival.substring(0, 16);

            if (task.task_type === 'pickup') {
                form.querySelector('input[name="pickup_reference"]').value = task.reference_number || '';
                form.querySelector('input[name="pickup_name"]').value = task.pickup_name || '';
                form.querySelector('textarea[name="pickup_location"]').value = task.pickup_location || '';
                form.querySelector('input[name="destination_name"]').value = task.destination_name || '';
                form.querySelector('textarea[name="pickup_destination"]').value = task.destination || '';
            } else {
                const so = task.sales_order || {};
                form.querySelector('input[name="delivery_so_number"]').value = so.so_number || '';
                form.querySelector('input[name="customer_name"]').value = so.customer_name || '';
                form.querySelector('input[name="delivery_pickup_name"]').value = task.pickup_name || '';
                form.querySelector('textarea[name="delivery_pickup_location"]').value = task.pickup_location || '';
                
                let address = '';
                if (so.source_data) {
                    let sourceData = typeof so.source_data === 'string' ? JSON.parse(so.source_data) : so.source_data;
                    address = sourceData.address || '';
                }
                form.querySelector('textarea[name="delivery_address"]').value = address;
            }

            items = taskItems;
            toggleFields();
            renderItems();
        }
        
        var modal = new bootstrap.Modal(document.getElementById('createTaskModal'));
        modal.show();
    };

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatNumber(value) {
        const number = Number(value || 0);
        return new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 2
        }).format(number);
    }

    function formatCurrency(value) {
        const number = Number(value || 0);
        return 'Rp ' + new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 2
        }).format(number);
    }

    function resetItemEditor() {
        itemNumberInput.value = '';
        itemDescriptionInput.value = '';
        itemQuantityInput.value = '';
        itemUnitInput.value = '';
        itemPriceInput.value = '';
        itemDescriptionInput.focus();
    }

    function renderItems() {
        tableBody.innerHTML = '';
        hiddenInputs.innerHTML = '';
        itemCount.textContent = items.length;

        if (items.length === 0) {
            emptyState.style.display = 'flex';
            document.getElementById('itemsTable').style.display = 'none';
            return;
        }

        emptyState.style.display = 'none';
        document.getElementById('itemsTable').style.display = 'table';

        items.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center fw-semibold">${index + 1}</td>
                <td>${escapeHtml(item.item_number || '-')}</td>
                <td class="fw-semibold">${escapeHtml(item.item_description)}</td>
                <td class="text-end">${formatNumber(item.quantity)}</td>
                <td>${escapeHtml(item.unit || '-')}</td>
                <td class="text-end">${formatCurrency(item.unit_price)}</td>
                <td class="text-center">
                    <button type="button"
                            class="btn btn-sm btn-outline-danger btn-remove-row"
                            data-index="${index}"
                            title="Hapus item">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(tr);

            const values = {
                item_number: item.item_number,
                item_description: item.item_description,
                quantity: item.quantity,
                unit: item.unit,
                unit_price: item.unit_price
            };

            Object.entries(values).forEach(([key, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `items[${index}][${key}]`;
                input.value = value ?? '';
                hiddenInputs.appendChild(input);
            });
        });
    }

    btnAddItem.addEventListener('click', function () {
        const description = itemDescriptionInput.value.trim();
        const quantity = itemQuantityInput.value;

        if (!description) {
            itemDescriptionInput.focus();
            itemDescriptionInput.classList.add('is-invalid');
            return;
        }

        itemDescriptionInput.classList.remove('is-invalid');

        if (quantity === '' || Number(quantity) <= 0) {
            itemQuantityInput.focus();
            itemQuantityInput.classList.add('is-invalid');
            return;
        }

        itemQuantityInput.classList.remove('is-invalid');

        items.push({
            item_number: itemNumberInput.value.trim(),
            item_description: description,
            quantity: quantity,
            unit: itemUnitInput.value.trim(),
            unit_price: itemPriceInput.value || 0
        });

        renderItems();
        resetItemEditor();
    });

    tableBody.addEventListener('click', function (event) {
        const button = event.target.closest('.btn-remove-row');
        if (!button) return;

        const index = Number(button.dataset.index);
        items.splice(index, 1);
        renderItems();
    });

    itemDescriptionInput.addEventListener('input', function () {
        this.classList.remove('is-invalid');
    });

    itemQuantityInput.addEventListener('input', function () {
        this.classList.remove('is-invalid');
    });

    form.addEventListener('submit', function (event) {
        if (items.length === 0) {
            event.preventDefault();

            itemDescriptionInput.focus();
            itemDescriptionInput.classList.add('is-invalid');

            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Daftar barang masih kosong',
                    text: 'Tambahkan minimal 1 barang sebelum menyimpan tugas.',
                    confirmButtonColor: '#f97316'
                });
            } else {
                alert('Tambahkan minimal 1 barang sebelum menyimpan tugas.');
            }
        }
    });

    renderItems();
});
</script>