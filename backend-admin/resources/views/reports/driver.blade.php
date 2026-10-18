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
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-lg-4">
                <form action="{{ route('reports.driver') }}" method="GET" id="driverReportFilterForm">
                    <input type="hidden" name="preset" id="presetInput" value="{{ $preset }}">

                    <div class="row g-3 align-items-center">
                        <!-- Presets -->
                        <div class="col-12 d-flex flex-wrap align-items-center gap-2 pb-2 border-bottom border-light-subtle">
                            <span class="text-muted small fw-semibold me-2"><i class="fa-solid fa-sliders me-1"></i> Rentang Periode:</span>
                            <a href="javascript:void(0)" onclick="setPreset('today')" class="preset-pill {{ $preset == 'today' ? 'active' : '' }}">Hari Ini</a>
                            <a href="javascript:void(0)" onclick="setPreset('this_week')" class="preset-pill {{ $preset == 'this_week' ? 'active' : '' }}">Minggu Ini</a>
                            <a href="javascript:void(0)" onclick="setPreset('this_month')" class="preset-pill {{ $preset == 'this_month' ? 'active' : '' }}">Bulan Ini</a>
                            <a href="javascript:void(0)" onclick="setPreset('last_month')" class="preset-pill {{ $preset == 'last_month' ? 'active' : '' }}">Bulan Lalu</a>
                            <a href="javascript:void(0)" onclick="setPreset('all')" class="preset-pill {{ $preset == 'all' ? 'active' : '' }}">Semua Riwayat</a>
                            <a href="javascript:void(0)" onclick="setPreset('custom')" class="preset-pill {{ $preset == 'custom' ? 'active' : '' }}">Custom</a>
                        </div>

                        <!-- Date Filters -->
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ $startDate->toDateString() }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-bold mb-1">Tanggal Akhir</label>
                            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ $endDate->toDateString() }}">
                        </div>

                        <!-- Driver Filter -->
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold mb-1">Pilih Driver</label>
                            <select name="driver_id" class="form-select form-select-sm">
                                <option value="">Semua Driver</option>
                                @foreach($drivers as $d)
                                    <option value="{{ $d->id }}" {{ $driverId == $d->id ? 'selected' : '' }}>{{ $d->full_name ?? $d->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Vehicle Filter -->
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold mb-1">Pilih Kendaraan</label>
                            <select name="vehicle_id" class="form-select form-select-sm">
                                <option value="">Semua Mobil</option>
                                @foreach($allVehicles as $v)
                                    <option value="{{ $v->id }}" {{ $vehicleId == $v->id ? 'selected' : '' }}>{{ $v->plate_number }} ({{ $v->name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-2 d-flex gap-2 align-items-end mt-4">
                            <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold rounded-3 shadow-xs" style="background: var(--report-orange-gradient); border: none;">
                                <i class="fa-solid fa-filter me-1"></i> Terapkan
                            </button>
                            <a href="{{ route('reports.driver') }}" class="btn btn-sm btn-light border rounded-3 px-3" title="Reset Filter">
                                <i class="fa-solid fa-rotate-right"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- TOP KPI STATS CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
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

            <div class="col-6 col-lg-3">
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

            <div class="col-6 col-lg-3">
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

        <!-- 5 GRAFIK ANALITIK DRIVER & KENDARAAN -->
        <div class="row g-4 mb-4">

            <!-- GRAFIK 1: Persentase Pemakaian Kendaraan (Standby vs Operasional) -->
            <div class="col-lg-5">
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
            <div class="col-lg-7">
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
            <div class="col-lg-4">
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

            <!-- GRAFIK 4: Jarak Tempuh per Driver (KM) -->
            <div class="col-lg-4">
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
            <div class="col-lg-4">
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

        </div>

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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
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

        <!-- Hidden Form for Export with Base64 Charts -->
        <form id="exportWithChartsForm" method="POST" action="{{ route('reports.driver.export') }}" style="display: none;">
            @csrf
            <input type="hidden" name="preset" value="{{ $preset }}">
            <input type="hidden" name="start_date" value="{{ $startDate->toDateString() }}">
            <input type="hidden" name="end_date" value="{{ $endDate->toDateString() }}">
            <input type="hidden" name="driver_id" value="{{ $driverId }}">
            <input type="hidden" name="vehicle_id" value="{{ $vehicleId }}">
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

            document.getElementById('exportWithChartsForm').submit();
        }

        // INITIALIZE ALL 5 CHARTS
        document.addEventListener('DOMContentLoaded', function () {
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
