<!-- Modal Create Task -->
<style>
    #createTaskModal {
        --task-orange: #f97316;
        --task-orange-dark: #ea580c;
        --task-orange-soft: #fff7ed;
        --task-border: #e2e8f0;
        --task-border-strong: #cbd5e1;
        --task-muted: #64748b;
        --task-text: #0f172a;
        --task-bg: #f8fafc;
        --task-card-bg: #ffffff;
        --task-shadow: 0 20px 60px rgba(15, 23, 42, .16);
    }

    #createTaskModal .modal-dialog {
        max-width: 1320px;
    }

    #createTaskModal .modal-content {
        border: 0;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: var(--task-shadow);
        background: #fff;
    }

    #createTaskModal .modal-header {
        padding: 22px 28px 18px;
        background: #fff;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    #createTaskModal .modal-title-wrap {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    #createTaskModal .title-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: linear-gradient(135deg, #fb923c 0%, var(--task-orange) 100%);
        box-shadow: 0 8px 18px rgba(249, 115, 22, .22);
        flex: 0 0 auto;
    }

    #createTaskModal .modal-title {
        color: var(--task-text);
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -.02em;
        margin: 0 0 3px 0;
        line-height: 1.2;
    }

    #createTaskModal .modal-subtitle {
        margin: 0;
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
    }

    #createTaskModal .btn-close {
        box-shadow: none !important;
        margin: 0;
    }

    #createTaskModal .modal-body {
        padding: 18px 22px 20px;
        background: #fbfcfe;
        max-height: 72vh;
        overflow-y: auto;
        overflow-x: hidden;
    }

    #createTaskModal .task-card {
        background: var(--task-card-bg);
        border: 1px solid var(--task-border);
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 3px 10px rgba(15, 23, 42, .03);
    }

    #createTaskModal .block-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        color: #334155;
        font-size: 13px;
        font-weight: 800;
    }

    #createTaskModal .form-label {
        margin-bottom: 7px;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
    }

    #createTaskModal .required-star {
        color: #ef4444;
    }

    #createTaskModal .form-control,
    #createTaskModal .form-select,
    #createTaskModal .input-group-text {
        min-height: 44px;
        border: 1px solid #dbe3ec;
        background-color: #fff;
        color: #1e293b;
        font-size: 14px;
        border-radius: 10px;
        box-shadow: none;
    }

    #createTaskModal textarea.form-control {
        min-height: 74px;
        resize: vertical;
        padding-top: 11px;
    }

    #createTaskModal .form-control::placeholder {
        color: #94a3b8;
    }

    #createTaskModal .form-control:focus,
    #createTaskModal .form-select:focus {
        border-color: #fdba74;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .12);
    }

    #createTaskModal .input-group-text {
        background: #fff;
        color: #64748b;
    }

    /* Segmented Pickup / Delivery */
    #createTaskModal .task-mode-switch {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    #createTaskModal .task-mode-btn {
        width: 100%;
        min-height: 72px;
        border-radius: 14px;
        border: 1px solid var(--task-border-strong);
        background: #fff;
        text-align: left;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: .18s ease;
        color: #334155;
    }

    #createTaskModal .task-mode-btn:hover {
        border-color: #fdba74;
        background: #fffaf5;
    }

    #createTaskModal .task-mode-btn.active {
        border-color: #fb923c;
        background: #fff7ed;
        box-shadow: inset 0 0 0 1px rgba(249, 115, 22, .14);
    }

    #createTaskModal .task-mode-btn .mode-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(249, 115, 22, .10);
        color: var(--task-orange);
        font-size: 18px;
        flex: 0 0 auto;
    }

    #createTaskModal .task-mode-btn:not(.active) .mode-icon {
        background: #f8fafc;
        color: #475569;
    }

    #createTaskModal .mode-title {
        display: block;
        font-size: 15px;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 4px;
    }

    #createTaskModal .mode-desc {
        display: block;
        color: #64748b;
        font-size: 12px;
        line-height: 1.25;
    }

    /* Search reference */
    #createTaskModal .reference-search-row {
        display: grid;
        grid-template-columns: 1fr 54px;
        gap: 12px;
        align-items: center;
    }

    #createTaskModal .reference-search-group .input-group-text {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    #createTaskModal .reference-search-group .form-control {
        border-left: 0;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    #createTaskModal .btn-search-reference {
        min-height: 44px;
        border: 0;
        border-radius: 10px;
        color: #fff;
        background: linear-gradient(135deg, #fb923c 0%, var(--task-orange) 100%);
        box-shadow: 0 8px 16px rgba(249, 115, 22, .16);
    }

    #createTaskModal .btn-search-reference:hover {
        background: linear-gradient(135deg, var(--task-orange) 0%, var(--task-orange-dark) 100%);
    }

    #createTaskModal .helper-text {
        margin-top: 8px;
        color: #64748b;
        font-size: 12px;
    }

    /* Section title */
    #createTaskModal .card-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 14px;
    }

    #createTaskModal .card-section-title i {
        color: var(--task-orange);
        font-size: 17px;
    }

    /* Items */
    #createTaskModal .items-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    #createTaskModal .item-count-badge {
        background: #f1f5f9;
        color: #475569;
        border-radius: 999px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 800;
    }

    #createTaskModal .item-editor {
        border: 1px dashed #fdba74;
        background: #fffdf9;
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 14px;
    }

    #createTaskModal .btn-add-item {
        min-height: 44px;
        border: 1px dashed #fb923c;
        border-radius: 10px;
        color: var(--task-orange-dark);
        background: #fff;
        font-weight: 800;
        padding: 0 16px;
        transition: .18s ease;
    }

    #createTaskModal .btn-add-item:hover {
        background: #fff7ed;
    }

    #createTaskModal .btn-update-item {
        min-height: 44px;
        border: 0;
        border-radius: 10px;
        color: #fff;
        background: linear-gradient(135deg, #fb923c 0%, var(--task-orange) 100%);
        font-weight: 800;
        padding: 0 18px;
    }

    #createTaskModal .btn-item-cancel {
        min-height: 44px;
        border-radius: 10px;
        font-weight: 700;
    }

    #createTaskModal .items-table-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
    }

    #createTaskModal .items-table {
        margin: 0;
        table-layout: fixed;
    }

    #createTaskModal .items-table thead th {
        padding: 12px 12px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        color: #475569;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
        vertical-align: middle;
    }

    #createTaskModal .items-table tbody td {
        padding: 12px 12px;
        border-color: #edf2f7;
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    #createTaskModal .item-name-cell strong {
        display: block;
        font-weight: 800;
        color: #334155;
    }

    #createTaskModal .item-name-cell small {
        display: block;
        color: #94a3b8;
        margin-top: 2px;
    }

    #createTaskModal .btn-action-icon {
        width: 34px;
        height: 34px;
        padding: 0;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    #createTaskModal .empty-items {
        min-height: 110px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #94a3b8;
        text-align: center;
        padding: 18px;
    }

    #createTaskModal .empty-items i {
        color: #fdba74;
        font-size: 28px;
    }

    #createTaskModal .empty-items strong {
        color: #334155;
        font-size: 14px;
    }

    #createTaskModal .modal-footer {
        padding: 14px 22px 18px;
        background: #fff;
        border-top: 1px solid #eef2f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    #createTaskModal .footer-note {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
    }

    #createTaskModal .footer-note i {
        color: #94a3b8;
        font-size: 16px;
    }

    #createTaskModal .footer-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #createTaskModal .btn-cancel {
        min-width: 110px;
        min-height: 44px;
        border-radius: 10px;
        border: 1px solid #dbe3ec;
        background: #fff;
        color: #334155;
        font-weight: 700;
    }

    #createTaskModal .btn-save {
        min-width: 170px;
        min-height: 44px;
        border: 0;
        border-radius: 10px;
        color: #fff;
        font-weight: 800;
        background: linear-gradient(135deg, #fb923c 0%, var(--task-orange) 100%);
        box-shadow: 0 8px 16px rgba(249, 115, 22, .18);
    }

    #createTaskModal .btn-save:hover {
        background: linear-gradient(135deg, var(--task-orange) 0%, var(--task-orange-dark) 100%);
    }

    @media (max-width: 991.98px) {
        #createTaskModal .modal-body {
            padding: 14px;
            max-height: 78vh;
        }

        #createTaskModal .task-card {
            padding: 14px;
        }

        #createTaskModal .reference-search-row,
        #createTaskModal .task-mode-switch {
            grid-template-columns: 1fr;
        }

        #createTaskModal .footer-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>

<div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <form id="createTaskForm" action="{{ route('pickup-tasks.store') }}" method="POST" class="w-100">
            @csrf

            <!-- hidden real values for backend -->
            <input type="hidden" name="task_type" id="taskTypeInput" value="pickup">
            <input type="hidden" name="pickup_reference" id="pickupReferenceHidden">
            <input type="hidden" name="delivery_so_number" id="deliverySoHidden">

            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title-wrap">
                        <span class="title-icon">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                        <div>
                            <h5 class="modal-title">Buat Tugas Driver Baru</h5>
                            <p class="modal-subtitle">Buat penugasan pickup atau delivery untuk driver</p>
                        </div>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- TOP SECTION -->
                    <section class="task-card mb-3">
                        <div class="row g-3 align-items-start">
                            <div class="col-lg-6">
                                <label class="block-label">Jenis Perjalanan</label>

                                <div class="task-mode-switch" id="taskModeSwitch">
                                    <button type="button" class="task-mode-btn active" data-type="pickup">
                                        <span class="mode-icon">
                                            <i class="fa-solid fa-box-open"></i>
                                        </span>
                                        <span>
                                            <span class="mode-title">Pickup</span>
                                            <span class="mode-desc">Penjemputan barang</span>
                                        </span>
                                    </button>

                                    <button type="button" class="task-mode-btn" data-type="delivery">
                                        <span class="mode-icon">
                                            <i class="fa-solid fa-truck"></i>
                                        </span>
                                        <span>
                                            <span class="mode-title">Delivery</span>
                                            <span class="mode-desc">Pengiriman barang</span>
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label class="block-label">
                                    <span id="referenceSearchLabel">Nomor PO</span>
                                    <i class="fa-regular fa-circle-question text-muted"></i>
                                </label>

                                <div class="reference-search-row">
                                    <div class="input-group reference-search-group">
                                        <span class="input-group-text">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </span>
                                        <input
                                            type="text"
                                            id="referenceSearchInput"
                                            class="form-control"
                                            placeholder="Cari nomor PO..."
                                        >
                                    </div>

                                    <button type="button" class="btn btn-search-reference" id="btnSearchReference" title="Cari">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                </div>

                                <div class="helper-text" id="referenceSearchHelper">
                                    Otomatis berubah ke Nomor SO saat memilih Delivery.
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    Pilih Driver <span class="required-star">*</span>
                                </label>
                                <select name="driver_id" class="form-select" required>
                                    <option value="">Pilih driver...</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    Pilih Kendaraan <span class="required-star">*</span>
                                </label>
                                <select name="vehicle_id" class="form-select" required>
                                    <option value="">Pilih kendaraan...</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }} - {{ $vehicle->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    Tanggal Penjemputan / Pengiriman <span class="required-star">*</span>
                                </label>
                                <input type="datetime-local" name="dispatch_date" class="form-control" value="{{ date('Y-m-d\TH:i') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Estimasi Waktu Tiba</label>
                                <input type="datetime-local" name="estimated_arrival" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Prioritas (Opsional)</label>
                                <select name="priority" class="form-select">
                                    <option value="normal">Normal</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- PICKUP FIELDS -->
                    <div class="task-fields pickup-fields">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <section class="task-card h-100">
                                    <div class="card-section-title">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span>Lokasi Pickup</span>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">
                                                Vendor / Supplier <span class="required-star">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                name="pickup_name"
                                                class="form-control pickup-required"
                                                placeholder="Pilih vendor / supplier..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Nama PIC</label>
                                            <input
                                                type="text"
                                                name="pickup_pic_name"
                                                class="form-control"
                                                placeholder="Nama PIC vendor..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Lokasi Pickup</label>
                                            <input
                                                type="text"
                                                name="pickup_point"
                                                class="form-control"
                                                placeholder="Pilih lokasi pickup..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">
                                                Alamat Pickup <span class="required-star">*</span>
                                            </label>
                                            <textarea
                                                name="pickup_location"
                                                class="form-control pickup-required"
                                                placeholder="Masukkan alamat lengkap lokasi pickup..."
                                            ></textarea>
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <div class="col-lg-6">
                                <section class="task-card h-100">
                                    <div class="card-section-title">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span>Lokasi Tujuan</span>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">
                                                Nama Tempat Tujuan <span class="required-star">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                name="destination_name"
                                                class="form-control pickup-required"
                                                placeholder="Masukkan nama tempat tujuan..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Penerima / PIC</label>
                                            <input
                                                type="text"
                                                name="destination_pic_name"
                                                class="form-control"
                                                placeholder="Masukkan nama penerima atau PIC..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Lokasi Tujuan</label>
                                            <input
                                                type="text"
                                                name="destination_point"
                                                class="form-control"
                                                placeholder="Pilih lokasi tujuan..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">
                                                Alamat Tujuan <span class="required-star">*</span>
                                            </label>
                                            <textarea
                                                name="pickup_destination"
                                                class="form-control pickup-required"
                                                placeholder="Masukkan alamat lengkap tujuan..."
                                            ></textarea>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>

                    <!-- DELIVERY FIELDS -->
                    <div class="task-fields delivery-fields d-none">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <section class="task-card h-100">
                                    <div class="card-section-title">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span>Lokasi Asal</span>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">
                                                Nama Gudang / Lokasi Asal <span class="required-star">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                name="delivery_pickup_name"
                                                class="form-control delivery-required"
                                                placeholder="Gudang Utama / Cabang..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">PIC Pengirim</label>
                                            <input
                                                type="text"
                                                name="delivery_sender_pic"
                                                class="form-control"
                                                placeholder="Nama PIC pengirim..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Lokasi Asal</label>
                                            <input
                                                type="text"
                                                name="delivery_origin_point"
                                                class="form-control"
                                                placeholder="Pilih lokasi asal..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">
                                                Alamat Asal <span class="required-star">*</span>
                                            </label>
                                            <textarea
                                                name="delivery_pickup_location"
                                                class="form-control delivery-required"
                                                placeholder="Alamat lengkap asal barang..."
                                            ></textarea>
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <div class="col-lg-6">
                                <section class="task-card h-100">
                                    <div class="card-section-title">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span>Lokasi Tujuan</span>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">
                                                Nama Pelanggan / Customer <span class="required-star">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                name="customer_name"
                                                class="form-control delivery-required"
                                                placeholder="PT Maju Jaya..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Penerima / PIC</label>
                                            <input
                                                type="text"
                                                name="delivery_receiver_pic"
                                                class="form-control"
                                                placeholder="Masukkan nama penerima atau PIC..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Lokasi Tujuan</label>
                                            <input
                                                type="text"
                                                name="delivery_target_point"
                                                class="form-control"
                                                placeholder="Pilih lokasi tujuan..."
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">
                                                Alamat Tujuan <span class="required-star">*</span>
                                            </label>
                                            <textarea
                                                name="delivery_address"
                                                class="form-control delivery-required"
                                                placeholder="Alamat lengkap tujuan pengiriman..."
                                            ></textarea>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>

                    <!-- ITEMS -->
                    <section class="task-card mt-3">
                        <div class="items-card-header">
                            <div class="card-section-title mb-0">
                                <i class="fa-solid fa-boxes-stacked"></i>
                                <span>Daftar Barang</span>
                            </div>

                            <span class="item-count-badge">
                                <span id="itemCount">0</span> item
                            </span>
                        </div>

                        <div class="item-editor">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label">No Barang</label>
                                    <input
                                        type="text"
                                        id="itemNumberInput"
                                        class="form-control"
                                        placeholder="Opsional"
                                    >
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Nama / Deskripsi Barang <span class="required-star">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="itemDescriptionInput"
                                        class="form-control"
                                        placeholder="Wajib diisi"
                                    >
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">
                                        Qty <span class="required-star">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        id="itemQuantityInput"
                                        class="form-control"
                                        placeholder="0"
                                    >
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Satuan</label>
                                    <input
                                        type="text"
                                        id="itemUnitInput"
                                        class="form-control"
                                        placeholder="pcs / kg"
                                    >
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Harga Satuan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            id="itemPriceInput"
                                            class="form-control"
                                            placeholder="0"
                                        >
                                    </div>
                                </div>

                                <div class="col-12 d-flex gap-2 flex-wrap">
                                    <button type="button" id="btnAddItem" class="btn btn-add-item">
                                        <i class="fa-solid fa-plus me-2"></i>
                                        Tambah Barang
                                    </button>

                                    <button type="button" id="btnCancelEditItem" class="btn btn-light btn-item-cancel d-none">
                                        Batal Edit
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="items-table-wrap">
                            <div class="table-responsive">
                                <table class="table items-table align-middle mb-0" id="itemsTable" style="display:none;">
                                    <colgroup>
                                        <col style="width: 36%;">
                                        <col style="width: 12%;">
                                        <col style="width: 12%;">
                                        <col style="width: 15%;">
                                        <col style="width: 15%;">
                                        <col style="width: 10%;">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th class="text-end">Qty</th>
                                            <th>Satuan</th>
                                            <th class="text-end">Harga Satuan</th>
                                            <th class="text-end">Total Harga</th>
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

                        <div id="itemsHiddenInputs"></div>
                    </section>
                </div>

                <div class="modal-footer">
                    <div class="footer-note">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Pastikan data pickup dan tujuan sudah benar.</span>
                    </div>

                    <div class="footer-actions">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-save">
                            <i class="fa-solid fa-floppy-disk me-2"></i>
                            Simpan Tugas
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('createTaskForm');
    const taskTypeInput = document.getElementById('taskTypeInput');
    const taskModeButtons = document.querySelectorAll('#taskModeSwitch .task-mode-btn');

    const pickupFields = document.querySelector('#createTaskModal .pickup-fields');
    const deliveryFields = document.querySelector('#createTaskModal .delivery-fields');

    const pickupRequired = document.querySelectorAll('#createTaskModal .pickup-required');
    const deliveryRequired = document.querySelectorAll('#createTaskModal .delivery-required');

    const referenceLabel = document.getElementById('referenceSearchLabel');
    const referenceInput = document.getElementById('referenceSearchInput');
    const referenceHelper = document.getElementById('referenceSearchHelper');
    const btnSearchReference = document.getElementById('btnSearchReference');
    const pickupReferenceHidden = document.getElementById('pickupReferenceHidden');
    const deliverySoHidden = document.getElementById('deliverySoHidden');

    const itemNumberInput = document.getElementById('itemNumberInput');
    const itemDescriptionInput = document.getElementById('itemDescriptionInput');
    const itemQuantityInput = document.getElementById('itemQuantityInput');
    const itemUnitInput = document.getElementById('itemUnitInput');
    const itemPriceInput = document.getElementById('itemPriceInput');
    const btnAddItem = document.getElementById('btnAddItem');
    const btnCancelEditItem = document.getElementById('btnCancelEditItem');
    const tableBody = document.getElementById('itemsTableBody');
    const emptyState = document.getElementById('emptyItemsState');
    const hiddenInputs = document.getElementById('itemsHiddenInputs');
    const itemCount = document.getElementById('itemCount');
    const itemsTable = document.getElementById('itemsTable');

    let items = [];
    let editingIndex = null;

    function getCurrentTaskType() {
        return taskTypeInput.value || 'pickup';
    }

    function syncReferenceValue() {
        if (getCurrentTaskType() === 'pickup') {
            pickupReferenceHidden.value = referenceInput.value.trim();
            deliverySoHidden.value = '';
        } else {
            deliverySoHidden.value = referenceInput.value.trim();
            pickupReferenceHidden.value = '';
        }
    }

    function updateReferenceUI(type) {
        if (type === 'pickup') {
            referenceLabel.textContent = 'Nomor PO';
            referenceInput.placeholder = 'Cari nomor PO...';
            referenceHelper.textContent = 'Otomatis berubah ke Nomor SO saat memilih Delivery.';
        } else {
            referenceLabel.textContent = 'Nomor SO';
            referenceInput.placeholder = 'Cari nomor SO...';
            referenceHelper.textContent = 'Otomatis berubah ke Nomor PO saat memilih Pickup.';
        }
        syncReferenceValue();
    }

    function toggleRequiredFields(type) {
        pickupRequired.forEach(el => {
            el.required = (type === 'pickup');
        });

        deliveryRequired.forEach(el => {
            el.required = (type === 'delivery');
        });
    }

    function switchTaskType(type) {
        taskTypeInput.value = type;

        taskModeButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.type === type);
        });

        pickupFields.classList.toggle('d-none', type !== 'pickup');
        deliveryFields.classList.toggle('d-none', type !== 'delivery');

        updateReferenceUI(type);
        toggleRequiredFields(type);
    }

    taskModeButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            switchTaskType(this.dataset.type);
        });
    });

    referenceInput.addEventListener('input', syncReferenceValue);

    btnSearchReference.addEventListener('click', async function () {
        const refNumber = referenceInput.value.trim();
        if (!refNumber) {
            if (window.Swal) {
                Swal.fire({ icon: 'warning', title: 'Oops', text: 'Silakan masukkan nomor referensi (SO/PO) terlebih dahulu.' });
            } else {
                alert('Silakan masukkan nomor referensi (SO/PO) terlebih dahulu.');
            }
            return;
        }

        const taskType = getCurrentTaskType();
        const isDelivery = taskType === 'delivery';
        
        // Langsung tembak ke API asli sesuai permintaan
        const apiUrl = isDelivery 
            ? `https://akurasi-api.aqpa-indonesia.com/api/integration/penjualan-so?search=${encodeURIComponent(refNumber)}`
            : `https://akurasi-api.aqpa-indonesia.com/api/integration/pembelian?search=${encodeURIComponent(refNumber)}`;

        syncReferenceValue();

        // UI feedback
        const originalIcon = this.innerHTML;
        this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        this.disabled = true;

        try {
            const response = await fetch(apiUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-API-Key': 'Ym95Y29tcG9zaXRpb25leHBsYW5hdGlvbnRob3VnaHRwZWFjZWdpcmxjb2FjaHNlbnM='
                }
            });
            if (!response.ok) throw new Error('Data tidak ditemukan');
            const resData = await response.json();
            
            // API mengembalikan { data: [...] }
            const apiItems = resData.data || [];
            if (apiItems.length === 0) throw new Error('Data kosong');
            
            // Ambil data pertama untuk informasi utama
            const firstItem = apiItems[0];

            // Populate Fields
            if (isDelivery) {
                form.querySelector('input[name="customer_name"]').value = firstItem.nama_pelanggan || '';
                form.querySelector('textarea[name="delivery_address"]').value = firstItem.shipto || '';
            } else {
                form.querySelector('input[name="pickup_name"]').value = firstItem.nama_pemasok || '';
            }

            // Populate Items
            items = []; // Clear existing items
            apiItems.forEach(apiItem => {
                items.push({
                    item_number: apiItem.no_barang || '',
                    item_description: apiItem.deskripsi_barang || apiItem.nama_barang || '',
                    quantity: apiItem.qty || 1,
                    unit: apiItem.unit || apiItem.satuan || '',
                    unit_price: apiItem.unit_price || apiItem.harga_satuan || apiItem.harga || 0
                });
            });
            renderItems();

            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Data Ditemukan',
                    text: 'Lokasi dan daftar barang telah diisi otomatis.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } catch (error) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak Ditemukan',
                    text: 'Data tidak ditemukan di API. Silakan isi form secara manual.',
                    confirmButtonColor: '#f97316'
                });
            } else {
                alert('Data tidak ditemukan di API. Silakan isi form secara manual.');
            }
        } finally {
            this.innerHTML = originalIcon;
            this.disabled = false;
        }
    });

    // Also trigger search on Enter key in the input
    referenceInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnSearchReference.click();
        }
    });

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
        editingIndex = null;
        itemNumberInput.value = '';
        itemDescriptionInput.value = '';
        itemQuantityInput.value = '';
        itemUnitInput.value = '';
        itemPriceInput.value = '';
        btnAddItem.innerHTML = '<i class="fa-solid fa-plus me-2"></i>Tambah Barang';
        btnAddItem.classList.remove('btn-update-item');
        btnAddItem.classList.add('btn-add-item');
        btnCancelEditItem.classList.add('d-none');

        itemDescriptionInput.classList.remove('is-invalid');
        itemQuantityInput.classList.remove('is-invalid');
    }

    function startEditItem(index) {
        const item = items[index];
        if (!item) return;

        editingIndex = index;
        itemNumberInput.value = item.item_number || '';
        itemDescriptionInput.value = item.item_description || '';
        itemQuantityInput.value = item.quantity || '';
        itemUnitInput.value = item.unit || '';
        itemPriceInput.value = item.unit_price || '';

        btnAddItem.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i>Simpan Item';
        btnAddItem.classList.remove('btn-add-item');
        btnAddItem.classList.add('btn-update-item');
        btnCancelEditItem.classList.remove('d-none');

        itemDescriptionInput.focus();
    }

    btnCancelEditItem.addEventListener('click', resetItemEditor);

    function renderItems() {
        tableBody.innerHTML = '';
        hiddenInputs.innerHTML = '';
        itemCount.textContent = items.length;

        if (items.length === 0) {
            emptyState.style.display = 'flex';
            itemsTable.style.display = 'none';
            return;
        }

        emptyState.style.display = 'none';
        itemsTable.style.display = 'table';

        items.forEach((item, index) => {
            const totalPrice = (Number(item.quantity) || 0) * (Number(item.unit_price) || 0);
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="item-name-cell">
                    <strong>${escapeHtml(item.item_description)}</strong>
                    ${item.item_number ? `<small>No Barang: ${escapeHtml(item.item_number)}</small>` : ''}
                </td>
                <td class="text-end">${formatNumber(item.quantity)}</td>
                <td>${escapeHtml(item.unit || '-')}</td>
                <td class="text-end">${formatCurrency(item.unit_price)}</td>
                <td class="text-end fw-bold">${formatCurrency(totalPrice)}</td>
                <td class="text-center">
                    <div class="d-inline-flex gap-1">
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary btn-action-icon btn-edit-row"
                            data-index="${index}"
                            title="Edit item"
                        >
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger btn-action-icon btn-remove-row"
                            data-index="${index}"
                            title="Hapus item"
                        >
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
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
            itemDescriptionInput.classList.add('is-invalid');
            itemDescriptionInput.focus();
            return;
        }

        if (quantity === '' || Number(quantity) <= 0) {
            itemQuantityInput.classList.add('is-invalid');
            itemQuantityInput.focus();
            return;
        }

        itemDescriptionInput.classList.remove('is-invalid');
        itemQuantityInput.classList.remove('is-invalid');

        const itemPayload = {
            item_number: itemNumberInput.value.trim(),
            item_description: description,
            quantity: quantity,
            unit: itemUnitInput.value.trim(),
            unit_price: itemPriceInput.value || 0
        };

        if (editingIndex === null) {
            items.push(itemPayload);
        } else {
            items[editingIndex] = itemPayload;
        }

        renderItems();
        resetItemEditor();
    });

    tableBody.addEventListener('click', function (event) {
        const editBtn = event.target.closest('.btn-edit-row');
        if (editBtn) {
            const index = Number(editBtn.dataset.index);
            startEditItem(index);
            return;
        }

        const deleteBtn = event.target.closest('.btn-remove-row');
        if (deleteBtn) {
            const index = Number(deleteBtn.dataset.index);
            items.splice(index, 1);

            if (editingIndex === index) {
                resetItemEditor();
            } else if (editingIndex !== null && index < editingIndex) {
                editingIndex--;
            }

            renderItems();
        }
    });

    itemDescriptionInput.addEventListener('input', function () {
        this.classList.remove('is-invalid');
    });

    itemQuantityInput.addEventListener('input', function () {
        this.classList.remove('is-invalid');
    });

    window.openTaskModal = function(mode, task = null, taskItems = []) {
        document.querySelectorAll('#createTaskModal .is-invalid').forEach(el => el.classList.remove('is-invalid'));
        resetItemEditor();

        const title = document.querySelector('#createTaskModal .modal-title');
        const subtitle = document.querySelector('#createTaskModal .modal-subtitle');
        const icon = document.querySelector('#createTaskModal .title-icon i');
        const submitBtn = document.querySelector('#createTaskModal .btn-save');

        if (mode === 'create') {
            form.action = "{{ route('pickup-tasks.store') }}";
            title.textContent = 'Buat Tugas Driver Baru';
            subtitle.textContent = 'Buat penugasan pickup atau delivery untuk driver';
            icon.className = 'fa-solid fa-plus';
            submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i>Simpan Tugas';

            form.querySelector('input[name="_method"]')?.remove();

            form.reset();
            items = [];

            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            form.querySelector('input[name="dispatch_date"]').value = now.toISOString().slice(0, 16);

            referenceInput.value = '';
            pickupReferenceHidden.value = '';
            deliverySoHidden.value = '';

            switchTaskType('pickup');
            renderItems();
        }

        if (mode === 'edit' && task) {
            form.action = task.update_url;
            title.textContent = 'Edit Tugas Driver';
            subtitle.textContent = 'Perbarui penugasan pickup atau delivery untuk driver';
            icon.className = 'fa-solid fa-pen-to-square';
            submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan';

            if (!form.querySelector('input[name="_method"]')) {
                form.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');
            }

            form.querySelector('select[name="driver_id"]').value = task.driver_id || '';
            form.querySelector('select[name="vehicle_id"]').value = task.vehicle_id || '';
            form.querySelector('select[name="priority"]').value = task.priority || 'normal';

            if (task.dispatch_date) {
                form.querySelector('input[name="dispatch_date"]').value = task.dispatch_date.substring(0, 16);
            }
            if (task.estimated_arrival) {
                form.querySelector('input[name="estimated_arrival"]').value = task.estimated_arrival.substring(0, 16);
            }

            const type = task.task_type || 'pickup';
            switchTaskType(type);

            if (type === 'pickup') {
                referenceInput.value = task.reference_number || '';
                pickupReferenceHidden.value = task.reference_number || '';
                deliverySoHidden.value = '';

                form.querySelector('input[name="pickup_name"]').value = task.pickup_name || '';
                form.querySelector('textarea[name="pickup_location"]').value = task.pickup_location || '';
                form.querySelector('input[name="destination_name"]').value = task.destination_name || '';
                form.querySelector('textarea[name="pickup_destination"]').value = task.destination || '';

                form.querySelector('input[name="pickup_pic_name"]').value = task.pickup_pic_name || '';
                form.querySelector('input[name="pickup_point"]').value = task.pickup_point || '';
                form.querySelector('input[name="destination_pic_name"]').value = task.destination_pic_name || '';
                form.querySelector('input[name="destination_point"]').value = task.destination_point || '';
            } else {
                const so = task.sales_order || {};

                referenceInput.value = so.so_number || task.reference_number || '';
                deliverySoHidden.value = referenceInput.value;
                pickupReferenceHidden.value = '';

                form.querySelector('input[name="delivery_pickup_name"]').value = task.pickup_name || task.delivery_pickup_name || '';
                form.querySelector('textarea[name="delivery_pickup_location"]').value = task.pickup_location || task.delivery_pickup_location || '';
                form.querySelector('input[name="customer_name"]').value = so.customer_name || task.customer_name || '';

                let address = task.delivery_address || '';
                if (!address && so.source_data) {
                    try {
                        const sourceData = typeof so.source_data === 'string'
                            ? JSON.parse(so.source_data)
                            : so.source_data;
                        address = sourceData.address || '';
                    } catch (e) {}
                }

                form.querySelector('textarea[name="delivery_address"]').value = address;
                form.querySelector('input[name="delivery_sender_pic"]').value = task.delivery_sender_pic || '';
                form.querySelector('input[name="delivery_origin_point"]').value = task.delivery_origin_point || '';
                form.querySelector('input[name="delivery_receiver_pic"]').value = task.delivery_receiver_pic || '';
                form.querySelector('input[name="delivery_target_point"]').value = task.delivery_target_point || '';
            }

            items = Array.isArray(taskItems) ? [...taskItems] : [];
            renderItems();
        }

        const modal = new bootstrap.Modal(document.getElementById('createTaskModal'));
        modal.show();
    };

    form.addEventListener('submit', function (event) {
        syncReferenceValue();

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



    switchTaskType('pickup');
    renderItems();
});
</script>