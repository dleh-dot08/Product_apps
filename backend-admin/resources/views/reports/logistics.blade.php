<x-app-layout>
    <style>
        :root {
            --logistics-primary: #2563eb;
            --logistics-primary-hover: #1d4ed8;
            --logistics-orange: #f59e0b;
            --logistics-green: #10b981;
            --logistics-bg-light: #f8fafc;
            --logistics-border: #e2e8f0;
            --logistics-text-main: #0f172a;
            --logistics-text-muted: #64748b;
        }

        body {
            background-color: #f1f5f9;
        }

        .logistics-header-card {
            background: #ffffff;
            border-radius: 1rem;
            padding: 1.5rem 1.75rem;
            border: 1px solid var(--logistics-border);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        html[data-bs-theme="dark"] .logistics-header-card {
            background: #1e293b;
            border-color: #334155;
        }

        .header-title-box h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--logistics-text-main);
            letter-spacing: -0.02em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        html[data-bs-theme="dark"] .header-title-box h1 {
            color: #f8fafc;
        }

        .header-title-box p {
            color: var(--logistics-text-muted);
            margin-top: 0.35rem;
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        /* Nav Tab Pills */
        .logistics-nav-tabs {
            background: #ffffff;
            padding: 0.5rem;
            border-radius: 0.85rem;
            display: inline-flex;
            gap: 0.5rem;
            border: 1px solid var(--logistics-border);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
            width: 100%;
            overflow-x: auto;
        }

        html[data-bs-theme="dark"] .logistics-nav-tabs {
            background: #1e293b;
            border-color: #334155;
        }

        .logistics-nav-link {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.65rem 1.25rem;
            border-radius: 0.6rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
            border: none;
            background: transparent;
            cursor: pointer;
        }

        html[data-bs-theme="dark"] .logistics-nav-link {
            color: #94a3b8;
        }

        .logistics-nav-link:hover {
            color: var(--logistics-primary);
            background: rgba(37, 99, 235, 0.05);
        }

        .logistics-nav-link.active {
            background: #2563eb !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
        }

        .logistics-nav-link .badge-count {
            padding: 0.2rem 0.55rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(0, 0, 0, 0.07);
            color: inherit;
        }

        .logistics-nav-link.active .badge-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Summary Cards */
        .stat-card {
            background: #ffffff;
            border-radius: 0.85rem;
            padding: 1.25rem;
            border: 1px solid var(--logistics-border);
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            gap: 1rem;
            height: 100%;
        }

        html[data-bs-theme="dark"] .stat-card {
            background: #1e293b;
            border-color: #334155;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .stat-content .stat-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--logistics-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.25rem;
        }

        .stat-content .stat-value {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--logistics-text-main);
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        html[data-bs-theme="dark"] .stat-content .stat-value {
            color: #f8fafc;
        }

        /* Filter Box */
        .filter-card {
            background: #ffffff;
            border-radius: 0.85rem;
            border: 1px solid var(--logistics-border);
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        html[data-bs-theme="dark"] .filter-card {
            background: #1e293b;
            border-color: #334155;
        }

        .filter-input {
            border-radius: 0.5rem;
            border: 1px solid var(--logistics-border);
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }

        html[data-bs-theme="dark"] .filter-input {
            background: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        /* Visualizations */
        .viz-card {
            background: #ffffff;
            border-radius: 0.85rem;
            border: 1px solid var(--logistics-border);
            padding: 1.25rem;
            height: 100%;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        html[data-bs-theme="dark"] .viz-card {
            background: #1e293b;
            border-color: #334155;
        }

        .viz-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--logistics-text-main);
            margin-bottom: 1rem;
        }

        html[data-bs-theme="dark"] .viz-title {
            color: #f8fafc;
        }

        /* Table Design */
        .table-card {
            background: #ffffff;
            border-radius: 0.85rem;
            border: 1px solid var(--logistics-border);
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
        }

        html[data-bs-theme="dark"] .table-card {
            background: #1e293b;
            border-color: #334155;
        }

        .table-custom {
            margin-bottom: 0;
            font-size: 0.86rem;
        }

        .table-custom thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--logistics-border);
            white-space: nowrap;
        }

        html[data-bs-theme="dark"] .table-custom thead th {
            background: #0f172a;
            color: #94a3b8;
            border-bottom-color: #334155;
        }

        .table-custom tbody td {
            padding: 0.85rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--logistics-border);
            color: #334155;
        }

        html[data-bs-theme="dark"] .table-custom tbody td {
            border-bottom-color: #334155;
            color: #cbd5e1;
        }

        .table-custom tbody tr:hover {
            background-color: rgba(241, 245, 249, 0.6);
        }

        html[data-bs-theme="dark"] .table-custom tbody tr:hover {
            background-color: rgba(51, 65, 85, 0.4);
        }

        /* Badge Status */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
        }
        .status-pill.terkirim {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
        }
        .status-pill.dalam-pengiriman {
            background: rgba(37, 99, 235, 0.12);
            color: #2563eb;
        }
        .status-pill.pending {
            background: rgba(245, 158, 11, 0.15);
            color: #d97706;
        }
        .status-pill.draft {
            background: rgba(100, 116, 139, 0.12);
            color: #475569;
        }

        /* Print styles */
        @media print {
            .app-sidebar, .logistics-nav-tabs, .filter-card, .btn-no-print {
                display: none !important;
            }
            .logistics-header-card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
            body {
                background: white !important;
            }
        }
    </style>

    <div class="container-fluid px-3 px-md-4 py-3">

        <!-- HEADER -->
        <div class="logistics-header-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="header-title-box">
                    <h1>
                        <span class="text-warning"><i class="fa-solid fa-coins"></i></span>
                        Laporan Logistik & Distribusi
                    </h1>
                    <p>Rekap biaya logistik, biaya ekspedisi, dan biaya pengiriman berdasarkan SO/PO.</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <!-- Date Picker Filter -->
                    <form method="GET" action="{{ route('reports.logistics') }}" id="dateFilterForm" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="tab" value="{{ $activeTab }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="customer" value="{{ request('customer') }}">
                        <input type="hidden" name="task_type" value="{{ request('task_type') }}">
                        <input type="hidden" name="status" value="{{ request('status') }}">

                        <div class="input-group input-group-sm" style="width: auto;">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-regular fa-calendar"></i></span>
                            <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" class="form-control border-start-0 ps-0" onchange="document.getElementById('dateFilterForm').submit()" title="Mulai Tanggal">
                            <span class="input-group-text bg-light border-start-0 border-end-0 text-muted">-</span>
                            <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" class="form-control border-start-0 ps-0" onchange="document.getElementById('dateFilterForm').submit()" title="Sampai Tanggal">
                        </div>
                    </form>

                    <!-- Export Excel -->
                    @if(auth()->user()->hasPermission('Export Laporan Logistik'))
                    <a href="{{ route('reports.logistics.export', request()->all()) }}" class="btn btn-outline-success btn-sm fw-bold px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-2 shadow-sm btn-no-print">
                        <i class="fa-solid fa-file-excel fs-6"></i>
                        <span>Export Excel</span>
                    </a>
                    @endif

                    <!-- Cetak PDF -->
                    <button type="button" onclick="window.print()" class="btn btn-outline-danger btn-sm fw-bold px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-2 shadow-sm btn-no-print">
                        <i class="fa-solid fa-file-pdf fs-6"></i>
                        <span>Cetak PDF</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB NAVIGATION -->
        <div class="logistics-nav-tabs">
            <button type="button" class="logistics-nav-link {{ $activeTab === 'logistics' ? 'active' : '' }}" onclick="switchTab('logistics')">
                <i class="fa-solid fa-layer-group"></i>
                <span>Report Biaya Logistik</span>
                <span class="badge-count">{{ $logistics['summary']['total_sopo'] ?? 0 }}</span>
            </button>
            <button type="button" class="logistics-nav-link {{ $activeTab === 'expedition' ? 'active' : '' }}" onclick="switchTab('expedition')">
                <i class="fa-solid fa-truck-ramp-box"></i>
                <span>Report Biaya Ekspedisi</span>
                <span class="badge-count">{{ $expedition['summary']['total_deliveries'] ?? 0 }}</span>
            </button>
            <button type="button" class="logistics-nav-link {{ $activeTab === 'internal' ? 'active' : '' }}" onclick="switchTab('internal')">
                <i class="fa-solid fa-truck-moving"></i>
                <span>Report Biaya Pengiriman</span>
                <span class="badge-count">{{ $internal['summary']['total_trips'] ?? 0 }}</span>
            </button>
        </div>

        <!-- GLOBAL FILTER BAR -->
        <div class="filter-card btn-no-print">
            <form method="GET" action="{{ route('reports.logistics') }}" id="globalFilterForm" class="row g-2 align-items-center">
                <input type="hidden" name="tab" id="formActiveTab" value="{{ $activeTab }}">
                <input type="hidden" name="start_date" value="{{ $startDate->toDateString() }}">
                <input type="hidden" name="end_date" value="{{ $endDate->toDateString() }}">

                <div class="col-12 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Cari No. SO/PO, Customer, Vendor...">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <select name="customer" class="form-select form-select-sm filter-input">
                        <option value="ALL">Semua Customer</option>
                        @foreach($allCustomers as $cust)
                            <option value="{{ $cust }}" {{ request('customer') === $cust ? 'selected' : '' }}>{{ Str::limit($cust, 25) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="task_type" class="form-select form-select-sm filter-input">
                        <option value="ALL">Semua Jenis Tugas</option>
                        @foreach($allTaskTypes as $tType)
                            <option value="{{ $tType }}" {{ request('task_type') === $tType ? 'selected' : '' }}>{{ $tType }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="status" class="form-select form-select-sm filter-input">
                        <option value="ALL">Semua Status</option>
                        <option value="terkirim" {{ strtolower(request('status')) === 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                        <option value="dalam pengiriman" {{ strtolower(request('status')) === 'dalam pengiriman' ? 'selected' : '' }}>Dalam Pengiriman</option>
                        <option value="pending" {{ strtolower(request('status')) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="draft" {{ strtolower(request('status')) === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                <div class="col-12 col-md-3 d-flex gap-2 justify-content-end">
                    <a href="{{ route('reports.logistics', ['tab' => $activeTab, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" class="btn btn-outline-secondary btn-sm px-3 rounded-2 fw-semibold">
                        <i class="fa-solid fa-rotate-right me-1"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-3 rounded-2 fw-semibold">
                        <i class="fa-solid fa-filter me-1"></i> Terapkan
                    </button>
                </div>
            </form>
        </div>

        <!-- ==================================================== -->
        <!-- TAB 1: REPORT BIAYA LOGISTIK (DEFAULT / MAIN REPORT) -->
        <!-- ==================================================== -->
        <div id="tab-logistics" class="tab-pane-content" style="{{ $activeTab === 'logistics' ? 'display: block;' : 'display: none;' }}">
            <!-- SUMMARY CARDS TAB 1 -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="fa-regular fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total SO/PO</div>
                            <div class="stat-value">{{ number_format($logistics['summary']['total_sopo']) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Nilai Barang</div>
                            <div class="stat-value">Rp {{ number_format($logistics['summary']['total_item_value'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-danger-subtle text-danger">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Biaya Logistik</div>
                            <div class="stat-value text-primary">Rp {{ number_format($logistics['summary']['total_logistics_cost'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-purple-subtle text-purple" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                            <i class="fa-solid fa-percent"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">% Biaya thd Nilai Barang</div>
                            <div class="stat-value">{{ $logistics['summary']['overall_percentage'] }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VISUALISASI: DONUT & LINE CHART -->
            <div class="row g-3 mb-4">
                <!-- Donut Chart Komposisi Biaya -->
                <div class="col-12 col-lg-6">
                    <div class="viz-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="viz-title mb-0">Komposisi Biaya Logistik</h3>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-12 col-sm-6 text-center position-relative py-2">
                                <div style="height: 200px; max-width: 200px; margin: 0 auto; position: relative;">
                                    <canvas id="compositionDonutChart"></canvas>
                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                                        <div style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Biaya</div>
                                        <div style="font-size: 1rem; font-weight: 800; color: #0f172a;">Rp {{ number_format($logistics['summary']['total_logistics_cost'] / 1000000, 1) }} M</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <ul class="list-unstyled mb-0 d-flex flex-column gap-2.5">
                                    <li class="d-flex justify-content-between align-items-center small">
                                        <span class="d-flex align-items-center gap-2">
                                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #3b82f6;"></span>
                                            <span class="fw-medium">Pengiriman Internal (HPP)</span>
                                        </span>
                                        <span class="fw-bold">Rp {{ number_format($logistics['composition']['internal']['amount'], 0, ',', '.') }} ({{ $logistics['composition']['internal']['pct'] }}%)</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center small">
                                        <span class="d-flex align-items-center gap-2">
                                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;"></span>
                                            <span class="fw-medium">Ekspedisi (Vendor 3PL)</span>
                                        </span>
                                        <span class="fw-bold">Rp {{ number_format($logistics['composition']['expedition']['amount'], 0, ',', '.') }} ({{ $logistics['composition']['expedition']['pct'] }}%)</span>
                                    </li>
                                    <li class="d-flex justify-content-between align-items-center small">
                                        <span class="d-flex align-items-center gap-2">
                                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span>
                                            <span class="fw-medium">Packaging</span>
                                        </span>
                                        <span class="fw-bold">Rp {{ number_format($logistics['composition']['packaging']['amount'], 0, ',', '.') }} ({{ $logistics['composition']['packaging']['pct'] }}%)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Line Chart Tren Biaya Logistik -->
                <div class="col-12 col-lg-6">
                    <div class="viz-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="viz-title mb-0">Tren Biaya Logistik</h3>
                            <span class="badge bg-light text-muted border">Per Bulan</span>
                        </div>
                        <div style="height: 200px;">
                            <canvas id="trendLineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL DETAIL TAB 1 -->
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No.</th>
                                <th>No. SO/PO</th>
                                <th>Customer / Vendor</th>
                                <th class="text-end">Nilai Barang</th>
                                <th class="text-end">Biaya Internal</th>
                                <th class="text-end">Biaya Ekspedisi (3PL)</th>
                                <th class="text-end">Biaya Packaging</th>
                                <th class="text-end">Total Biaya Logistik</th>
                                <th class="text-center">% Nilai</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 60px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logistics['rows'] as $index => $row)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <a href="javascript:void(0)" class="fw-bold text-primary font-monospace text-decoration-none" onclick="showSoDetailModal('{{ $row['ref_no'] }}', {{ json_encode($row) }})">
                                            {{ $row['ref_no'] }}
                                        </a>
                                    </td>
                                    <td class="fw-semibold">{{ $row['customer'] }}</td>
                                    <td class="text-end font-monospace">Rp {{ number_format($row['item_value'], 0, ',', '.') }}</td>
                                    <td class="text-end font-monospace">Rp {{ number_format($row['internal_cost'], 0, ',', '.') }}</td>
                                    <td class="text-end font-monospace">Rp {{ number_format($row['expedition_cost'], 0, ',', '.') }}</td>
                                    <td class="text-end font-monospace">Rp {{ number_format($row['packaging_cost'], 0, ',', '.') }}</td>
                                    <td class="text-end font-monospace fw-bold text-dark">Rp {{ number_format($row['total_logistics_cost'], 0, ',', '.') }}</td>
                                    <td class="text-center fw-bold {{ $row['cost_percentage'] > 15 ? 'text-danger' : 'text-success' }}">
                                        {{ $row['cost_percentage'] }}%
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $stClass = 'draft';
                                            $stLower = strtolower($row['status']);
                                            if (str_contains($stLower, 'terkirim') || str_contains($stLower, 'selesai')) $stClass = 'terkirim';
                                            elseif (str_contains($stLower, 'kirim') || str_contains($stLower, 'jalan')) $stClass = 'dalam-pengiriman';
                                            elseif (str_contains($stLower, 'pending') || str_contains($stLower, 'tunggu')) $stClass = 'pending';
                                        @endphp
                                        <span class="status-pill {{ $stClass }}">
                                            <i class="fa-solid fa-circle" style="font-size: 6px;"></i>
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-light border p-1 rounded-2" title="Lihat Rincian" onclick="showSoDetailModal('{{ $row['ref_no'] }}', {{ json_encode($row) }})">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <i class="fa-regular fa-folder-open fs-2 mb-2 d-block"></i>
                                        Tidak ada data transaksi logistik dalam periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-3 py-2 border-top bg-light d-flex justify-content-between align-items-center small text-muted">
                    <span>Menampilkan {{ count($logistics['rows']) }} transaksi SO/PO</span>
                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- TAB 2: REPORT BIAYA EKSPEDISI (VENDOR 3PL)           -->
        <!-- ==================================================== -->
        <div id="tab-expedition" class="tab-pane-content" style="{{ $activeTab === 'expedition' ? 'display: block;' : 'display: none;' }}">
            <!-- SUMMARY CARDS TAB 2 -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="fa-solid fa-plane-departure"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Pengiriman</div>
                            <div class="stat-value">{{ number_format($expedition['summary']['total_deliveries']) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="fa-solid fa-handshake"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Vendor Ekspedisi</div>
                            <div class="stat-value">{{ number_format($expedition['summary']['total_vendors']) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Nilai Barang</div>
                            <div class="stat-value">Rp {{ number_format($expedition['summary']['total_item_value'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-danger-subtle text-danger">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Biaya Ekspedisi</div>
                            <div class="stat-value text-danger">Rp {{ number_format($expedition['summary']['total_expedition_cost'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL DETAIL TAB 2 -->
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No.</th>
                                <th>Nomor SO/PO</th>
                                <th>Customer / Vendor</th>
                                <th>Vendor Ekspedisi</th>
                                <th>Nomor Resi</th>
                                <th class="text-center">Tanggal Kirim</th>
                                <th class="text-center">ETA</th>
                                <th class="text-center">Aktual Sampai</th>
                                <th class="text-end">Nilai Barang</th>
                                <th class="text-end">Biaya Ekspedisi</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expedition['rows'] as $index => $row)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-bold font-monospace text-primary">{{ $row['ref_no'] }}</td>
                                    <td>{{ $row['customer'] }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $row['vendor_name'] }}</span></td>
                                    <td class="font-monospace small text-muted">{{ $row['tracking_number'] }}</td>
                                    <td class="text-center">{{ $row['shipping_date'] }}</td>
                                    <td class="text-center">{{ $row['estimated_arrival_date'] }}</td>
                                    <td class="text-center">{{ $row['actual_arrival_date'] }}</td>
                                    <td class="text-end font-monospace">Rp {{ number_format($row['item_value'], 0, ',', '.') }}</td>
                                    <td class="text-end font-monospace fw-bold text-dark">Rp {{ number_format($row['cost_amount'], 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @php
                                            $stClass = 'draft';
                                            $stLower = strtolower($row['status']);
                                            if (str_contains($stLower, 'terkirim') || str_contains($stLower, 'delivered')) $stClass = 'terkirim';
                                            elseif (str_contains($stLower, 'kirim') || str_contains($stLower, 'delivery')) $stClass = 'dalam-pengiriman';
                                            elseif (str_contains($stLower, 'pending')) $stClass = 'pending';
                                        @endphp
                                        <span class="status-pill {{ $stClass }}">
                                            <i class="fa-solid fa-circle" style="font-size: 6px;"></i>
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-truck fs-2 mb-2 d-block"></i>
                                        Tidak ada data pengiriman ekspedisi dalam periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-3 py-2 border-top bg-light d-flex justify-content-between align-items-center small text-muted">
                    <span>Menampilkan {{ count($expedition['rows']) }} data ekspedisi 3PL</span>
                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- TAB 3: REPORT BIAYA PENGIRIMAN (INTERNAL DRIVER/HPP) -->
        <!-- ==================================================== -->
        <div id="tab-internal" class="tab-pane-content" style="{{ $activeTab === 'internal' ? 'display: block;' : 'display: none;' }}">
            <!-- SUMMARY CARDS TAB 3 -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="fa-solid fa-route"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Perjalanan</div>
                            <div class="stat-value">{{ number_format($internal['summary']['total_trips']) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="fa-solid fa-truck"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Kendaraan</div>
                            <div class="stat-value">{{ number_format($internal['summary']['total_vehicles']) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Driver</div>
                            <div class="stat-value">{{ number_format($internal['summary']['total_drivers']) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon bg-danger-subtle text-danger">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Biaya Internal</div>
                            <div class="stat-value text-success">Rp {{ number_format($internal['summary']['total_internal_cost'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL DETAIL TAB 3: REPORT BIAYA PENGIRIMAN -->
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px;">No.</th>
                                <th>Jenis Tugas</th>
                                <th>Nomor SO / PO</th>
                                <th>Nama Costumer / Vendor</th>
                                <th>No DO Easy</th>
                                <th class="text-center">Tanggal Tugas / Kirim</th>
                                <th class="text-end">Total Harga Barang Sesuai SO/PO</th>
                                <th class="text-end">Biaya Kirim</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($internal['rows'] as $index => $row)
                                <tr>
                                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary fw-bold">{{ $row['task_type'] }}</span></td>
                                    <td class="fw-bold font-monospace text-primary">{{ $row['ref_no'] }}</td>
                                    <td class="fw-semibold text-dark">{{ $row['customer'] }}</td>
                                    <td class="font-monospace small text-muted">{{ $row['do_easy'] ?: '-' }}</td>
                                    <td class="text-center">{{ $row['date'] }}</td>
                                    <td class="text-end font-monospace">Rp {{ number_format($row['item_value'], 0, ',', '.') }}</td>
                                    <td class="text-end font-monospace fw-bold text-dark">Rp {{ number_format($row['total_cost'], 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @php
                                            $stClass = 'draft';
                                            $stLower = strtolower($row['status']);
                                            if (str_contains($stLower, 'terkirim') || str_contains($stLower, 'selesai') || str_contains($stLower, 'completed')) $stClass = 'terkirim';
                                            elseif (str_contains($stLower, 'kirim') || str_contains($stLower, 'jalan') || str_contains($stLower, 'active')) $stClass = 'dalam-pengiriman';
                                        @endphp
                                        <span class="status-pill {{ $stClass }}">
                                            <i class="fa-solid fa-circle" style="font-size: 6px;"></i>
                                            {{ $row['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-car-side fs-2 mb-2 d-block"></i>
                                        Tidak ada data pengiriman armada internal dalam periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-3 py-2 border-top bg-light d-flex justify-content-between align-items-center small text-muted">
                    <span>Menampilkan {{ count($internal['rows']) }} pengiriman armada internal</span>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL DETAIL TRANSAKSI SO/PO -->
    <div class="modal fade" id="soDetailModal" tabindex="-1" aria-labelledby="soDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-3">
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold" id="soDetailModalLabel">
                        <i class="fa-solid fa-file-invoice text-primary me-2"></i> Detail Transaksi: <span id="modalRefNo" class="text-primary font-monospace"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="modalBodyContent">
                    <!-- Dynamic details injected via JS -->
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Switch Tab Navigation
        function switchTab(tabName) {
            document.querySelectorAll('.tab-pane-content').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.logistics-nav-link').forEach(el => el.classList.remove('active'));

            const targetContent = document.getElementById('tab-' + tabName);
            if (targetContent) targetContent.style.display = 'block';

            // Active button
            event.currentTarget.classList.add('active');

            // Update hidden inputs
            document.getElementById('formActiveTab').value = tabName;

            // Update URL query parameter without refresh
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        }

        // Render Donut & Line Charts
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Donut Chart Komposisi
            const donutCtx = document.getElementById('compositionDonutChart');
            if (donutCtx) {
                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Internal (HPP)', 'Ekspedisi (3PL)', 'Packaging'],
                        datasets: [{
                            data: [
                                {{ $logistics['composition']['internal']['amount'] ?? 0 }},
                                {{ $logistics['composition']['expedition']['amount'] ?? 0 }},
                                {{ $logistics['composition']['packaging']['amount'] ?? 0 }}
                            ],
                            backgroundColor: ['#3b82f6', '#f59e0b', '#10b981'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            // 2. Line Chart Tren Biaya
            const trendCtx = document.getElementById('trendLineChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($logistics['trend']['labels'] ?? []) !!},
                        datasets: [{
                            label: 'Biaya Logistik',
                            data: {!! json_encode($logistics['trend']['values'] ?? []) !!},
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#2563eb',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(val) {
                                        return 'Rp ' + (val / 1000000).toFixed(0) + ' Jt';
                                    },
                                    font: { size: 10 }
                                },
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 10 } }
                            }
                        }
                    }
                });
            }
        });

        // Detail Transaksi Modal
        function showSoDetailModal(refNo, rowData) {
            document.getElementById('modalRefNo').innerText = refNo;
            const body = document.getElementById('modalBodyContent');

            let html = `
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="text-muted small">Customer / Vendor</div>
                        <div class="fw-bold fs-6">${rowData.customer}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Nilai Barang</div>
                        <div class="fw-bold fs-6 text-dark font-monospace">Rp ${Number(rowData.item_value).toLocaleString('id-ID')}</div>
                    </div>
                </div>

                <h6 class="fw-bold mb-2 text-uppercase tracking-wider small text-muted"><i class="fa-solid fa-coins me-1"></i> Rincian Komponen Biaya</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Komponen Biaya</th>
                                <th>Sumber Data</th>
                                <th class="text-end">Nominal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Pengiriman Internal</td>
                                <td><span class="badge bg-primary-subtle text-primary">HPP Ritase / Shift</span></td>
                                <td class="text-end font-monospace">Rp ${Number(rowData.internal_cost).toLocaleString('id-ID')}</td>
                            </tr>
                            <tr>
                                <td>Ekspedisi Luar (3PL)</td>
                                <td><span class="badge bg-warning-subtle text-warning">Vendor Kargo / Ekspedisi</span></td>
                                <td class="text-end font-monospace">Rp ${Number(rowData.expedition_cost).toLocaleString('id-ID')}</td>
                            </tr>
                            <tr>
                                <td>Biaya Packaging</td>
                                <td><span class="badge bg-success-subtle text-success">Modul Packing</span></td>
                                <td class="text-end font-monospace">Rp ${Number(rowData.packaging_cost).toLocaleString('id-ID')}</td>
                            </tr>
                            <tr class="table-light fw-bold">
                                <td colspan="2">TOTAL BIAYA LOGISTIK</td>
                                <td class="text-end font-monospace text-primary">Rp ${Number(rowData.total_logistics_cost).toLocaleString('id-ID')}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;

            body.innerHTML = html;
            new bootstrap.Modal(document.getElementById('soDetailModal')).show();
        }
    </script>
    @endpush
</x-app-layout>

