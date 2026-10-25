@if(auth()->check() && (auth()->user()->hasPermission('View Laporan') || (auth()->user()->roleRelation && strtoupper(auth()->user()->roleRelation->name) === 'SUPER ADMIN')))
<x-app-layout>
    <style>
        :root {
            --report-orange: #ea580c;
            --report-orange-gradient: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
        }

        .report-header-banner {
            background: linear-gradient(135deg, rgba(234, 88, 12, 0.08) 0%, rgba(249, 115, 22, 0.02) 100%);
            border: 1px solid rgba(234, 88, 12, 0.15);
            border-radius: 1.25rem;
            padding: 1.5rem 1.75rem;
        }

        html[data-bs-theme="dark"] .report-header-banner {
            background: linear-gradient(135deg, rgba(234, 88, 12, 0.12) 0%, rgba(15, 23, 42, 0.6) 100%);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .report-card {
            border-radius: 1rem;
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        html[data-bs-theme="dark"] .report-card {
            border-color: rgba(255, 255, 255, 0.08);
            background-color: #111418 !important;
        }

        .report-card:hover {
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.06) !important;
        }

        .report-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .preset-pill {
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.35rem 0.85rem;
            border-radius: 2rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .preset-pill.active {
            background: var(--report-orange);
            color: #ffffff !important;
        }

        .preset-pill:not(.active) {
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
        }

        .preset-pill:not(.active):hover {
            background: rgba(234, 88, 12, 0.12);
            color: var(--report-orange);
        }

        .chart-box {
            position: relative;
            min-height: 270px;
            max-height: 320px;
            width: 100%;
        }

        .report-nav-tab {
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.65rem 1.25rem;
            border-radius: 0.75rem;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .report-nav-tab.active {
            color: #ffffff !important;
            background: var(--report-orange-gradient);
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
        }

        .report-nav-tab:not(.active):hover {
            background: rgba(234, 88, 12, 0.08);
            color: var(--report-orange);
        }

        /* Select2 Bootstrap-5 Custom Modern Styling */
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 0.65rem;
            border-color: #e2e8f0;
            font-size: 0.85rem;
            min-height: 38px;
            transition: all 0.2s ease-in-out;
            background-color: #f8fafc;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            border-color: var(--report-orange) !important;
            box-shadow: 0 0 0 0.2rem rgba(234, 88, 12, 0.15) !important;
            background-color: #ffffff;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border-radius: 0.75rem;
            border-color: rgba(0, 0, 0, 0.08);
            box-shadow: 0 14px 30px -4px rgba(0, 0, 0, 0.12);
            font-size: 0.85rem;
            z-index: 1055;
            overflow: hidden;
            margin-top: 4px;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
            background-color: var(--report-orange);
            color: #ffffff;
        }

        /* Compact & Sleek Multi-select Container with neat scroll */
        .select2-container--bootstrap-5 .select2-selection--multiple {
            max-height: 78px;
            overflow-y: auto;
            padding: 3px 6px !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple::-webkit-scrollbar {
            width: 4px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
            padding: 0;
            gap: 4px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
            background: #fff7ed;
            border: 1px solid #ffedd5;
            color: #c2410c;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.4rem;
            padding: 1px 7px;
            margin: 2px 3px 2px 0;
            display: inline-flex;
            align-items: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            line-height: 1.5;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice__remove {
            color: #ea580c;
            margin-right: 5px;
            border-right: 1px solid #fed7aa;
            padding-right: 4px;
            font-size: 0.85rem;
            transition: color 0.15s;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice__remove:hover {
            color: #9a3412;
        }

        .select2-container--bootstrap-5 .select2-search__field {
            font-size: 0.82rem;
            margin-top: 2px;
        }

        html[data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection {
            background-color: #111418;
            border-color: rgba(255, 255, 255, 0.12);
            color: #e2e8f0;
        }

        html[data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-dropdown {
            background-color: #1e293b;
            border-color: rgba(255, 255, 255, 0.12);
            color: #e2e8f0;
        }

        html[data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-results__option {
            color: #cbd5e1;
        }

        html[data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
            background-color: rgba(234, 88, 12, 0.2);
            border-color: rgba(234, 88, 12, 0.4);
            color: #fdba74;
        }

        html[data-bs-theme="dark"] .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice__remove {
            color: #fdba74;
            border-right-color: rgba(234, 88, 12, 0.4);
        }
    </style>

    <div class="container-fluid py-3">

        <!-- SUB NAV / SWITCHER: LAPORAN DRIVER vs LAPORAN KEUANGAN -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2 p-1 bg-body-tertiary border rounded-4 shadow-xs">
                @if(auth()->user()->hasPermission('View Laporan'))
                <a href="{{ route('reports.driver') }}" class="report-nav-tab active">
                    <i class="fa-solid fa-id-card"></i> Laporan Driver & Kendaraan
                </a>
                @endif
                @if(auth()->user()->hasPermission('View Pengeluaran'))
                <a href="{{ route('expenses.index') }}" class="report-nav-tab">
                    <i class="fa-solid fa-money-bill-wave"></i> Laporan Keuangan (Pengeluaran)
                </a>
                @endif
            </div>
            @if(auth()->user()->hasPermission('Export Laporan'))
            <div class="d-flex align-items-center gap-2">
                <button type="button" onclick="exportDriverReportWithCharts()" class="btn btn-sm btn-success fw-bold rounded-3 px-3 shadow-xs">
                    <i class="fa-solid fa-file-excel me-1"></i> Ekspor Laporan Driver + Grafik (.xlsx)
                </button>
            </div>
            @endif
        </div>

        <!-- HEADER BANNER -->
        <div class="report-header-banner shadow-sm mb-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="report-icon-box text-white" style="background: var(--report-orange-gradient);">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Laporan Aktivitas Driver & Kendaraan</h4>
                            <p class="text-muted mb-0 small">
                                Monitoring detail perjalanan driver, odometer awal & akhir, jam berangkat & tiba, durasi, serta analitik utilisasi kendaraan.
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

        <!-- FILTER TOOLBAR -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-body p-3 p-lg-4">
                <form action="{{ route('reports.driver') }}" method="GET" id="driverReportFilterForm">
                    <input type="hidden" name="preset" id="presetInput" value="{{ $preset }}">

                    <!-- Presets Header -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="text-secondary small fw-bold me-1">
                                <i class="fa-solid fa-calendar-week text-orange me-1"></i> Periode Cepat:
                            </span>
                            <a href="javascript:void(0)" onclick="setPreset('today')" class="preset-pill {{ $preset == 'today' ? 'active' : '' }}">Hari Ini</a>
                            <a href="javascript:void(0)" onclick="setPreset('this_week')" class="preset-pill {{ $preset == 'this_week' ? 'active' : '' }}">Minggu Ini</a>
                            <a href="javascript:void(0)" onclick="setPreset('this_month')" class="preset-pill {{ $preset == 'this_month' ? 'active' : '' }}">Bulan Ini</a>
                            <a href="javascript:void(0)" onclick="setPreset('last_month')" class="preset-pill {{ $preset == 'last_month' ? 'active' : '' }}">Bulan Lalu</a>
                            <a href="javascript:void(0)" onclick="setPreset('all')" class="preset-pill {{ $preset == 'all' ? 'active' : '' }}">Semua Riwayat</a>
                            <a href="javascript:void(0)" onclick="setPreset('custom')" class="preset-pill {{ $preset == 'custom' ? 'active' : '' }}">Kustom</a>
                        </div>
                        <div class="d-none d-md-flex align-items-center text-muted small">
                            <i class="fa-solid fa-circle-info text-info me-1"></i> Pilih filter & klik terapkan
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Date Filters -->
                        <div class="col-sm-6 col-lg-3">
                            <label class="form-label text-secondary small fw-bold mb-1">
                                <i class="fa-regular fa-calendar text-primary me-1"></i> Tanggal Mulai
                            </label>
                            <input type="date" name="start_date" id="start_date" class="form-control form-control-sm rounded-3 py-2" value="{{ $startDate->toDateString() }}">
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <label class="form-label text-secondary small fw-bold mb-1">
                                <i class="fa-regular fa-calendar-check text-success me-1"></i> Tanggal Akhir
                            </label>
                            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm rounded-3 py-2" value="{{ $endDate->toDateString() }}">
                        </div>

                        <!-- Status Filter -->
                        <div class="col-sm-6 col-lg-3">
                            <label class="form-label text-secondary small fw-bold mb-1">
                                <i class="fa-solid fa-circle-check text-info me-1"></i> Status Perjalanan
                            </label>
                            <select name="status" id="status" class="form-select form-select-sm rounded-3 py-2">
                                <option value="">Semua Status (Selesai & Aktif)</option>
                                <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Selesai Saja (Check Out)</option>
                                <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>Aktif Saja (Dalam Perjalanan)</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-sm-6 col-lg-3 d-flex gap-2 align-items-end">
                            <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold rounded-3 shadow-xs d-flex align-items-center justify-content-center gap-2" style="background: var(--report-orange-gradient); border: none; height: 38px;">
                                <i class="fa-solid fa-filter"></i> Terapkan Filter
                            </button>
                            <a href="{{ route('reports.driver') }}" class="btn btn-sm btn-light border rounded-3 px-3 d-flex align-items-center justify-content-center" title="Reset Filter" style="height: 38px;">
                                <i class="fa-solid fa-rotate-right text-secondary"></i>
                            </a>
                        </div>

                        <!-- Multi-Select Driver Filter -->
                        <div class="col-lg-6">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label text-secondary small fw-bold mb-0 d-flex align-items-center gap-1">
                                    <i class="fa-solid fa-user-tie text-primary"></i> Filter Driver
                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary fw-semibold px-2" id="driver_count_badge" style="font-size: 0.7rem;">
                                        {{ count((array)($driverIds ?? [])) > 0 ? count((array)$driverIds) . ' Dipilih' : 'Semua Driver' }}
                                    </span>
                                </label>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold text-primary me-2" onclick="selectAll('driver_id')" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-check-double me-1"></i>Pilih Semua
                                    </button>
                                    <span class="text-muted small">|</span>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold text-danger ms-2" onclick="clearAll('driver_id')" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-xmark me-1"></i>Kosongkan
                                    </button>
                                </div>
                            </div>
                            <select name="driver_id[]" id="driver_id" class="form-select select2-filter" multiple="multiple" data-placeholder="Cari driver (kosongkan = semua driver)...">
                                @foreach($drivers as $d)
                                    <option value="{{ $d->id }}" {{ in_array($d->id, (array)($driverIds ?? [])) ? 'selected' : '' }}>
                                        {{ $d->full_name ?? $d->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Multi-Select Vehicle Filter -->
                        <div class="col-lg-6">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label text-secondary small fw-bold mb-0 d-flex align-items-center gap-1">
                                    <i class="fa-solid fa-truck text-warning"></i> Filter Kendaraan / Armada
                                    <span class="badge rounded-pill bg-warning bg-opacity-15 text-warning-emphasis fw-semibold px-2" id="vehicle_count_badge" style="font-size: 0.7rem;">
                                        {{ count((array)($vehicleIds ?? [])) > 0 ? count((array)$vehicleIds) . ' Dipilih' : 'Semua Armada' }}
                                    </span>
                                </label>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold text-primary me-2" onclick="selectAll('vehicle_id')" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-check-double me-1"></i>Pilih Semua
                                    </button>
                                    <span class="text-muted small">|</span>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold text-danger ms-2" onclick="clearAll('vehicle_id')" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-xmark me-1"></i>Kosongkan
                                    </button>
                                </div>
                            </div>
                            <select name="vehicle_id[]" id="vehicle_id" class="form-select select2-filter" multiple="multiple" data-placeholder="Cari plat nomor / tipe armada (kosongkan = semua)...">
                                @foreach($allVehicles as $v)
                                    <option value="{{ $v->id }}" {{ in_array($v->id, (array)($vehicleIds ?? [])) ? 'selected' : '' }}>
                                        {{ $v->plate_number }} ({{ $v->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @php
            $hasDriverFilter = !empty($driverIds);
            $hasVehicleFilter = !empty($vehicleIds);
            $showVehicleCards = !$hasDriverFilter || $hasVehicleFilter;
            $showVehicleCharts = !$hasDriverFilter || $hasVehicleFilter;
            $showDriverCharts = !$hasVehicleFilter || $hasDriverFilter;
        @endphp

        <!-- TOP KPI STATS CARDS -->
        <div class="row g-3 mb-4">
            <div class="{{ $showVehicleCards ? 'col-6 col-lg-3' : 'col-12 col-md-4' }}">
                <div class="card report-card shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase">Total Ritase Perjalanan</span>
                            <div class="report-icon-box bg-primary bg-opacity-10 text-primary">
                                <i class="fa-solid fa-route"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark">{{ count($tableData) }}</h3>
                        <span class="text-muted small">Shift perjalanan driver</span>
                    </div>
                </div>
            </div>

            <div class="{{ $showVehicleCards ? 'col-6 col-lg-3' : 'col-12 col-md-4' }}">
                <div class="card report-card shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase">Total Jarak Tempuh</span>
                            <div class="report-icon-box bg-success bg-opacity-10 text-success">
                                <i class="fa-solid fa-gauge-high"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-success">{{ number_format($totalKmAll, 0, ',', '.') }} <small class="fs-6 fw-normal">KM</small></h3>
                        <span class="text-muted small">Total odometer perjalanan</span>
                    </div>
                </div>
            </div>

            @if($showVehicleCards)
            <div class="col-6 col-lg-3">
                <div class="card report-card shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase">Status Kendaraan</span>
                            <div class="report-icon-box bg-warning bg-opacity-10 text-warning">
                                <i class="fa-solid fa-truck"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark">{{ $operasionalVehiclesCount }} <small class="fs-6 fw-normal text-muted">/ {{ $totalVehiclesCount }} Armada</small></h3>
                        <div class="d-flex align-items-center justify-content-between text-muted small mt-1">
                            <span class="text-success fw-bold">{{ $pctOperasional }}% Operasional</span>
                            <span class="text-secondary fw-semibold">{{ $pctStandby }}% Standby</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="{{ $showVehicleCards ? 'col-6 col-lg-3' : 'col-12 col-md-4' }}">
                <div class="card report-card shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small fw-bold text-uppercase">Total Tugas Selesai</span>
                            <div class="report-icon-box bg-info bg-opacity-10 text-info">
                                <i class="fa-solid fa-clipboard-check"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-info">{{ $totalTasksAll }}</h3>
                        <span class="text-muted small">Penugasan Pickup & Delivery</span>
                    </div>
                </div>
            </div>
        </div>

        @if($showVehicleCharts || $showDriverCharts)
        <!-- GRAFIK ANALITIK DRIVER & KENDARAAN (SESUAI FILTER) -->
        <div class="row g-4 mb-4">

            @if($showVehicleCharts)
            <!-- GRAFIK 1: Persentase Pemakaian Kendaraan (Standby vs Operasional) -->
            <div class="{{ $showDriverCharts ? 'col-lg-5' : 'col-lg-5' }}">
                <div class="card report-card shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-1"><i class="fa-solid fa-chart-pie text-orange me-2"></i>Persentase Pemakaian Kendaraan</h6>
                                <p class="text-muted small mb-0">Rasio armada yang beroperasi vs standby pada periode ini.</p>
                            </div>
                            <span class="badge bg-light text-dark border">{{ $totalVehiclesCount }} Total Mobil</span>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2 d-flex flex-column align-items-center justify-content-center">
                        <div class="chart-box d-flex align-items-center justify-content-center" style="max-height: 250px;">
                            <canvas id="chartVehiclePercentage"></canvas>
                        </div>
                        <div class="row w-100 mt-3 text-center border-top pt-3">
                            <div class="col-6 border-end">
                                <span class="text-success fw-bold fs-5">{{ $pctOperasional }}%</span>
                                <div class="text-muted small">Operasional ({{ $operasionalVehiclesCount }} Unit)</div>
                            </div>
                            <div class="col-6">
                                <span class="text-secondary fw-bold fs-5">{{ $pctStandby }}%</span>
                                <div class="text-muted small">Standby ({{ $standbyVehiclesCount }} Unit)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRAFIK 2: Pemakaian per Kendaraan (Standby & Operasional) -->
            <div class="{{ $showDriverCharts ? 'col-lg-7' : 'col-lg-7' }}">
                <div class="card report-card shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-1"><i class="fa-solid fa-chart-column text-primary me-2"></i>Frekuensi Pemakaian per Kendaraan</h6>
                                <p class="text-muted small mb-0">Perbandingan hari operasional vs standby tiap armada kendaraan.</p>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary">Operasional vs Standby</span>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="chart-box">
                            <canvas id="chartUsagePerVehicle"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRAFIK 3: Jarak Tempuh per Kendaraan (KM) -->
            <div class="{{ $showDriverCharts ? 'col-lg-4' : 'col-12' }}">
                <div class="card report-card shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <h6 class="fw-bold mb-1"><i class="fa-solid fa-gauge-high text-success me-2"></i>Jarak Tempuh per Kendaraan</h6>
                        <p class="text-muted small mb-0">Akumulasi KM odometer per armada.</p>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="chart-box">
                            <canvas id="chartKmPerVehicle"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($showDriverCharts)
            <!-- GRAFIK 4: Jarak Tempuh per Driver (KM) -->
            <div class="{{ $showVehicleCharts ? 'col-lg-4' : 'col-lg-6' }}">
                <div class="card report-card shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <h6 class="fw-bold mb-1"><i class="fa-solid fa-road text-warning me-2"></i>Jarak Tempuh per Driver</h6>
                        <p class="text-muted small mb-0">Akumulasi KM tempuh oleh masing-masing driver.</p>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="chart-box">
                            <canvas id="chartKmPerDriver"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRAFIK 5: Jumlah Tugas yang Didapat per Driver -->
            <div class="{{ $showVehicleCharts ? 'col-lg-4' : 'col-lg-6' }}">
                <div class="card report-card shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <h6 class="fw-bold mb-1"><i class="fa-solid fa-tasks text-info me-2"></i>Jumlah Tugas per Driver</h6>
                        <p class="text-muted small mb-0">Banyaknya tugas Pickup & Delivery per driver.</p>
                    </div>
                    <div class="card-body px-4 pb-4 pt-2">
                        <div class="chart-box">
                            <canvas id="chartTasksPerDriver"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
        @endif

        <!-- TABEL LIST SELURUH TUGAS & PERJALANAN DRIVER -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center p-4">
                <div>
                    <h5 class="fw-bold mb-1"><i class="fa-solid fa-table-list text-orange me-2"></i>Daftar Riwayat Perjalanan & Tugas Driver</h5>
                    <p class="text-muted small mb-0">Rekapitulasi lengkap driver, mobil, odometer awal/akhir, total KM, jam berangkat/tiba, durasi, dan tugas terkait.</p>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                    <input type="text" id="tableSearchInput" onkeyup="filterTable()" placeholder="Cari driver / plat / tugas..." class="form-control form-control-sm" style="width: 220px;">
                    <button type="button" onclick="exportDriverReportWithCharts()" class="btn btn-sm btn-success fw-bold rounded-3 text-nowrap">
                        <i class="fa-solid fa-file-excel me-1"></i> Download Excel + Grafik
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" style="min-height: 250px;">
                    <table class="table table-hover align-middle mb-0" id="driverTasksTable">
                        <thead class="table-light text-uppercase small" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            <tr>
                                <th class="px-4 py-3" style="width: 40px;">No</th>
                                <th class="py-3">Driver</th>
                                <th class="py-3">Mobil & Plat</th>
                                <th class="py-3 text-end">Odo Awal</th>
                                <th class="py-3 text-end">Odo Akhir</th>
                                <th class="py-3 text-end">Total Odo (KM)</th>
                                <th class="py-3 text-center">Perjalanan Awal</th>
                                <th class="py-3 text-center">Sampai</th>
                                <th class="py-3 text-center">Total Durasi</th>
                                <th class="py-3">Tugas Terkait</th>
                                <th class="py-3 text-center px-4">Status</th>
                                <th class="py-3 text-center px-3" style="width: 110px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tableData as $index => $row)
                            <tr>
                                <td class="px-4 py-3 text-muted">{{ $index + 1 }}</td>
                                <td class="py-3 fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px; font-weight: bold;">
                                            {{ strtoupper(substr($row['driver_name'], 0, 2)) }}
                                        </div>
                                        <div>
                                            <span>{{ $row['driver_name'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-semibold text-dark">{{ $row['vehicle_plate'] }}</div>
                                    <div class="text-muted small" style="font-size: 0.78rem;">{{ $row['vehicle_name'] }}</div>
                                </td>
                                <td class="py-3 text-end font-monospace">
                                    {{ number_format($row['odo_start'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-end font-monospace">
                                    {{ number_format($row['odo_end'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-end font-monospace fw-bold text-primary">
                                    {{ number_format($row['total_odo'], 0, ',', '.') }} KM
                                </td>
                                <td class="py-3 text-center small">
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="fa-regular fa-clock me-1 text-primary"></i> {{ $row['start_time'] }}
                                    </span>
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">{{ $row['start_date'] }}</div>
                                </td>
                                <td class="py-3 text-center small">
                                    @if($row['end_time'] != '-')
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            <i class="fa-regular fa-circle-check me-1 text-success"></i> {{ $row['end_time'] }}
                                        </span>
                                        <div class="text-muted small mt-1" style="font-size: 0.72rem;">{{ $row['end_date'] }}</div>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2 py-1">
                                            Sedang Jalan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 text-center fw-semibold">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 rounded-pill">
                                        {{ $row['durasi'] }}
                                    </span>
                                </td>
                                <td class="py-3" style="max-width: 250px;">
                                    @if(count($row['tasks']) > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($row['tasks'] as $t)
                                                 <span class="badge bg-light text-dark border small" style="font-size: 0.72rem;">
                                                    <strong class="{{ $t['type'] == 'Pickup' ? 'text-primary' : 'text-success' }}">{{ $t['type'] }}:</strong> {{ $t['ref'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">Tanpa Tugas Terkait</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center px-4">
                                    @if($row['status'] == 'Selesai')
                                        <span class="badge bg-success rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-check me-1"></i> SELESAI
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1">
                                            <i class="fa-solid fa-spinner fa-spin me-1"></i> AKTIF
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 text-center px-3">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 text-nowrap shadow-xs" 
                                            onclick='openUploadModal(@json($row))'
                                            title="Lihat Data Upload & File">
                                        <i class="fa-solid fa-paperclip me-1"></i> File 
                                        @if(($row['files_count'] ?? 0) > 0)
                                            <span class="badge bg-primary text-white rounded-pill ms-1">{{ $row['files_count'] }}</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-25 text-secondary rounded-pill ms-1">0</span>
                                        @endif
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-inbox fs-2 mb-2 d-block opacity-50"></i>
                                    Tidak ada data riwayat perjalanan driver pada periode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MODAL DETAIL DATA UPLOAD / LAMPIRAN FILE -->
        <div class="modal fade" id="modalUploadFiles" tabindex="-1" aria-labelledby="modalUploadFilesLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content rounded-4 border-0 shadow">
                    <div class="modal-header border-bottom py-3 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary">
                                <i class="fa-solid fa-folder-open fs-5"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0" id="modalUploadFilesLabel">Data Upload & Lampiran File</h5>
                                <div class="text-muted small" id="modalUploadSubtitle">Detail riwayat file upload driver</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 bg-body-tertiary">
                        <!-- Ringkasan Driver & Armada Info Box -->
                        <div class="card border rounded-3 p-3 mb-3 bg-body shadow-xs">
                            <div class="row g-2 small">
                                <div class="col-sm-6 col-md-3">
                                    <div class="text-muted">Driver:</div>
                                    <strong id="modalDriverName" class="text-dark">-</strong>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="text-muted">Kendaraan:</div>
                                    <strong id="modalVehicle" class="text-dark">-</strong>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="text-muted">Tanggal / Jam:</div>
                                    <strong id="modalDatetime" class="text-dark">-</strong>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="text-muted">Odometer:</div>
                                    <strong id="modalOdo" class="text-primary">-</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Tab / Pills dalam Modal -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold mb-0">
                                <i class="fa-solid fa-images text-orange me-1"></i> Daftar Lampiran (<span id="modalFileTotal">0</span>)
                            </h6>
                            <span class="badge bg-light text-muted border px-2 py-1 small" id="modalSourceBadge">Semua Modul</span>
                        </div>

                        <!-- Container Kartu Lampiran -->
                        <div id="modalFilesContainer" class="row g-3">
                            <!-- Items dynamically injected here -->
                        </div>

                        <!-- Empty State -->
                        <div id="modalFilesEmpty" class="text-center py-5 d-none">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                <i class="fa-solid fa-file-circle-xmark text-muted fs-3"></i>
                            </div>
                            <h6 class="fw-bold text-muted mb-1">Belum Ada File Terunggah</h6>
                            <p class="text-muted small mb-0">Driver belum mengunggah foto armada, bukti tugas, atau struk pengeluaran pada perjalanan ini.</p>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-2 px-4 bg-body">
                        <button type="button" class="btn btn-secondary rounded-3 px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden Form for Export with Base64 Charts -->
        <form id="exportWithChartsForm" method="POST" action="{{ route('reports.driver.export') }}" style="display: none;">
            @csrf
            <input type="hidden" name="preset" value="{{ $preset }}">
            <input type="hidden" name="start_date" value="{{ $startDate->toDateString() }}">
            <input type="hidden" name="end_date" value="{{ $endDate->toDateString() }}">
            <input type="hidden" name="status" value="{{ $status ?? '' }}">
            <div id="exportDriverInputs"></div>
            <div id="exportVehicleInputs"></div>
            <input type="hidden" name="chart_vehicle_pct" id="input_chart_vehicle_pct">
            <input type="hidden" name="chart_usage" id="input_chart_usage">
            <input type="hidden" name="chart_km_vehicle" id="input_chart_km_vehicle">
            <input type="hidden" name="chart_km_driver" id="input_chart_km_driver">
            <input type="hidden" name="chart_tasks_driver" id="input_chart_tasks_driver">
        </form>

    </div>

    <!-- SCRIPT CHART.JS & FILTER LOGIC -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Modal Upload Function
        function openUploadModal(row) {
            document.getElementById('modalDriverName').innerText = row.driver_name || '-';
            document.getElementById('modalVehicle').innerText = (row.vehicle_plate || '-') + ' (' + (row.vehicle_name || '-') + ')';
            document.getElementById('modalDatetime').innerText = (row.start_date || '-') + ' ' + (row.start_time || '');
            document.getElementById('modalOdo').innerText = (row.odo_start ? Number(row.odo_start).toLocaleString('id-ID') : 0) + ' -> ' + 
                                                           (row.odo_end ? Number(row.odo_end).toLocaleString('id-ID') : 0) + ' (' + (row.total_odo || 0) + ' KM)';
            
            const files = row.files || [];
            document.getElementById('modalFileTotal').innerText = files.length;

            const container = document.getElementById('modalFilesContainer');
            const emptyEl = document.getElementById('modalFilesEmpty');
            container.innerHTML = '';

            if (files.length === 0) {
                container.classList.add('d-none');
                emptyEl.classList.remove('d-none');
            } else {
                container.classList.remove('d-none');
                emptyEl.classList.add('d-none');

                files.forEach((file, idx) => {
                    const isPdf = (file.url || '').toLowerCase().endsWith('.pdf');
                    const col = document.createElement('div');
                    col.className = 'col-sm-6 col-md-4';
                    
                    let previewHtml = '';
                    if (isPdf) {
                        previewHtml = `
                            <div class="d-flex flex-column align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-3" style="height: 140px; cursor: pointer;">
                                <i class="fa-solid fa-file-pdf fs-1 mb-2"></i>
                                <span class="badge bg-danger">Dokumen PDF</span>
                            </div>
                        `;
                    } else {
                        previewHtml = `
                            <div class="position-relative overflow-hidden rounded-3 bg-black bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 140px;">
                                <img src="${file.url}" alt="${file.title}" class="w-100 h-100 object-fit-cover transition" onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\\'text-center text-muted p-3\\'><i class=\\'fa-regular fa-image fs-1 mb-1 d-block\\'></i><small>Gagal memuat gambar</small></div>';">
                                <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75 text-white" style="font-size: 0.7rem;">
                                    <i class="fa-solid fa-expand me-1"></i> Preview
                                </span>
                            </div>
                        `;
                    }

                    col.innerHTML = `
                        <div class="card h-100 border shadow-xs rounded-3 overflow-hidden">
                            <a href="${file.url}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                ${previewHtml}
                            </a>
                            <div class="p-3 d-flex flex-column justify-content-between flex-grow-1 bg-body">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.7rem;">
                                            ${file.source || 'Lampiran'}
                                        </span>
                                        <small class="text-muted" style="font-size: 0.7rem;">${file.created_at || '-'}</small>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-truncate text-dark" title="${file.title || file.category}">${file.title || file.category}</h6>
                                    ${file.notes ? `<p class="text-muted small mb-2 text-truncate" title="${file.notes}" style="font-size: 0.78rem;">${file.notes}</p>` : ''}
                                </div>
                                <div class="pt-2 border-top mt-2">
                                    <a href="${file.url}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-light border w-100 rounded-2 fw-semibold" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka File
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                });
            }

            const modal = new bootstrap.Modal(document.getElementById('modalUploadFiles'));
            modal.show();
        }

        function setPreset(val) {
            document.getElementById('presetInput').value = val;
            const today = new Date();
            const formatDate = (d) => d.toISOString().split('T')[0];

            if (val === 'today') {
                document.getElementById('start_date').value = formatDate(today);
                document.getElementById('end_date').value = formatDate(today);
            } else if (val === 'this_week') {
                const day = today.getDay() || 7;
                const firstDay = new Date(today);
                firstDay.setDate(today.getDate() - day + 1);
                const lastDay = new Date(today);
                lastDay.setDate(today.getDate() - day + 7);
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
            } else if (val === 'all') {
                document.getElementById('start_date').value = '2020-01-01';
                document.getElementById('end_date').value = formatDate(today);
            }

            document.getElementById('driverReportFilterForm').submit();
        }

        // Live Table Filter Search
        function filterTable() {
            const input = document.getElementById("tableSearchInput").value.toUpperCase();
            const table = document.getElementById("driverTasksTable");
            const trs = table.getElementsByTagName("tr");

            for (let i = 1; i < trs.length; i++) {
                const text = trs[i].innerText.toUpperCase();
                trs[i].style.display = text.indexOf(input) > -1 ? "" : "none";
            }
        }

        // Helper buttons for Select2 Multi-Select
        function selectAll(id) {
            $('#' + id + ' > option').prop('selected', true);
            $('#' + id).trigger('change');
        }

        function clearAll(id) {
            $('#' + id).val(null).trigger('change');
        }

        // Export Excel with all 5 canvas charts embedded
        function exportDriverReportWithCharts() {
            const c1 = document.getElementById('chartVehiclePercentage');
            const c2 = document.getElementById('chartUsagePerVehicle');
            const c3 = document.getElementById('chartKmPerVehicle');
            const c4 = document.getElementById('chartKmPerDriver');
            const c5 = document.getElementById('chartTasksPerDriver');

            if (c1) document.getElementById('input_chart_vehicle_pct').value = c1.toDataURL('image/png');
            if (c2) document.getElementById('input_chart_usage').value = c2.toDataURL('image/png');
            if (c3) document.getElementById('input_chart_km_vehicle').value = c3.toDataURL('image/png');
            if (c4) document.getElementById('input_chart_km_driver').value = c4.toDataURL('image/png');
            if (c5) document.getElementById('input_chart_tasks_driver').value = c5.toDataURL('image/png');

            // Populate multi-select driver inputs
            const driverContainer = document.getElementById('exportDriverInputs');
            if (driverContainer) {
                driverContainer.innerHTML = '';
                const selectedDrivers = $('#driver_id').val() || [];
                selectedDrivers.forEach(val => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'driver_id[]';
                    inp.value = val;
                    driverContainer.appendChild(inp);
                });
            }

            // Populate multi-select vehicle inputs
            const vehicleContainer = document.getElementById('exportVehicleInputs');
            if (vehicleContainer) {
                vehicleContainer.innerHTML = '';
                const selectedVehicles = $('#vehicle_id').val() || [];
                selectedVehicles.forEach(val => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'vehicle_id[]';
                    inp.value = val;
                    vehicleContainer.appendChild(inp);
                });
            }

            document.getElementById('exportWithChartsForm').submit();
        }

        // INITIALIZE ALL 5 CHARTS & SELECT2
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Select2 with Search & Multi-Select
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2-filter').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    closeOnSelect: false,
                    allowClear: true
                });

                // Update badge counts dynamically on change
                function updateSelectBadges() {
                    const selectedDrivers = $('#driver_id').val() || [];
                    const dBadge = document.getElementById('driver_count_badge');
                    if (dBadge) {
                        dBadge.innerText = selectedDrivers.length > 0 ? `${selectedDrivers.length} Dipilih` : 'Semua Driver';
                    }

                    const selectedVehicles = $('#vehicle_id').val() || [];
                    const vBadge = document.getElementById('vehicle_count_badge');
                    if (vBadge) {
                        vBadge.innerText = selectedVehicles.length > 0 ? `${selectedVehicles.length} Dipilih` : 'Semua Armada';
                    }
                }

                $('#driver_id, #vehicle_id').on('change', updateSelectBadges);
            }

            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.05)';

            // 1. Chart Persentase Pemakaian Kendaraan (Donut)
            const ctxPct = document.getElementById('chartVehiclePercentage');
            if (ctxPct) {
                new Chart(ctxPct, {
                    type: 'doughnut',
                    data: {
                        labels: @json($chartVehicleStatus['labels']),
                        datasets: [{
                            data: @json($chartVehicleStatus['data']),
                            backgroundColor: ['#10b981', '#94a3b8'],
                            borderWidth: 2,
                            borderColor: isDark ? '#111418' : '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: textColor, font: { size: 11, weight: '600' } }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        const val = ctx.raw || 0;
                                        const pct = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const p = pct > 0 ? ((val / pct) * 100).toFixed(1) : 0;
                                        return ` ${val} Armada (${p}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }

            // 2. Chart Pemakaian per Kendaraan (Standby & Operasional) (Stacked Bar)
            const ctxUsage = document.getElementById('chartUsagePerVehicle');
            if (ctxUsage) {
                new Chart(ctxUsage, {
                    type: 'bar',
                    data: {
                        labels: @json($chartUsagePerVehicle['labels']),
                        datasets: [
                            {
                                label: 'Operasional (Ritase)',
                                data: @json($chartUsagePerVehicle['operasional']),
                                backgroundColor: '#10b981',
                                borderRadius: 6
                            },
                            {
                                label: 'Standby (Hari)',
                                data: @json($chartUsagePerVehicle['standby']),
                                backgroundColor: isDark ? 'rgba(255, 255, 255, 0.12)' : 'rgba(100, 116, 139, 0.2)',
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true,
                                grid: { display: false },
                                ticks: { color: textColor, font: { size: 10 } }
                            },
                            y: {
                                stacked: true,
                                grid: { color: gridColor },
                                ticks: { color: textColor, stepSize: 1 }
                            }
                        },
                        plugins: {
                            legend: { position: 'top', labels: { color: textColor, font: { size: 11 } } }
                        }
                    }
                });
            }

            // 3. Chart Jarak Tempuh per Kendaraan (KM)
            const ctxKmVehicle = document.getElementById('chartKmPerVehicle');
            if (ctxKmVehicle) {
                new Chart(ctxKmVehicle, {
                    type: 'bar',
                    data: {
                        labels: @json($chartKmPerVehicle['labels']),
                        datasets: [{
                            label: 'Jarak (KM)',
                            data: @json($chartKmPerVehicle['data']),
                            backgroundColor: '#0284c7',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        scales: {
                            x: { grid: { color: gridColor }, ticks: { color: textColor } },
                            y: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) { return ` ${Number(ctx.raw).toLocaleString('id-ID')} KM`; }
                                }
                            }
                        }
                    }
                });
            }

            // 4. Chart Jarak Tempuh per Driver (KM)
            const ctxKmDriver = document.getElementById('chartKmPerDriver');
            if (ctxKmDriver) {
                new Chart(ctxKmDriver, {
                    type: 'bar',
                    data: {
                        labels: @json($chartKmPerDriver['labels']),
                        datasets: [{
                            label: 'Jarak (KM)',
                            data: @json($chartKmPerDriver['data']),
                            backgroundColor: '#f59e0b',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } },
                            y: { grid: { color: gridColor }, ticks: { color: textColor } }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) { return ` ${Number(ctx.raw).toLocaleString('id-ID')} KM`; }
                                }
                            }
                        }
                    }
                });
            }

            // 5. Chart Jumlah Tugas per Driver
            const ctxTasksDriver = document.getElementById('chartTasksPerDriver');
            if (ctxTasksDriver) {
                new Chart(ctxTasksDriver, {
                    type: 'bar',
                    data: {
                        labels: @json($chartTasksPerDriver['labels']),
                        datasets: [{
                            label: 'Tugas Dikerjakan',
                            data: @json($chartTasksPerDriver['data']),
                            backgroundColor: '#8b5cf6',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } },
                            y: { grid: { color: gridColor }, ticks: { color: textColor, stepSize: 1 } }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) { return ` ${ctx.raw} Tugas Selesai`; }
                                }
                            }
                        }
                    }
                });
            }
        });
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
