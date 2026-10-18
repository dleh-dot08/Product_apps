@if(auth()->check() && (auth()->user()->hasPermission('View Laporan') || (auth()->user()->roleRelation && strtoupper(auth()->user()->roleRelation->name) === 'SUPER ADMIN')))
<x-app-layout>
    <style>
        :root {
            --report-orange: #ea580c;
            --report-orange-light: #fff7ed;
            --report-orange-gradient: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
        }

        .report-page-header {
            background: linear-gradient(135deg, rgba(234, 88, 12, 0.08) 0%, rgba(249, 115, 22, 0.03) 100%);
            border: 1px solid rgba(234, 88, 12, 0.15);
            border-radius: 1.25rem;
            padding: 1.5rem 1.75rem;
            position: relative;
            overflow: hidden;
        }

        html[data-bs-theme="dark"] .report-page-header {
            background: linear-gradient(135deg, rgba(234, 88, 12, 0.12) 0%, rgba(15, 23, 42, 0.6) 100%);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .report-stat-card {
            border-radius: 1rem;
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        html[data-bs-theme="dark"] .report-stat-card {
            border-color: rgba(255, 255, 255, 0.06);
            background-color: #111418 !important;
        }

        .report-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.08) !important;
        }

        .report-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .report-tabs .nav-link {
            font-weight: 600;
            font-size: 0.9rem;
            color: #64748b;
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .report-tabs .nav-link:hover {
            color: var(--report-orange);
            background-color: rgba(234, 88, 12, 0.06);
        }

        .report-tabs .nav-link.active {
            color: #ffffff !important;
            background: var(--report-orange-gradient) !important;
            box-shadow: 0 6px 15px -3px rgba(234, 88, 12, 0.4) !important;
        }

        .export-card {
            border-radius: 1rem;
            border: 1px solid rgba(0, 0, 0, 0.07);
            transition: all 0.25s ease;
            background: #ffffff;
        }

        html[data-bs-theme="dark"] .export-card {
            background-color: #111418 !important;
            border-color: rgba(255, 255, 255, 0.08);
        }

        .export-card:hover {
            border-color: var(--report-orange);
            box-shadow: 0 12px 25px -4px rgba(234, 88, 12, 0.15);
            transform: translateY(-2px);
        }

        .custom-filter-pill {
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.4rem 0.85rem;
            border-radius: 2rem;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .custom-filter-pill.active {
            background: var(--report-orange);
            color: #ffffff !important;
        }

        .custom-filter-pill:not(.active) {
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
        }

        .custom-filter-pill:not(.active):hover {
            background: rgba(234, 88, 12, 0.12);
            color: var(--report-orange);
        }
    </style>

    <div class="container-fluid py-3">

        <!-- HEADER BANNER -->
        <div class="report-page-header shadow-sm mb-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="report-icon-box text-white" style="background: var(--report-orange-gradient);">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Pusat Laporan & Rekapitulasi</h3>
                            <p class="text-muted mb-0 small">
                                Rekap data komprehensif logistik, monitoring armada, kalkulasi HPP ritase, beban pengeluaran, dan penjualan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill shadow-xs">
                        <i class="fa-regular fa-calendar-days text-orange me-1"></i>
                        {{ $startDate->translatedFormat('d M Y') }} - {{ $endDate->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- FILTER & PRESET CONTROLS -->
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="overflow: hidden;">
            <div class="card-body p-3 p-lg-4">
                <form action="{{ route('reports.index') }}" method="GET" id="reportFilterForm">
                    <input type="hidden" name="tab" id="activeTabInput" value="{{ $activeTab }}">
                    <input type="hidden" name="preset" id="presetInput" value="{{ $preset }}">

                    <div class="row g-3 align-items-center">
                        <!-- Quick Presets -->
                        <div class="col-12 d-flex flex-wrap align-items-center gap-2 pb-2 border-bottom border-light-subtle">
                            <span class="text-muted small fw-semibold me-2"><i class="fa-solid fa-sliders me-1"></i> Rentang Periode:</span>
                            <a href="javascript:void(0)" onclick="setPreset('today')" class="custom-filter-pill {{ $preset == 'today' ? 'active' : '' }}">Hari Ini</a>
                            <a href="javascript:void(0)" onclick="setPreset('this_week')" class="custom-filter-pill {{ $preset == 'this_week' ? 'active' : '' }}">Minggu Ini</a>
                            <a href="javascript:void(0)" onclick="setPreset('this_month')" class="custom-filter-pill {{ $preset == 'this_month' ? 'active' : '' }}">Bulan Ini</a>
                            <a href="javascript:void(0)" onclick="setPreset('last_month')" class="custom-filter-pill {{ $preset == 'last_month' ? 'active' : '' }}">Bulan Lalu</a>
                            <a href="javascript:void(0)" onclick="setPreset('custom')" class="custom-filter-pill {{ $preset == 'custom' ? 'active' : '' }}">Custom</a>
                        </div>

                        <!-- Date Inputs & Driver Filter -->
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold mb-1">Tanggal Mulai</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fa-regular fa-calendar text-muted"></i></span>
                                <input type="date" name="start_date" id="start_date" class="form-control border-start-0" value="{{ $startDate->toDateString() }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold mb-1">Tanggal Akhir</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-body-tertiary border-end-0"><i class="fa-regular fa-calendar-check text-muted"></i></span>
                                <input type="date" name="end_date" id="end_date" class="form-control border-start-0" value="{{ $endDate->toDateString() }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold mb-1">Filter Driver</label>
                            <select name="driver_id" class="form-select form-select-sm">
                                <option value="">Semua Driver</option>
                                @foreach($drivers as $d)
                                    <option value="{{ $d->id }}" {{ $driverId == $d->id ? 'selected' : '' }}>{{ $d->full_name ?? $d->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-2 align-items-end mt-4">
                            <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold rounded-3 shadow-xs" style="background: var(--report-orange-gradient); border: none;">
                                <i class="fa-solid fa-filter me-1"></i> Terapkan
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-light border rounded-3 px-3" title="Reset Filter">
                                <i class="fa-solid fa-rotate-right"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- STATISTIK RINGKASAN CARDS -->
        <div class="row g-3 mb-4">
            <!-- 1. Total Tugas -->
            <div class="col-sm-6 col-xl-3">
                <div class="card report-stat-card shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase">Tugas & Ritase</span>
                            <div class="report-icon-box bg-primary bg-opacity-10 text-primary">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-1">{{ number_format($totalTasksCount) }}</h3>
                        <div class="d-flex align-items-center justify-content-between small text-muted">
                            <span>Selesai: <strong class="text-success">{{ $completedTasksCount }}</strong></span>
                            <span>Pending/Proses: <strong class="text-warning">{{ $totalTasksCount - $completedTasksCount }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Total Estimasi HPP Biaya -->
            <div class="col-sm-6 col-xl-3">
                <div class="card report-stat-card shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase">Estimasi HPP Ritase</span>
                            <div class="report-icon-box bg-success bg-opacity-10 text-success">
                                <i class="fa-solid fa-coins"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-1 text-success">Rp {{ number_format($totalHppCost, 0, ',', '.') }}</h3>
                        <div class="d-flex align-items-center justify-content-between small text-muted">
                            <span>Total Odometer: <strong>{{ number_format($totalOdometerKm, 1) }} KM</strong></span>
                            <span>{{ $shifts->count() }} Shift</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Total Pengeluaran Operasional -->
            <div class="col-sm-6 col-xl-3">
                <div class="card report-stat-card shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase">Beban Pengeluaran</span>
                            <div class="report-icon-box bg-danger bg-opacity-10 text-danger">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-1 text-danger">Rp {{ number_format($totalExpensesAmount, 0, ',', '.') }}</h3>
                        <div class="d-flex align-items-center justify-content-between small text-muted">
                            <span>Jumlah Transaksi: <strong>{{ $expenses->count() }} Bon</strong></span>
                            <span>{{ $expenseCategoriesSummary->count() }} Kategori</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Total Sales Order (SO) -->
            <div class="col-sm-6 col-xl-3">
                <div class="card report-stat-card shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase">Pesanan Penjualan (SO)</span>
                            <div class="report-icon-box bg-info bg-opacity-10 text-info">
                                <i class="fa-solid fa-bag-shopping"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-1">{{ number_format($totalSoCount) }}</h3>
                        <div class="d-flex align-items-center justify-content-between small text-muted">
                            <span>Pesanan: <strong>{{ number_format($totalOrderedQty, 0) }}</strong></span>
                            <span>Terkirim: <strong class="text-success">{{ number_format($totalShippedQty, 0) }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NAVIGATION TABS -->
        <ul class="nav report-tabs nav-pills gap-2 mb-4" id="reportTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link {{ $activeTab == 'ringkasan' ? 'active' : '' }}" onclick="switchTab('ringkasan')">
                    <i class="fa-solid fa-border-all me-2"></i> Pusat Ekspor & Ringkasan
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab == 'tugas' ? 'active' : '' }}" onclick="switchTab('tugas')">
                    <i class="fa-solid fa-list-check me-2"></i> Rekap Tugas Driver ({{ $allTasks->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab == 'hpp' ? 'active' : '' }}" onclick="switchTab('hpp')">
                    <i class="fa-solid fa-dollar-sign me-2"></i> Rekap HPP Ritase ({{ $shifts->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab == 'pengeluaran' ? 'active' : '' }}" onclick="switchTab('pengeluaran')">
                    <i class="fa-solid fa-receipt me-2"></i> Rekap Pengeluaran ({{ $expenses->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab == 'penjualan' ? 'active' : '' }}" onclick="switchTab('penjualan')">
                    <i class="fa-solid fa-chart-line me-2"></i> Rekap Penjualan SO ({{ $salesOrders->count() }})
                </button>
            </li>
        </ul>

        <!-- TAB CONTENT -->
        <div class="tab-content" id="reportTabsContent">

            <!-- TAB 1: PUSAT EKSPOR & RINGKASAN -->
            <div class="tab-pane fade {{ $activeTab == 'ringkasan' ? 'show active' : '' }}" id="tab-ringkasan">
                <div class="row g-4 mb-4">

                    <!-- Kartu 1: Laporan Tugas Driver -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card export-card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="report-icon-box bg-primary bg-opacity-10 text-primary">
                                        <i class="fa-solid fa-truck-fast"></i>
                                    </div>
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">Tugas & Delivery</span>
                                </div>
                                <h5 class="fw-bold mb-2">Laporan Tugas Driver</h5>
                                <p class="text-muted small mb-4">
                                    Rekap seluruh penugasan Pickup dan Delivery Order, lokasi asal/tujuan, driver penerima, dan status pengiriman.
                                </p>
                            </div>
                            <div>
                                <div class="p-2 rounded-3 bg-light-subtle small mb-3 border">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Total Baris:</span>
                                        <strong>{{ $allTasks->count() }} Data</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Tuntas:</span>
                                        <strong class="text-success">{{ $completedTasksCount }} Selesai</strong>
                                    </div>
                                </div>
                                <a href="{{ route('reports.export.tugas', ['preset' => $preset, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString(), 'driver_id' => $driverId]) }}" class="btn btn-primary w-100 fw-bold rounded-3 shadow-xs">
                                    <i class="fa-solid fa-file-excel me-2"></i> Download Excel
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu 2: Laporan HPP Ritase -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card export-card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="report-icon-box bg-success bg-opacity-10 text-success">
                                        <i class="fa-solid fa-money-bill-transfer"></i>
                                    </div>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">HPP Ritase</span>
                                </div>
                                <h5 class="fw-bold mb-2">Laporan HPP Ritase</h5>
                                <p class="text-muted small mb-4">
                                    Kalkulasi biaya riil operasional armada, BBM, upah supir, tol, serta alokasi prorata ke dokumen barang.
                                </p>
                            </div>
                            <div>
                                <div class="p-2 rounded-3 bg-light-subtle small mb-3 border">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Total Shift:</span>
                                        <strong>{{ $shifts->count() }} Ritase</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Total Biaya:</span>
                                        <strong class="text-success">Rp {{ number_format($totalHppCost, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                                <a href="{{ route('reports.export.hpp', ['preset' => $preset, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString(), 'driver_id' => $driverId]) }}" class="btn btn-success w-100 fw-bold rounded-3 shadow-xs">
                                    <i class="fa-solid fa-file-excel me-2"></i> Download Excel
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu 3: Laporan Pengeluaran -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card export-card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="report-icon-box bg-danger bg-opacity-10 text-danger">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">Operasional</span>
                                </div>
                                <h5 class="fw-bold mb-2">Laporan Pengeluaran</h5>
                                <p class="text-muted small mb-4">
                                    Rincian catatan bon dan struk pengeluaran harian sopir (Bensin, Uang Tol, Parkir, Tambal Ban, dan Makan).
                                </p>
                            </div>
                            <div>
                                <div class="p-2 rounded-3 bg-light-subtle small mb-3 border">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Transaksi:</span>
                                        <strong>{{ $expenses->count() }} Catatan</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Total Beban:</span>
                                        <strong class="text-danger">Rp {{ number_format($totalExpensesAmount, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                                <a href="{{ route('reports.export.expenses', ['preset' => $preset, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString(), 'driver_id' => $driverId]) }}" class="btn btn-danger w-100 fw-bold rounded-3 shadow-xs">
                                    <i class="fa-solid fa-file-excel me-2"></i> Download Excel
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu 4: Laporan Penjualan (SO) -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card export-card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="report-icon-box bg-info bg-opacity-10 text-info">
                                        <i class="fa-solid fa-bag-shopping"></i>
                                    </div>
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1">Penjualan SO</span>
                                </div>
                                <h5 class="fw-bold mb-2">Laporan Penjualan (SO)</h5>
                                <p class="text-muted small mb-4">
                                    Data pesanan Sales Order akurasi terintegrasi, kuantitas terkirim, sisa stok, dan status distribusi pengiriman.
                                </p>
                            </div>
                            <div>
                                <div class="p-2 rounded-3 bg-light-subtle small mb-3 border">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Total SO:</span>
                                        <strong>{{ $salesOrders->count() }} Dokumen</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Terkirim:</span>
                                        <strong>{{ number_format($totalShippedQty, 0) }} Unit</strong>
                                    </div>
                                </div>
                                <a href="{{ route('reports.export.sales-orders', ['preset' => $preset, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" class="btn btn-info text-white w-100 fw-bold rounded-3 shadow-xs">
                                    <i class="fa-solid fa-file-excel me-2"></i> Download Excel
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- KOMPOSISI BIAYA OPERASIONAL SECTION -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <h5 class="fw-bold mb-1"><i class="fa-solid fa-chart-pie text-orange me-2"></i>Komposisi Estimasi Biaya HPP Pengiriman</h5>
                        <p class="text-muted small mb-0">Rincian breakdown beban komponen operasional logistik pada periode terpilih.</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md">
                                <div class="p-3 border rounded-3 bg-light-subtle text-center">
                                    <div class="text-muted small fw-bold">Biaya BBM</div>
                                    <h5 class="fw-bold text-dark mt-1 mb-0">Rp {{ number_format($hppComposition['bbm'], 0, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="p-3 border rounded-3 bg-light-subtle text-center">
                                    <div class="text-muted small fw-bold">Upah / Manpower</div>
                                    <h5 class="fw-bold text-dark mt-1 mb-0">Rp {{ number_format($hppComposition['manpower'], 0, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="p-3 border rounded-3 bg-light-subtle text-center">
                                    <div class="text-muted small fw-bold">Tol & Parkir</div>
                                    <h5 class="fw-bold text-dark mt-1 mb-0">Rp {{ number_format($hppComposition['tol'] + $hppComposition['parkir'], 0, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="p-3 border rounded-3 bg-light-subtle text-center">
                                    <div class="text-muted small fw-bold">Biaya Lainnya</div>
                                    <h5 class="fw-bold text-dark mt-1 mb-0">Rp {{ number_format($hppComposition['lainnya'], 0, ',', '.') }}</h5>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="p-3 border rounded-3 text-center" style="background: rgba(234, 88, 12, 0.1); border-color: rgba(234, 88, 12, 0.3) !important;">
                                    <div class="text-orange small fw-bold">Total Estimasi HPP</div>
                                    <h5 class="fw-bold text-orange mt-1 mb-0">Rp {{ number_format($totalHppCost, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: REKAP TUGAS DRIVER -->
            <div class="tab-pane fade {{ $activeTab == 'tugas' ? 'show active' : '' }}" id="tab-tugas">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center p-4">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="fa-solid fa-list-check text-orange me-2"></i>Rincian Rekap Tugas Driver</h5>
                            <p class="text-muted small mb-0">Daftar seluruh tugas pickup dan delivery dalam rentang tanggal terpilih.</p>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <a href="{{ route('reports.export.tugas', ['preset' => $preset, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString(), 'driver_id' => $driverId]) }}" class="btn btn-sm btn-primary fw-bold rounded-3">
                                <i class="fa-solid fa-file-excel me-1"></i> Ekspor Tugas
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-uppercase small" style="font-size: 0.75rem;">
                                    <tr>
                                        <th class="px-4 py-3">No</th>
                                        <th class="py-3">Tipe</th>
                                        <th class="py-3">No Referensi / SO</th>
                                        <th class="py-3">Driver</th>
                                        <th class="py-3">Armada Mobil</th>
                                        <th class="py-3">Tanggal</th>
                                        <th class="py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allTasks as $idx => $task)
                                    <tr>
                                        <td class="px-4 py-3 text-muted">{{ $idx + 1 }}</td>
                                        <td class="py-3">
                                            @if($task->task_type == 'pickup')
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">
                                                    <i class="fa-solid fa-box-open me-1"></i> PICKUP
                                                </span>
                                            @else
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                                    <i class="fa-solid fa-truck-ramp-box me-1"></i> DELIVERY
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 fw-bold">
                                            {{ $task->task_type == 'pickup' ? $task->reference_number : ($task->salesOrder->so_number ?? '-') }}
                                        </td>
                                        <td class="py-3">
                                            {{ $task->driver->full_name ?? $task->driver->name ?? 'Belum Ditugaskan' }}
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-light text-dark border px-2 py-1">
                                                {{ $task->vehicle->plate_number ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="py-3 small text-muted">
                                            {{ $task->sort_date ? \Carbon\Carbon::parse($task->sort_date)->format('d M Y, H:i') : '-' }}
                                        </td>
                                        <td class="py-3 text-center">
                                            @php
                                                $st = strtolower($task->status ?? 'draft');
                                                $badgeClass = match($st) {
                                                    'completed', 'delivered' => 'bg-success',
                                                    'on_route', 'in_progress' => 'bg-warning text-dark',
                                                    'assigned' => 'bg-info text-white',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1">
                                                {{ strtoupper($st) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-inbox fs-2 mb-2 d-block opacity-50"></i>
                                            Tidak ada data tugas pada periode ini.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: REKAP HPP RITASE -->
            <div class="tab-pane fade {{ $activeTab == 'hpp' ? 'show active' : '' }}" id="tab-hpp">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center p-4">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="fa-solid fa-coins text-orange me-2"></i>Rincian Rekap HPP Ritase Shift</h5>
                            <p class="text-muted small mb-0">Kalkulasi biaya riil operasional per shift driver & ritase armada.</p>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <a href="{{ route('reports.export.hpp', ['preset' => $preset, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString(), 'driver_id' => $driverId]) }}" class="btn btn-sm btn-success fw-bold rounded-3">
                                <i class="fa-solid fa-file-excel me-1"></i> Ekspor HPP Ritase
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-uppercase small" style="font-size: 0.75rem;">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="py-3">Driver</th>
                                        <th class="py-3">Kendaraan</th>
                                        <th class="py-3 text-end">Jarak (KM)</th>
                                        <th class="py-3 text-end">BBM</th>
                                        <th class="py-3 text-end">Manpower</th>
                                        <th class="py-3 text-end">Tol & Parkir</th>
                                        <th class="py-3 text-end px-4">Total HPP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($shifts as $shift)
                                    @php
                                        $dist = ($shift->end_odometer && $shift->start_odometer) ? max(0, $shift->end_odometer - $shift->start_odometer) : 0;
                                        $calc = $shift->calc_details ?? [];
                                        $costs = $calc['costs'] ?? [];
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 fw-bold">
                                            {{ $shift->work_date ? $shift->work_date->format('d M Y') : '-' }}
                                        </td>
                                        <td class="py-3">
                                            {{ $shift->driver->full_name ?? $shift->driver->name ?? '-' }}
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-light text-dark border px-2 py-1">
                                                {{ $shift->vehicle->plate_number ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-end fw-semibold">
                                            {{ number_format($dist, 1) }} KM
                                        </td>
                                        <td class="py-3 text-end">
                                            Rp {{ number_format($costs['fuel'] ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 text-end">
                                            Rp {{ number_format($costs['manpower'] ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 text-end">
                                            Rp {{ number_format(($costs['toll'] ?? 0) + ($costs['parking'] ?? 0), 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 text-end px-4 fw-bold text-success">
                                            Rp {{ number_format($shift->total_cost ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-inbox fs-2 mb-2 d-block opacity-50"></i>
                                            Tidak ada data shift ritase pada periode ini.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: REKAP PENGELUARAN -->
            <div class="tab-pane fade {{ $activeTab == 'pengeluaran' ? 'show active' : '' }}" id="tab-pengeluaran">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center p-4">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="fa-solid fa-wallet text-orange me-2"></i>Rincian Pengeluaran Operasional</h5>
                            <p class="text-muted small mb-0">Catatan bon, struk BBM, tol, dan pengeluaran driver di lapangan.</p>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <a href="{{ route('reports.export.expenses', ['preset' => $preset, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString(), 'driver_id' => $driverId]) }}" class="btn btn-sm btn-danger fw-bold rounded-3">
                                <i class="fa-solid fa-file-excel me-1"></i> Ekspor Pengeluaran
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-uppercase small" style="font-size: 0.75rem;">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="py-3">Driver</th>
                                        <th class="py-3">Kategori</th>
                                        <th class="py-3">Keterangan</th>
                                        <th class="py-3 text-end px-4">Nominal (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenses as $exp)
                                    <tr>
                                        <td class="px-4 py-3 small text-muted">
                                            {{ $exp->occurred_at ? $exp->occurred_at->format('d M Y, H:i') : '-' }}
                                        </td>
                                        <td class="py-3 fw-semibold">
                                            {{ $exp->driver->full_name ?? $exp->driver->name ?? '-' }}
                                        </td>
                                        <td class="py-3">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 rounded-pill">
                                                {{ ucfirst($exp->category ?? 'Lainnya') }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-muted">
                                            {{ $exp->description ?? '-' }}
                                        </td>
                                        <td class="py-3 text-end px-4 fw-bold text-danger">
                                            Rp {{ number_format($exp->amount ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-inbox fs-2 mb-2 d-block opacity-50"></i>
                                            Tidak ada catatan pengeluaran pada periode ini.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: REKAP PENJUALAN (SO) -->
            <div class="tab-pane fade {{ $activeTab == 'penjualan' ? 'show active' : '' }}" id="tab-penjualan">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center p-4">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="fa-solid fa-bag-shopping text-orange me-2"></i>Rincian Rekap Sales Order (SO)</h5>
                            <p class="text-muted small mb-0">Daftar pesanan penjualan dan status pemenuhan logistik pengiriman.</p>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <a href="{{ route('reports.export.sales-orders', ['preset' => $preset, 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}" class="btn btn-sm btn-info text-white fw-bold rounded-3">
                                <i class="fa-solid fa-file-excel me-1"></i> Ekspor Penjualan (SO)
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-uppercase small" style="font-size: 0.75rem;">
                                    <tr>
                                        <th class="px-4 py-3">No SO</th>
                                        <th class="py-3">Tanggal SO</th>
                                        <th class="py-3">Customer</th>
                                        <th class="py-3 text-end">Pesanan</th>
                                        <th class="py-3 text-end">Terkirim</th>
                                        <th class="py-3 text-end">Sisa</th>
                                        <th class="py-3 text-center px-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salesOrders as $so)
                                    <tr>
                                        <td class="px-4 py-3 fw-bold text-primary">
                                            {{ $so->so_number ?? '-' }}
                                        </td>
                                        <td class="py-3 small text-muted">
                                            {{ $so->so_date ? \Carbon\Carbon::parse($so->so_date)->format('d M Y') : '-' }}
                                        </td>
                                        <td class="py-3 fw-semibold">
                                            {{ $so->customer_name ?? '-' }}
                                        </td>
                                        <td class="py-3 text-end fw-bold">
                                            {{ number_format($so->ordered_quantity ?? 0, 0) }}
                                        </td>
                                        <td class="py-3 text-end text-success fw-bold">
                                            {{ number_format($so->shipped_quantity ?? 0, 0) }}
                                        </td>
                                        <td class="py-3 text-end text-danger fw-semibold">
                                            {{ number_format($so->remaining_quantity ?? 0, 0) }}
                                        </td>
                                        <td class="py-3 text-center px-4">
                                            <span class="badge bg-light text-dark border px-3 py-1 rounded-pill">
                                                {{ strtoupper($so->status ?? 'DRAFT') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-inbox fs-2 mb-2 d-block opacity-50"></i>
                                            Tidak ada data Sales Order pada periode ini.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        function setPreset(val) {
            document.getElementById('presetInput').value = val;
            
            const today = new Date();
            const formatDate = (d) => d.toISOString().split('T')[0];

            if (val === 'today') {
                document.getElementById('start_date').value = formatDate(today);
                document.getElementById('end_date').value = formatDate(today);
            } else if (val === 'this_week') {
                const firstDay = new Date(today.setDate(today.getDate() - today.getDay() + 1));
                const lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 7));
                document.getElementById('start_date').value = formatDate(firstDay);
                document.getElementById('end_date').value = formatDate(lastDay);
            } else if (val === 'this_month') {
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                document.getElementById('start_date').value = formatDate(firstDay);
                document.getElementById('end_date').value = formatDate(lastDay);
            } else if (val === 'last_month') {
                const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
                document.getElementById('start_date').value = formatDate(firstDay);
                document.getElementById('end_date').value = formatDate(lastDay);
            }

            document.getElementById('reportFilterForm').submit();
        }

        function switchTab(tabName) {
            document.getElementById('activeTabInput').value = tabName;
            
            // Switch tab view dynamically
            const tabEl = document.getElementById('tab-' + tabName);
            if (tabEl) {
                document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('show', 'active'));
                tabEl.classList.add('show', 'active');
                
                document.querySelectorAll('#reportTabs .nav-link').forEach(el => el.classList.remove('active'));
                event.target.classList.add('active');
            }
        }
    </script>
</x-app-layout>
@else
<x-app-layout>
    <div class="container py-5 text-center">
        <div class="alert alert-danger d-inline-block shadow-sm rounded-4 px-4 py-3">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> Anda tidak memiliki hak akses untuk melihat halaman Laporan ini.
        </div>
    </div>
</x-app-layout>
@endif
