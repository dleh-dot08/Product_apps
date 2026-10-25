<x-app-layout>
    <style>
        .dashboard-banner {
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            border: 1px solid rgba(79, 70, 229, 0.2);
            transition: all 0.3s ease;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
            overflow: hidden;
        }
        html:not([data-bs-theme="dark"]) .dashboard-banner {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(238, 242, 255, 0.85) 60%, rgba(224, 231, 255, 0.85) 100%);
            box-shadow: 0 4px 20px -2px rgba(99, 102, 241, 0.15);
        }
        html[data-bs-theme="dark"] .dashboard-banner {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 27, 75, 0.85) 100%);
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
            background-color: rgba(99, 102, 241, 0.15);
            color: #4f46e5;
            border: 1px solid rgba(79, 70, 229, 0.2);
        }
        html[data-bs-theme="dark"] .banner-badge {
            background-color: rgba(99, 102, 241, 0.25);
            color: #a5b4fc;
            border: 1px solid rgba(129, 140, 248, 0.3);
        }
        .banner-title {
            font-size: clamp(1.35rem, 2.2vw, 1.85rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            margin: 0;
        }
        html:not([data-bs-theme="dark"]) .banner-title { color: #0f172a; }
        html[data-bs-theme="dark"] .banner-title { color: #f8fafc; }

        .premium-card {
            border-radius: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        .premium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px -8px rgba(0, 0, 0, 0.25);
        }
        .premium-icon-box {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
        }

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
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #64748b;
            border-bottom-width: 2px;
            padding-top: 0.9rem;
            padding-bottom: 0.9rem;
        }

        /* Modal Styles — Clean White Elevated Style (Matching Task Modal) */
        #createExpeditionModal, #editExpeditionModal {
            --exp-primary: #4f46e5;
            --exp-primary-dark: #3730a3;
            --exp-border: #e2e8f0;
            --exp-border-strong: #cbd5e1;
            --exp-text: #0f172a;
        }

        .exp-modal-dialog {
            max-width: 1240px;
        }

        .exp-modal-content {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(15, 23, 42, .18);
            background: #ffffff;
        }

        .exp-modal-header {
            padding: 20px 26px 16px;
            background: #ffffff;
            border-bottom: 1px solid #eef2f7;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .exp-title-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            box-shadow: 0 8px 18px rgba(79, 70, 229, .22);
            flex: 0 0 auto;
        }

        .exp-title-icon.warning-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 8px 18px rgba(217, 119, 6, .22);
        }

        .exp-modal-body {
            padding: 20px 26px;
            background: #fbfcfe;
            max-height: 75vh;
            overflow-y: auto;
        }

        .exp-card {
            background: #ffffff;
            border: 1px solid var(--exp-border);
            border-radius: 16px;
            padding: 18px 20px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .03);
            margin-bottom: 16px;
        }

        .exp-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 14px;
            letter-spacing: -0.01em;
        }

        .exp-card-title i {
            color: var(--exp-primary);
            font-size: 16px;
        }

        .exp-form-label {
            margin-bottom: 6px;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            display: block;
        }

        .exp-form-control,
        .exp-form-select {
            min-height: 42px;
            border: 1px solid #dbe3ec;
            background-color: #ffffff;
            color: #1e293b;
            font-size: 13.5px;
            border-radius: 10px;
            transition: all 0.18s ease;
        }

        .exp-form-control:focus,
        .exp-form-select:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .14);
        }

        /* Segmented Mode Button */
        .exp-mode-switch {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .exp-mode-btn {
            width: 100%;
            min-height: 58px;
            border-radius: 12px;
            border: 1px solid var(--exp-border-strong);
            background: #ffffff;
            text-align: left;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: .15s ease;
            color: #334155;
            cursor: pointer;
        }

        .exp-mode-btn:hover {
            border-color: #818cf8;
            background: #faf5ff;
        }

        .exp-mode-btn.active {
            border-color: #6366f1;
            background: #eef2ff;
            box-shadow: inset 0 0 0 1px rgba(79, 70, 229, .2);
        }

        .exp-mode-btn .mode-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(79, 70, 229, .10);
            color: var(--exp-primary);
            font-size: 16px;
            flex: 0 0 auto;
        }

        .exp-mode-btn:not(.active) .mode-icon {
            background: #f1f5f9;
            color: #64748b;
        }

        .btn-sync-reference {
            min-height: 42px;
            border: 0;
            border-radius: 10px;
            color: #ffffff;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            box-shadow: 0 4px 12px rgba(79, 70, 229, .2);
            font-weight: 700;
            padding: 0 16px;
        }
        .btn-sync-reference:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: #ffffff;
        }

        /* Items Editor Table */
        .items-editor-card {
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            padding: 14px;
            background: #f8fafc;
            margin-bottom: 12px;
        }
        .items-table th {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            background: #f1f5f9;
            padding: 8px 12px;
        }
        .items-table td {
            font-size: 13px;
            padding: 8px 12px;
        }
        .empty-items-box {
            padding: 30px 20px;
            text-align: center;
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            color: #94a3b8;
        }
    </style>

    <div class="container-fluid px-3 px-md-4 py-3">

        <!-- Flash Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3 shadow-sm border-0 rounded-3" role="alert">
                <i class="fa-solid fa-circle-check me-2 fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-3 shadow-sm border-0 rounded-3" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm rounded-3">
                <div class="fw-bold mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i> Terdapat kesalahan input:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- BANNER HEADER -->
        <div class="dashboard-banner mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <span class="banner-badge">
                        <i class="fa-solid fa-plane-departure"></i> LOGISTIK & DISTRIBUSI (3PL)
                    </span>
                    <h1 class="banner-title">Monitoring Pengiriman Ekspedisi</h1>
                    <p class="text-muted mb-0 small mt-1">
                        Pencatatan resi, sync SO/PO otomatis, multi-item barang, tracking rute pengiriman ke pelanggan melalui ekspedisi luar & kargo.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('ekspedisi.vendors.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm fw-bold">
                        <i class="fa-solid fa-handshake"></i>
                        <span>Master Vendor</span>
                    </a>
                    @if(auth()->user()->hasPermission('Create Ekspedisi'))
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createExpeditionModal">
                        <i class="fa-solid fa-plus"></i>
                        <span>Input Ekspedisi Baru</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- KPI SUMMARY CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card premium-card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small fw-bold text-uppercase tracking-wider">Dalam Perjalanan</span>
                                <h3 class="fw-extrabold mb-0 mt-1 text-white">{{ number_format($stats['total_active']) }}</h3>
                                <div class="small text-white-50 mt-1">Status On Delivery</div>
                            </div>
                            <div class="premium-icon-box">
                                <i class="fa-solid fa-truck-fast fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card premium-card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small fw-bold text-uppercase tracking-wider">Telah Terkirim</span>
                                <h3 class="fw-extrabold mb-0 mt-1 text-white">{{ number_format($stats['total_delivered']) }}</h3>
                                <div class="small text-white-50 mt-1">POD Tiba di Tujuan</div>
                            </div>
                            <div class="premium-icon-box">
                                <i class="fa-solid fa-circle-check fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card premium-card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small fw-bold text-uppercase tracking-wider">Total Muatan</span>
                                <h3 class="fw-extrabold mb-0 mt-1 text-white">{{ number_format($stats['total_colli']) }} <small class="fs-6 fw-normal">Koli</small></h3>
                                <div class="small text-white-50 mt-1">Akumulasi Barang Kirim</div>
                            </div>
                            <div class="premium-icon-box">
                                <i class="fa-solid fa-boxes-stacked fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card premium-card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-white-50 small fw-bold text-uppercase tracking-wider">Total Biaya Ongkir</span>
                                <h3 class="fw-extrabold mb-0 mt-1 text-white">Rp {{ number_format($stats['total_cost'], 0, ',', '.') }}</h3>
                                <div class="small text-white-50 mt-1">Biaya Vendor 3PL</div>
                            </div>
                            <div class="premium-icon-box">
                                <i class="fa-solid fa-money-bill-wave fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER & DATA TABLE CARD -->
        <div class="card data-card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent py-3 px-3 border-bottom">
                <form method="GET" action="{{ route('ekspedisi.index') }}" class="row g-2 align-items-center">
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Cari No. Resi, Vendor, SO/PO, Customer...">
                        </div>
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="ALL" {{ request('status') === 'ALL' ? 'selected' : '' }}>Semua Status</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="on_delivery" {{ request('status') === 'on_delivery' ? 'selected' : '' }}>Dalam Pengiriman</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Terkirim (Sampai)</option>
                            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Retur</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="vendor_id" class="form-select" onchange="this.form.submit()">
                            <option value="ALL">Semua Vendor</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->vendor_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4 d-flex gap-2 justify-content-end">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" title="Mulai Tanggal Kirim">
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" title="Sampai Tanggal Kirim">
                        <button type="submit" class="btn btn-secondary px-3">Filter</button>
                        @if(request()->anyFilled(['search', 'status', 'vendor_id', 'start_date', 'end_date']))
                            <a href="{{ route('ekspedisi.index') }}" class="btn btn-outline-danger" title="Reset Filter"><i class="fa-solid fa-xmark"></i></a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-premium align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Pengiriman & Resi</th>
                                <th>Vendor & Layanan</th>
                                <th>Tipe & Referensi</th>
                                <th>Rute (Asal → Tujuan)</th>
                                <th>Muatan & Rincian Item</th>
                                <th class="text-end">Biaya (Rp)</th>
                                <th class="text-center">Tgl Kirim & ETA</th>
                                <th class="text-center">Aktual Sampai</th>
                                <th class="text-center">Status</th>
                                <th class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expeditions as $item)
                                <tr>
                                    <td class="ps-3">
                                        <span class="fw-bold text-primary font-monospace d-block">{{ $item->expedition_number }}</span>
                                        <span class="font-monospace small text-muted">
                                            Resi: <strong class="text-dark">{{ $item->tracking_number ?: '-' }}</strong>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold d-block">{{ $item->vendor_name }}</span>
                                        <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.68rem;">{{ $item->service_type ?: 'Cargo Darat' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->task_type === 'delivery' ? 'bg-primary-subtle text-primary border border-primary' : 'bg-warning-subtle text-warning border border-warning' }} text-uppercase fw-bold" style="font-size: 0.68rem;">
                                            {{ $item->task_type }}
                                        </span>
                                        @if($item->reference_number)
                                            <span class="d-block small font-monospace fw-semibold mt-0.5">{{ $item->reference_number }}</span>
                                        @endif
                                        @if($item->customer_vendor_name)
                                            <span class="d-block text-muted" style="font-size: 0.72rem;">{{ Str::limit($item->customer_vendor_name, 22) }}</span>
                                        @endif
                                    </td>
                                    <td style="max-width: 220px;">
                                        <div class="small text-truncate" title="{{ $item->origin_address }}">
                                            <i class="fa-solid fa-circle-dot text-muted me-1" style="font-size: 0.65rem;"></i> {{ $item->origin_name ?: 'Gudang AQPA' }}
                                        </div>
                                        <div class="small fw-bold text-truncate text-indigo" title="{{ $item->destination_address }}">
                                            <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $item->destination_name ?: $item->destination_address }}
                                        </div>
                                        @if($item->receiver_pic_name)
                                            <div class="text-muted" style="font-size: 0.68rem;">PIC: {{ $item->receiver_pic_name }} ({{ $item->receiver_pic_phone ?: '-' }})</div>
                                        @endif
                                    </td>
                                    <td style="max-width: 240px;">
                                        <span class="d-block small fw-semibold text-truncate" title="{{ $item->item_description }}">
                                            {{ $item->item_description }}
                                        </span>
                                        <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                            <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.68rem;">
                                                {{ $item->total_colli }} Koli {{ $item->weight_kg ? '• ' . number_format($item->weight_kg, 1) . ' Kg' : '' }}
                                            </span>
                                            @if($item->items && $item->items->count() > 0)
                                                <span class="badge bg-primary-subtle text-primary border border-primary font-monospace" style="font-size: 0.68rem;">
                                                    {{ $item->items->count() }} Item
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end font-monospace fw-bold text-primary">
                                        Rp {{ number_format($item->cost_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center small">
                                        <div><span class="text-muted">Kirim:</span> <span class="fw-semibold">{{ $item->shipping_date ? $item->shipping_date->translatedFormat('d M Y') : '-' }}</span></div>
                                        <div><span class="text-muted">ETA:</span> <span class="fw-bold text-primary">{{ $item->estimated_arrival_date ? $item->estimated_arrival_date->translatedFormat('d M Y') : '-' }}</span></div>
                                    </td>
                                    <td class="text-center small">
                                        @if($item->actual_arrival_date)
                                            <span class="badge bg-success-subtle text-success border border-success font-monospace">
                                                {{ $item->actual_arrival_date->translatedFormat('d M Y H:i') }}
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic">Belum Tiba</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->status === 'delivered')
                                            <span class="badge bg-success px-2 py-1"><i class="fa-solid fa-check me-1"></i> Terkirim</span>
                                        @elseif($item->status === 'on_delivery')
                                            <span class="badge bg-primary px-2 py-1"><i class="fa-solid fa-truck-fast me-1"></i> On Delivery</span>
                                        @elseif($item->status === 'returned')
                                            <span class="badge bg-danger px-2 py-1"><i class="fa-solid fa-rotate-left me-1"></i> Retur</span>
                                        @elseif($item->status === 'cancelled')
                                            <span class="badge bg-secondary px-2 py-1">Batal</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-2 py-1">Draft</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-flex justify-content-center gap-1">
                                            @if($item->status !== 'delivered' && auth()->user()->hasPermission('Edit Ekspedisi'))
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success px-2 py-1 btn-mark-delivered" 
                                                        data-id="{{ $item->id }}"
                                                        data-number="{{ $item->expedition_number }}"
                                                        data-vendor="{{ $item->vendor_name }}"
                                                        data-destination="{{ $item->destination_name ?: $item->destination_address }}"
                                                        data-receiver="{{ $item->receiver_pic_name }}"
                                                        data-action="{{ route('ekspedisi.mark-delivered', $item->id) }}"
                                                        title="Tandai Sampai">
                                                    <i class="fa-solid fa-check-double"></i>
                                                </button>
                                            @endif

                                            @if(auth()->user()->hasPermission('Edit Ekspedisi'))
                                                <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1 btn-edit-expedition" data-expedition='@json($item)' title="Edit">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            @endif

                                            @if(auth()->user()->hasPermission('Delete Ekspedisi'))
                                                <form method="POST" action="{{ route('ekspedisi.destroy', $item->id) }}" class="d-inline" onsubmit="return confirm('Hapus data pengiriman ekspedisi {{ $item->expedition_number }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                                        <p class="mb-0 fw-semibold">Belum ada data pengiriman ekspedisi yang tercatat.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($expeditions->hasPages())
                <div class="card-footer bg-transparent py-2 border-top">
                    {{ $expeditions->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- MODAL CREATE EKSPEDISI (CLEAN TASK MODAL STYLE WITH SYNC SO/PO) -->
    <div class="modal fade" id="createExpeditionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered exp-modal-dialog">
            <div class="modal-content exp-modal-content">
                <form id="createExpeditionForm" method="POST" action="{{ route('ekspedisi.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="task_type" id="createTaskType" value="delivery">

                    <!-- Modal Header -->
                    <div class="exp-modal-header">
                        <div class="d-flex align-items-center gap-3">
                            <span class="exp-title-icon">
                                <i class="fa-solid fa-plane-departure"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">Buat Pengiriman Ekspedisi Baru</h5>
                                <p class="text-muted small mb-0">Input pengiriman 3PL dengan integrasi sinkronisasi nomor PO/SO dan rincian item barang.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="exp-modal-body">
                        <!-- TOP SECTION: MODE SO/PO & SYNC SEARCH -->
                        <div class="exp-card">
                            <div class="row g-3 align-items-center">
                                <div class="col-lg-5">
                                    <label class="exp-form-label">Jenis Tugas Pengiriman <span class="text-danger">*</span></label>
                                    <div class="exp-mode-switch" id="createModeSwitch">
                                        <button type="button" class="exp-mode-btn active" data-type="delivery">
                                            <span class="mode-icon"><i class="fa-solid fa-truck"></i></span>
                                            <span>
                                                <strong class="d-block fs-7">Delivery (SO)</strong>
                                                <small class="text-muted" style="font-size: 0.72rem;">Kirim Pesanan Sales</small>
                                            </span>
                                        </button>
                                        <button type="button" class="exp-mode-btn" data-type="pickup">
                                            <span class="mode-icon"><i class="fa-solid fa-box-open"></i></span>
                                            <span>
                                                <strong class="d-block fs-7">Pickup (PO)</strong>
                                                <small class="text-muted" style="font-size: 0.72rem;">Ambil Pembelian PO</small>
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-lg-7">
                                    <label class="exp-form-label d-flex justify-content-between">
                                        <span id="labelReferenceInput">Cari & Sinkron Nomor SO</span>
                                        <span class="text-muted fw-normal" style="font-size: 0.7rem;">Tekan tombol cari untuk otomatis mengisi shipto & item</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fa-solid fa-barcode text-muted"></i></span>
                                        <input type="text" name="reference_number" id="createReferenceInput" class="form-control exp-form-control font-monospace fw-bold" placeholder="Ketik nomor SO (contoh: SO2026/01/0001)...">
                                        <button type="button" class="btn btn-sync-reference d-flex align-items-center gap-1.5" id="btnSyncReference">
                                            <i class="fa-solid fa-arrows-rotate"></i>
                                            <span>Sinkron PO/SO</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 1: VENDOR & RESI -->
                        <div class="exp-card">
                            <div class="exp-card-title">
                                <i class="fa-solid fa-handshake"></i>
                                <span>Informasi Rekanan Ekspedisi & Resi</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Pilih Master Vendor Ekspedisi</label>
                                    <select name="vendor_id" id="create_vendor_id" class="form-select exp-form-select">
                                        <option value="">-- Ketik Vendor Manual --</option>
                                        @foreach($vendors as $v)
                                            <option value="{{ $v->id }}" data-name="{{ $v->vendor_name }}" data-services='@json($v->supported_services)'>{{ $v->vendor_name }} ({{ $v->vendor_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Nama Vendor Ekspedisi <span class="text-danger">*</span></label>
                                    <input type="text" name="vendor_name" id="create_vendor_name" required class="form-control exp-form-control" placeholder="Contoh: Dakota Cargo">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Layanan Pengiriman</label>
                                    <input type="text" name="service_type" id="create_service_type" class="form-control exp-form-control" placeholder="Cargo Darat / Laut / Charter">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Nomor Resi / AWB / Surat Jalan Vendor</label>
                                    <input type="text" name="tracking_number" class="form-control exp-form-control font-monospace" placeholder="Cth: DKT-8891023">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Nama Pelanggan / Vendor Shipto</label>
                                    <input type="text" name="customer_vendor_name" id="create_customer_vendor_name" class="form-control exp-form-control" placeholder="Nama Customer / Supplier">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Nomor DO Easy (Opsional)</label>
                                    <input type="text" name="do_easy" class="form-control exp-form-control font-monospace" placeholder="DO-EASY-...">
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: RUTE ASAL & TUJUAN -->
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-lg-6">
                                <div class="exp-card h-100 mb-0">
                                    <div class="exp-card-title text-primary">
                                        <i class="fa-solid fa-warehouse"></i>
                                        <span>Lokasi Asal (Pengirim)</span>
                                    </div>
                                    <div class="mb-2.5">
                                        <label class="exp-form-label">Nama Tempat Asal</label>
                                        <input type="text" name="origin_name" class="form-control exp-form-control" value="Gudang AQPA Cikarang">
                                    </div>
                                    <div class="mb-2.5">
                                        <label class="exp-form-label">Alamat Lengkap Asal <span class="text-danger">*</span></label>
                                        <textarea name="origin_address" rows="2" required class="form-control exp-form-control" placeholder="Alamat asal...">Kawasan Industri Jababeka, Cikarang, Bekasi</textarea>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="exp-form-label">PIC Pengirim</label>
                                            <input type="text" name="origin_pic_name" class="form-control exp-form-control" placeholder="Nama PIC">
                                        </div>
                                        <div class="col-6">
                                            <label class="exp-form-label">No. Telepon Asal</label>
                                            <input type="text" name="origin_pic_phone" class="form-control exp-form-control" placeholder="0812...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="exp-card h-100 mb-0">
                                    <div class="exp-card-title text-danger">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span>Tujuan Akhir (Penerima Shipto)</span>
                                    </div>
                                    <div class="mb-2.5">
                                        <label class="exp-form-label">Nama Pelanggan / Tempat Tujuan</label>
                                        <input type="text" name="destination_name" id="create_destination_name" class="form-control exp-form-control" placeholder="Nama PT / Customer Penerima">
                                    </div>
                                    <div class="mb-2.5">
                                        <label class="exp-form-label">Alamat Lengkap Tujuan <span class="text-danger">*</span></label>
                                        <textarea name="destination_address" id="create_destination_address" rows="2" required class="form-control exp-form-control" placeholder="Alamat lengkap penerima..."></textarea>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="exp-form-label">PIC Penerima</label>
                                            <input type="text" name="receiver_pic_name" class="form-control exp-form-control" placeholder="Nama PIC Penerima">
                                        </div>
                                        <div class="col-6">
                                            <label class="exp-form-label">No. HP Penerima</label>
                                            <input type="text" name="receiver_pic_phone" class="form-control exp-form-control" placeholder="0813...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: MULTI-ITEM BARANG MUATAN & SINKRONISASI -->
                        <div class="exp-card">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="exp-card-title mb-0">
                                    <i class="fa-solid fa-boxes-packing"></i>
                                    <span>Rincian Item Barang Muatan (<span id="createItemCountBadge">0 Item</span>)</span>
                                </div>
                                <span class="badge bg-light text-muted border font-monospace">Bisa Lebih Dari 1 Item</span>
                            </div>

                            <!-- Input Bar Tambah Item Manual -->
                            <div class="items-editor-card">
                                <div class="row g-2 align-items-end">
                                    <div class="col-12 col-md-5">
                                        <label class="exp-form-label">Deskripsi / Nama Barang</label>
                                        <input type="text" id="manualItemDesc" class="form-control exp-form-control" placeholder="Ketik nama / spesifikasi barang...">
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <label class="exp-form-label">Qty</label>
                                        <input type="number" min="1" id="manualItemQty" class="form-control exp-form-control text-center" value="1">
                                    </div>
                                    <div class="col-4 col-md-2">
                                        <label class="exp-form-label">Satuan</label>
                                        <input type="text" id="manualItemUnit" class="form-control exp-form-control text-center" placeholder="pcs / koli / box">
                                    </div>
                                    <div class="col-4 col-md-3">
                                        <button type="button" class="btn btn-outline-primary w-100 fw-bold exp-form-control d-flex align-items-center justify-content-center gap-1.5" id="btnAddManualItem">
                                            <i class="fa-solid fa-plus"></i>
                                            <span>Tambah Item</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Table Items Container -->
                            <div class="table-responsive" id="createItemsTableWrapper" style="display: none;">
                                <table class="table table-bordered items-table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ref SO/PO</th>
                                            <th>Kode Barang</th>
                                            <th>Deskripsi Barang</th>
                                            <th class="text-center" style="width: 80px;">Qty</th>
                                            <th class="text-center" style="width: 90px;">Satuan</th>
                                            <th class="text-center" style="width: 60px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="createItemsTableBody"></tbody>
                                </table>
                            </div>

                            <div class="empty-items-box" id="createEmptyItemsBox">
                                <i class="fa-solid fa-box-open fs-2 mb-2 text-muted opacity-50"></i>
                                <div class="fw-semibold text-secondary">Belum ada item barang dalam daftar.</div>
                                <div class="small text-muted">Ketik nomor PO/SO di atas lalu klik "Sinkron PO/SO" atau tambahkan item manual.</div>
                            </div>

                            <div id="createItemsHiddenInputs"></div>

                            <!-- Total Ringkasan Muatan & Ongkir -->
                            <div class="row g-3 mt-2 pt-3 border-top">
                                <div class="col-4 col-md-3">
                                    <label class="exp-form-label">Total Koli / Palet <span class="text-danger">*</span></label>
                                    <input type="number" name="total_colli" id="create_total_colli" min="1" value="1" required class="form-control exp-form-control text-center fw-bold">
                                </div>
                                <div class="col-4 col-md-3">
                                    <label class="exp-form-label">Berat Total (Kg)</label>
                                    <input type="number" step="0.1" name="weight_kg" class="form-control exp-form-control text-center" placeholder="Kg">
                                </div>
                                <div class="col-4 col-md-6">
                                    <label class="exp-form-label">Biaya Ongkos Kirim Vendor (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white fw-bold text-primary">Rp</span>
                                        <input type="number" name="cost_amount" min="0" required class="form-control exp-form-control fw-extrabold text-primary" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 4: TANGGAL & STATUS -->
                        <div class="exp-card mb-0">
                            <div class="exp-card-title">
                                <i class="fa-solid fa-calendar-check"></i>
                                <span>Jadwal Pengiriman & Status</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Tanggal Kirim Ke Vendor <span class="text-danger">*</span></label>
                                    <input type="date" name="shipping_date" required value="{{ date('Y-m-d') }}" class="form-control exp-form-control">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Estimasi Kedatangan (ETA)</label>
                                    <input type="date" name="estimated_arrival_date" class="form-control exp-form-control">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Status Awal</label>
                                    <select name="status" class="form-select exp-form-select fw-semibold">
                                        <option value="on_delivery" selected>Dalam Pengiriman (On Delivery)</option>
                                        <option value="draft">Draft (Persiapan)</option>
                                        <option value="delivered">Terkirim (Sampai di Tujuan)</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="exp-form-label">Catatan Tambahan / Kontak Driver Ekspedisi</label>
                                    <input type="text" name="notes" class="form-control exp-form-control" placeholder="Catatan instruksi khusus pengiriman...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-bold text-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold d-flex align-items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Ekspedisi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT EKSPEDISI -->
    <div class="modal fade" id="editExpeditionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered exp-modal-dialog">
            <div class="modal-content exp-modal-content">
                <form id="editExpeditionForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    
                    <div class="exp-modal-header">
                        <div class="d-flex align-items-center gap-3">
                            <span class="exp-title-icon warning-icon">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">Edit Data Pengiriman Ekspedisi</h5>
                                <p class="text-muted small mb-0">Perbarui informasi resi, rute pengantaran, atau perbarui item barang.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="exp-modal-body">
                        <div class="exp-card">
                            <div class="row g-3">
                                <div class="col-12 col-md-3">
                                    <label class="exp-form-label">Nomor Ekspedisi</label>
                                    <input type="text" id="edit_expedition_number" readonly class="form-control exp-form-control bg-light font-monospace fw-bold text-primary">
                                </div>
                                <div class="col-12 col-md-5">
                                    <label class="exp-form-label">Nama Vendor Ekspedisi <span class="text-danger">*</span></label>
                                    <input type="text" name="vendor_name" id="edit_vendor_name" required class="form-control exp-form-control">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Layanan Kirim</label>
                                    <input type="text" name="service_type" id="edit_service_type" class="form-control exp-form-control">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">No. Resi / AWB</label>
                                    <input type="text" name="tracking_number" id="edit_tracking_number" class="form-control exp-form-control font-monospace">
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="exp-form-label">Tipe Tugas</label>
                                    <select name="task_type" id="edit_task_type" class="form-select exp-form-select">
                                        <option value="delivery">Delivery</option>
                                        <option value="pickup">Pickup</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="exp-form-label">No. SO / PO</label>
                                    <input type="text" name="reference_number" id="edit_reference_number" class="form-control exp-form-control font-monospace">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-lg-6">
                                <div class="exp-card h-100 mb-0">
                                    <div class="exp-card-title text-primary">
                                        <i class="fa-solid fa-warehouse"></i>
                                        <span>Asal (Pengirim)</span>
                                    </div>
                                    <div class="mb-2">
                                        <label class="exp-form-label">Nama Asal</label>
                                        <input type="text" name="origin_name" id="edit_origin_name" class="form-control exp-form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label class="exp-form-label">Alamat Asal <span class="text-danger">*</span></label>
                                        <textarea name="origin_address" id="edit_origin_address" rows="2" required class="form-control exp-form-control"></textarea>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="text" name="origin_pic_name" id="edit_origin_pic_name" class="form-control exp-form-control" placeholder="PIC">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="origin_pic_phone" id="edit_origin_pic_phone" class="form-control exp-form-control" placeholder="Telp">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="exp-card h-100 mb-0">
                                    <div class="exp-card-title text-danger">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span>Tujuan Akhir (Penerima)</span>
                                    </div>
                                    <div class="mb-2">
                                        <label class="exp-form-label">Nama Penerima</label>
                                        <input type="text" name="destination_name" id="edit_destination_name" class="form-control exp-form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label class="exp-form-label">Alamat Tujuan <span class="text-danger">*</span></label>
                                        <textarea name="destination_address" id="edit_destination_address" rows="2" required class="form-control exp-form-control"></textarea>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="text" name="receiver_pic_name" id="edit_receiver_pic_name" class="form-control exp-form-control" placeholder="PIC">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="receiver_pic_phone" id="edit_receiver_pic_phone" class="form-control exp-form-control" placeholder="HP">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="exp-card">
                            <div class="exp-card-title">
                                <i class="fa-solid fa-boxes-packing"></i>
                                <span>Muatan Barang & Biaya</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="exp-form-label">Deskripsi Muatan <span class="text-danger">*</span></label>
                                    <input type="text" name="item_description" id="edit_item_description" required class="form-control exp-form-control">
                                </div>
                                <div class="col-4 col-md-2">
                                    <label class="exp-form-label">Jml Koli</label>
                                    <input type="number" name="total_colli" id="edit_total_colli" min="1" required class="form-control exp-form-control text-center fw-bold">
                                </div>
                                <div class="col-4 col-md-2">
                                    <label class="exp-form-label">Berat (Kg)</label>
                                    <input type="number" step="0.1" name="weight_kg" id="edit_weight_kg" class="form-control exp-form-control text-center">
                                </div>
                                <div class="col-4 col-md-2">
                                    <label class="exp-form-label">Ongkir (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" name="cost_amount" id="edit_cost_amount" min="0" required class="form-control exp-form-control text-primary fw-bold text-end">
                                </div>
                            </div>
                        </div>

                        <div class="exp-card mb-0">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Tanggal Kirim <span class="text-danger">*</span></label>
                                    <input type="date" name="shipping_date" id="edit_shipping_date" required class="form-control exp-form-control">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Estimasi Tiba (ETA)</label>
                                    <input type="date" name="estimated_arrival_date" id="edit_estimated_arrival_date" class="form-control exp-form-control">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="exp-form-label">Status</label>
                                    <select name="status" id="edit_status" class="form-select exp-form-select">
                                        <option value="draft">Draft</option>
                                        <option value="on_delivery">Dalam Pengiriman</option>
                                        <option value="delivered">Terkirim (Sampai)</option>
                                        <option value="returned">Retur</option>
                                        <option value="cancelled">Dibatalkan</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4" id="container_edit_actual_arrival" style="display: none;">
                                    <label class="exp-form-label">Tgl & Jam Sampai (Aktual)</label>
                                    <input type="datetime-local" name="actual_arrival_date" id="edit_actual_arrival_date" class="form-control exp-form-control">
                                </div>
                                <div class="col-12">
                                    <label class="exp-form-label">Catatan</label>
                                    <input type="text" name="notes" id="edit_notes" class="form-control exp-form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-bold text-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold">Perbarui Ekspedisi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL TANDAI SAMPAI (HARI INI / BACKDATE) -->
    <div class="modal fade" id="markDeliveredModal" tabindex="-1" aria-labelledby="markDeliveredModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="formMarkDelivered" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                @csrf
                @method('PATCH')
                <div class="modal-header text-white border-bottom-0 py-3 px-4" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="fa-solid fa-check-double fs-6 text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="markDeliveredModalLabel">Konfirmasi Pengiriman Sampai</h5>
                            <small class="text-white text-opacity-75" style="font-size: 0.75rem;">Atur tanggal dan konfirmasi barang telah diterima</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-light">
                    <!-- Info Pengiriman Card -->
                    <div class="card border-0 shadow-sm rounded-3 mb-3 bg-white">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted small">No. Pengiriman</span>
                                <span class="fw-bold font-monospace text-primary" id="deliv_modal_number">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Vendor</span>
                                <span class="fw-semibold text-dark small" id="deliv_modal_vendor">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="text-muted small me-2">Tujuan</span>
                                <span class="text-dark small text-end fw-medium text-truncate" style="max-width: 240px;" id="deliv_modal_destination">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Tanggal Sampai: Hari Ini vs Backdate -->
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3 mb-3">
                        <label class="form-label fw-bold text-secondary small mb-2">
                            <i class="fa-solid fa-clock me-1 text-success"></i>Waktu Sampai Tujuan
                        </label>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="arrival_type" id="arrival_type_today" value="today" checked onchange="toggleArrivalDateType()">
                                <label class="btn btn-outline-success w-100 py-2.5 rounded-3 d-flex flex-column align-items-center justify-content-center h-100" for="arrival_type_today" style="border-width: 2px;">
                                    <i class="fa-solid fa-calendar-day fs-5 mb-1"></i>
                                    <span class="fw-bold" style="font-size: 0.85rem;">Hari Ini (Sekarang)</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ now()->translatedFormat('d M Y H:i') }}</small>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="arrival_type" id="arrival_type_backdate" value="backdate" onchange="toggleArrivalDateType()">
                                <label class="btn btn-outline-warning w-100 py-2.5 rounded-3 d-flex flex-column align-items-center justify-content-center h-100" for="arrival_type_backdate" style="border-width: 2px;">
                                    <i class="fa-solid fa-calendar-alt fs-5 mb-1"></i>
                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">Backdate</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Tanggal/Waktu Lampau</small>
                                </label>
                            </div>
                        </div>

                        <!-- Input Tanggal & Waktu (Muncul saat Backdate dipilih) -->
                        <div id="container_custom_arrival_date" style="display: none;">
                            <label class="form-label fw-semibold text-dark small mb-1">
                                Masukkan Tanggal & Waktu Sampai: <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border"><i class="fa-solid fa-calendar-check"></i></span>
                                <input type="datetime-local" 
                                       name="actual_arrival_date" 
                                       id="deliv_custom_date" 
                                       class="form-control" 
                                       value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">Pilih tanggal dan jam saat paket benar-benar telah sampai di tujuan.</small>
                        </div>
                    </div>

                    <!-- Penerima / Catatan Opsional -->
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-3">
                        <div class="mb-2">
                            <label class="form-label fw-semibold text-secondary small mb-1">Nama Penerima (Opsional)</label>
                            <input type="text" name="receiver_pic_name" id="deliv_modal_receiver" class="form-control form-control-sm" placeholder="Contoh: Bpk. Bambang (Security)">
                        </div>
                        <div>
                            <label class="form-label fw-semibold text-secondary small mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="delivery_notes" class="form-control form-control-sm" placeholder="Contoh: Paket diterima kondisi baik, ditaruh pos">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top-0 py-3 px-4 d-flex justify-content-between align-items-center rounded-bottom-4">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-3 fw-semibold text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3 fw-bold text-white shadow-sm">
                        <i class="fa-solid fa-check me-1.5"></i>Tandai Sampai
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Vendor select auto-populate
            const vendorSelect = document.getElementById('create_vendor_id');
            const vendorNameInput = document.getElementById('create_vendor_name');
            const serviceTypeInput = document.getElementById('create_service_type');

            if (vendorSelect) {
                vendorSelect.addEventListener('change', function () {
                    const opt = this.options[this.selectedIndex];
                    if (opt && opt.value) {
                        vendorNameInput.value = opt.getAttribute('data-name') || '';
                        try {
                            const services = JSON.parse(opt.getAttribute('data-services') || '[]');
                            if (services.length > 0) {
                                serviceTypeInput.value = services[0];
                            }
                        } catch (e) {}
                    }
                });
            }

            // Task Mode Switch (Delivery SO vs Pickup PO)
            const modeButtons = document.querySelectorAll('#createModeSwitch .exp-mode-btn');
            const taskTypeInput = document.getElementById('createTaskType');
            const refInput = document.getElementById('createReferenceInput');
            const labelRef = document.getElementById('labelReferenceInput');

            modeButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    modeButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const type = this.getAttribute('data-type');
                    taskTypeInput.value = type;

                    if (type === 'delivery') {
                        labelRef.textContent = 'Cari & Sinkron Nomor SO';
                        refInput.placeholder = 'Ketik nomor SO (contoh: SO2026/01/0001)...';
                    } else {
                        labelRef.textContent = 'Cari & Sinkron Nomor PO';
                        refInput.placeholder = 'Ketik nomor PO (contoh: PO2026/01/0001)...';
                    }
                });
            });

            // MULTI ITEMS LOGIC FOR CREATE MODAL
            let expeditionItems = [];

            function renderItems() {
                const tbody = document.getElementById('createItemsTableBody');
                const tableWrapper = document.getElementById('createItemsTableWrapper');
                const emptyBox = document.getElementById('createEmptyItemsBox');
                const hiddenInputs = document.getElementById('createItemsHiddenInputs');
                const badge = document.getElementById('createItemCountBadge');

                badge.textContent = `${expeditionItems.length} Item`;

                if (expeditionItems.length === 0) {
                    tableWrapper.style.display = 'none';
                    emptyBox.style.display = 'block';
                    hiddenInputs.innerHTML = '';
                    tbody.innerHTML = '';
                    return;
                }

                tableWrapper.style.display = 'block';
                emptyBox.style.display = 'none';

                let tbodyHtml = '';
                let inputsHtml = '';

                let totalQty = 0;

                expeditionItems.forEach((item, index) => {
                    totalQty += Number(item.quantity || 1);
                    tbodyHtml += `
                        <tr>
                            <td class="font-monospace small">${item.ref_no || '-'}</td>
                            <td class="font-monospace small text-muted">${item.item_number || '-'}</td>
                            <td class="fw-semibold text-dark">${item.item_description}</td>
                            <td class="text-center font-monospace fw-bold text-primary">${item.quantity}</td>
                            <td class="text-center small text-muted">${item.unit || 'pcs'}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1.5 remove-item-btn" data-index="${index}" title="Hapus Item">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    inputsHtml += `
                        <input type="hidden" name="items[${index}][ref_no]" value="${item.ref_no || ''}">
                        <input type="hidden" name="items[${index}][item_number]" value="${item.item_number || ''}">
                        <input type="hidden" name="items[${index}][item_description]" value="${item.item_description || ''}">
                        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity || 1}">
                        <input type="hidden" name="items[${index}][unit]" value="${item.unit || ''}">
                        <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price || 0}">
                    `;
                });

                tbody.innerHTML = tbodyHtml;
                hiddenInputs.innerHTML = inputsHtml;

                // Bind remove buttons
                document.querySelectorAll('.remove-item-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const idx = parseInt(this.getAttribute('data-index'));
                        expeditionItems.splice(idx, 1);
                        renderItems();
                    });
                });
            }

            // Tambah Item Manual
            document.getElementById('btnAddManualItem').addEventListener('click', function () {
                const desc = document.getElementById('manualItemDesc').value.trim();
                const qty = parseInt(document.getElementById('manualItemQty').value) || 1;
                const unit = document.getElementById('manualItemUnit').value.trim() || 'pcs';
                const ref = refInput.value.trim();

                if (!desc) {
                    alert('Silakan isi nama / deskripsi barang terlebih dahulu.');
                    return;
                }

                expeditionItems.push({
                    ref_no: ref,
                    item_number: '',
                    item_description: desc,
                    quantity: qty,
                    unit: unit,
                    unit_price: 0
                });

                document.getElementById('manualItemDesc').value = '';
                document.getElementById('manualItemQty').value = 1;
                document.getElementById('manualItemUnit').value = '';

                renderItems();
            });

            // SINKRONISASI PO / SO DENGAN API INTEGRASI AKURASI
            const btnSync = document.getElementById('btnSyncReference');
            btnSync.addEventListener('click', async function () {
                const ref = refInput.value.trim();
                if (!ref) {
                    alert('Silakan masukkan nomor PO atau SO terlebih dahulu!');
                    return;
                }

                const type = taskTypeInput.value;
                const isDelivery = type === 'delivery';
                const upperRef = ref.toUpperCase();
                const apiUrl = isDelivery 
                    ? `/api/integration/detail-so/${encodeURIComponent(upperRef)}`
                    : `/api/integration/detail-po/${encodeURIComponent(upperRef)}`;

                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sinkron...';
                this.disabled = true;

                try {
                    const response = await fetch(apiUrl, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        const err = await response.json().catch(() => ({}));
                        throw new Error(err.error || `Data ${type.toUpperCase()} tidak ditemukan`);
                    }

                    const resData = await response.json();
                    let apiItems = [];
                    let mainInfo = {};

                    if (resData.data && Array.isArray(resData.data)) {
                        apiItems = resData.data;
                        mainInfo = apiItems[0] || {};
                    } else if (resData.items && Array.isArray(resData.items)) {
                        apiItems = resData.items;
                        mainInfo = resData;
                    } else {
                        apiItems = Array.isArray(resData) ? resData : [resData];
                        mainInfo = apiItems[0] || {};
                    }

                    // Auto-fill Nama Pelanggan / Vendor Shipto & Alamat
                    const custVendorName = mainInfo.pelanggan || mainInfo.nama_pelanggan || mainInfo.pemasok || mainInfo.nama_pemasok || '';
                    const shiptoAddress = mainInfo.shipto || mainInfo.alamat || '';

                    if (custVendorName) {
                        document.getElementById('create_customer_vendor_name').value = custVendorName;
                        if (isDelivery) {
                            document.getElementById('create_destination_name').value = custVendorName;
                        }
                    }

                    if (shiptoAddress && isDelivery) {
                        document.getElementById('create_destination_address').value = shiptoAddress;
                    }

                    // Tambahkan Item dari API ke daftar
                    apiItems.forEach(it => {
                        const desc = it.deskripsi_barang || it.nama_barang || it.item_description || '';
                        if (desc) {
                            expeditionItems.push({
                                ref_no: mainInfo.no_so || mainInfo.no_po || ref,
                                item_number: it.no_barang || it.item_no || '',
                                item_description: desc,
                                quantity: Number(it.qty || it.quantity || 1),
                                unit: it.uom || it.satuan || it.unit || 'pcs',
                                unit_price: Number(it.price || it.harga || 0)
                            });
                        }
                    });

                    renderItems();

                } catch (e) {
                    alert(e.message || 'Gagal menyinkronkan data.');
                } finally {
                    this.innerHTML = originalHtml;
                    this.disabled = false;
                }
            });

            function parseDateForInput(val) {
                if (!val) return '';
                if (typeof val === 'string') {
                    if (val.includes('T') && val.endsWith('Z')) {
                        const d = new Date(val);
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    }
                    return val.split('T')[0].split(' ')[0];
                }
                return '';
            }

            // Edit button handler
            const editButtons = document.querySelectorAll('.btn-edit-expedition');
            const editModalEl = document.getElementById('editExpeditionModal');
            const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
            const editForm = document.getElementById('editExpeditionForm');
            const editStatusSelect = document.getElementById('edit_status');

            if (editStatusSelect) {
                editStatusSelect.addEventListener('change', function () {
                    const actualArrivalContainer = document.getElementById('container_edit_actual_arrival');
                    if (actualArrivalContainer) {
                        actualArrivalContainer.style.display = (this.value === 'delivered') ? 'block' : 'none';
                        if (this.value === 'delivered' && !document.getElementById('edit_actual_arrival_date').value) {
                            const now = new Date();
                            const offset = now.getTimezoneOffset() * 60000;
                            const localISOTime = (new Date(now.getTime() - offset)).toISOString().slice(0, 16);
                            document.getElementById('edit_actual_arrival_date').value = localISOTime;
                        }
                    }
                });
            }

            editButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const data = JSON.parse(this.getAttribute('data-expedition'));
                    if (!data) return;

                    editForm.action = `/ekspedisi/${data.id}`;
                    document.getElementById('edit_expedition_number').value = data.expedition_number || '';
                    document.getElementById('edit_vendor_name').value = data.vendor_name || '';
                    document.getElementById('edit_service_type').value = data.service_type || '';
                    document.getElementById('edit_tracking_number').value = data.tracking_number || '';
                    document.getElementById('edit_task_type').value = data.task_type || 'delivery';
                    document.getElementById('edit_reference_number').value = data.reference_number || '';
                    document.getElementById('edit_origin_name').value = data.origin_name || '';
                    document.getElementById('edit_origin_address').value = data.origin_address || '';
                    document.getElementById('edit_origin_pic_name').value = data.origin_pic_name || '';
                    document.getElementById('edit_origin_pic_phone').value = data.origin_pic_phone || '';
                    document.getElementById('edit_destination_name').value = data.destination_name || '';
                    document.getElementById('edit_destination_address').value = data.destination_address || '';
                    document.getElementById('edit_receiver_pic_name').value = data.receiver_pic_name || '';
                    document.getElementById('edit_receiver_pic_phone').value = data.receiver_pic_phone || '';
                    document.getElementById('edit_item_description').value = data.item_description || '';
                    document.getElementById('edit_total_colli').value = data.total_colli || 1;
                    document.getElementById('edit_weight_kg').value = data.weight_kg || '';
                    document.getElementById('edit_cost_amount').value = data.cost_amount || 0;
                    document.getElementById('edit_shipping_date').value = parseDateForInput(data.shipping_date);
                    document.getElementById('edit_estimated_arrival_date').value = parseDateForInput(data.estimated_arrival_date);
                    document.getElementById('edit_status').value = data.status || 'on_delivery';
                    document.getElementById('edit_notes').value = data.notes || '';

                    const actualArrivalContainer = document.getElementById('container_edit_actual_arrival');
                    if (actualArrivalContainer) {
                        actualArrivalContainer.style.display = (data.status === 'delivered') ? 'block' : 'none';
                        if (data.actual_arrival_date) {
                            document.getElementById('edit_actual_arrival_date').value = data.actual_arrival_date.substring(0, 16);
                        } else {
                            document.getElementById('edit_actual_arrival_date').value = '';
                        }
                    }

                    editModal.show();
                });
            });

            // Mark Delivered Modal Handler
            window.toggleArrivalDateType = function() {
                const isBackdate = document.getElementById('arrival_type_backdate').checked;
                const container = document.getElementById('container_custom_arrival_date');
                const input = document.getElementById('deliv_custom_date');
                if (container) {
                    container.style.display = isBackdate ? 'block' : 'none';
                    if (isBackdate && input) {
                        input.focus();
                    }
                }
            };

            const markDeliveredButtons = document.querySelectorAll('.btn-mark-delivered');
            const markDeliveredModalEl = document.getElementById('markDeliveredModal');
            const markDeliveredModal = markDeliveredModalEl ? new bootstrap.Modal(markDeliveredModalEl) : null;
            const formMarkDelivered = document.getElementById('formMarkDelivered');

            if (markDeliveredButtons.length > 0 && markDeliveredModal && formMarkDelivered) {
                markDeliveredButtons.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const actionUrl = this.getAttribute('data-action');
                        const number = this.getAttribute('data-number');
                        const vendor = this.getAttribute('data-vendor');
                        const destination = this.getAttribute('data-destination');
                        const receiver = this.getAttribute('data-receiver');

                        formMarkDelivered.action = actionUrl;
                        document.getElementById('deliv_modal_number').textContent = number || '-';
                        document.getElementById('deliv_modal_vendor').textContent = vendor || '-';
                        document.getElementById('deliv_modal_destination').textContent = destination || '-';
                        document.getElementById('deliv_modal_receiver').value = receiver || '';

                        // Reset radio to today
                        document.getElementById('arrival_type_today').checked = true;
                        toggleArrivalDateType();

                        // Set default datetime-local to current local time
                        const now = new Date();
                        const offset = now.getTimezoneOffset() * 60000;
                        const localISOTime = (new Date(now.getTime() - offset)).toISOString().slice(0, 16);
                        document.getElementById('deliv_custom_date').value = localISOTime;

                        markDeliveredModal.show();
                    });
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
